<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AttendanceCorrection;

class AdminAttendanceCorrectionController extends Controller
{
    // 申請一覧
    public function index()
    {
        $corrections = AttendanceCorrection::with('user')->orderBy('created_at', 'desc')->paginate(20);
        return view('admin.corrections.index', compact('corrections'));
    }

    // 承認
    public function approve($id)
    {
        $correction = AttendanceCorrection::findOrFail($id);
        $correction->status = 'approved';
        $correction->save();
        return redirect()->route('admin.corrections.index')->with('message', '申請を承認しました');
    }

    // 却下
    public function reject($id)
    {
        $correction = AttendanceCorrection::findOrFail($id);
        $correction->status = 'rejected';
        $correction->save();
        return redirect()->route('admin.corrections.index')->with('message', '申請を却下しました');
    }
}
