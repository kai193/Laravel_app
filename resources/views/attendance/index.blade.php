@extends('layouts.app')

@section('head')
<style>
    .clock-date, .clock-time {
        font-size: 2rem;
        color: #183153;
        font-weight: bold;
    }
    .circle-btn {
        width: 110px;
        height: 110px;
        border-radius: 50%;
        border: 2.5px solid #2176ae;
        background: #fff;
        color: #2176ae;
        font-size: 1.4rem;
        font-weight: 500;
        margin: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: 0.2s;
        box-shadow: 0 2px 8px rgba(24,49,83,0.07);
    }
    .circle-btn:hover {
        background: #2176ae;
        color: #fff;
        border-color: #183153;
    }
    .circle-btn.red { border-color: #e76f51; color: #e76f51; }
    .circle-btn.red:hover { background: #e76f51; color: #fff; }
    .circle-btn.yellow { border-color: #f4a261; color: #f4a261; }
    .circle-btn.yellow:hover { background: #f4a261; color: #fff; }
    .button-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        grid-gap: 20px 40px;
        justify-items: center;
        align-items: center;
        margin-top: 30px;
    }
</style>
<script>
    function updateClock() {
        const now = new Date();
        const y = now.getFullYear();
        const m = (now.getMonth()+1).toString().padStart(2,'0');
        const d = now.getDate().toString().padStart(2,'0');
        const w = ['日','月','火','水','木','金','土'][now.getDay()];
        document.getElementById('clock-date').textContent = `${y}年${m}月${d}日(${w})`;
        const h = now.getHours().toString().padStart(2,'0');
        const min = now.getMinutes().toString().padStart(2,'0');
        const sec = now.getSeconds().toString().padStart(2,'0');
        document.getElementById('clock-time').textContent = `${h}:${min}`;
        document.getElementById('clock-sec').textContent = sec;
    }
    setInterval(updateClock, 1000);
    window.onload = updateClock;
</script>
@endsection

@section('content')
<div class="container mt-5">
    <div class="row align-items-center">
        <!-- 左側：日付・時間 -->
        <div class="col-md-6 d-flex flex-column align-items-start">
            <div class="clock-date" id="clock-date"></div>
            <div class="clock-time" id="clock-time"></div>
            <span style="font-size:2rem;color:#888;vertical-align:top;" id="clock-sec"></span>
            <div style="font-size:1.2rem; color:#555; margin-top:10px;">
                {{ Auth::user()->name }} さん
            </div>
            @if($status === 'working')
                <div style="color: #e3342f; font-weight: bold; margin-top: 10px;">
                    勤務中です
                </div>
            @elseif($status === 'break')
                <div style="color: #3490dc; font-weight: bold; margin-top: 10px;">
                    休憩中です
                </div>
            @elseif($status === 'done')
                <div style="color: #38c172; font-weight: bold; margin-top: 10px;">
                    お疲れ様でした
                </div>
            @endif
            @if(session('message'))
                <div class="alert alert-info mt-2" style="font-size:1.1rem;">
                    {{ session('message') }}
                </div>
            @endif
        </div>
        <!-- 右側：打刻ボタン -->
        <div class="col-md-6 d-flex justify-content-center">
            <div class="button-grid">
                <form action="{{ route('attendance.clock-in') }}" method="POST">
                    @csrf
                    <button type="submit" class="circle-btn" {{ isset($attendance) && $attendance->clock_in ? 'disabled' : '' }}>出勤</button>
                </form>
                <form action="{{ route('attendance.clock-out') }}" method="POST">
                    @csrf
                    <button type="submit" class="circle-btn" {{ !isset($attendance) || !$attendance->clock_in || $attendance->clock_out ? 'disabled' : '' }}>退勤</button>
                </form>
                @if($status === 'working')
                    <form action="{{ route('attendance.breakin') }}" method="POST">
                        @csrf
                        <button type="submit" class="circle-btn red">休憩入り</button>
                    </form>
                @elseif($status === 'break')
                    <form action="{{ route('attendance.breakout') }}" method="POST">
                        @csrf
                        <button type="submit" class="circle-btn red">休憩戻り</button>
                    </form>
                @else
                    <button type="button" class="circle-btn red" disabled>休憩</button>
                @endif
                <button type="button" class="circle-btn yellow" data-bs-toggle="modal" data-bs-target="#absenceModal">欠勤</button>
                <button type="button" class="circle-btn yellow" data-bs-toggle="modal" data-bs-target="#earlyLeaveModal">早退</button>
                <button type="button" class="circle-btn yellow" data-bs-toggle="modal" data-bs-target="#lateModal">遅刻</button>
            </div>
        </div>
    </div>
</div>

<!-- 欠勤モーダル -->
<div class="modal fade" id="absenceModal" tabindex="-1" aria-labelledby="absenceModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="absenceModalLabel">欠勤理由</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('attendance.absence') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="absenceReason" class="form-label">欠勤理由</label>
                        <textarea class="form-control" id="absenceReason" name="note" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">キャンセル</button>
                    <button type="submit" class="btn btn-primary">送信</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- 早退モーダル -->
<div class="modal fade" id="earlyLeaveModal" tabindex="-1" aria-labelledby="earlyLeaveModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="earlyLeaveModalLabel">早退理由</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('attendance.early-leave') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="earlyLeaveReason" class="form-label">早退理由</label>
                        <textarea class="form-control" id="earlyLeaveReason" name="note" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">キャンセル</button>
                    <button type="submit" class="btn btn-primary">送信</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- 遅刻モーダル -->
<div class="modal fade" id="lateModal" tabindex="-1" aria-labelledby="lateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="lateModalLabel">遅刻理由</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('attendance.late') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="lateReason" class="form-label">遅刻理由</label>
                        <textarea class="form-control" id="lateReason" name="note" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">キャンセル</button>
                    <button type="submit" class="btn btn-primary">送信</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 