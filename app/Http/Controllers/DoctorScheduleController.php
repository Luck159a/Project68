<?php

namespace App\Http\Controllers;

use App\Models\DoctorSchedule;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DoctorScheduleController extends Controller
{
    /**
     * ใช้ Constructor เพื่อเช็คสิทธิ์ Middleware
     */
    public function __construct()
    {
        // บรรทัดนี้จะทำงานได้ต้องมั่นใจว่าคุณมี Middleware ชื่อ 'role' อยู่ในโปรเจกต์
        // ถ้าไม่มี Middleware นี้ ให้ลบบรรทัดล่างนี้ออก แล้วใช้การเช็คในฟังก์ชันแทน (ผมทำเผื่อไว้ให้แล้ว)
        // $this->middleware(['auth', 'role:admin,staff'])->except(['index', 'show']);
    }

    /**
     * แสดงรายการตารางเวลา (ทุกคนดูได้)
     */
    public function index(Request $request)
    {
        $query = DoctorSchedule::with('user');

        if ($request->filled('search')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        $schedules = $query->orderBy('schedule_date', 'desc')->paginate(10);
        return view('doctor_schedules.index', compact('schedules'));
    }

    /**
     * หน้าฟอร์มสร้างตารางเวลา (อนุญาต Admin และ Staff)
     */
    public function create()
    {
        // แก้ไข: ใช้ strtolower เพื่อรองรับ STAFF หรือ staff
        if (!Auth::check() || !in_array(strtolower(Auth::user()->role), ['admin', 'staff'])) {
            abort(403, 'คุณไม่มีสิทธิ์เข้าถึงหน้านี้');
        }

        $doctors = User::where('role', 'doctor')->get();
        return view('doctor_schedules.form', compact('doctors'));
    }

    /**
     * บันทึกข้อมูล
     */
    public function store(Request $request)
    {
        if (!Auth::check() || !in_array(strtolower(Auth::user()->role), ['admin', 'staff'])) {
            abort(403);
        }

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'schedule_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
            'status' => 'nullable|string'
        ]);

        DoctorSchedule::create($validated);
        return redirect()->route('doctor-schedules.index')->with('success', 'บันทึกตารางเวลาสำเร็จ');
    }

    /**
     * หน้าแก้ไข
     */
    public function edit(DoctorSchedule $doctorSchedule)
    {
        if (!Auth::check() || !in_array(strtolower(Auth::user()->role), ['admin', 'staff'])) {
            abort(403);
        }

        $doctors = User::where('role', 'doctor')->get();
        return view('doctor_schedules.form', [
            'schedule' => $doctorSchedule,
            'doctors' => $doctors
        ]);
    }

    /**
     * อัปเดตข้อมูล
     */
    public function update(Request $request, DoctorSchedule $doctorSchedule)
    {
        if (!Auth::check() || !in_array(strtolower(Auth::user()->role), ['admin', 'staff'])) {
            abort(403);
        }

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'schedule_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
            'status' => 'required|string'
        ]);

        $doctorSchedule->update($validated);
        return redirect()->route('doctor-schedules.index')->with('success', 'อัปเดตตารางเวลาสำเร็จ');
    }

    /**
     * ลบข้อมูล
     */
    public function destroy(DoctorSchedule $doctorSchedule)
    {
        if (!Auth::check() || !in_array(strtolower(Auth::user()->role), ['admin', 'staff'])) {
            abort(403);
        }

        $doctorSchedule->delete();
        return redirect()->route('doctor-schedules.index')->with('success', 'ลบตารางเวลาเรียบร้อยแล้ว');
    }
} // ปีกกาปิด Class ต้องอยู่ตรงนี้ท้ายสุดเสมอ!