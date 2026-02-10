<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany; // เพิ่มการใช้งาน HasMany

class DoctorSchedule extends Model
{
    protected $fillable = [
        'user_id',
        'schedule_date',
        'start_time',
        'end_time',
        'status', // เพิ่ม status เผื่อกรณีหมอไม่อยู่หรือยกเลิก
    ];

    /**
     * เชื่อมกับ User (หมอ)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * เชื่อมกับตาราง Queues (การจองคิว)
     * หนึ่งตารางเวลาของหมอ สามารถมีการจองได้หลายคิว (HasMany)
     */
    public function queues(): HasMany
    {
        return $this->hasMany(Queue::class, 'docschId');
    }
}