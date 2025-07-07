@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">従業員詳細</h5>
                    <div>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">一覧に戻る</a>
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">ダッシュボードに戻る</a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6>基本情報</h6>
                            <table class="table">
                                <tr>
                                    <th style="width: 150px;">名前</th>
                                    <td>{{ $user->name }}</td>
                                </tr>
                                <tr>
                                    <th>メールアドレス</th>
                                    <td>{{ $user->email }}</td>
                                </tr>
                                <tr>
                                    <th>登録日</th>
                                    <td>{{ $user->created_at->format('Y/m/d') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <h6>勤怠記録</h6>
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
                                    <th>備考</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($user->attendances as $attendance)
                                <tr>
                                    <td>{{ $attendance->date->format('Y/m/d') }}</td>
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
                                    <td>
                                        <span class="text-dark">{{ $attendance->note }}</span>
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