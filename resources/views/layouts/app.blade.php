<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>KIU Student Task & Activity Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f4f7fb;
        }

        .kiu-navbar {
            background: #12355b;
        }

        .kiu-brand-mark {
            width: 34px;
            height: 34px;
            border-radius: 8px;
            background: #f9c74f;
            color: #12355b;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            letter-spacing: 0;
        }

        .portal-header {
            background: linear-gradient(135deg, #12355b, #1f6f8b);
            color: #fff;
            border-radius: 8px;
        }

        .tag-pill {
            background: #e9f2fb;
            color: #12355b;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark kiu-navbar mb-4">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ auth()->check() ? route('tasks.index') : route('login') }}">
                <span class="kiu-brand-mark">KIU</span>
                <span>Student Task & Activity Management</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mainNavbar">
                <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
                    @auth
                        <li class="nav-item"><a class="nav-link" href="{{ route('tasks.index') }}">Tasks</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('tasks.create') }}">Create Task</a></li>
                        <li class="nav-item text-white-50 small px-lg-2">{{ auth()->user()->name }}</li>
                        <li class="nav-item">
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button class="btn btn-sm btn-warning" type="submit">Logout</button>
                            </form>
                        </li>
                    @else
                        <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Login</a></li>
                        <li class="nav-item"><a class="btn btn-sm btn-warning" href="{{ route('register') }}">Register</a></li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <main class="container pb-5">
        @include('components.alerts')
        @yield('content')
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>
</html>
