@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">打刻修正申請一覧</h2>
    <a href="{{ route('corrections.create') }}" class="btn btn-primary mb-3">新規申請</a>
    @if(session('message'))
        <div class="alert alert-success">{{ session('message') }}</div>
    @endif
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>申請日</th>
                    <th>修正日付</th>
                    <th>出勤時刻</th>
                    <th>退勤時刻</th>
                    <th>休憩時間（分）</th>
                    <th>理由</th>
                    <th>ステータス</th>
                </tr>
            </thead>
            <tbody>
                @forelse($corrections as $correction)
                <tr>
                    <td>{{ $correction->created_at->format('Y/m/d H:i') }}</td>
                    <td>{{ $correction->date }}</td>
                    <td>{{ $correction->correct_clock_in ?? '-' }}</td>
                    <td>{{ $correction->correct_clock_out ?? '-' }}</td>
                    <td>{{ $correction->correct_break_time ?? '-' }}</td>
                    <td>{{ $correction->reason }}</td>
                    <td>
                        @if($correction->status === 'pending')
                            <span class="badge bg-warning text-dark">申請中</span>
                        @elseif($correction->status === 'approved')
                            <span class="badge bg-success">承認</span>
                        @elseif($correction->status === 'rejected')
                            <span class="badge bg-danger">却下</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center">申請はありません</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="d-flex justify-content-center mt-4">
        {{ $corrections->links() }}
    </div>
</div>
@endsection 