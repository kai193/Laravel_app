<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AttendanceCorrection;
use Illuminate\Support\Facades\Auth;

class AttendanceCorrectionController extends Controller
{
    // 申請一覧
    public function index()
    {
        $corrections = AttendanceCorrection::where('user_id', Auth::id())->orderBy('created_at', 'desc')->paginate(10);
        return view('corrections.index', compact('corrections'));
    }

    // 申請フォーム
    public function create()
    {
        return view('corrections.create');
    }

    // 申請保存
    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'correct_clock_in' => 'nullable|date_format:H:i',
            'correct_clock_out' => 'nullable|date_format:H:i',
            'correct_break_time' => 'nullable|integer|min:0',
            'reason' => 'required|string|max:255',
        ]);
        AttendanceCorrection::create([
            'user_id' => Auth::id(),
            'date' => $request->date,
            'correct_clock_in' => $request->correct_clock_in,
            'correct_clock_out' => $request->correct_clock_out,
            'correct_break_time' => $request->correct_break_time,
            'reason' => $request->reason,
            'status' => 'pending',
        ]);
        return redirect()->route('corrections.index')->with('message', '打刻修正申請を送信しました');
    }
}
