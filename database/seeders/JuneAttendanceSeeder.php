<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;

class JuneAttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // testユーザーを取得
        $user = User::where('name', 'test')->first();
        
        if (!$user) {
            $this->command->error('testユーザーが見つかりません');
            return;
        }

        // 6月の勤務記録を削除（既存のものがあれば）
        Attendance::where('user_id', $user->id)
            ->whereYear('date', 2025)
            ->whereMonth('date', 6)
            ->delete();

        $year = 2025;
        $month = 6;
        
        // 6月の各日をループ
        for ($day = 1; $day <= 30; $day++) {
            $date = Carbon::create($year, $month, $day);
            $dayOfWeek = $date->dayOfWeek; // 0=日曜日, 1=月曜日, ..., 6=土曜日
            
            // 土日は休み
            if ($dayOfWeek == 0 || $dayOfWeek == 6) {
                continue;
            }
            
            // 17日、18日、19日は欠勤
            if ($day == 17 || $day == 18 || $day == 19) {
                $this->createAbsence($user, $date);
                continue;
            }
            
            // 26日、27日は有給
            if ($day == 26 || $day == 27) {
                $this->createPaidLeave($user, $date);
                continue;
            }
            
            // 通常勤務（月〜金、8時間勤務）
            $this->createNormalWork($user, $date);
        }
        
        $this->command->info('testさんの6月の勤務記録を作成しました');
    }
    
    /**
     * 欠勤記録を作成
     */
    private function createAbsence($user, $date)
    {
        Attendance::create([
            'user_id' => $user->id,
            'date' => $date,
            'status' => 'absence',
            'note' => '欠勤',
        ]);
    }
    
    /**
     * 有給休暇記録を作成
     */
    private function createPaidLeave($user, $date)
    {
        Attendance::create([
            'user_id' => $user->id,
            'date' => $date,
            'status' => 'absence', // 有給もabsenceとして記録
            'note' => '有給休暇',
        ]);
    }
    
    /**
     * 通常勤務記録を作成（8時間勤務）
     */
    private function createNormalWork($user, $date)
    {
        // 9:00出勤、18:00退勤（1時間休憩含む）
        $clockIn = $date->copy()->setTime(9, 0, 0);
        $clockOut = $date->copy()->setTime(18, 0, 0);
        
        Attendance::create([
            'user_id' => $user->id,
            'date' => $date,
            'clock_in' => $clockIn,
            'clock_out' => $clockOut,
            'break_time' => 60, // 1時間休憩
            'is_on_break' => false,
            'note' => '通常勤務',
        ]);
    }
}
