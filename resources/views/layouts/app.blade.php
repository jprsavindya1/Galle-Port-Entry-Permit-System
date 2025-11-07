<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    <!-- CRITICAL: Preload logo FIRST with absolute highest priority -->
    <link rel="preload" href="{{ asset('images/Sri_Lanka_Ports_Authority_logo.png') }}" as="image" type="image/png" fetchpriority="high">
    <link rel="preload" href="{{ asset('css/navbar-critical.css') }}" as="style" fetchpriority="high">
    
    <!-- CRITICAL: Inline CSS to prevent ANY layout shift or flash - loads INSTANTLY -->
    <style>
        /* Prevent flash of unstyled navbar - applied immediately before any CSS loads */
        nav.navbar.navbar-slpa-logo { 
            background-color: #002B5C !important; 
            min-height: 70px !important; 
            border-bottom: 3px solid #FFC107 !important;
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
            background: #002B5C !important;
            flex-shrink: 0 !important;
            opacity: 1 !important;
            content-visibility: auto !important;
        }
        
        /* Placeholder while image loads */
        nav.navbar.navbar-slpa-logo .navbar-brand img[src*="Sri_Lanka_Ports_Authority_logo.png"] {
            background: #002B5C url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 55 55"><rect fill="%23002B5C" width="55" height="55"/></svg>') center/contain no-repeat !important;
        }
        nav.navbar.navbar-slpa-logo .brand-text { 
            color: #FFC107 !important;
            font-size: 1.5rem !important; 
            font-weight: 700 !important; 
            white-space: nowrap !important; 
            line-height: 1.2 !important;
        }
        /* Logout button - critical inline styles */
        nav.navbar.navbar-slpa-logo .btn-slpa-logout {
            background-color: #FFC107 !important;
            border-color: #FFC107 !important;
            color: #002B5C !important;
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

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            /* Colors extracted from the SLPA Logo */
            --slpa-logo-blue: #002B5C;      /* Deep Navy Blue (Primary) */
            --slpa-logo-gold: #FFC107;      /* Gold/Yellow (Accent) */
            --slpa-logo-darker-gold: #E5AD00; /* Darker Gold for hover */
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
            border-bottom: 3px solid var(--slpa-logo-gold) !important; 
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
            min-height: 100vh;
            background-color: var(--slpa-logo-blue); 
            box-shadow: 3px 0 10px rgba(0, 0, 0, 0.15);
            padding-top: 20px;
        }

        /* Sidebar Navigation Links */
        .sidebar-slpa-logo .nav-link {
            padding: 12px 20px;
            color: var(--slpa-logo-white) !important; 
            border-left: 5px solid transparent; 
            transition: all 0.2s ease-in-out;
            margin-bottom: 2px; /* Reduced spacing */
            font-weight: 500;
        }
        
        /* Sidebar Hover Effect */
        .sidebar-slpa-logo .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.2); 
            border-left-color: var(--slpa-logo-gold); 
            color: var(--slpa-logo-white) !important;
        }

        /* Sidebar Active Link - Gold Background */
        .sidebar-slpa-logo .nav-link.active {
            background-color: var(--slpa-logo-gold); 
            border-left-color: var(--slpa-logo-white); 
            color: var(--slpa-logo-blue) !important; 
            font-weight: 700;
            border-radius: 0 5px 5px 0;
        }

        /* New Navigation Section Headers */
        .sidebar-header {
            color: var(--slpa-logo-gold); /* Gold text for section headers */
            font-size: 1.1rem;
            margin-top: 15px;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2); /* Light separator */
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
              class="me-2 navbar-logo-img" 
              style="height: 55px !important; width: 55px !important; min-height: 55px !important; min-width: 55px !important; display: block !important; object-fit: contain !important; visibility: visible !important;"
              width="55"
              height="55"
              fetchpriority="high">
            <span class="brand-text">SLPA Permit System</span>
        </a>

        <div class="ms-auto pe-4">
            @auth
                <span class="user-name me-2">{{ Auth::user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-slpa-logout ms-2">Logout</button>
                </form>
            @endauth
        </div>
    </nav>

    <div class="d-flex">
        
        <div class="sidebar-slpa-logo shadow-lg">
            
            <h5 class="px-4 sidebar-header text-uppercase">
                Main Navigation
            </h5>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                        Dashboard
                    </a>
                </li>
            </ul>
            
            <h5 class="px-4 sidebar-header text-uppercase">
                Issue Permit
            </h5>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('permit.temporary') ? 'active' : '' }}" href="{{ route('permit.temporary') }}">
                        Temporary Permit
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('permit.monthly') ? 'active' : '' }}" href="{{ route('permit.monthly') }}">
                        Monthly Permit
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('permit.vehicle') ? 'active' : '' }}" href="{{ route('permit.vehicle') }}">
                        Vehicle Permit
                    </a>
                </li>
            </ul>

            <h5 class="px-4 sidebar-header text-uppercase">
                Reports
            </h5>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('permits.submitted') ? 'active' : '' }}" href="{{ route('permits.submitted') ?? '#' }}"> 
                        View All Permits
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('reports.user') ? 'active' : '' }}" href="{{ route('reports.user') ?? '#' }}">
                        User Report
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('reports.payment') ? 'active' : '' }}" href="{{ route('reports.payment') ?? '#' }}">
                        Revenue Report
                    </a>
                </li>
            </ul>
        </div>

        <main class="flex-grow-1 p-4">
            @yield('content')
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    {{-- Session Expiration Modal --}}
    <div class="modal fade" id="sessionExpiredModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border: 3px solid #FFC107; border-radius: 1rem;">
                <div class="modal-header" style="background-color: #002B5C; border-bottom: 3px solid #FFC107; border-radius: 0.85rem 0.85rem 0 0;">
                    <h5 class="modal-title text-white">
                        <i class="bi bi-clock-history me-2"></i>Session Expired
                    </h5>
                </div>
                <div class="modal-body text-center py-4" style="background-color: #f8f9fa;">
                    <i class="bi bi-exclamation-triangle-fill" style="font-size: 3rem; color: #FFC107;"></i>
                    <p class="mt-3 mb-0" style="font-size: 1.1rem; color: #002B5C; font-weight: 500;">
                        Your session has expired due to inactivity.
                    </p>
                    <p class="text-muted mb-0">You will be redirected to the login page.</p>
                </div>
                <div class="modal-footer justify-content-center" style="border-top: 2px solid #FFC107; background-color: #f8f9fa; border-radius: 0 0 0.85rem 0.85rem;">
                    <button type="button" class="btn" onclick="window.location.href='{{ route('login') }}'" 
                            style="background-color: #FFC107; border-color: #FFC107; color: #002B5C; font-weight: 600; padding: 0.5rem 2rem; border-radius: 0.5rem;">
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
            
            // Handle fetch API errors
            const originalFetch = window.fetch;
            window.fetch = function(...args) {
                return originalFetch.apply(this, args)
                    .then(response => {
                        if (response.status === 401 || response.status === 419) {
                            alert('Your session has expired. Please login again.');
                            window.location.href = '{{ route('login') }}';
                        }
                        return response;
                    });
            };
        })();
    </script>
    
    @stack('scripts')
</body>
</html>