@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">従業員一覧</h5>
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">ダッシュボードに戻る</a>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>名前</th>
                                    <th>メールアドレス</th>
                                    <th>登録日</th>
                                    <th>最終出勤日</th>
                                    <th>操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $user)
                                <tr>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->created_at->format('Y/m/d') }}</td>
                                    <td>
                                        @if($user->attendances->isNotEmpty())
                                            {{ $user->attendances->sortByDesc('date')->first()->date->format('Y/m/d') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.users.detail', $user->id) }}" class="btn btn-primary btn-sm">詳細</a>
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