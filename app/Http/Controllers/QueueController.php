<?php

namespace App\Http\Controllers;

use App\Models\Queue;
use App\Models\DoctorSchedule;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class QueueController extends Controller
{
    /**
     * Display: หน้ารวมรายการคิว (Staff/Admin)
     */
    public function index(Request $request)
    {
        if (strtolower(Auth::user()->role) === 'patient') {
            return redirect()->route('queue.history');
        }

        $date = $request->input('date');
        $search = $request->input('search');

        $query = Queue::with(['user', 'doctorSchedule.user']);

        // กรองตามวันที่ (เพิ่มเงื่อนไข 'today')
        if ($date === 'today') {
            $today = Carbon::today()->toDateString();
            $query->whereHas('doctorSchedule', function ($q) use ($today) {
                $q->where('schedule_date', $today);
            });
        } elseif ($date) {
            $query->whereHas('doctorSchedule', function ($q) use ($date) {
                $q->where('schedule_date', $date);
            });
        }

        // กรองตามคำค้นหา
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('labelNo', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($u) use ($search) {
                        $u->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('doctorSchedule.user', function ($d) use ($search) {
                        $d->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $queues = $query->orderBy('labelNo', 'asc')
            ->paginate(10)
            ->appends($request->query());

        $availableDates = DoctorSchedule::select('schedule_date')
            ->distinct()
            ->orderBy('schedule_date', 'asc')
            ->get();

        return view('queues.index', compact('queues', 'availableDates'));
    }

    /**
     * Book: เลือกตารางหมอ (ดักจับถ้าเป็นหมอให้ดูคิวตัวเอง)
     */
    public function book()
    {
        $user = auth()->user();

        // 🌟 ถ้าผู้ใช้งานเป็น "หมอ" (Doctor) 🌟
        if (strtolower($user->role) === 'doctor') {
            $today = \Carbon\Carbon::today()->toDateString();

            $todayQueues = \App\Models\Queue::with(['user', 'doctorSchedule'])
                ->whereHas('doctorSchedule', function ($q) use ($user, $today) {
                    $q->where('user_id', $user->id) 
                      ->where('schedule_date', $today);
                })
                ->where('status', '!=', 'ยกเลิก')
                ->orderBy('period', 'asc')
                ->get();

            $totalQueuesToday = $todayQueues->count();

            return view('queues.doctor_today', compact('todayQueues', 'totalQueuesToday'));
        }

        // 🌟 ถ้าเป็น "คนไข้" หรือ "แอดมิน" ให้ไปหน้าเลือกหมอตามปกติ 🌟
        $schedules = DoctorSchedule::with('user')
            ->where('schedule_date', '>=', now()->toDateString())
            ->orderBy('schedule_date', 'asc')
            ->get();
            
        return view('queues.book', compact('schedules'));
    }

    /**
     * Create View: เลือกช่วงเวลาที่จะจอง
     */
    public function create($scheduleId)
    {
        $schedule = DoctorSchedule::with(['user', 'queues'])->findOrFail($scheduleId);
        
        $bookedPeriods = $schedule->queues
            ->where('status', '!=', 'ยกเลิก')
            ->pluck('period')
            ->toArray();

        $slots = [];
        $startTime = Carbon::parse($schedule->start_time);
        $endTime = Carbon::parse($schedule->end_time);

        while ($startTime->copy()->addMinutes(20) <= $endTime) {
            $slotEnd = $startTime->copy()->addMinutes(20);
            $timeRange = $startTime->format('H:i') . ' - ' . $slotEnd->format('H:i');
            
            $isBooked = in_array($timeRange, $bookedPeriods);

            $slots[] = [
                'time' => $timeRange,
                'is_available' => !$isBooked
            ];
            $startTime->addMinutes(20);
        }

        return view('queues.create', compact('schedule', 'slots'));
    }

    /**
     * Store: บันทึกข้อมูลการจองคิว
     */
    public function store(Request $request)
    {
        $request->validate([
            'docschId' => 'required',
            'period' => 'required',
            'Note' => 'nullable|string'
        ], [
            'period.required' => 'กรุณาเลือกช่วงเวลาที่ต้องการก่อนกดยืนยันครับ'
        ]);

        $user = auth()->user();
        $schedule = DoctorSchedule::findOrFail($request->docschId);

        // ระบบตรวจสอบ: จำกัดคนไข้จองได้ 1 คิวต่อวัน
        if (strtolower($user->role) === 'patient') {
            $existingQueue = Queue::where('userId', $user->id)
                ->whereHas('doctorSchedule', function ($query) use ($schedule) {
                    $query->where('schedule_date', $schedule->schedule_date);
                })
                ->where('status', '!=', 'ยกเลิก') 
                ->first();

            if ($existingQueue) {
                return redirect()->back()->withErrors([
                    'period' => 'คุณได้ทำการจองคิวสำหรับวันที่ ' . Carbon::parse($schedule->schedule_date)->format('d/m/Y') . ' ไปแล้ว (จำกัดการจอง 1 คิวต่อวัน)'
                ]);
            }
        }

        // ตรวจสอบว่าช่วงเวลานี้มีคนอื่นชิงจองไปแล้วหรือยัง
        $isTimeSlotTaken = Queue::where('docschId', $schedule->id)
            ->where('period', $request->period)
            ->where('status', '!=', 'ยกเลิก')
            ->exists();

        if ($isTimeSlotTaken) {
            return redirect()->back()->withErrors([
                'period' => 'ขออภัย ช่วงเวลานี้ถูกจองไปแล้ว กรุณาเลือกช่วงเวลาอื่น'
            ]);
        }

        // 🌟 [ระบบแปลงหมอเป็นตัวอักษร A, B, C...] 🌟
        // 1. ดึง ID ของหมอทั้งหมดมาเรียงลำดับ
        $allDoctors = \App\Models\User::where('role', 'doctor')
            ->orderBy('id', 'asc')
            ->pluck('id')
            ->toArray();
        
        // 2. หาว่าหมอที่ถูกจองคิวอยู่ลำดับที่เท่าไหร่
        $doctorIndex = array_search($schedule->user_id, $allDoctors);

        // 3. แปลงเป็นตัวอักษร (65 คือ A, 66 คือ B ...)
        $doctorLetter = ($doctorIndex !== false) ? chr(65 + $doctorIndex) : 'A';

        // 4. บันทึกข้อมูลคิวลง Database (ดึงคิวล่าสุดของตารางนั้นๆ)
        $lastQueue = Queue::where('docschId', $schedule->id)->count();
        
        // ประกอบร่างเลขคิว เช่น Q-A001, Q-B002
        $newQueueNumber = 'Q-' . $doctorLetter . str_pad($lastQueue + 1, 3, '0', STR_PAD_LEFT);

        $queue = Queue::create([
            'userId' => $user->id,
            'docschId' => $schedule->id,
            'period' => $request->period,
            'labelNo' => $newQueueNumber,
            'Note' => $request->Note,
            'status' => 'รอเรียก',
            'created_by' => auth()->id() // กัน Error created_by
        ]);

        return redirect()->route('queue.success', $queue->id)->with('success', 'จองคิวสำเร็จแล้ว!');
    }

    /**
     * Success: หน้าแสดงใบยืนยันหลังจองสำเร็จ
     */
    public function success($id)
    {
        $queue = Queue::with(['user', 'doctorSchedule.user'])->findOrFail($id);
        $queueBeforeCount = Queue::where('docschId', $queue->docschId)
            ->where('id', '<', $queue->id)
            ->whereIn('status', ['รอเรียก', 'กำลังใช้บริการ'])
            ->count();
        $myOrder = $queueBeforeCount + 1;

        return view('queues.success', compact('queue', 'queueBeforeCount', 'myOrder'));
    }

    /**
     * History: ดูประวัติการจอง (สำหรับคนไข้)
     */
    public function history()
    {
        $queues = Queue::with(['doctorSchedule.user'])
            ->where('userId', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('queues.history', compact('queues'));
    }

    /**
     * Update Status: สำหรับเจ้าหน้าที่เรียกคิว หรือจบงาน
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:รอเรียก,กำลังใช้บริการ,เสร็จสิ้น'
        ]);

        $queue = Queue::findOrFail($id);
        $queue->status = $request->status;
        $queue->save();

        return back()->with('success', 'อัปเดตสถานะสำเร็จ!');
    }

    /**
     * Cancel: ยกเลิกคิว (คนไข้ยกเลิกคิวตัวเอง)
     */
    public function cancel($id)
    {
        $queue = Queue::findOrFail($id);
        
        $user = auth()->user();
        if (strtolower($user->role) === 'patient' && $queue->userId !== $user->id) {
            return redirect()->back()->withErrors('คุณไม่มีสิทธิ์ยกเลิกคิวนี้ครับ');
        }

        $queue->update(['status' => 'ยกเลิก']);
        return redirect()->back()->with('success', 'ยกเลิกคิวเรียบร้อยแล้ว ช่วงเวลานี้จะกลับมาว่างอีกครั้ง');
    }

    /**
     * ฟังก์ชันสำหรับสร้างไฟล์ PDF ใบยืนยันคิว (รายคน)
     */
    public function exportTicketPDF($id)
    {
        $queue = Queue::with(['user', 'doctorSchedule.user'])->findOrFail($id);
        $queueBeforeCount = Queue::where('docschId', $queue->docschId)
            ->where('id', '<', $queue->id)
            ->whereIn('status', ['รอเรียก', 'กำลังใช้บริการ'])
            ->count();
        $myOrder = $queueBeforeCount + 1;

        $pdf = Pdf::loadView('reports.queue_ticket', compact('queue', 'queueBeforeCount', 'myOrder'))
            ->setPaper([0, 0, 400, 500], 'portrait')
            ->setOptions([
                'isRemoteEnabled' => true,
                'defaultFont' => 'Sarabun'
            ]);

        return $pdf->stream('Queue-Ticket-' . $queue->labelNo . '.pdf');
    }

    /**
     * ฟังก์ชันสำหรับส่งออกรายงานคิวทั้งหมด หรือตามเงื่อนไขการค้นหา (PDF)
     */
    public function exportPDF(Request $request)
    {
        $query = Queue::with(['user', 'doctorSchedule.user']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('labelNo', 'like', "%{$search}%")
                  ->orWhereHas('user', function($u) use ($search) {
                      $u->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('doctorSchedule.user', function($d) use ($search) {
                      $d->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('date')) {
            $query->whereHas('doctorSchedule', function($q) use ($request) {
                $q->where('schedule_date', $request->date);
            });
        }

        $queues = $query->orderBy('labelNo', 'asc')->get();

        $pdf = Pdf::loadView('reports.all_queues_pdf', compact('queues'))
                  ->setPaper('a4', 'landscape')
                  ->setOptions([
                      'isRemoteEnabled' => true,
                      'defaultFont' => 'Sarabun'
                  ]);

        return $pdf->stream('Queue-Report.pdf');
    }
}