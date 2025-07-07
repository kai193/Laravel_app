@extends('layouts.app')

@section('head')
<style>
.table-striped th {
    background: #f0f4fa;
    text-align: center;
    font-weight: bold;
    border-bottom: 2px solid #dee2e6;
}
.table-striped td {
    text-align: center;
    vertical-align: middle;
    border-bottom: 1px solid #dee2e6;
    background: #fff;
}
.table-striped tbody tr:hover {
    background: #f5faff;
}
.table-striped td, .table-striped th {
    padding: 0.7rem 0.5rem;
    font-size: 1.05rem;
}
/* 備考欄の時間外労働を赤文字 */
.text-danger-fw-bold, .table-striped .text-danger.fw-bold {
    color: #e3342f !important;
    font-weight: bold !important;
}
/* ページネーションの矢印を小さく or 非表示 */
svg, .arrow, .fa-chevron-left, .fa-chevron-right {
    width: 24px !important;
    height: 24px !important;
    font-size: 24px !important;
}
/* もし不要なら完全非表示にする場合は下記を有効化 */
/*
svg, .arrow, .fa-chevron-left, .fa-chevron-right {
    display: none !important;
}
*/
</style>
@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">勤怠記録一覧</h5>
                    <a href="{{ route('dashboard') }}" class="btn btn-secondary">ダッシュボードに戻る</a>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th class="text-center" style="min-width:110px;">日付</th>
                                    <th class="text-center" style="min-width:90px;">出勤時間</th>
                                    <th class="text-center" style="min-width:90px;">退勤時間</th>
                                    <th class="text-center" style="min-width:90px;">休憩時間</th>
                                    <th class="text-center" style="min-width:120px;">勤務時間</th>
                                    <th class="text-center" style="min-width:120px;">備考</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($attendances as $attendance)
                                <tr>
                                    <td class="text-center">{{ $attendance->date->format('Y/m/d') }}</td>
                                    <td class="text-center">{{ $attendance->clock_in ? $attendance->clock_in->setTimezone('Asia/Tokyo')->format('H:i') : '-' }}</td>
                                    <td class="text-center">{{ $attendance->clock_out ? $attendance->clock_out->setTimezone('Asia/Tokyo')->format('H:i') : '-' }}</td>
                                    <td class="text-center">
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
                                    <td class="text-center">
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
                                        @elseif($attendance->clock_in && $attendance->clock_out)
                                            @php
                                                $workMinutes = $attendance->working_minutes - ($attendance->break_time ?? 0);
                                                $workHours = floor($workMinutes / 60);
                                                $workMinutesRemainder = $workMinutes % 60;
                                            @endphp
                                            {{ $workHours > 0 ? $workHours . '時間' : '' }}{{ $workMinutesRemainder }}分
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if(isset($attendance->note) && strpos($attendance->note, '時間外労働') !== false)
                                            <span class="text-danger fw-bold">{{ $attendance->note }}</span>
                                        @else
                                            <span class="text-dark">{{ $attendance->note ?? '' }}</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- ページネーション削除 -->
                    <!-- <div class="d-flex justify-content-center mt-4">
                        {{ $attendances->links() }}
                    </div> -->
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 