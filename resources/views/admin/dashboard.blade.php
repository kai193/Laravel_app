@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">本日の出勤状況</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle text-center">
                            <thead class="table-light">
                                <tr>
                                    <th>ユーザー名</th>
                                    <th>出勤時間</th>
                                    <th>退勤時間</th>
                                    <th>状態</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($todayAttendances as $attendance)
                                <tr>
                                    <td class="fw-bold">{{ $attendance->user->name }}</td>
                                    <td>
                                        @if($attendance->clock_in)
                                            <span class="text-dark fw-semibold">{{ $attendance->clock_in->format('H:i') }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($attendance->clock_out)
                                            <span class="text-dark fw-semibold">{{ $attendance->clock_out->format('H:i') }}</span>
                                        @else
                                            <span class="text-muted">-</span>
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
                                            <span class="badge bg-danger fs-6">{{ $statusMap[$attendance->status] }}</span>
                                        @elseif($attendance->clock_out)
                                            <span class="badge bg-success fs-6">退勤済み</span>
                                        @elseif($attendance->clock_in)
                                            <span class="badge bg-warning text-dark fs-6">勤務中</span>
                                        @else
                                            <span class="badge bg-secondary fs-6">未出勤</span>
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