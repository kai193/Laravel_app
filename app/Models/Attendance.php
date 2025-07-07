<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'user_id',
        'date',
        'clock_in',
        'clock_out',
        'break_time',
        'is_on_break',
        'note',
        'status',
    ];

    protected $casts = [
        'date' => 'date',
        'clock_in' => 'datetime',
        'clock_out' => 'datetime',
    ];

    /**
     * この勤怠記録に紐づくユーザーを取得
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
