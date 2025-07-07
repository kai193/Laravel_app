<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Http\Controllers\Controller;

class AdminController extends Controller
{
    public function dashboard()
    {
        $users = User::all();
        $today = Carbon::today();
        
        $todayAttendances = Attendance::with('user')
            ->whereDate('date', $today)
            ->get();

        return view('admin.dashboard', compact('users', 'todayAttendances'));
    }

    public function userList()
    {
        $users = User::with('attendances')->get();
        return view('admin.users.index', compact('users'));
    }

    public function attendanceList()
    {
        $attendances = Attendance::with('user')
            ->orderBy('date', 'desc')
            ->orderBy('clock_in', 'desc')
            ->paginate(20);
        
        return view('admin.attendances.index', compact('attendances'));
    }

    public function userDetail($id)
    {
        $user = User::with(['attendances' => function($query) {
            $query->orderBy('date', 'desc');
        }])->findOrFail($id);

        return view('admin.users.detail', compact('user'));
    }
} 