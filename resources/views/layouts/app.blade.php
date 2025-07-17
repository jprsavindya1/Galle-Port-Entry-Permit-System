<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel Permit System') }}</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

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
<div class="bg-white text-dark" style="min-width: 220px; min-height: 100vh;">
    <div class="p-4">
        <h5 class="text-dark mb-4">Navigation</h5>
        <ul class="nav flex-column">
            <li class="nav-item mb-2">
                <a class="nav-link text-dark {{ request()->routeIs('dashboard') ? 'active bg-secondary rounded' : '' }}" href="{{ route('dashboard') }}">
                    Dashboard
                </a>
            </li>
            <li class="nav-item mb-2">
                <a class="nav-link text-dark {{ request()->routeIs('permit.temporary.create') ? 'active bg-secondary rounded' : '' }}" href="{{ route('permit.temporary.create') }}">
                    Temporary Permit
                </a>
            </li>
            <li class="nav-item mb-2">
                <a class="nav-link text-dark {{ request()->routeIs('permit.monthly.create') ? 'active bg-secondary rounded' : '' }}" href="{{ route('permit.monthly.create') }}">
                    Monthly Permit
                </a>
            </li>
            <li class="nav-item mb-2">
                <a class="nav-link text-dark hover:text-primary {{ request()->routeIs('permit.vehicle.create') ? 'active bg-secondary rounded' : '' }}" href="{{ route('permit.vehicle.create') }}">

                    Vehicle Permit
                </a>
            </li>
        </ul>
    </div>
</div>


        <!-- Main Content -->
        <main class="flex-grow-1 p-4">
            @yield('content')
        </main>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
