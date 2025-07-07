<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index()
    {
        $attendance = Attendance::where('user_id', Auth::id())
            ->where('date', today())
            ->first();

        $status = null;
        if ($attendance) {
            if ($attendance->clock_in && !$attendance->clock_out) {
                // 休憩中判定
                if ($attendance->is_on_break) {
                    $status = 'break'; // 休憩中
                } else {
                    $status = 'working'; // 勤務中
                }
            } elseif ($attendance->clock_in && $attendance->clock_out) {
                $status = 'done'; // 退勤済み
            }
        }

        return view('attendance.index', compact('attendance', 'status'));
    }

    public function clockIn(Request $request)
    {
        try {
            $userId = Auth::id();
            if (!$userId) {
                return redirect()->route('attendance.index')->with('error', 'ログインが必要です');
            }

            $today = Carbon::today()->toDateString();

            // 今日の日付の打刻が存在するか確認
            $existingAttendance = Attendance::where('user_id', $userId)
                ->whereDate('date', $today)
                ->first();

            if ($existingAttendance) {
                // 既に今日の打刻が存在する場合
                if (is_null($existingAttendance->clock_in)) {
                    // 出勤打刻が未設定の場合のみ更新
                    $existingAttendance->clock_in = now();
                    $existingAttendance->note = $request->input('note');
                    $existingAttendance->save();
                    return redirect()->route('attendance.index')->with('message', '出勤打刻しました');
                }
                return redirect()->route('attendance.index')->with('error', '本日は既に出勤打刻済みです');
            }

            // 新しい打刻レコードを作成
            $attendance = new Attendance();
            $attendance->user_id = $userId;
            $attendance->date = $today;
            $attendance->clock_in = now();
            $attendance->note = $request->input('note');
            $attendance->save();

            return redirect()->route('attendance.index')->with('message', '出勤打刻しました');
        } catch (\Exception $e) {
            Log::error('出勤打刻エラー: ' . $e->getMessage());
            return redirect()->route('attendance.index')->with('error', '打刻処理中にエラーが発生しました');
        }
    }

    public function clockOut(Request $request)
    {
        $userId = Auth::id();
        $today = now()->toDateString();

        $attendance = Attendance::where('user_id', $userId)
            ->where('date', $today)
            ->first();

        if ($attendance && is_null($attendance->clock_out)) {
            $attendance->clock_out = now();
            $attendance->note = $request->input('note');
            $attendance->save();
            return redirect()->route('attendance.index')->with('message', '退勤打刻しました');
        }

        return redirect()->route('attendance.index')->with('message', '本日の出勤記録がありません、または既に退勤済みです');
    }

    public function list()
    {
        $attendances = Attendance::where('user_id', Auth::id())
            ->orderBy('date', 'desc')
            ->orderBy('clock_in', 'desc')
            ->paginate(20);

        // 勤務時間の計算を修正（日本時間で計算）
        foreach ($attendances as $attendance) {
            if ($attendance->clock_in && $attendance->clock_out) {
                $clockIn = Carbon::parse($attendance->clock_in)->setTimezone('Asia/Tokyo');
                $clockOut = Carbon::parse($attendance->clock_out)->setTimezone('Asia/Tokyo');
                $attendance->working_minutes = $clockIn->diffInMinutes($clockOut);
            }
        }

        return view('attendance.list', compact('attendances'));
    }

    public function monthly(Request $request)
    {
        $userId = Auth::id();
        $month = $request->input('month', now()->format('Y-m'));
        $start = Carbon::parse($month.'-01')->startOfMonth();
        $end = Carbon::parse($month.'-01')->endOfMonth();

        $attendances = Attendance::where('user_id', $userId)
            ->whereBetween('date', [$start, $end])
            ->orderBy('date')
            ->get();

        return view('attendance.monthly', compact('attendances', 'month'));
    }

    public function breakIn(Request $request)
    {
        $userId = Auth::id();
        $today = now()->toDateString();

        $attendance = Attendance::where('user_id', $userId)
            ->where('date', $today)
            ->first();

        if ($attendance) {
            $attendance->is_on_break = true;
            $attendance->note = $request->input('note');
            $attendance->save();
            return redirect()->route('attendance.index')->with('message', '休憩に入りました');
        }

        return redirect()->route('attendance.index')->with('error', '出勤打刻がありません');
    }

    public function breakOut(Request $request)
    {
        $userId = Auth::id();
        $today = now()->toDateString();

        $attendance = Attendance::where('user_id', $userId)
            ->where('date', $today)
            ->first();

        if ($attendance && $attendance->is_on_break) {
            $attendance->is_on_break = false;
            $attendance->note = $request->input('note');
            $attendance->save();
            return redirect()->route('attendance.index')->with('message', '休憩から戻りました');
        }

        return redirect()->route('attendance.index')->with('error', '休憩中ではありません');
    }

    public function updateNote(Request $request, $id)
    {
        $attendance = Attendance::findOrFail($id);
        
        // 本人の勤怠記録かチェック
        if ($attendance->user_id !== Auth::id()) {
            return redirect()->route('attendance.list')->with('error', '権限がありません');
        }

        $attendance->note = $request->input('note');
        $attendance->save();

        return redirect()->route('attendance.list')->with('message', 'メモを更新しました');
    }

    public function deleteNote($id)
    {
        $attendance = Attendance::findOrFail($id);
        
        // 本人の勤怠記録かチェック
        if ($attendance->user_id !== Auth::id()) {
            return redirect()->route('attendance.list')->with('error', '権限がありません');
        }

        $attendance->note = null;
        $attendance->save();

        return redirect()->route('attendance.list')->with('message', 'メモを削除しました');
    }

    public function absence(Request $request)
    {
        $userId = Auth::id();
        $today = Carbon::today()->toDateString();

        // 今日の日付の打刻が存在するか確認
        $existingAttendance = Attendance::where('user_id', $userId)
            ->whereDate('date', $today)
            ->first();

        if ($existingAttendance) {
            return redirect()->route('attendance.index')->with('error', '本日は既に打刻済みです');
        }

        // 新しい打刻レコードを作成
        $attendance = new Attendance();
        $attendance->user_id = $userId;
        $attendance->date = $today;
        $attendance->status = 'absence';
        $attendance->note = $request->input('note');
        $attendance->save();

        return redirect()->route('attendance.index')->with('message', '欠勤を登録しました');
    }

    public function earlyLeave(Request $request)
    {
        $userId = Auth::id();
        $today = Carbon::today()->toDateString();

        $attendance = Attendance::where('user_id', $userId)
            ->where('date', $today)
            ->first();

        if (!$attendance) {
            return redirect()->route('attendance.index')->with('error', '出勤打刻がありません');
        }

        if ($attendance->clock_out) {
            return redirect()->route('attendance.index')->with('error', '既に退勤済みです');
        }

        $attendance->clock_out = now();
        $attendance->status = 'early_leave';
        $attendance->note = $request->input('note');
        $attendance->save();

        return redirect()->route('attendance.index')->with('message', '早退を登録しました');
    }

    public function late(Request $request)
    {
        $userId = Auth::id();
        $today = Carbon::today()->toDateString();

        // 今日の日付の打刻が存在するか確認
        $existingAttendance = Attendance::where('user_id', $userId)
            ->whereDate('date', $today)
            ->first();

        if ($existingAttendance) {
            return redirect()->route('attendance.index')->with('error', '本日は既に打刻済みです');
        }

        // 新しい打刻レコードを作成
        $attendance = new Attendance();
        $attendance->user_id = $userId;
        $attendance->date = $today;
        $attendance->clock_in = now();
        $attendance->status = 'late';
        $attendance->note = $request->input('note');
        $attendance->save();

        return redirect()->route('attendance.index')->with('message', '遅刻を登録しました');
    }

    public function calendar(Request $request)
    {
        $userId = Auth::id();
        $month = $request->input('month', now()->format('Y-m'));
        $start = Carbon::parse($month.'-01')->startOfMonth();
        $end = Carbon::parse($month.'-01')->endOfMonth();
        $attendances = Attendance::where('user_id', $userId)
            ->whereBetween('date', [$start, $end])
            ->get();
        return view('attendance.calendar', compact('attendances', 'month'));
    }
}