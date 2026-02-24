<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Queue; // เพิ่มไว้ตรงนี้เพื่อให้เรียกใช้งานง่าย
use Barryvdh\DomPDF\Facade\Pdf; 

class ReportController extends Controller
{
    /**
     * สำหรับส่งออกรายงานผู้ใช้ทั้งหมด
     */
    public function exportUserPDF() 
    {
        $users = User::all();
        
        $pdf = Pdf::loadView('reports.users_pdf', compact('users'))
                  ->setPaper('a4', 'portrait')
                  ->setOptions($this->getPdfOptions());

        return $pdf->stream('users_report.pdf');
    }

    /**
     * สำหรับส่งออกรายงานคิวรายวัน (ปุ่มในหน้า Staff)
     */
    public function daily()
    {
        // ดึงข้อมูลคิวของวันนี้ พร้อมข้อมูลคนไข้และตารางหมอ
        $queues = Queue::with(['user', 'doctorSchedule.user'])
                    ->whereDate('created_at', today())
                    ->get();

        // โหลด View รายงานคิว (ต้องมีไฟล์ resources/views/reports/daily_queues.blade.php)
        $pdf = Pdf::loadView('reports.daily_queues', compact('queues'))
                  ->setPaper('a4', 'landscape') // คิวมักจะมีคอลัมน์เยอะ แนะนำแนวนอน (landscape)
                  ->setOptions($this->getPdfOptions());
        
        return $pdf->stream('daily-report-' . date('Y-m-d') . '.pdf');
    }

    /**
     * ฟังก์ชันตัวช่วยสำหรับตั้งค่า PDF ให้รองรับภาษาไทยและฟอนต์ Sarabun
     */
    private function getPdfOptions()
    {
        return [
            'tempDir' => public_path(),
            'chroot'  => public_path(),
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => 'Sarabun' 
        ];
    }
}