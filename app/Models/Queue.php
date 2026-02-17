<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Queue extends Model
{
    // 1. อนุญาตให้บันทึกข้อมูลลง Database (แก้ MassAssignmentException)
    protected $fillable = [
        'docschId', 
        'userId', 
        'labelNo', 
        'period', 
        'Note',
        'status',
        'created_by'
    ];

    // 2. เชื่อมโยงกับตารางตารางเวลายืนยัน (แก้ RelationNotFoundException)
    // ตรวจสอบชื่อ Model (DoctorSchedule) ให้ตรงกับที่คุณใช้จริง
    public function doctorSchedule()
{
    // ตรวจสอบว่า Foreign Key คือ docschId ตามที่คุณใช้ใน store() หรือไม่
    return $this->belongsTo(DoctorSchedule::class, 'docschId');
}

    // 3. เชื่อมโยงกับตาราง User (คนไข้ที่จอง)
    public function user()
    {
        return $this->belongsTo(User::class, 'userId');
    }
}