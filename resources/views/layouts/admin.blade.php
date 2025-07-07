<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>勤怠管理システム（管理者）</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; }
        .sidebar {
            width: 220px;
            height: 100vh;
            background: #183153;
            color: #fff;
            position: fixed;
            top: 0;
            left: 0;
            padding-top: 40px;
        }
        .sidebar .nav-link, .sidebar .navbar-brand {
            color: #fff;
            font-weight: 500;
        }
        .sidebar .nav-link.active, .sidebar .nav-link:hover {
            background: #25406a;
            color: #fff;
        }
        .main-content {
            margin-left: 220px;
            padding: 40px 30px 30px 30px;
        }
    </style>
    @yield('head')
</head>
<body>
<div class="sidebar d-flex flex-column">
    <a class="navbar-brand mb-4 px-3" href="{{ route('admin.dashboard') }}">勤怠管理</a>
    <ul class="nav flex-column mb-auto">
        <li class="nav-item">
            <a class="nav-link{{ request()->routeIs('admin.dashboard') ? ' active' : '' }}" href="{{ route('admin.dashboard') }}">
                ホーム（ダッシュボード）
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link{{ request()->routeIs('admin.dashboard') ? ' active' : '' }}" href="{{ route('admin.dashboard') }}#today">
                本日の出勤状況
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link{{ request()->routeIs('admin.users.index') ? ' active' : '' }}" href="{{ route('admin.users.index') }}">
                ユーザー一覧
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link{{ request()->routeIs('admin.attendances.index') ? ' active' : '' }}" href="{{ route('admin.attendances.index') }}">
                月別まとめ
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link{{ request()->routeIs('admin.corrections.index') ? ' active' : '' }}" href="{{ route('admin.corrections.index') }}">
                打刻修正申請
            </a>
        </li>

    </ul>
    <div class="mt-auto px-3">
        <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button type="submit" class="btn btn-link nav-link p-0" style="color:#fff;">ログアウト</button>
        </form>
    </div>
</div>
<div class="main-content">
    @yield('content')
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 