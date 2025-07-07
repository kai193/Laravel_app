<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        // 本日の勤怠記録を取得
        $attendance = Attendance::where('user_id', Auth::id())
            ->where('date', $today)
            ->first();

        // 当月の勤怠記録を取得
        $monthlyAttendances = Attendance::where('user_id', Auth::id())
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->orderBy('date', 'desc')
            ->get();

        return view('dashboard', compact('attendance', 'monthlyAttendances'));
    }
} 