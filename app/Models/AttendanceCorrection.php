<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceCorrection extends Model
{
    protected $fillable = [
        'user_id',
        'date',
        'correct_clock_in',
        'correct_clock_out',
        'correct_break_time',
        'reason',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
