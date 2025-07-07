<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;

class JulyAttendanceSeeder extends Seeder
{
    public function run(): void
    {
        // testユーザーを取得
        $user = User::where('name', 'test')->first();
        
        if (!$user) {
            echo "testユーザーが見つかりません。\n";
            return;
        }

        // 既存の7月データを削除
        Attendance::where('user_id', $user->id)
            ->whereYear('date', 2025)
            ->whereMonth('date', 7)
            ->delete();

        // 7月1日から6日までのデータを作成
        $attendanceData = [
            [
                'date' => '2025-07-01',
                'clock_in' => '09:00:00',
                'clock_out' => '18:00:00',
                'break_time' => 60,
                'status' => 'late',
                'note' => '遅刻'
            ],
            [
                'date' => '2025-07-02',
                'clock_in' => '09:00:00',
                'clock_out' => '23:00:00', // 時間外労働5時間（18:00-23:00）
                'break_time' => 60,
                'status' => 'overtime',
                'note' => '時間外労働5時間'
            ],
            [
                'date' => '2025-07-03',
                'clock_in' => '09:00:00',
                'clock_out' => '18:00:00',
                'break_time' => 60,
                'status' => 'absence',
                'note' => '欠勤'
            ],
            [
                'date' => '2025-07-04',
                'clock_in' => '09:00:00',
                'clock_out' => '23:00:00', // 時間外労働5時間（18:00-23:00）
                'break_time' => 60,
                'status' => 'overtime',
                'note' => '時間外労働5時間'
            ],
            [
                'date' => '2025-07-05',
                'clock_in' => '09:00:00',
                'clock_out' => '18:00:00',
                'break_time' => 60,
                'status' => 'holiday',
                'note' => '休日'
            ],
            [
                'date' => '2025-07-06',
                'clock_in' => '09:00:00',
                'clock_out' => '18:00:00',
                'break_time' => 60,
                'status' => 'holiday',
                'note' => '休日'
            ]
        ];

        foreach ($attendanceData as $data) {
            Attendance::create([
                'user_id' => $user->id,
                'date' => $data['date'],
                'clock_in' => $data['clock_in'],
                'clock_out' => $data['clock_out'],
                'break_time' => $data['break_time'],
                'status' => $data['status'],
                'note' => $data['note'],
                'is_on_break' => false
            ]);
        }

        echo "7月1日から6日までの勤怠データを作成しました。\n";
    }
} 