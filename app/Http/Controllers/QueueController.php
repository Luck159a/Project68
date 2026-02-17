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
     */
   /**
 * Display: หน้ารวมรายการคิว (Staff/Admin)
 */
/**
 * Display: หน้ารวมรายการคิว (Staff/Admin)
 */
public function index(Request $request)
{
    // 1. ตรวจสอบสิทธิ์ (ถ้าเป็นคนไข้ให้เด้งไปหน้าประวัติ)
    if (strtolower(Auth::user()->role) === 'patient') {
        return redirect()->route('queue.history');
    }

    // 2. รับค่าจาก Filter และ Search
    $date = $request->input('date');
    $search = $request->input('search');

    // 3. เริ่ม Query พร้อมดึงความสัมพันธ์ (Eager Loading)
    $query = Queue::with(['user', 'doctorSchedule.user']);

    // ⭐ กรองตามวันที่ (ข้ามไปเช็คที่ตาราง doctor_schedules)
    if ($date) {
        $query->whereHas('doctorSchedule', function($q) use ($date) {
            $q->where('schedule_date', $date);
        });
    }

    // ⭐ กรองตามคำค้นหา (เลขคิว, ชื่อคนไข้, หรือชื่อหมอ)
    if ($search) {
        $query->where(function($q) use ($search) {
            $q->where('labelNo', 'like', "%{$search}%")
              ->orWhereHas('user', function ($u) use ($search) {
                  $u->where('name', 'like', "%{$search}%");
              })
              ->orWhereHas('doctorSchedule.user', function ($d) use ($search) {
                  $d->where('name', 'like', "%{$search}%");
              });
        });
    }

    // 4. ดึงข้อมูลรายการคิว (เรียงลำดับตามความเหมาะสม)
    // เปลี่ยนจาก id desc เป็น labelNo asc หรือเลือกตามต้องการครับ
    $queues = $query->orderBy('labelNo', 'asc') 
        ->paginate(10)
        ->appends($request->query());

    // 5. ดึงรายการวันที่ทั้งหมดที่มีในระบบ (เพื่อให้ Select Box มีตัวเลือกวันที่)
    $availableDates = DoctorSchedule::select('schedule_date')
        ->distinct()
        ->orderBy('schedule_date', 'asc')
        ->get();

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
     * ⭐ แก้ไขแล้ว: History: ดูประวัติการจอง (สำหรับคนไข้)
     * ดึงชื่อแพทย์และเรียงลำดับล่าสุดมาแสดง
     */
    public function history()
    {
        // โหลด doctorSchedule.user เพื่อให้แสดงชื่อแพทย์ในหน้าประวัติได้
        $queues = Queue::with(['doctorSchedule.user'])
            ->where('userId', Auth::id())
            ->orderBy('created_at', 'desc') // เอาประวัติล่าสุดขึ้นก่อน
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
    /**
 * Cancel: ยกเลิกคิว
 */
public function cancel($id)
{
    $queue = Queue::findOrFail($id);
    
    // เปลี่ยนจาก 'cancelled' เป็น 'ยกเลิก' ให้ตรงกับค่าใน Database
    $queue->update(['status' => 'ยกเลิก']);

    return redirect()->back()->with('success', 'ยกเลิกคิวเรียบร้อยแล้ว');
}
}