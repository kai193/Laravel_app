@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">月別集計</h2>
    <form method="GET" action="{{ route('attendance.monthly') }}" class="mb-4">
        <label for="month" class="form-label">月を選択：</label>
        <input type="month" id="month" name="month" value="{{ $month }}" class="form-control" style="max-width:200px;display:inline-block;">
        <button type="submit" class="btn btn-primary ms-2">表示</button>
    </form>
    @php
        use Carbon\Carbon;
        $workDays = 0;
        $totalMinutes = 0; // 総勤務時間
        $scheduledMinutes = 0; // 所定内労働
        $sumMinutes = 0; // 合計勤務時間
        $overtimeMinutes = 0; // 時間外労働
        $nightMinutes = 0; // 深夜労働
        $absenceDays = 0;
        $paidLeaveDays = 0;
        $lateDays = 0;
        $earlyLeaveDays = 0;
        $holidayDays = 0;
        $overtimeThreshold = 8 * 60; // 8時間
        foreach ($attendances as $a) {
            // 欠勤
            if (isset($a->status) && $a->status === 'absence') {
                if ($a->note === '有給休暇') {
                    $paidLeaveDays++;
                } else {
                    $absenceDays++;
                }
                continue;
            }
            // 遅刻・早退
            if (isset($a->status) && $a->status === 'late') $lateDays++;
            if (isset($a->status) && $a->status === 'early_leave') $earlyLeaveDays++;
            // 休日
            if (isset($a->status) && $a->status === 'holiday') {
                $holidayDays++;
            }
            // 勤務時間
            if ($a->clock_in && $a->clock_out) {
                $clockIn = Carbon::parse($a->clock_in);
                $clockOut = Carbon::parse($a->clock_out);
                $break = (is_numeric($a->break_time) && $a->break_time >= 0 && $a->break_time <= 60) ? $a->break_time : 0;
                $workMinutes = max(abs($clockOut->diffInMinutes($clockIn)) - $break, 0);
                
                // 休日以外の場合のみ労働時間をカウント
                if ($a->status !== 'holiday') {
                    $scheduledMinutes += min($workMinutes, $overtimeThreshold); // 1日最大8時間まで所定内
                    $sumMinutes += min($workMinutes, $overtimeThreshold); // 合計勤務時間も同じ
                    if ($workMinutes > $overtimeThreshold) {
                        $overtimeMinutes += $workMinutes - $overtimeThreshold;
                    }
                    $workDays++;
                }
                
                // 深夜労働時間の計算（22時以降）
                $cur = $clockIn->copy();
                while ($cur < $clockOut) {
                    if ($cur->hour >= 22 || $cur->hour < 5) {
                        $nightMinutes++;
                    }
                    $cur->addMinute();
                }
            }
        }
        $totalMinutes = $workDays * 8 * 60; // 総勤務時間
    @endphp
    <div class="card mb-4 shadow-sm">
        <div class="card-body p-3">
            <div class="row text-center g-0">
                <div class="col border-end">
                    <div class="small text-muted">労働日数</div>
                    <div class="fw-bold fs-5">{{ $workDays }}日</div>
                </div>
                <div class="col border-end">
                    <div class="small text-muted">総勤務時間</div>
                    <div class="fw-bold fs-5">{{ floor($totalMinutes/60) }}時間 {{ $totalMinutes%60 }}分</div>
                </div>
                <div class="col border-end">
                    <div class="small text-primary">所定内労働</div>
                    <div class="fw-bold text-primary fs-5">{{ floor($scheduledMinutes/60) }}時間 {{ $scheduledMinutes%60 }}分</div>
                </div>
                <div class="col border-end">
                    <div class="small text-danger">時間外労働</div>
                    <div class="fw-bold text-danger fs-5">{{ floor($overtimeMinutes/60) }}時間 {{ $overtimeMinutes%60 }}分</div>
                </div>
                <div class="col border-end">
                    <div class="small text-info">深夜労働</div>
                    <div class="fw-bold text-info fs-5">{{ floor($nightMinutes/60) }}時間 {{ $nightMinutes%60 }}分</div>
                </div>
                <div class="col border-end">
                    <div class="small text-secondary">欠勤日数</div>
                    <div class="fw-bold text-secondary fs-5">{{ $absenceDays }}日</div>
                </div>
                <div class="col border-end">
                    <div class="small text-success">有給休暇</div>
                    <div class="fw-bold text-success fs-5">{{ $paidLeaveDays }}日</div>
                </div>
                <div class="col">
                    <div class="small text-muted">遅刻/早退回数</div>
                    <div class="fw-bold fs-5">{{ $lateDays + $earlyLeaveDays }}回</div>
                </div>
            </div>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>日付</th>
                    <th>出勤時間</th>
                    <th>退勤時間</th>
                    <th>休憩時間</th>
                    <th>勤務時間</th>
                    <th>備考</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $sum = 0;
                @endphp
                @foreach($attendances as $attendance)
                    @if($attendance->status === 'holiday')
                        @continue
                    @endif
                    <tr>
                        <td>{{ $attendance->date ? \Carbon\Carbon::parse($attendance->date)->format('Y-m-d') : '-' }}</td>
                        <td>{{ $attendance->clock_in ? \Carbon\Carbon::parse($attendance->clock_in)->format('H:i') : '-' }}</td>
                        <td>{{ $attendance->clock_out ? \Carbon\Carbon::parse($attendance->clock_out)->format('H:i') : '-' }}</td>
                        <td>{{ is_numeric($attendance->break_time) ? $attendance->break_time : 0 }}分</td>
                        <td>
                            @php
                                $statusMap = [
                                    'absence' => '欠勤',
                                    'early_leave' => '早退',
                                    'late' => '遅刻',
                                    'holiday' => '休日',
                                ];
                                $statuses = array_keys($statusMap);
                                $workMinutes = null;
                                if ($attendance->clock_in && $attendance->clock_out) {
                                    $clockIn = \Carbon\Carbon::parse($attendance->clock_in);
                                    $clockOut = \Carbon\Carbon::parse($attendance->clock_out);
                                    $break = (is_numeric($attendance->break_time) && $attendance->break_time >= 0 && $attendance->break_time <= 60) ? $attendance->break_time : 0;
                                    $workMinutes = max(abs($clockOut->diffInMinutes($clockIn)) - $break, 0);
                                    $sum += $workMinutes;
                                }
                            @endphp
                            @if($attendance->status === 'absence' && $attendance->note === '有給休暇')
                                <span class="text-success fw-bold">有給</span>
                            @elseif($attendance->status === 'absence')
                                <span class="text-danger fw-bold">欠勤</span>
                            @elseif($attendance->status === 'late')
                                <span class="text-danger fw-bold">遅刻</span>
                            @elseif($attendance->status === 'early_leave')
                                <span class="text-danger fw-bold">早退</span>
                            @elseif($attendance->status === 'holiday')
                                0時間 0分
                            @elseif($workMinutes !== null)
                                8時間 0分
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            @if(strpos($attendance->note, '時間外労働') !== false)
                                <span class="text-danger fw-bold">{{ $attendance->note }}</span>
                            @else
                                <span class="text-dark">{{ $attendance->note }}</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="4">合計勤務時間</th>
                    <th colspan="2">
                        {{ floor($sumMinutes / 60) }}時間 {{ $sumMinutes % 60 }}分
                    </th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endsection 