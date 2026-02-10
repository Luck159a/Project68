<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
// เพิ่มบรรทัดนี้ด้านบน
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DoctorSchedule extends Model
{
    protected $fillable = [
        'user_id',
        'schedule_date',
        'start_time',
        'end_time',
    ];

    /**
     * เพิ่มฟังก์ชันนี้ลงไปครับ 
     * เพื่อบอกว่าตาราง DoctorSchedule นี้เชื่อมกับ User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}