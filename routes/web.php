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


Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified', 'admin.or.staff'])->group(function () {
    // ใช้ Resource Route ตัวเดียว ครอบคลุมทั้ง Index, Create, Store, Edit, Update, Destroy
    Route::resource('doctor-schedules', DoctorScheduleController::class);
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/queue/book', [QueueController::class, 'book'])->name('queue.book'); // หน้าจองคิว
    Route::get('/queue/schedule', [ScheduleController::class, 'index'])->name('queue.schedule'); // ตารางการจองคิว
    Route::get('/queue/my', [QueueController::class, 'myQueue'])->name('queue.my'); // สถานะคิวของตนเอง
    Route::get('/queue/current', [QueueController::class, 'currentQueue'])->name('queue.current'); // ลำดับคิวปัจจุบัน
    Route::get('/queue/history', [QueueController::class, 'history'])->name('queue.history'); // ประวัติ + ดาวน์โหลด PDF
    Route::get('/staff/dashboard', [StaffController::class, 'dashboard'])->name('staff.dashboard'); // สรุปคิวปัจจุบัน + สถิติ + ข้อมูลผู้รับบริการ
    Route::get('/queue/manage', [QueueController::class, 'manage'])->name('queue.manage'); // จัดการคิว (เพิ่ม/แก้ไข/ลบ/ค้นหา)
    Route::get('/queue/call', [QueueController::class, 'call'])->name('queue.call'); // เรียกคิว / ข้ามคิว / ปิดคิว / อัพเดทสถานะ
    Route::get('/report/daily', [ReportController::class, 'daily'])->name('report.daily'); // ออกรายงาน PDF ประจำวัน
    Route::get('/doctor/queue', [DoctorController::class, 'queueList'])->name('doctor.queue.list'); // ดูและค้นหารายการคิว
    Route::get('/doctor/patient/record', [DoctorController::class, 'recordPatient'])->name('doctor.patient.record'); // บันทึกข้อมูลเบื้องต้น
    Route::get('/doctor/patient/history', [DoctorController::class, 'patientHistory'])->name('doctor.patient.history'); // ดูประวัติผู้รับบริการ
    Route::get('/doctor/report', [ReportController::class, 'doctorReport'])->name('doctor.report.pdf'); // ออกรายงาน PDF
    Route::get('/admin/doctor/schedule', [DoctorScheduleController::class, 'index'])
        ->name('doctor.schedule');

    Route::get('/admin/report/users/pdf', [ReportController::class, 'usersPdf'])
        ->name('report.users.pdf');

    Route::get('/admin/report/services/pdf', [ReportController::class, 'servicesPdf'])
        ->name('report.service.pdf');

});

Route::get('/users', [UserController::class, 'index'])->name('users.index');
Route::get('/users/create', [UserController::class, 'create'])->name('users.create'); 
Route::post('/users', [UserController::class, 'store'])->name('users.store');
Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit'); 
Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update'); 
Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
require __DIR__.'/auth.php';
