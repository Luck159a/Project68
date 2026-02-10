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
     * Display: แสดงรายการคิว พร้อมระบบ Search และ Pagination
     */
    public function index(Request $request)
    {
        $queues = Queue::with(['doctorSchedule.user'])
            ->where('userId', Auth::id()) 
            ->when($request->search, function($query, $search) {
                $query->where('labelNo', 'like', "%{$search}%")
                      ->orWhere('status', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10);

        return view('queues.index', compact('queues'));
    }

    /**
     * Book: แสดงรายการตารางเวลาของหมอทั้งหมดที่เปิดให้จอง
     * แก้ไข Error: Call to undefined method App\Http\Controllers\QueueController::book()
     */
    public function book()
    {
        // ดึงข้อมูลตารางหมอที่ยังมาไม่ถึง (อนาคต) เพื่อให้เลือกจอง
        $schedules = DoctorSchedule::with('user')
            ->where('start_time', '>=', now())
            ->orderBy('start_time', 'asc')
            ->get();

        return view('queues.book', compact('schedules'));
    }

    /**
     * Create View: คำนวณช่วงเวลาละ 20 นาที และเช็กคิวว่าง
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
     * Store: บันทึกข้อมูลการจองใหม่
     */
    public function store(Request $request)
    {
        $request->validate([
            'docschId' => 'required|exists:doctor_schedules,id',
            'period' => 'required',
        ]);

        // Double Check ป้องกันการจองซ้ำ
        $exists = Queue::where('docschId', $request->docschId)
                       ->where('period', $request->period)
                       ->exists();
        
        if ($exists) {
            return back()->withErrors(['period' => 'ขออภัย ช่วงเวลานี้ถูกจองไปแล้ว']);
        }

        Queue::create([
            'docschId' => $request->docschId,
            'userId' => Auth::id(),
            'labelNo' => 'Q-' . strtoupper(substr(uniqid(), -4)),
            'period' => $request->period,
            'Note' => $request->Note,
            'status' => 'รอเรียก',
            'created_by' => Auth::id()
        ]);

        return redirect()->route('queues.index')->with('success', 'จองคิวสำเร็จ!');
    }

    /**
     * Update Status: เปลี่ยนสถานะคิว (รอเรียก -> กำลังใช้บริการ -> เสร็จสิ้น)
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:รอเรียก,กำลังใช้บริการ,เสร็จสิ้น'
        ]);

        $queue = Queue::findOrFail($id);
        $queue->status = $request->status;
        $queue->save();

        if ($request->ajax()) {
            return response()->json([
                'success' => true, 
                'message' => 'อัปเดตสถานะเป็น ' . $request->status . ' เรียบร้อยแล้ว'
            ]);
        }

        return back()->with('success', 'อัปเดตสถานะสำเร็จ!');
        }
}