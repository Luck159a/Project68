<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// use App\Models\Booking; // สมมติว่ามี Model การจอง

class QueueController extends Controller
{
    public function index()
    {
        return view('queues.index');
    }

    // ฟังก์ชันสำหรับหน้าจองคิว
    public function book() 
{
    return view('queues.book');
}
    public function history()
    {
        // โค้ดสำหรับดึงข้อมูลประวัติการจองมาแสดง
        // $histories = Booking::all(); 
        
        return view('queues.history'); 
    }
}