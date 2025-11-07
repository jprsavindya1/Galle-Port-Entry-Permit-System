<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>
    
    <!-- Favicon - Browser Tab Icon -->
    <link rel="icon" type="image/png" href="{{ asset('images/Sri_Lanka_Ports_Authority_logo.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('images/Sri_Lanka_Ports_Authority_logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/Sri_Lanka_Ports_Authority_logo.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Custom Styles -->
    <style>
        body {
            background: linear-gradient(135deg, #093c76ff, #3a6073) !important; 
            color: #2d3748 !important; 
            font-family: 'Figtree', sans-serif;
        }
        .auth-card {
            background: #ffffffee !important; 
            border-radius: 1rem;
            padding: 2.5rem;
            box-shadow: 0 12px 30px rgba(0,0,0,0.2);
            backdrop-filter: blur(6px); 
        }
        .auth-logo img {
            width: 160px !important;
            height: auto !important;
            filter: drop-shadow(0 4px 6px rgba(0,0,0,0.25));
        }
        .page-title {
            font-size: 1.75rem; 
            font-weight: 700;
            color: #fbfbfbff; 
            margin-bottom: 1rem;
        }
    </style>
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen flex flex-col justify-center items-center">
        
        <!-- Logo -->
        <div class="auth-logo mb-6">
            <a href="/" class="flex justify-center">
                <img src="{{ asset('images/Sri_Lanka_Ports_Authority_logo.png') }}" alt="App Logo">
            </a>
        </div>
        
        <!-- Title -->
        <div class="mb-4 text-center">
            <h1 class="page-title">Welcome to {{ config('app.name', 'Laravel') }}</h1>
        </div>
       
        <!-- Card -->
        <div class="w-full sm:max-w-md auth-card">
            {{ $slot }}
        </div>
    </div>
</body>
</html>
