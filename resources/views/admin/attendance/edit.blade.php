@extends('layouts.admin')

@section('content')
<div class="container">
    <h2 class="mb-4">勤怠情報の編集</h2>

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('admin.attendance.update', $attendance->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="clock_in" class="form-label">出勤時間</label>
            <input type="datetime-local" class="form-control @error('clock_in') is-invalid @enderror" 
                   id="clock_in" name="clock_in" 
                   value="{{ old('clock_in', $attendance->clock_in->format('Y-m-d\TH:i')) }}" required>
            @error('clock_in')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="clock_out" class="form-label">退勤時間</label>
            <input type="datetime-local" class="form-control @error('clock_out') is-invalid @enderror" 
                   id="clock_out" name="clock_out" 
                   value="{{ old('clock_out', $attendance->clock_out ? $attendance->clock_out->format('Y-m-d\TH:i') : '') }}">
            @error('clock_out')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="break_time" class="form-label">休憩時間（分）</label>
            <input type="number" class="form-control @error('break_time') is-invalid @enderror" 
                   id="break_time" name="break_time" 
                   value="{{ old('break_time', $attendance->break_time) }}" required min="0">
            @error('break_time')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="note" class="form-label">備考</label>
            <textarea class="form-control @error('note') is-invalid @enderror" 
                      id="note" name="note" rows="3">{{ old('note', $attendance->note) }}</textarea>
            @error('note')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-flex justify-content-between">
            <button type="submit" class="btn btn-primary">更新</button>
            <a href="{{ route('attendance.list') }}" class="btn btn-secondary">戻る</a>
        </div>
    </form>
</div>
@endsection 