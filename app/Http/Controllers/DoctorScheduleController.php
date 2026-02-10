<?php

namespace App\Http\Controllers;

use App\Models\DoctorSchedule;
use App\Models\User;
use Illuminate\Http\Request;

class DoctorScheduleController extends Controller
{
    public function index(Request $request)
    {
        $query = DoctorSchedule::with('user');

        // Search/Filter logic
        if ($request->filled('search')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        $schedules = $query->paginate(10); // แบ่งหน้าเพจ
        return view('doctor_schedules.index', compact('schedules'));
    }

    public function create()
{
    // ดึงเฉพาะ User ที่มีบทบาทเป็น doctor
    $doctors = \App\Models\User::where('role', 'doctor')->get();
    
    // ส่งตัวแปร $doctors ไปที่ View
    return view('doctor_schedules.form', compact('doctors'));
}

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required',
            'schedule_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
        ]);

        DoctorSchedule::create($validated);
        return redirect()->route('doctor-schedules.index')->with('success', 'บันทึกสำเร็จ');
    }

    // เพิ่ม Edit, Update, Destroy ตามความเหมาะสม...
    public function edit(DoctorSchedule $doctorSchedule)
    {
        $doctors = User::where('role', 'doctor')->get();
        // ส่งตัวแปรชื่อ $schedule เพื่อให้ตรงกับใน View
        return view('doctor_schedules.form', [
            'schedule' => $doctorSchedule,
            'doctors' => $doctors
        ]);
    }

    public function update(Request $request, DoctorSchedule $doctorSchedule)
    {
        $validated = $request->validate([
            'user_id' => 'required',
            'schedule_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
        ]);

        $doctorSchedule->update($validated);
        return redirect()->route('doctor-schedules.index')->with('success', 'อัพเดทสำเร็จ');
    }

    public function destroy(DoctorSchedule $doctorSchedule)
    {
        $doctorSchedule->delete();
        return redirect()->route('doctor-schedules.index')->with('success', 'ลบสำเร็จ');
    }
}