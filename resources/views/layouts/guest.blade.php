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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Custom Styles -->
    <style>
        body {
            /* Clearer daytime gradient: keeps the sky bright and daylight colors realistic */
            background: linear-gradient(to bottom, rgba(15, 32, 67, 0.15) 0%, rgba(5, 16, 38, 0.4) 100%), 
                        url('{{ asset("images/galle_port_bg.png") }}') no-repeat center center fixed;
            background-size: cover;
            color: #ffffff !important;
            font-family: 'Outfit', sans-serif;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }
        
        .auth-container {
            width: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 2.5rem 1.5rem;
        }

        .auth-logo img {
            width: 140px !important;
            height: auto !important;
            filter: drop-shadow(0 8px 16px rgba(0,0,0,0.5));
            transition: transform 0.3s ease;
        }
        .auth-logo img:hover {
            transform: scale(1.05);
        }

        .auth-card {
            background: transparent !important;
            border: none !important;
            box-shadow: none !important;
            backdrop-filter: none !important;
            -webkit-backdrop-filter: none !important;
            width: 100%;
            max-width: 440px;
            padding: 0.5rem;
        }

        /* Input Overrides */
        .auth-card input[type="text"], 
        .auth-card input[type="email"], 
        .auth-card input[type="password"] {
            background-color: rgba(0, 0, 0, 0.35) !important;
            border: 1.5px solid rgba(255, 255, 255, 0.25) !important;
            color: #ffffff !important;
            border-radius: 0.5rem !important;
            height: 46px !important;
            transition: all 0.2s ease !important;
            font-size: 0.95rem !important;
        }
        
        .auth-card input[type="text"]:focus, 
        .auth-card input[type="email"]:focus, 
        .auth-card input[type="password"]:focus {
            border-color: #3b82f6 !important;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.35) !important;
            background-color: rgba(0, 0, 0, 0.45) !important;
            outline: none !important;
        }
        
        .auth-card label {
            color: #ffffff !important;
            font-size: 0.95rem !important;
            font-weight: 500 !important;
            margin-bottom: 0.35rem !important;
            display: inline-block;
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.4);
        }
        
        .auth-card span {
            color: rgba(255, 255, 255, 0.9) !important;
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.4);
        }

        .auth-card a {
            color: #60a5fa !important;
            font-weight: 500 !important;
            transition: color 0.2s ease !important;
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.4);
        }
        .auth-card a:hover {
            color: #93c5fd !important;
            text-decoration: underline !important;
        }

        /* Checkbox Override */
        .auth-card input[type="checkbox"] {
            background-color: rgba(0, 0, 0, 0.4) !important;
            border: 1px solid rgba(255, 255, 255, 0.3) !important;
            border-radius: 0.25rem !important;
            color: #3b82f6 !important;
            width: 17px !important;
            height: 17px !important;
        }

        /* Redesign Animations & Custom Styles */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .animate-fade-in-up {
            animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
        .animate-fade-in-down {
            animation: fadeInDown 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
        .text-glow {
            text-shadow: 0 0 20px rgba(31, 181, 199, 0.45), 
                         0 0 40px rgba(19, 49, 76, 0.35), 
                         0 2px 10px rgba(16, 21, 27, 0.55);
        }
        .sub-shadow {
            text-shadow: 0 2px 8px rgba(19, 49, 76, 0.65), 
                         0 1px 3px rgba(16, 21, 27, 0.5);
        }
    </style>
</head>
<body class="antialiased">
    @if (request()->routeIs('login'))
        <div class="w-full min-h-screen flex items-center justify-center relative overflow-hidden p-6 md:p-12 z-10">
            <!-- Content Container -->
            <div class="max-w-7xl mx-auto w-full grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16 items-center">
                
                <!-- Left Column: Welcome / Brand -->
                <div class="hidden lg:flex lg:col-span-7 flex-col space-y-6 animate-fade-in-up lg:self-start lg:-mt-4">
                    <!-- Logo -->
                    <div class="flex items-center">
                        <img src="{{ asset('images/Sri_Lanka_Ports_Authority_logo.png') }}" 
                             alt="SLPA Logo" 
                             class="w-[90px] h-auto filter drop-shadow-[0_8px_16px_rgba(0,0,0,0.5)] hover:scale-105 transition-transform duration-300">
                    </div>

                    <!-- Text Headings -->
                    <div class="space-y-4">
                        <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold text-white tracking-tight leading-tight text-glow">
                            Welcome to SLPA<br>
                            Permit System
                        </h1>
                        <div class="w-20 h-1 bg-[#F6BA18] rounded my-2"></div>
                        <p class="text-lg md:text-xl text-white/85 font-medium leading-relaxed sub-shadow">
                            Official Gateway for Galle Port Permit Management
                        </p>
                    </div>
                </div>

                <!-- Right Column: Glassmorphic Login Form -->
                <div class="lg:col-span-5 flex justify-center lg:justify-end animate-fade-in-down">
                    <div class="w-full max-w-[450px] bg-white/35 backdrop-blur-2xl border border-white/50 rounded-3xl p-8 shadow-[0_20px_50px_rgba(0,0,0,0.15)] relative overflow-hidden transition-all duration-500 hover:border-white/70 group"
                         style="box-shadow: 0 30px 60px rgba(0,0,0,0.15), inset 0 0 0 1px rgba(255, 255, 255, 0.45);">
                        
                        <!-- Circular cyber lines background -->
                        <div class="absolute inset-0 pointer-events-none opacity-20 overflow-hidden rounded-3xl -z-10">
                            <svg class="absolute -right-16 -top-16 w-80 h-80 text-[#1FB5C7]" viewBox="0 0 200 200" fill="none" stroke="currentColor" stroke-width="0.5">
                                <circle cx="100" cy="100" r="80" stroke-dasharray="2 4"/>
                                <circle cx="100" cy="100" r="60"/>
                                <circle cx="100" cy="100" r="40" stroke-dasharray="5 5"/>
                                <circle cx="100" cy="100" r="20"/>
                            </svg>
                            <svg class="absolute -left-16 -bottom-16 w-80 h-80 text-[#1278B9]" viewBox="0 0 200 200" fill="none" stroke="currentColor" stroke-width="0.5">
                                <circle cx="100" cy="100" r="90"/>
                                <circle cx="100" cy="100" r="70" stroke-dasharray="4 4"/>
                                <circle cx="100" cy="100" r="50"/>
                                <circle cx="100" cy="100" r="30" stroke-dasharray="1 3"/>
                            </svg>
                        </div>
                        
                        {{ $slot }}
                    </div>
                </div>

            </div>
        </div>
    @else
        <div class="auth-container">
            
            <!-- Logo -->
            <div class="auth-logo mb-4">
                <a href="/" class="flex justify-center">
                    <img src="{{ asset('images/Sri_Lanka_Ports_Authority_logo.png') }}" alt="SLPA Logo">
                </a>
            </div>
            
            <!-- Title & Subtitle styled cleanly exactly like user mockup -->
            <div class="mb-6 text-center">
                <p style="color: rgba(255, 255, 255, 0.9); font-size: 0.95rem; font-weight: 600; text-transform: uppercase; letter-spacing: 2px; text-shadow: 0 2px 5px rgba(0,0,0,0.6); margin: 0 0 0.5rem 0;">
                    Sri Lanka Ports Authority (SLPA)
                </p>
                <h1 style="font-size: 2.6rem; font-weight: 800; color: #ffffff; text-shadow: 0 4px 15px rgba(0,0,0,0.65); margin: 0 0 0.5rem 0; line-height: 1.1;">
                    Welcome to SLPA Permit System
                </h1>
                <p style="color: rgba(255, 255, 255, 0.9); font-size: 1.15rem; font-weight: 600; text-shadow: 0 2px 5px rgba(0,0,0,0.6); margin: 0; letter-spacing: 0.5px;">
                    - Galle Port -
                </p>
            </div>
           
            <!-- Card Wrapper -->
            <div class="auth-card">
                {{ $slot }}
            </div>
        </div>
    @endif
</body>
</html>
