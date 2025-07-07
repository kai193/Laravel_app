<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AdminAttendanceController;
use App\Http\Controllers\AttendanceCorrectionController;

Route::get('/', function () {
    return redirect()->route('attendance.index');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    
    // 勤怠関連のルート
    Route::post('/attendance/clock-in', [App\Http\Controllers\AttendanceController::class, 'clockIn'])->name('attendance.clock-in');
    Route::post('/attendance/clock-out', [App\Http\Controllers\AttendanceController::class, 'clockOut'])->name('attendance.clock-out');

    // 打刻修正申請（ユーザー用）
    Route::get('/corrections', [\App\Http\Controllers\AttendanceCorrectionController::class, 'index'])->name('corrections.index');
    Route::get('/corrections/create', [\App\Http\Controllers\AttendanceCorrectionController::class, 'create'])->name('corrections.create');
    Route::post('/corrections', [\App\Http\Controllers\AttendanceCorrectionController::class, 'store'])->name('corrections.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::get('/attendance/list', [\App\Http\Controllers\AttendanceController::class, 'list'])->name('attendance.list');
    Route::get('/attendance/monthly', [AttendanceController::class, 'monthly'])->name('attendance.monthly');
    Route::put('/attendance/{id}/note', [AttendanceController::class, 'updateNote'])->name('attendance.update-note');
    Route::delete('/attendance/{id}/note', [AttendanceController::class, 'deleteNote'])->name('attendance.delete-note');
    Route::post('/attendance/absence', [AttendanceController::class, 'absence'])->name('attendance.absence');
    Route::post('/attendance/early-leave', [AttendanceController::class, 'earlyLeave'])->name('attendance.early-leave');
    Route::post('/attendance/late', [AttendanceController::class, 'late'])->name('attendance.late');
    Route::get('/attendance/calendar', [AttendanceController::class, 'calendar'])->name('attendance.calendar');
});

// 管理者用ルート
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/attendance/{id}/edit', [AdminAttendanceController::class, 'edit'])->name('admin.attendance.edit');
    Route::put('/admin/attendance/{id}', [AdminAttendanceController::class, 'update'])->name('admin.attendance.update');
    Route::delete('/admin/attendance/{id}', [AdminAttendanceController::class, 'destroy'])->name('admin.attendance.destroy');
});
Route::post('/attendance/break-in', [AttendanceController::class, 'breakIn'])->name('attendance.breakin');
Route::post('/attendance/break-out', [AttendanceController::class, 'breakOut'])->name('attendance.breakout');

// 管理者用ルート
Route::prefix('admin')->group(function () {
    // 認証関連
    Route::get('login', [App\Http\Controllers\Admin\AuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('login', [App\Http\Controllers\Admin\AuthController::class, 'login']);
    Route::post('logout', [App\Http\Controllers\Admin\AuthController::class, 'logout'])->name('admin.logout');
    
    // 管理者登録
    Route::get('register', [App\Http\Controllers\Admin\RegisterController::class, 'showRegistrationForm'])->name('admin.register');
    Route::post('register', [App\Http\Controllers\Admin\RegisterController::class, 'register']);

    // 管理者ページ（認証必須）
    Route::middleware(['auth:admin'])->group(function () {
        Route::get('dashboard', [App\Http\Controllers\AdminController::class, 'dashboard'])->name('admin.dashboard');
        Route::get('users', [App\Http\Controllers\AdminController::class, 'userList'])->name('admin.users.index');
        Route::get('users/{id}', [App\Http\Controllers\AdminController::class, 'userDetail'])->name('admin.users.detail');
        Route::get('attendances', [App\Http\Controllers\AdminAttendanceController::class, 'index'])->name('admin.attendances.index');
        Route::get('profile', function() { return view('admin.profile'); })->name('admin.profile');
        Route::get('settings', function() { return view('admin.settings'); })->name('admin.settings');
    });

    // 管理者用 打刻修正申請承認
    Route::get('/corrections', [\App\Http\Controllers\AdminAttendanceCorrectionController::class, 'index'])->name('admin.corrections.index');
    Route::post('/corrections/{id}/approve', [\App\Http\Controllers\AdminAttendanceCorrectionController::class, 'approve'])->name('admin.corrections.approve');
    Route::post('/corrections/{id}/reject', [\App\Http\Controllers\AdminAttendanceCorrectionController::class, 'reject'])->name('admin.corrections.reject');
});

require __DIR__.'/auth.php';
