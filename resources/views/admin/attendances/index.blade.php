@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">勤怠一覧</h5>
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">ダッシュボードに戻る</a>
                </div>

                <div class="card-body">
                    <form method="GET" action="" class="mb-4 row g-3 align-items-end">
                        <div class="col-md-3">
                            <label for="date_from" class="form-label">日付（以降）</label>
                            <input type="date" id="date_from" name="date_from" value="{{ request('date_from') }}" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label for="month" class="form-label">月</label>
                            <input type="month" id="month" name="month" value="{{ request('month') }}" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label for="work_time" class="form-label">勤務時間</label>
                            <select id="work_time" name="work_time" class="form-select">
                                <option value="">指定なし</option>
                                <option value="lt5" {{ request('work_time') == 'lt5' ? 'selected' : '' }}>5時間未満</option>
                                <option value="gte8" {{ request('work_time') == 'gte8' ? 'selected' : '' }}>8時間以上</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="user_name" class="form-label">ユーザー名</label>
                            <input type="text" id="user_name" name="user_name" value="{{ request('user_name') }}" class="form-control">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">検索</button>
                        </div>
                    </form>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>日付</th>
                                    <th>従業員名</th>
                                    <th>出勤時間</th>
                                    <th>退勤時間</th>
                                    <th>勤務時間</th>
                                    <th>休憩時間</th>
                                    <th>実労働時間</th>
                                    <th>状態</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($attendances as $attendance)
                                <tr>
                                    <td>{{ $attendance->date->format('Y/m/d') }}</td>
                                    <td>{{ $attendance->user->name }}</td>
                                    <td>{{ $attendance->clock_in ? $attendance->clock_in->format('H:i') : '-' }}</td>
                                    <td>{{ $attendance->clock_out ? $attendance->clock_out->format('H:i') : '-' }}</td>
                                    <td>
                                        @if($attendance->clock_in && $attendance->clock_out)
                                            @php
                                                $clockIn = $attendance->clock_in;
                                                $clockOut = $attendance->clock_out;
                                                $totalMinutes = $clockIn->diffInMinutes($clockOut);
                                                $hours = floor($totalMinutes / 60);
                                                $minutes = $totalMinutes % 60;
                                            @endphp
                                            {{ $hours > 0 ? $hours . '時間' : '' }}{{ $minutes }}分
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if($attendance->break_time)
                                            @php
                                                $breakHours = floor($attendance->break_time / 60);
                                                $breakMinutes = $attendance->break_time % 60;
                                            @endphp
                                            {{ $breakHours > 0 ? $breakHours . '時間' : '' }}{{ $breakMinutes }}分
                                        @else
                                            0分
                                        @endif
                                    </td>
                                    <td>
                                        @if($attendance->clock_in && $attendance->clock_out)
                                            @php
                                                $clockIn = $attendance->clock_in;
                                                $clockOut = $attendance->clock_out;
                                                $totalMinutes = $clockIn->diffInMinutes($clockOut);
                                                $breakMinutes = $attendance->break_time ?? 0;
                                                $workMinutes = $totalMinutes - $breakMinutes;
                                                $workHours = floor($workMinutes / 60);
                                                $workMinutesRemainder = $workMinutes % 60;
                                            @endphp
                                            {{ $workHours > 0 ? $workHours . '時間' : '' }}{{ $workMinutesRemainder }}分
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $statusMap = [
                                                'absence' => '欠勤',
                                                'early_leave' => '早退',
                                                'late' => '遅刻',
                                            ];
                                            $statuses = array_keys($statusMap);
                                        @endphp
                                        @if(in_array($attendance->status, $statuses))
                                            <span class="text-danger fw-bold">{{ $statusMap[$attendance->status] }}</span>
                                        @elseif($attendance->clock_out)
                                            <span class="badge bg-success">退勤済み</span>
                                        @elseif($attendance->clock_in)
                                            <span class="badge bg-warning">勤務中</span>
                                        @else
                                            <span class="badge bg-secondary">未出勤</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- ページネーション削除 -->
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 