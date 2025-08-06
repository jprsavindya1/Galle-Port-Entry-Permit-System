<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel Permit System') }}</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Tailwind / Laravel Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-light">
    <!-- Top Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark px-4">
        <a class="navbar-brand" href="{{ route('dashboard') }}">{{ config('app.name', 'Permit Portal') }}</a>
        <div class="ms-auto text-white">
            @auth
                {{ Auth::user()->name }}
                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-light ms-2">Logout</button>
                </form>
            @endauth
        </div>
    </nav>

    <!-- Layout Row -->
    <div class="d-flex">
        <!-- Sidebar -->
        <div class="text-white shadow p-4" style="min-width: 220px; min-height: 100vh; background: linear-gradient(to bottom, #002b5c, #00bfff);">
            <h5 class="mb-4">Navigation</h5>
            <ul class="nav flex-column">
                <li class="nav-item mb-2">
                    <a class="nav-link text-white {{ request()->routeIs('dashboard') ? 'active bg-secondary rounded' : '' }}" href="{{ route('dashboard') }}">
                        Dashboard
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a class="nav-link text-white {{ request()->routeIs('permit.temporary') ? 'active bg-secondary rounded' : '' }}" href="{{ route('permit.temporary') }}">
                        Temporary Permit
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a class="nav-link text-white {{ request()->routeIs('permit.monthly') ? 'active bg-secondary rounded' : '' }}" href="{{ route('permit.monthly') }}">
                        Monthly Permit
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a class="nav-link text-white {{ request()->routeIs('permit.vehicle') ? 'active bg-secondary rounded' : '' }}" href="{{ route('permit.vehicle') }}">
                        Vehicle Permit
                    </a>
                </li>
            </ul>
        </div>

        <!-- Main Content -->
        <main class="flex-grow-1 p-4">
            @yield('content')
        </main>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
