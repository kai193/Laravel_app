@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">打刻修正申請</h2>
    <form action="{{ route('corrections.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="date" class="form-label">修正日付</label>
            <input type="date" id="date" name="date" class="form-control" value="{{ old('date') }}" required>
        </div>
        <div class="mb-3">
            <label for="correct_clock_in" class="form-label">出勤時刻</label>
            <input type="time" id="correct_clock_in" name="correct_clock_in" class="form-control" value="{{ old('correct_clock_in') }}">
        </div>
        <div class="mb-3">
            <label for="correct_clock_out" class="form-label">退勤時刻</label>
            <input type="time" id="correct_clock_out" name="correct_clock_out" class="form-control" value="{{ old('correct_clock_out') }}">
        </div>
        <div class="mb-3">
            <label for="correct_break_time" class="form-label">休憩時間（分）</label>
            <input type="number" id="correct_break_time" name="correct_break_time" class="form-control" value="{{ old('correct_break_time') }}" min="0">
        </div>
        <div class="mb-3">
            <label for="reason" class="form-label">修正理由</label>
            <textarea id="reason" name="reason" class="form-control" rows="3" required>{{ old('reason') }}</textarea>
        </div>
        <button type="submit" class="btn btn-primary">申請する</button>
        <a href="{{ route('corrections.index') }}" class="btn btn-secondary ms-2">戻る</a>
    </form>
</div>
@endsection 