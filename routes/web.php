<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\QueueController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\DoctorScheduleController;

// --- หน้าแรกและ Dashboard ---
Route::get('/', function () { return view('welcome'); });
Route::get('/dashboard', function () { return view('dashboard'); })->middleware(['auth', 'verified'])->name('dashboard');

// --- จัดการตารางเวลาหมอ ---
Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('doctor-schedules', DoctorScheduleController::class);
});

// --- กลุ่ม Route สำหรับผู้ที่ล็อกอินแล้ว ---
Route::middleware('auth')->group(function () {
    
    // โปรไฟล์
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ✅ การจองคิว (แยก GET สำหรับดูหน้าฟอร์ม และ POST สำหรับกดบันทึก)
    // หน้าเข้าจอง (ต้องมี ID เสมอ) -> พิมพ์ URL ทดสอบ: http://127.0.0.1:8000/queue/book/1
    Route::get('/queue/book/{scheduleId}', [QueueController::class, 'create'])->name('queues.create');
    
    // หน้ากดบันทึก (ห้ามพิมพ์ URL นี้ใน Browser ตรงๆ)
    Route::post('/queue/book', [QueueController::class, 'store'])->name('queues.store');

    // จัดการคิวและรายงาน
    Route::get('/queue/my', [QueueController::class, 'myQueue'])->name('queue.my');
    Route::get('/queue/current', [QueueController::class, 'currentQueue'])->name('queue.current');
    Route::get('/queue/history', [QueueController::class, 'history'])->name('queue.history');
    Route::get('/queues', [QueueController::class, 'index'])->name('queues.index');
    Route::patch('/queues/{id}/status', [QueueController::class, 'updateStatus'])->name('queues.updateStatus');
    Route::get('/staff/dashboard', [StaffController::class, 'dashboard'])->name('staff.dashboard');
    Route::get('/report/daily', [ReportController::class, 'daily'])->name('report.daily');
    Route::get('/doctor/queue', [DoctorController::class, 'queueList'])->name('doctor.queue.list');
    Route::get('/doctor/report', [ReportController::class, 'doctorReport'])->name('doctor.report.pdf');
    Route::get('/admin/doctor/schedule', [DoctorScheduleController::class, 'index'])->name('doctor.schedule');
});

Route::resource('users', UserController::class);

require __DIR__.'/auth.php';