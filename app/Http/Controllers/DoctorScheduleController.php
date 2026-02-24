<?php

namespace App\Http\Controllers;

use App\Models\DoctorSchedule;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DoctorScheduleController extends Controller
{
    /**
     * แสดงรายการตารางเวลา (ทุกคนดูได้)
     */
    public function index(Request $request)
    {
        $query = DoctorSchedule::with('user');

        // ระบบค้นหาชื่อแพทย์
        if ($request->filled('search')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        $schedules = $query->orderBy('schedule_date', 'desc')->paginate(10);
        return view('doctor_schedules.index', compact('schedules'));
    }

    /**
     * หน้าฟอร์มสร้างตารางเวลา (เฉพาะ Admin และ Staff)
     */
    public function create()
    {
        // เช็คสิทธิ์: ถ้าไม่ใช่ admin และไม่ใช่ staff ให้ดีดออก
        if (Auth::user()->role !== 'admin' && Auth::user()->role !== 'staff') {
            abort(403, 'คุณไม่มีสิทธิ์เข้าถึงหน้านี้');
        }

        $doctors = User::where('role', 'doctor')->get();
        // ใช้หน้า doctor_schedules.form ตามโครงสร้างเดิมของคุณ
        return view('doctor_schedules.form', compact('doctors'));
    }

    /**
     * บันทึกข้อมูลตารางเวลา (เฉพาะ Admin และ Staff)
     */
    public function store(Request $request)
    {
        // เช็คสิทธิ์เพื่อความปลอดภัย
        if (Auth::user()->role !== 'admin' && Auth::user()->role !== 'staff') {
            abort(403);
        }

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'schedule_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
            'status' => 'nullable|string' // เพิ่มกรณีมีสถานะ available/unavailable
        ]);

        DoctorSchedule::create($validated);
        
        return redirect()->route('doctor-schedules.index')->with('success', 'บันทึกตารางเวลาสำเร็จ');
    }

    /**
     * หน้าฟอร์มแก้ไข (เฉพาะ Admin และ Staff)
     */
    public function edit(DoctorSchedule $doctorSchedule)
    {
        // เช็คสิทธิ์
        if (Auth::user()->role !== 'admin' && Auth::user()->role !== 'staff') {
            abort(403);
        }

        $doctors = User::where('role', 'doctor')->get();
        return view('doctor_schedules.form', [
            'schedule' => $doctorSchedule,
            'doctors' => $doctors
        ]);
    }

    /**
     * อัปเดตข้อมูล (เฉพาะ Admin และ Staff)
     */
    public function update(Request $request, DoctorSchedule $doctorSchedule)
    {
        // เช็คสิทธิ์
        if (Auth::user()->role !== 'admin' && Auth::user()->role !== 'staff') {
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
     * ลบข้อมูล (เฉพาะ Admin และ Staff)
     */
    public function destroy(DoctorSchedule $doctorSchedule)
    {
        // เช็คสิทธิ์
        if (Auth::user()->role !== 'admin' && Auth::user()->role !== 'staff') {
            abort(403);
        }

        $doctorSchedule->delete();
        
        return redirect()->route('doctor-schedules.index')->with('success', 'ลบตารางเวลาเรียบร้อยแล้ว');
    }
}