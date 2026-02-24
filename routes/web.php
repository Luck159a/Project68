<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\QueueController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\DoctorScheduleController;
use App\Http\Controllers\DashboardController;


// --- หน้าแรกและ Dashboard ---
Route::get('/', function () { return view('welcome'); });
Route::get('/dashboard', function () { return view('dashboard'); })->middleware(['auth', 'verified'])->name('dashboard');

// --- จัดการตารางเวลาหมอ (Resource Route สำหรับ CRUD) ---
Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('doctor-schedules', DoctorScheduleController::class);
});

// --- กลุ่ม Route สำหรับผู้ที่ล็อกอินแล้ว ---
Route::middleware('auth')->group(function () {
    
    // โปรไฟล์
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ✅ ส่วนของการจองคิว (Patient/User)
    Route::get('/queue/book/{scheduleId}', [QueueController::class, 'create'])->name('queues.create');
    Route::post('/queue/book', [QueueController::class, 'store'])->name('queues.store');
    Route::get('/queue/success/{id}', [QueueController::class, 'success'])->name('queue.success');
    Route::get('/queue/history', [QueueController::class, 'history'])->name('queue.history');

    // ✅ ส่วนของการจัดการคิว (Admin/Staff)
    Route::get('/queues', [QueueController::class, 'index'])->name('queues.index');
    Route::patch('/queues/{id}/status', [QueueController::class, 'updateStatus'])->name('queues.updateStatus');
    
    // ⭐ เพิ่มใหม่: Route สำหรับยกเลิกคิว (เพื่อรองรับปุ่มสีแดงที่คุณต้องการ)
    Route::patch('/queues/{id}/cancel', [QueueController::class, 'cancel'])->name('queues.cancel');

    // ส่วนของเจ้าหน้าที่และหมอ
    Route::get('/staff/dashboard', [StaffController::class, 'dashboard'])->name('staff.dashboard');
    Route::get('/report/daily', [ReportController::class, 'daily'])->name('report.daily');
    Route::get('/doctor/queue', [DoctorController::class, 'queueList'])->name('doctor.queue.list');
    Route::get('/doctor/report', [ReportController::class, 'doctorReport'])->name('doctor.report.pdf');
    Route::get('/admin/doctor/schedule', [DoctorScheduleController::class, 'index'])->name('doctor.schedule');

    // รายงาน PDF ต่างๆ
    Route::get('/admin/report/services/pdf', [ReportController::class, 'servicesPdf'])->name('report.service.pdf');
// เปลี่ยนจาก 'usersPdf' เป็น 'exportUserPDF' ให้ตรงกับที่แก้ใน Controller ล่าสุด
    Route::get('/admin/report/users/pdf', [App\Http\Controllers\ReportController::class, 'exportUserPDF'])->name('reports.users.pdf');
        // ไฟล์ routes/web.php
    Route::get('/queues/pdf/{id}', [QueueController::class, 'exportTicketPDF'])->name('queues.pdf');
    // ในไฟล์ routes/web.php
    // ตัวอย่างการตั้งชื่อให้ตรงกับหน้า View
    Route::get('/admin/export-queues-pdf', [QueueController::class, 'exportPDF'])
        ->name('admin.queues.export-pdf');
        Route::get('/admin/users/export-pdf', [App\Http\Controllers\UserController::class, 'exportPDF'])
        ->name('admin.users.export-pdf');
     // เพิ่มเข้าไปในกลุ่มที่ Admin/Staff เข้าถึงได้
    Route::get('/users/export-pdf', [UserController::class, 'exportPdf'])->name('admin.users.export-pdf');
    // Route สำหรับดาวน์โหลด PDF ของคิว (จัดการคิว)
    Route::get('/queues/export-pdf', [QueueController::class, 'exportPdf'])->name('queues.export-pdf');
    Route::get('/admin/report/services/pdf', [QueueController::class, 'exportPdf'])->name('reports.services.pdf');
    Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');
});

// จัดการข้อมูลผู้ใช้
Route::resource('users', UserController::class)->middleware('auth');

require __DIR__.'/auth.php';