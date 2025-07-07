<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>勤怠管理システム</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <style>
            body {
                background: #f8f9fa;
            }
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
            <a class="navbar-brand mb-4 px-3" href="{{ route('attendance.index') }}">勤怠管理</a>
            @include('layouts.navigation')
            <ul class="nav flex-column mb-auto">
                
                <li class="nav-item">
                    <a class="nav-link{{ request()->routeIs('dashboard') ? ' active' : '' }}" href="{{ route('dashboard') }}">
                        ホーム
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link{{ request()->routeIs('attendance.index') ? ' active' : '' }}" href="{{ route('attendance.index') }}">
                        打刻
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link{{ request()->routeIs('attendance.list') ? ' active' : '' }}" href="{{ route('attendance.list') }}">
                        勤怠一覧
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link{{ request()->routeIs('attendance.monthly') ? ' active' : '' }}" href="{{ route('attendance.monthly') }}">
                        月別集計
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link{{ request()->routeIs('attendance.calendar') ? ' active' : '' }}" href="{{ route('attendance.calendar') }}">
                        勤怠カレンダー
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link{{ request()->routeIs('corrections.index') ? ' active' : '' }}" href="{{ route('corrections.index') }}">
                        打刻修正
                    </a>
                </li>

            </ul>
            <div class="mt-auto px-3">
                            <form method="POST" action="{{ route('logout') }}">
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
