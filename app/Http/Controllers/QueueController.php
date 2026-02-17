<?php

namespace App\Http\Controllers;

use App\Models\Queue;
use App\Models\DoctorSchedule;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class QueueController extends Controller
{
    /**
     * Display: หน้ารวมรายการคิว (Staff/Admin)
     * กรองตามคำค้นหา และ วันที่จากตารางแพทย์
     */
    public function index(Request $request)
    {
        // ตรวจสอบสิทธิ์: ถ้าเป็นคนไข้ ให้เด้งไปหน้าประวัติของตัวเอง
        if (strtolower(Auth::user()->role) === 'patient') {
            return redirect()->route('queue.history');
        }

        // 1. ดึงวันที่ที่มีในตารางตารางหมอทั้งหมดมาทำ Dropdown
        $availableDates = DoctorSchedule::select('schedule_date')
            ->distinct()
            ->orderBy('schedule_date', 'asc')
            ->get();

        // 2. เริ่มต้น Query ข้อมูลคิว
        $query = Queue::with(['user', 'doctorSchedule.user']);

        // 3. กรองตามวันที่ที่เลือก (สำคัญมาก)
        // เมื่อเลือกวันที่ 24/02/2026 ระบบจะแสดงเฉพาะคิวในวันนั้นเท่านั้น
        if ($request->filled('date')) {
            $selectedDate = $request->date;
            $query->whereHas('doctorSchedule', function($q) use ($selectedDate) {
                $q->where('schedule_date', $selectedDate);
            });
        }

        // 4. กรองตามคำค้นหา (เลขคิว หรือ ชื่อคนไข้)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('labelNo', 'like', "%{$search}%")
                  ->orWhereHas('user', function($u) use ($search) {
                      $u->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // 5. เรียงลำดับตามเลขคิว และแบ่งหน้าแสดงผล
        // ใช้ appends(request()->query()) เพื่อให้ค่าวันที่ไม่หายเมื่อเปลี่ยนหน้า Pagination
        $queues = $query->orderBy('labelNo', 'asc')
                        ->paginate(10)
                        ->appends($request->query());

        // 6. ส่งข้อมูลไปยังหน้า View
        return view('queues.index', compact('queues', 'availableDates'));
    }

    /**
     * Book: เลือกตารางหมอ (สำหรับเริ่มจองคิว)
     */
    public function book()
    {
        $schedules = DoctorSchedule::with('user')
            ->where('schedule_date', '>=', now()->toDateString())
            ->orderBy('schedule_date', 'asc')
            ->orderBy('start_time', 'asc')
            ->get();

        return view('queues.book', compact('schedules'));
    }

    /**
     * Create View: เลือกช่วงเวลาที่จะจอง
     */
    public function create($scheduleId)
    {
        $schedule = DoctorSchedule::with(['user', 'queues'])->findOrFail($scheduleId);
        $bookedPeriods = $schedule->queues->pluck('period')->toArray();

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
     * Store: บันทึกข้อมูลการจองคิวลงฐานข้อมูล
     */
    public function store(Request $request)
    {
        $request->validate([
            'docschId' => 'required|exists:doctor_schedules,id',
            'period' => 'required',
        ]);

        $exists = Queue::where('docschId', $request->docschId)
                       ->where('period', $request->period)
                       ->exists();
        
        if ($exists) {
            return back()->withErrors(['period' => 'ขออภัย ช่วงเวลานี้ถูกจองไปแล้ว']);
        }

        $queue = Queue::create([
            'docschId' => $request->docschId,
            'userId' => Auth::id(),
            'labelNo' => 'Q-' . strtoupper(substr(uniqid(), -4)),
            'period' => $request->period,
            'Note' => $request->Note,
            'status' => 'รอเรียก',
            'created_by' => Auth::id()
        ]);

        return redirect()->route('queue.success', $queue->id)->with('success', 'จองคิวสำเร็จ!');
    }

    /**
     * Success: หน้าแสดงใบยืนยันหลังจองสำเร็จ
     */
    public function success($id)
    {
        $queue = Queue::with(['doctorSchedule.user'])->findOrFail($id);
        return view('queues.success', compact('queue'));
    }

    /**
     * History: ดูประวัติการจอง (สำหรับคนไข้)
     */
    public function history()
    {
        $queues = Queue::with(['doctorSchedule.user'])
            ->where('userId', Auth::id())
            ->latest()
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
     * Cancel: ยกเลิกคิว
     */
    public function cancel($id)
    {
        $queue = Queue::findOrFail($id);
        $queue->update(['status' => 'ยกเลิก']);

        return redirect()->back()->with('success', 'ยกเลิกคิวเรียบร้อยแล้ว');
    }
}