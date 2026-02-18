<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf; 

class ReportController extends Controller
{
    /**
     * ฟังก์ชันสำหรับส่งออกรายงานผู้ใช้เป็น PDF
     */
    public function exportUserPDF() 
    {
        // 1. ดึงข้อมูลผู้ใช้ทั้งหมด
        $users = User::all();
        
        // 2. โหลด View และตั้งค่า PDF
        $pdf = Pdf::loadView('reports.users_pdf', compact('users'))
                  ->setPaper('a4', 'portrait')
                  ->setOptions([
                      'tempDir' => public_path(),
                      'chroot'  => public_path(), // สำคัญมาก: อนุญาตให้ DomPDF เข้าถึงไฟล์ใน public/fonts ได้
                      'isHtml5ParserEnabled' => true,
                      'isRemoteEnabled' => true,
                      'defaultFont' => 'Sarabun' // กำหนดฟอนต์เริ่มต้นให้ตรงกับในไฟล์ Blade
                  ]);

        // 3. แสดงผล PDF บน Browser
        return $pdf->stream('users_report.pdf');
    }
}