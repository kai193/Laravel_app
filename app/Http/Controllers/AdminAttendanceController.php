<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAttendanceController extends Controller
{
    public function edit($id)
    {
        $attendance = Attendance::findOrFail($id);
        return view('admin.attendance.edit', compact('attendance'));
    }

    public function update(Request $request, $id)
    {
        $attendance = Attendance::findOrFail($id);
        
        $validated = $request->validate([
            'clock_in' => 'required|date',
            'clock_out' => 'nullable|date|after:clock_in',
            'break_time' => 'required|integer|min:0',
            'note' => 'nullable|string|max:255',
        ]);

        $attendance->update($validated);

        return redirect()->route('attendance.list')
            ->with('success', '勤怠情報を更新しました。');
    }

    public function destroy($id)
    {
        $attendance = Attendance::findOrFail($id);
        $attendance->delete();

        return redirect()->route('attendance.list')
            ->with('success', '勤怠情報を削除しました。');
    }

    public function index(Request $request)
    {
        $query = Attendance::with('user')->orderBy('date', 'desc')->orderBy('clock_in', 'desc');

        // 日付（以降）
        if ($request->filled('date_from')) {
            $query->where('date', '>=', $request->input('date_from'));
        }
        // 月
        if ($request->filled('month')) {
            $month = $request->input('month'); // 例: 2025-05
            $query->where('date', '>=', $month.'-01')
                  ->where('date', '<=', $month.'-31');
        }
        // 勤務時間
        if ($request->filled('work_time')) {
            $query->where(function($q) use ($request) {
                if ($request->input('work_time') === 'lt5') {
                    $q->whereRaw('(TIMESTAMPDIFF(MINUTE, clock_in, clock_out) - IFNULL(break_time,0)) < 300');
                } elseif ($request->input('work_time') === 'gte8') {
                    $q->whereRaw('(TIMESTAMPDIFF(MINUTE, clock_in, clock_out) - IFNULL(break_time,0)) >= 480');
                }
            });
        }
        // ユーザー名
        if ($request->filled('user_name')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('name', $request->input('user_name'));
            });
        }

        $attendances = $query->paginate(20)->appends($request->all());

        return view('admin.attendances.index', compact('attendances'));
    }
} 