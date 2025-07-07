@extends('layouts.admin')

@section('content')
<div class="container">
    <h2 class="mb-4">打刻修正申請（管理者）</h2>
    @if(session('message'))
        <div class="alert alert-success">{{ session('message') }}</div>
    @endif
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>申請日</th>
                    <th>ユーザー名</th>
                    <th>修正日付</th>
                    <th>出勤時刻</th>
                    <th>退勤時刻</th>
                    <th>休憩時間（分）</th>
                    <th>理由</th>
                    <th>ステータス</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                @forelse($corrections as $correction)
                <tr>
                    <td>{{ $correction->created_at->format('Y/m/d H:i') }}</td>
                    <td>{{ $correction->user->name ?? '-' }}</td>
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
                    <td>
                        @if($correction->status === 'pending')
                        <form action="{{ route('admin.corrections.approve', $correction->id) }}" method="POST" style="display:inline-block;">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm">承認</button>
                        </form>
                        <form action="{{ route('admin.corrections.reject', $correction->id) }}" method="POST" style="display:inline-block;">
                            @csrf
                            <button type="submit" class="btn btn-danger btn-sm">却下</button>
                        </form>
                        @else
                            -
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" class="text-center">申請はありません</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <!-- ページネーション削除 -->
</div>
@endsection 