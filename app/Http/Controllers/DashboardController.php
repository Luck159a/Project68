<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // ถ้าเป็นคนไข้ (Patient) ให้แสดงหน้า Dashboard ปกติแบบไม่มีสถิติ
        if (strtolower($user->role) === 'patient') {
            return view('dashboard');
        }

        // สำหรับ Admin และ Staff ให้คำนวณสถิติ
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();

        $stats = [
            // สถิติรายวัน (สมัครวันนี้)
            'daily' => [
                'doctor'  => User::where('role', 'doctor')->whereDate('created_at', $today)->count(),
                'staff'   => User::where('role', 'staff')->whereDate('created_at', $today)->count(),
                'patient' => User::where('role', 'patient')->whereDate('created_at', $today)->count(),
            ],
            // สถิติรายเดือน (สมัครเดือนนี้)
            'monthly' => [
                'doctor'  => User::where('role', 'doctor')->where('created_at', '>=', $startOfMonth)->count(),
                'staff'   => User::where('role', 'staff')->where('created_at', '>=', $startOfMonth)->count(),
                'patient' => User::where('role', 'patient')->where('created_at', '>=', $startOfMonth)->count(),
            ]
        ];

        return view('dashboard-admin', compact('stats'));
    }
}