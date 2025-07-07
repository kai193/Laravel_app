@extends('layouts.app')
@section('content')
<div class="container">
    <h2 class="mb-4">月間出勤カレンダー</h2>
    <form method="GET" action="" class="mb-4">
        <label for="month" class="form-label">月を選択：</label>
        <input type="month" id="month" name="month" value="{{ $month }}" class="form-control" style="max-width:200px;display:inline-block;">
        <button type="submit" class="btn btn-primary ms-2">表示</button>
    </form>
    <div class="table-responsive">
        <table class="table table-bordered text-center align-middle" style="table-layout:fixed;">
            <thead class="table-light">
                <tr>
                    <th>日</th>
                    <th>月</th>
                    <th>火</th>
                    <th>水</th>
                    <th>木</th>
                    <th>金</th>
                    <th>土</th>
                </tr>
            </thead>
            <tbody>
            @php
                use Carbon\Carbon;
                $firstDay = Carbon::parse($month.'-01');
                $lastDay = $firstDay->copy()->endOfMonth();
                $startWeek = $firstDay->dayOfWeek;
                $daysInMonth = $lastDay->day;
                $calendar = [];
                $row = [];
                // 1週目の空白
                for ($i = 0; $i < $startWeek; $i++) $row[] = null;
                for ($d = 1; $d <= $daysInMonth; $d++) {
                    $row[] = $d;
                    if (count($row) === 7) {
                        $calendar[] = $row;
                        $row = [];
                    }
                }
                if (count($row)) {
                    while (count($row) < 7) $row[] = null;
                    $calendar[] = $row;
                }
                $attendanceMap = [];
                foreach ($attendances as $a) {
                    $attendanceMap[(int)Carbon::parse($a->date)->format('d')] = $a;
                }
            @endphp
            @foreach($calendar as $week)
                <tr>
                @foreach($week as $day)
                    @php
                        $cell = '';
                        $class = '';
                        if ($day) {
                            if (isset($attendanceMap[$day])) {
                                $a = $attendanceMap[$day];
                                if ($a->status === 'holiday') {
                                    $class = 'bg-light';
                                    $cell = '';
                                } elseif ($a->status === 'absence' && $a->note === '有給休暇') {
                                    $class = 'bg-success text-white';
                                    $cell = '有給';
                                } elseif ($a->status === 'absence') {
                                    $class = 'bg-secondary text-white';
                                    $cell = '欠勤';
                                } else {
                                    // 勤務時間計算
                                    $showWorkTime = false;
                                    $workMinutes = null;
                                    if ($a->clock_in && $a->clock_out) {
                                        try {
                                            $break = (is_numeric($a->break_time) && $a->break_time >= 0 && $a->break_time <= 60) ? $a->break_time : 0;
                                            $clockIn = Carbon::parse($a->clock_in);
                                            $clockOut = Carbon::parse($a->clock_out);
                                            $workMinutes = max(abs($clockOut->diffInMinutes($clockIn)) - $break, 0);
                                            $showWorkTime = true;
                                        } catch (Exception $e) {
                                            $workMinutes = null;
                                        }
                                    }
                                    if ($a->status === 'early_leave') {
                                        $class = 'bg-warning';
                                        $cell = '早退';
                                        if ($showWorkTime) {
                                            $cell .= '<br>' . floor($workMinutes / 60) . '時間 ' . ($workMinutes % 60) . '分';
                                        }
                                    } elseif ($a->status === 'late') {
                                        $class = 'bg-danger text-white';
                                        $cell = '遅刻';
                                        if ($showWorkTime) {
                                            $cell .= '<br>' . floor($workMinutes / 60) . '時間 ' . ($workMinutes % 60) . '分';
                                        }
                                    } elseif ($showWorkTime) {
                                        $class = 'bg-primary text-white';
                                        $cell = $a->clock_in ? $clockIn->format('H:i') . '〜' . $clockOut->format('H:i') . '<br>' . floor($workMinutes / 60) . '時間 ' . ($workMinutes % 60) . '分' : '出勤';
                                        // 時間外労働
                                        $overtime = $workMinutes > 480 ? $workMinutes - 480 : 0;
                                        if ($overtime > 0) {
                                            $cell .= '<br><span style="color:darkred;font-weight:bold;">時間外労働' . floor($overtime/60) . '時間 ' . ($overtime%60) . '分</span>';
                                        }
                                    } else {
                                        $class = 'bg-light';
                                        $cell = '出勤予定';
                                    }
                                }
                            } else {
                                $class = 'bg-light';
                                $cell = '';
                            }
                        }
                    @endphp
                    <td class="{{ $class }}" style="height:80px;">
                        @if($day)
                            <div class="fw-bold">{{ $day }}</div>
                            <div style="font-size:0.9em;">{!! $cell !!}</div>
                        @endif
                    </td>
                @endforeach
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-3">
        <span class="badge bg-primary">通常勤務</span>
        <span class="badge bg-secondary">欠勤</span>
        <span class="badge bg-warning text-dark">早退</span>
        <span class="badge bg-danger">遅刻</span>
    </div>
    @php
        $sum = 0;
        foreach ($attendances as $a) {
            if ($a->clock_in && $a->clock_out) {
                try {
                    $break = (is_numeric($a->break_time) && $a->break_time >= 0 && $a->break_time <= 60) ? $a->break_time : 0;
                    $clockIn = Carbon::parse($a->clock_in);
                    $clockOut = Carbon::parse($a->clock_out);
                    $workMinutes = max(abs($clockOut->diffInMinutes($clockIn)) - $break, 0);
                    $sum += $workMinutes;
                } catch (Exception $e) {
                    // skip
                }
            }
        }
    @endphp
    <div class="mt-2 fw-bold">この月の労働時間合計：{{ floor($sum / 60) }}時間 {{ $sum % 60 }}分</div>
</div>
@endsection 