<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf; // เพิ่มบรรทัดนี้
class ReportController extends Controller
{
  public function usersPdf()
{
    $users = \App\Models\User::all();

    $pdf = Pdf::loadView('reports.users_pdf', compact('users'))
              ->setPaper('a4', 'portrait')
              ->setOptions([
                  'defaultFont' => 'Sarabun', // ตั้งค่าฟอนต์เริ่มต้นเป็นชื่อที่เราตั้งใน Blade
                  'isHtml5ParserEnabled' => true,
                  'isRemoteEnabled' => true 
              ]);

    return $pdf->stream('user-report.pdf');
}
}