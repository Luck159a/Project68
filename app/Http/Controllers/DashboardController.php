<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Queue; // 🌟 อย่าลืมบรรทัดนี้ ไม่งั้นจะ Error Class Queue not found
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // ถ้าเป็นคนไข้ (Patient) ให้แสดงหน้า Dashboard ปกติ
        if (strtolower($user->role) === 'patient') {
            return view('dashboard'); // หรือ view('dashboard') ตามที่คุณตั้งไว้
        }

        // สำหรับ Admin และ Staff ให้คำนวณสถิติ
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();

        $stats = [
            // สถิติรายวัน (สมัครวันนี้ และ คิววันนี้)
            'daily' => [
                // จำนวนคนสมัครใหม่วันนี้
                'doctor'  => User::where('role', 'doctor')->whereDate('created_at', $today)->count(),
                'staff'   => User::where('role', 'staff')->whereDate('created_at', $today)->count(),
                'patient' => User::where('role', 'patient')->whereDate('created_at', $today)->count(),
                
                // จำนวนคิวทั้งหมดที่เกิดขึ้นวันนี้
                'queues'  => Queue::whereDate('created_at', $today)->count(),
                
                // คิววันนี้ แยกตาม Role ของคนที่ทำรายการจอง (เช็คจากความสัมพันธ์ user)
                'queues_doctor'  => Queue::whereDate('created_at', $today)->whereHas('user', function($q) { 
                    $q->where('role', 'doctor'); 
                })->count(),
                
                'queues_staff'   => Queue::whereDate('created_at', $today)->whereHas('user', function($q) { 
                    $q->where('role', 'staff'); 
                })->count(),
                
                'queues_patient' => Queue::whereDate('created_at', $today)->whereHas('user', function($q) { 
                    $q->where('role', 'patient'); 
                })->count(),
            ],
            
            // สถิติรายเดือน (สมัครเดือนนี้)
            'monthly' => [
                'doctor'  => User::where('role', 'doctor')->where('created_at', '>=', $startOfMonth)->count(),
                'staff'   => User::where('role', 'staff')->where('created_at', '>=', $startOfMonth)->count(),
                'patient' => User::where('role', 'patient')->where('created_at', '>=', $startOfMonth)->count(),
            ],

            // ยอดรวมทั้งหมดสะสมตั้งแต่เปิดระบบ (Lifetime)
            'total' => [
                'patient' => User::where('role', 'patient')->count(),
                'queues'  => Queue::count(),
            ]
        ];
        
        return view('dashboard-admin', compact('stats'));
    }
}