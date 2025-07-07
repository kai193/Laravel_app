@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">本日の勤怠</h5>
                </div>
                <div class="card-body">
                    @if(session('message'))
                        <div class="alert alert-success">
                            {{ session('message') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="text-center mb-4">
                        <h3>{{ now()->format('Y年m月d日') }}</h3>
                        <div class="mb-3">
                            @if($attendance)
                                <p>
                                    出勤時間: {{ $attendance->clock_in ? $attendance->clock_in->setTimezone('Asia/Tokyo')->format('H:i') : '未出勤' }}
                                </p>
                                <p>
                                    退勤時間: {{ $attendance->clock_out ? $attendance->clock_out->setTimezone('Asia/Tokyo')->format('H:i') : '未退勤' }}
                                </p>
                            @else
                                <p>本日の勤怠記録はありません</p>
                            @endif
                        </div>

                        <div class="d-flex justify-content-center gap-3">
                            @if(!$attendance || !$attendance->clock_in)
                                <form action="{{ route('attendance.clock-in') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-primary btn-lg">出勤</button>
                                </form>
                            @endif

                            @if($attendance && $attendance->clock_in && !$attendance->clock_out)
                                <form action="{{ route('attendance.clock-out') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-danger btn-lg">退勤</button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">当月の勤怠記録</h5>
                    <span class="badge bg-primary">
                        合計勤務時間: 
                        @php
                            $totalMinutes = 0;
                            foreach($monthlyAttendances as $attendance) {
                                if($attendance->clock_in && $attendance->clock_out) {
                                    $clockIn = $attendance->clock_in->setTimezone('Asia/Tokyo');
                                    $clockOut = $attendance->clock_out->setTimezone('Asia/Tokyo');
                                    $totalMinutes += $clockIn->diffInMinutes($clockOut) - ($attendance->break_time ?? 0);
                                }
                            }
                            $totalHours = floor($totalMinutes / 60);
                            $totalMinutesRemainder = $totalMinutes % 60;
                        @endphp
                        {{ $totalHours }}時間{{ $totalMinutesRemainder }}分
                    </span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>日付</th>
                                    <th>出勤時間</th>
                                    <th>退勤時間</th>
                                    <th>勤務時間</th>
                                    <th>休憩時間</th>
                                    <th>実労働時間</th>
                                    <th>状態</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($monthlyAttendances as $attendance)
                                <tr>
                                    <td>{{ $attendance->date->format('Y/m/d') }}</td>
                                    <td>{{ $attendance->clock_in ? $attendance->clock_in->setTimezone('Asia/Tokyo')->format('H:i') : '-' }}</td>
                                    <td>{{ $attendance->clock_out ? $attendance->clock_out->setTimezone('Asia/Tokyo')->format('H:i') : '-' }}</td>
                                    <td>
                                        @if($attendance->clock_in && $attendance->clock_out)
                                            @php
                                                $clockIn = $attendance->clock_in->setTimezone('Asia/Tokyo');
                                                $clockOut = $attendance->clock_out->setTimezone('Asia/Tokyo');
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
                                                $clockIn = $attendance->clock_in->setTimezone('Asia/Tokyo');
                                                $clockOut = $attendance->clock_out->setTimezone('Asia/Tokyo');
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
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
