<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    <!-- Favicon - Browser Tab Icon -->
    <link rel="icon" type="image/png" href="{{ asset('images/Sri_Lanka_Ports_Authority_logo.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('images/Sri_Lanka_Ports_Authority_logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/Sri_Lanka_Ports_Authority_logo.png') }}">
    
    <!-- CRITICAL: Preload logo FIRST with absolute highest priority -->
    <link rel="preload" href="{{ asset('images/Sri_Lanka_Ports_Authority_logo.png') }}" as="image" type="image/png" fetchpriority="high">
    <link rel="preload" href="{{ asset('css/navbar-critical.css') }}" as="style" fetchpriority="high">
    
    <!-- CRITICAL: Inline CSS to prevent ANY layout shift or flash - loads INSTANTLY -->
    <style>
        /* Prevent flash of unstyled navbar - applied immediately before any CSS loads */
        nav.navbar.navbar-slpa-logo { 
            background-color: #13314C !important; 
            min-height: 70px !important; 
            border-bottom: none !important;
        }
        nav.navbar.navbar-slpa-logo .navbar-brand { 
            height: 70px !important; 
            min-height: 70px !important; 
            display: flex !important; 
            align-items: center !important; 
        }
        nav.navbar.navbar-slpa-logo .navbar-brand img { 
            height: 55px !important; 
            width: 55px !important; 
            min-width: 55px !important; 
            min-height: 55px !important; 
            max-height: 55px !important;
            max-width: 55px !important;
            display: block !important; 
            object-fit: contain !important;
            background: #13314C !important;
            flex-shrink: 0 !important;
            opacity: 1 !important;
            content-visibility: auto !important;
        }
        
        /* Placeholder while image loads */
        nav.navbar.navbar-slpa-logo .navbar-brand img[src*="Sri_Lanka_Ports_Authority_logo.png"] {
            background: #13314C url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 55 55"><rect fill="%2313314C" width="55" height="55"/></svg>') center/contain no-repeat !important;
        }
        nav.navbar.navbar-slpa-logo .brand-text { 
            color: #F6BA18 !important;
            font-size: 1.5rem !important; 
            font-weight: 700 !important; 
            white-space: nowrap !important; 
            line-height: 1.2 !important;
        }
        /* Logout button - critical inline styles */
        nav.navbar.navbar-slpa-logo .btn-slpa-logout {
            background-color: #F6BA18 !important;
            border-color: #F6BA18 !important;
            color: #13314C !important;
            font-weight: 600 !important;
            padding: 0.375rem 1rem !important;
            border-radius: 0.25rem !important;
        }
    </style>
    
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel Permit System') }}</title>

    <!-- CRITICAL: Load navbar CSS FIRST before Bootstrap -->
    <link href="{{ asset('css/navbar-critical.css') }}" rel="stylesheet">
    
    <!-- Inline script to force immediate logo load -->
    <script>
        // CRITICAL: Force logo to load synchronously before page renders
        (function() {
            const logoUrl = '{{ asset("images/Sri_Lanka_Ports_Authority_logo.png") }}';
            
            // Method 1: Create image object immediately
            window.__logoPreload = new Image();
            window.__logoPreload.src = logoUrl;
            
            // Method 2: Force synchronous load via inline image
            document.write('<img style="display:none;position:absolute;width:1px;height:1px;" src="' + logoUrl + '" />');
            
            // Lock navbar visibility immediately
            if (document.querySelector('.navbar-slpa-logo')) {
                document.querySelector('.navbar-slpa-logo').style.visibility = 'visible';
            }
        })();
    </script>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            /* Colors extracted from the SLPA Logo */
            --slpa-logo-blue: #13314C;      /* Deep Navy Blue (Primary) */
            --slpa-logo-gold: #F6BA18;      /* Gold/Yellow (Accent) */
            --slpa-logo-darker-gold: #DCA20D; /* Darker Gold for hover */
            --slpa-logo-white: #FFFFFF;
            --slpa-light-gray: #F8F9FA;     /* Very light background */
        }
        
        body {
            background-color: var(--slpa-light-gray) !important;
            overflow-y: scroll;
        }

        /* Top Navbar - Deep Navy Blue */
        nav.navbar.navbar-slpa-logo {
            background-color: var(--slpa-logo-blue) !important; 
            border-bottom: none !important; 
            padding: 0.75rem 1rem !important;
            min-height: 70px !important;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif !important;
        }
        
        /* Logo Image - Fixed Size */
        nav.navbar-slpa-logo .navbar-brand img {
            height: 55px !important;
            width: 55px !important;
            max-height: 55px !important;
            max-width: 55px !important;
            min-height: 55px !important;
            min-width: 55px !important;
            object-fit: contain !important;
            display: block !important;
            background-color: transparent !important;
            flex-shrink: 0 !important;
        }

        /* Brand Title - Gold Text */
        nav.navbar-slpa-logo .navbar-brand .brand-text {
            color: var(--slpa-logo-gold) !important;
            font-weight: 700 !important;
            font-size: 1.5rem !important;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.3) !important;
            line-height: 1.2 !important;
            white-space: nowrap !important;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif !important;
        }
        
        /* Navbar Brand Container */
        nav.navbar-slpa-logo .navbar-brand {
            display: flex !important;
            align-items: center !important;
            padding: 0.5rem 0 !important;
            height: 70px !important;
            min-height: 70px !important;
        }

        /* Sidebar - Deep Navy Blue */
        .sidebar-slpa-logo {
            min-width: 250px;
            width: 250px;
            height: calc(100vh - 70px); /* Full height minus navbar */
            background-color: var(--slpa-logo-blue); 
            box-shadow: 3px 0 10px rgba(0, 0, 0, 0.15);
            padding-top: 10px;
            position: fixed;
            top: 70px; /* Below navbar */
            left: 0;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            overflow: hidden; /* Prevent background image from scrolling */
        }
        .sidebar-nav-content {
            overflow-y: auto;
            flex-grow: 1;
            padding-bottom: 20px;
        }
        /* Custom scrollbar styling for sidebar navigation content */
        .sidebar-nav-content::-webkit-scrollbar {
            width: 5px;
        }
        .sidebar-nav-content::-webkit-scrollbar-track {
            background: transparent;
        }
        .sidebar-nav-content::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.15);
            border-radius: 10px;
        }
        .sidebar-nav-content::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.3);
        }
        
        .sidebar-lighthouse-footer {
            height: 180px;
            min-height: 180px;
            overflow: hidden;
            position: relative;
            margin-top: auto;
            width: 100%;
        }
        
        .sidebar-lighthouse-footer .fade-overlay {
            background: linear-gradient(to bottom, var(--slpa-logo-blue) 0%, rgba(19, 49, 76, 0) 100%);
            height: 50px;
            width: 100%;
            position: absolute;
            top: 0;
            left: 0;
            z-index: 5;
            pointer-events: none;
        }
        
        .sidebar-lighthouse-footer img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            opacity: 0.85;
            filter: saturate(0.9) brightness(0.9);
        }
        
        @media (max-width: 768px) {
            .sidebar-lighthouse-footer {
                display: none !important;
            }
        }
        
        /* Main content margin to account for fixed sidebar */
        main.flex-grow-1 {
            margin-left: 250px;
            width: calc(100% - 250px);
        }
        
        /* Responsive adjustments */
        @media (max-width: 992px) {
            .sidebar-slpa-logo {
                width: 200px;
                min-width: 200px;
            }
            main.flex-grow-1 {
                margin-left: 200px;
                width: calc(100% - 200px);
            }
        }
        
        @media (max-width: 768px) {
            .sidebar-slpa-logo {
                width: 180px;
                min-width: 180px;
            }
            main.flex-grow-1 {
                margin-left: 180px;
                width: calc(100% - 180px);
            }
            nav.navbar-slpa-logo .navbar-brand .brand-text {
                font-size: 1.2rem !important;
            }
        }
        
        @media (max-width: 576px) {
            .sidebar-slpa-logo {
                width: 60px;
                min-width: 60px;
            }
            .sidebar-slpa-logo .nav-link,
            .sidebar-slpa-logo .sidebar-header {
                font-size: 0;
                padding: 12px 8px;
                text-align: center;
            }
            .sidebar-slpa-logo .nav-link::before {
                content: '•';
                font-size: 1.5rem;
            }
            .sidebar-slpa-logo .sidebar-header {
                display: none;
            }
            main.flex-grow-1 {
                margin-left: 60px;
                width: calc(100% - 60px);
            }
            nav.navbar-slpa-logo .navbar-brand .brand-text {
                font-size: 0.9rem !important;
            }
        }
        
        /* Fixed navbar */
        nav.navbar.navbar-slpa-logo {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1001;
        }
        
        /* Body padding to account for fixed navbar */
        body {
            padding-top: 70px;
        }

        /* Sidebar Navigation Links */
        .sidebar-slpa-logo .nav-link {
            padding: 10px 16px !important;
            margin: 2px 12px !important;
            color: rgba(255, 255, 255, 0.65) !important; 
            border-left: none !important;
            border-radius: 8px !important;
            transition: all 0.25s ease-in-out !important;
            font-weight: 500 !important;
            display: flex !important;
            align-items: center !important;
            gap: 10px !important;
            font-size: 0.95rem !important;
        }
        
        /* Sidebar Hover Effect */
        .sidebar-slpa-logo .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.08) !important; 
            color: #ffffff !important;
            transform: translateX(4px) !important;
        }

        /* Sidebar Active Link - Blue Capsule Background */
        .sidebar-slpa-logo .nav-link.active {
            background-color: #0b5ed7 !important; /* Premium active blue background */
            color: #ffffff !important; 
            font-weight: 600 !important;
            border-left: none !important;
            border-radius: 8px !important;
            box-shadow: 0 4px 12px rgba(13, 110, 253, 0.25) !important;
        }

        /* Navigation Section Headers */
        .sidebar-header {
            color: #5d7c9e !important;
            font-size: 0.72rem !important;
            font-weight: 700 !important;
            letter-spacing: 0.8px !important;
            margin-top: 24px !important;
            margin-bottom: 8px !important;
            padding-bottom: 0 !important;
            border-bottom: none !important;
            text-transform: uppercase !important;
            padding-left: 20px !important;
        }

        /* Logout Button - Gold with Dark Text - Maximum specificity */
        nav.navbar-slpa-logo .btn-slpa-logout,
        nav.navbar-slpa-logo button.btn-slpa-logout,
        nav.navbar-slpa-logo .btn.btn-slpa-logout,
        nav.navbar-slpa-logo .btn.btn-sm.btn-slpa-logout {
            background-color: var(--slpa-logo-gold) !important; 
            border-color: var(--slpa-logo-gold) !important;
            color: var(--slpa-logo-blue) !important; 
            font-weight: 600 !important;
            padding: 0.375rem 1rem !important;
            border-radius: 0.25rem !important;
            transition: background-color 0.2s, border-color 0.2s, color 0.2s !important;
            font-size: 0.875rem !important;
            line-height: 1.5 !important;
            text-decoration: none !important;
            display: inline-block !important;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif !important;
        }
        
        /* Logout Button Hover */
        nav.navbar-slpa-logo .btn-slpa-logout:hover,
        nav.navbar-slpa-logo button.btn-slpa-logout:hover,
        nav.navbar-slpa-logo .btn.btn-slpa-logout:hover,
        nav.navbar-slpa-logo .btn.btn-sm.btn-slpa-logout:hover,
        nav.navbar-slpa-logo .btn-slpa-logout:focus,
        nav.navbar-slpa-logo .btn-slpa-logout:active {
            background-color: var(--slpa-logo-darker-gold) !important;
            border-color: var(--slpa-logo-darker-gold) !important;
            color: var(--slpa-logo-white) !important;
            transform: none !important;
            box-shadow: none !important;
        }

        /* User Name Display */
        nav.navbar-slpa-logo .user-name {
            color: var(--slpa-logo-white) !important;
            font-weight: 500 !important;
            font-size: 1rem !important;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif !important;
        }
    </style>
</head>
<body style="visibility: visible;">
    <nav class="navbar navbar-expand-lg navbar-dark navbar-slpa-logo shadow-sm" style="visibility: visible;">
        <a class="navbar-brand d-flex align-items-center ps-4" href="{{ route('dashboard') }}">
            <img src="{{ asset('images/Sri_Lanka_Ports_Authority_logo.png') }}" 
                alt="SLPA Logo" 
                class="me-3 navbar-logo-img" 
                style="height: 52px !important; width: 52px !important; min-height: 52px !important; min-width: 52px !important; display: block !important; object-fit: contain !important; visibility: visible !important;"
                width="52"
                height="52"
                fetchpriority="high">
            <div class="d-flex flex-column">
                <span class="brand-text text-white font-semibold" style="font-size: 1.2rem !important; font-weight: 600 !important; line-height: 1.2 !important; letter-spacing: 0.5px !important;">SLPA Permit System (Galle)</span>
                <span class="brand-subtext text-white/60 font-medium" style="font-size: 0.72rem !important; font-weight: 500 !important; line-height: 1.2 !important; margin-top: 2px !important;">Official Gateway for Galle Port Permit Management</span>
            </div>
        </a>

        <div class="ms-auto pe-4 d-flex align-items-center">
            @auth
                <!-- User Profile Dropdown Menu -->
                <div class="dropdown">
                    <div class="d-flex align-items-center bg-white/10 border border-white/10 rounded-pill p-1 pe-3 dropdown-toggle cursor-pointer" 
                         id="userProfileDropdown" 
                         data-bs-toggle="dropdown" 
                         aria-expanded="false" 
                         style="transition: all 0.2s; cursor: pointer;"
                         onmouseover="this.style.background='rgba(255,255,255,0.15)'"
                         onmouseout="this.style.background='rgba(255,255,255,0.1)'">
                        <div class="d-flex align-items-center justify-content-center bg-blue-600 rounded-full text-white me-2" style="width: 32px; height: 32px; background-color: #0b5ed7 !important;">
                            <i class="bi bi-person-fill"></i>
                        </div>
                        <div class="d-flex flex-column text-start">
                            <span class="text-white font-semibold" style="font-size: 0.8rem; font-weight: 600; line-height: 1.2;">{{ Auth::user()->name }}</span>
                            <span class="text-white/60 font-medium" style="font-size: 0.68rem; font-weight: 500; line-height: 1.2; margin-top: 1px;">{{ ucwords(str_replace('-', ' ', Auth::user()->role)) }}</span>
                        </div>
                        <i class="bi bi-chevron-down text-white/60 ms-2" style="font-size: 0.75rem;"></i>
                    </div>
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg mt-2 border-0" aria-labelledby="userProfileDropdown" style="border-radius: 12px; background: white; min-width: 160px; padding: 0.5rem 0;">
                        <li>
                            <form method="POST" action="{{ route('logout') }}" id="logout-form" class="m-0">
                                @csrf
                                <a class="dropdown-item d-flex align-items-center py-2 px-3 text-[#13314C] font-semibold" href="#" onclick="confirmLogout()" style="border-radius: 8px; font-size: 0.88rem; transition: background-color 0.2s;">
                                    <i class="bi bi-box-arrow-right me-2 text-danger fs-5"></i>Logout
                                </a>
                            </form>
                        </li>
                    </ul>
                </div>
            @endauth
        </div>
    </nav>

    <div class="d-flex">
        
        <div class="sidebar-slpa-logo shadow-lg">
            <!-- Scrollable Navigation Content -->
            <div class="sidebar-nav-content">
                <h5 class="sidebar-header">
                    Main Navigation
                </h5>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                            <i class="bi bi-grid-fill"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                </ul>
                
                <h5 class="sidebar-header">
                    Issue Permit
                </h5>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('permit.temporary') ? 'active' : '' }}" href="{{ route('permit.temporary') }}">
                            <i class="bi bi-file-earmark-text"></i>
                            <span>Temporary Permit</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('permit.monthly') ? 'active' : '' }}" href="{{ route('permit.monthly') }}">
                            <i class="bi bi-calendar3"></i>
                            <span>Monthly Permit</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('permit.vehicle') ? 'active' : '' }}" href="{{ route('permit.vehicle') }}">
                            <i class="bi bi-car-front"></i>
                            <span>Vehicle Permit</span>
                        </a>
                    </li>
                </ul>

                <h5 class="sidebar-header">
                    Reports
                </h5>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('permits.submitted') ? 'active' : '' }}" href="{{ route('permits.submitted') ?? '#' }}"> 
                            <i class="bi bi-list-check"></i>
                            <span>View All Permits</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('reports.user') ? 'active' : '' }}" href="{{ route('reports.user') ?? '#' }}">
                            <i class="bi bi-person"></i>
                            <span>User Report</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('reports.payment') ? 'active' : '' }}" href="{{ route('reports.payment') ?? '#' }}">
                            <i class="bi bi-bar-chart-line"></i>
                            <span>Revenue Report</span>
                        </a>
                    </li>
                </ul>
            </div>
            
            <!-- Fixed Lighthouse Graphic Footer -->
            <div class="sidebar-lighthouse-footer">
                <div class="fade-overlay"></div>
                <img src="{{ asset('images/galle_lighthouse_sidebar.png') }}" alt="Galle Lighthouse">
            </div>
        </div>

        <main class="flex-grow-1 p-4">
            @yield('content')
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    {{-- Logout Confirmation Modal --}}
    <div class="modal fade" id="logoutConfirmModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border: 3px solid #F6BA18; border-radius: 1rem;">
                <div class="modal-header" style="background-color: #13314C; border-bottom: 3px solid #F6BA18; border-radius: 0.85rem 0.85rem 0 0;">
                    <h5 class="modal-title text-white">
                        <i class="bi bi-box-arrow-right me-2"></i>Confirm Logout
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center py-4" style="background-color: #f8f9fa;">
                    <i class="bi bi-question-circle-fill" style="font-size: 3rem; color: #F6BA18;"></i>
                    <p class="mt-3 mb-0" style="font-size: 1.1rem; color: #13314C; font-weight: 500;">
                        Are you sure you want to logout?
                    </p>
                    <p class="text-muted mb-0">You will need to login again to access the system.</p>
                </div>
                <div class="modal-footer justify-content-center" style="border-top: 2px solid #F6BA18; background-color: #f8f9fa; border-radius: 0 0 0.85rem 0.85rem;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="padding: 0.5rem 1.5rem; border-radius: 0.5rem;">
                        <i class="bi bi-x-circle me-2"></i>Cancel
                    </button>
                    <button type="button" onclick="performLogout()" class="btn" style="background-color: #F6BA18; border-color: #F6BA18; color: #13314C; font-weight: 600; padding: 0.5rem 1.5rem; border-radius: 0.5rem;">
                        <i class="bi bi-box-arrow-right me-2"></i>Yes, Logout
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    {{-- Logout Confirmation Script --}}
    <script>
        function confirmLogout() {
            const modal = new bootstrap.Modal(document.getElementById('logoutConfirmModal'));
            modal.show();
        }
        
        function performLogout() {
            document.getElementById('logout-form').submit();
        }
    </script>
    
    {{-- Session Expiration Modal --}}
    <div class="modal fade" id="sessionExpiredModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border: 3px solid #F6BA18; border-radius: 1rem;">
                <div class="modal-header" style="background-color: #13314C; border-bottom: 3px solid #F6BA18; border-radius: 0.85rem 0.85rem 0 0;">
                    <h5 class="modal-title text-white">
                        <i class="bi bi-clock-history me-2"></i>Session Expired
                    </h5>
                </div>
                <div class="modal-body text-center py-4" style="background-color: #f8f9fa;">
                    <i class="bi bi-exclamation-triangle-fill" style="font-size: 3rem; color: #F6BA18;"></i>
                    <p class="mt-3 mb-0" style="font-size: 1.1rem; color: #13314C; font-weight: 500;">
                        Your session has expired due to inactivity.
                    </p>
                    <p class="text-muted mb-0">You will be redirected to the login page.</p>
                </div>
                <div class="modal-footer justify-content-center" style="border-top: 2px solid #F6BA18; background-color: #f8f9fa; border-radius: 0 0 0.85rem 0.85rem;">
                    <button type="button" class="btn" onclick="window.location.href='{{ route('login') }}'" 
                            style="background-color: #F6BA18; border-color: #F6BA18; color: #13314C; font-weight: 600; padding: 0.5rem 2rem; border-radius: 0.5rem;">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Go to Login
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    {{-- Session Expiration Handler --}}
    <script>
        (function() {
            // Auto-logout after session lifetime expires
            const sessionLifetime = {{ config('session.lifetime', 120) }} * 60 * 1000; // Convert to milliseconds
            
            // Set auto-logout timer
            setTimeout(function() {
                // Show styled modal instead of alert
                const modal = new bootstrap.Modal(document.getElementById('sessionExpiredModal'));
                modal.show();
                
                // Auto-redirect after 5 seconds if user doesn't click button
                setTimeout(function() {
                    window.location.href = '{{ route('login') }}';
                }, 5000);
            }, sessionLifetime);
            
            // Global AJAX error handler for session expiration
            if (typeof jQuery !== 'undefined') {
                $(document).ajaxError(function(event, jqxhr, settings, thrownError) {
                    if (jqxhr.status === 401 || jqxhr.status === 419) {
                        const modal = new bootstrap.Modal(document.getElementById('sessionExpiredModal'));
                        modal.show();
                        setTimeout(function() {
                            window.location.href = '{{ route('login') }}';
                        }, 3000);
                    }
                });
            }
            
            // Handle fetch API errors
            const originalFetch = window.fetch;
            window.fetch = function(...args) {
                return originalFetch.apply(this, args)
                    .then(response => {
                        if (response.status === 401 || response.status === 419) {
                            const modal = new bootstrap.Modal(document.getElementById('sessionExpiredModal'));
                            modal.show();
                            setTimeout(function() {
                                window.location.href = '{{ route('login') }}';
                            }, 3000);
                        }
                        return response;
                    });
            };
        })();
    </script>
    
    @stack('scripts')
</body>
</html>