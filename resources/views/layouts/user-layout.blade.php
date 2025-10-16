<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes, viewport-fit=cover">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <title>@yield('title', 'User Dashboard')</title>
    
    <!-- Preload critical images to prevent FOUC (Flash of Unstyled Content) -->
    <link rel="preload" as="image" href="{{ asset('hmblsc-logo.jpg') }}" fetchpriority="high">
    <link rel="preload" as="image" href="{{ asset('jrbgh-logo.png') }}" fetchpriority="high">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome (needed for many icons used across the app) -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Load Quicksand (body) and Merriweather (headings) from Google Fonts -->
    <link
        href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700&family=Merriweather:wght@400;700;900&display=swap"
        rel="stylesheet">
    <!-- Global Typography System -->
    <link href="{{ asset('css/typography.css') }}" rel="stylesheet">
    <!-- UI Components & Utilities -->
    <link href="{{ asset('css/ui-components.css') }}" rel="stylesheet">
    <!-- Responsive & Adaptive Styles -->
    <link href="{{ asset('css/responsive.css') }}?v={{ time() }}" rel="stylesheet">
    <!-- Global Tab Styles - Horizontal Alignment -->
    <link href="{{ asset('css/global-tabs.css') }}" rel="stylesheet">
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            /* Typography - Correct assignments */
            --heading-font: 'Merriweather', Georgia, 'Times New Roman', serif;
            --body-font: 'Quicksand', system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            --line-height: 1.6;
            --line-height-relaxed: 1.6;
            --line-height-normal: 1.5;
            --table-header-bg: #f8fafc;
            --table-border: #e9ecef;
            --primary-color: #0d6efd;
            --success-color: #198754;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
        }

        body {
            margin: 0;
            font-family: var(--body-font);
            color: #222;
            line-height: var(--line-height-relaxed);
        }

        /* Headings use Merriweather (serif) */
        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            font-family: var(--heading-font);
            font-weight: 700;
            margin-top: 0;
            line-height: var(--line-height-normal);
        }

        /* Reusable table styles */
        .table-standard thead th {
            font-family: var(--heading-font);
            font-weight: 700;
            background: var(--table-header-bg);
            border-bottom: 2px solid var(--table-border);
            padding: 0.65rem 0.6rem;
            text-align: left;
        }

        .table-standard tbody td {
            font-family: var(--body-font);
            font-weight: 400;
            padding: 0.6rem 0.6rem;
        }

        .table-standard tbody tr:nth-child(odd) {
            background: #ffffff;
        }

        .table-standard tbody tr:nth-child(even) {
            background: #fbfcfd;
        }

        .table-standard tbody tr:hover {
            background: #eef6ff;
        }

        .badge-status {
            font-family: var(--body-font);
            font-weight: 500;
            padding: 0.35rem 0.55rem;
            border-radius: 0.375rem;
        }

        /* Shared modal */
        .shared-modal .modal-header {
            font-family: var(--heading-font);
            font-weight: 700;
            border-bottom: 1px solid var(--table-border);
        }

        .shared-modal .modal-title {
            font-family: var(--heading-font);
        }

        .shared-modal .modal-body {
            font-family: var(--body-font);
        }

        /* Global Modal Responsive Styles */
        .modal-dialog {
            max-width: 90%;
            margin: 1.75rem auto;
        }

        .modal-dialog-centered {
            min-height: calc(100% - 3.5rem);
        }

        /* Fix modal z-index to be above Facebook widgets and other elements */
        .modal { 
            z-index: 12000 !important; 
        }
        
        .modal-backdrop { 
            z-index: 11999 !important; 
        }

        .modal-content {
            border-radius: 12px;
            border: none;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        }

        .modal-header {
            padding: 1rem 1.5rem;
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
        }

        .modal-body {
            padding: 1.5rem;
            max-height: calc(100vh - 200px);
            overflow-y: auto;
        }

        .modal-footer {
            padding: 1rem 1.5rem;
            border-bottom-left-radius: 12px;
            border-bottom-right-radius: 12px;
        }

        /* Modal sizes - responsive */
        .modal-sm {
            max-width: 300px;
        }

        .modal-lg {
            max-width: 800px;
        }

        .modal-xl {
            max-width: 1140px;
        }

        /* Tablet and below */
        @media (max-width: 991px) {
            .modal-dialog {
                max-width: 95%;
                margin: 1rem auto;
            }

            .modal-lg,
            .modal-xl {
                max-width: 95%;
            }

            .modal-body {
                max-height: calc(100vh - 180px);
                padding: 1.25rem;
            }

            .modal-header,
            .modal-footer {
                padding: 0.875rem 1.25rem;
            }
        }

        /* Mobile devices */
        @media (max-width: 576px) {
            .modal-dialog {
                max-width: 100%;
                margin: 0.5rem;
            }

            .modal-dialog-centered {
                min-height: calc(100% - 1rem);
            }

            .modal-sm,
            .modal-lg,
            .modal-xl {
                max-width: 100%;
            }

            .modal-content {
                border-radius: 8px;
            }

            .modal-header {
                padding: 0.75rem 1rem;
                border-top-left-radius: 8px;
                border-top-right-radius: 8px;
            }

            .modal-title {
                font-size: 1.1rem;
            }

            .modal-body {
                padding: 1rem;
                max-height: calc(100vh - 150px);
                font-size: 0.9rem;
            }

            .modal-footer {
                padding: 0.75rem 1rem;
                border-bottom-left-radius: 8px;
                border-bottom-right-radius: 8px;
                flex-wrap: wrap;
                gap: 0.5rem;
            }

            .modal-footer .btn {
                flex: 1 1 auto;
                min-width: 100px;
                font-size: 0.875rem;
            }

            /* Stack buttons vertically on very small screens */
            .modal-footer.stack-mobile .btn {
                flex: 1 1 100%;
                width: 100%;
            }
        }

        /* Global: Action Buttons - Keep text with icons on all devices */
        @media (max-width: 768px) {
            /* Ensure buttons are responsive and compact but keep text visible */
            .table .btn,
            .action-btn,
            .btn-sm,
            .btn-action {
                font-size: 0.75rem;
                padding: 0.375rem 0.5rem;
                white-space: nowrap;
            }

            /* Keep icons visible and properly sized */
            .btn i,
            .btn .fas,
            .btn .far,
            .btn .fab {
                font-size: 0.875rem;
                margin-right: 0.25rem;
            }

            /* Ensure buttons wrap text if needed */
            .btn {
                line-height: 1.2;
            }

            /* Modal buttons - always show text with icons */
            .modal-footer .btn,
            .modal-body .btn {
                font-size: 0.875rem;
                padding: 0.5rem 0.75rem;
                white-space: normal;
                min-width: auto;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 0.5rem;
            }

            /* Ensure modal button text is always visible */
            .modal-footer .btn i,
            .modal-body .btn i {
                margin: 0;
                font-size: 0.875rem;
            }

            /* Modal button specific sizing */
            .modal-footer .btn {
                flex: 1 1 auto;
                min-width: 100px;
            }
        }

        /* Extra small devices */
        @media (max-width: 375px) {
            .modal-dialog {
                margin: 0.25rem;
            }

            .modal-body {
                padding: 0.875rem;
                font-size: 0.85rem;
            }

            .modal-title {
                font-size: 1rem;
            }
        }

        /* Sidebar - pink theme (matches admin) */
        .sidebar {
            background: linear-gradient(180deg, #ffb3dd 0%, #e851a9 55%, #c22e8f 100%);
            color: #2b2b2b;
            padding: 20px;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            border-right: 1px solid rgba(0,0,0,0.04);
        }
        
        /* Smooth fade-in animation for sidebar logo */
        @keyframes fadeInSidebarLogo {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        .sidebar h3 {
            margin-top: 0;
            margin-bottom: 20px;
            font-size: 1.2rem;

        /* Sidebar badges */
        .sidebar .badge {
            background: rgba(255,255,255,0.95);
            color: #7a083e; /* darker compliment for visibility */
            font-weight: 700;
            border-radius: 999px;
            padding: 0.25rem 0.5rem;
            box-shadow: 0 1px 2px rgba(0,0,0,0.02);
        }

        .sidebar .icon {
            color: #a50e72; /* adjusted to harmonize with #e851a9 */
            min-width: 18px;
            text-align: center;
        }
        }

        .sidebar a {
            color: #2b2b2b;
            text-decoration: none;
            padding: 10px 12px;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: background 0.18s, color 0.18s;
            border-radius: 8px;
            width: 100%;
        }

        .sidebar a:hover {
            background-color: rgba(0,0,0,0.04);
            color: #111;
        }

        .sidebar a.active {
            background: linear-gradient(90deg, rgba(255,255,255,0.18), rgba(255,255,255,0.03));
            color: #111;
            box-shadow: inset 0 0 0 2px rgba(255,255,255,0.03);
        }

        .sidebar hr {
            border: 0.5px solid #7f8c8d;
            margin: 20px 0;
        }

        .logout {
            margin-top: auto;
            color: #e74c3c;
        }

        /* Content - responsive margins handled by responsive.css */
        .content {
            flex: 1;
            padding: 12px; /* reduced for compact layout */
            min-height: 100vh;
            box-sizing: border-box;
        }

        @yield('styles')
    </style>
</head>

<body>
    <!-- Fixed user header -->
    @php
// Small route->title map; views can override with @section('pageTitle', 'My Title')
$routeName = request()->route() ? request()->route()->getName() : null;
$titles = [
    'user.dashboard' => 'Dashboard',
    'user.health-screening' => 'Health Screening',
    'user.donate' => 'Donate',
    'user.breastmilk-request' => 'Breastmilk Request',
    'user.my-requests' => 'My Requests',
    'user.history' => 'My Donation History',
    'user.pending' => 'Pending Donation',
];
$defaultTitle = $titles[$routeName] ?? 'User';
    @endphp
@include('partials.header')

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Bootstrap Icons CDN -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

        <div style="margin-bottom: 16px; text-align: center; display: flex; flex-direction: column; align-items: center;">
            <!-- HMBLSC Logo -->
            <img src="{{ asset('hmblsc-logo.jpg') }}" alt="HMBLSC Logo" width="95" height="95" loading="eager" style="width: 95px; height: 95px; object-fit: cover; margin-bottom: 12px; border-radius: 50%; border: 3px solid rgba(255,255,255,0.7); display: block; opacity: 0; animation: fadeInSidebarLogo 0.4s ease-in 0.1s forwards;">
            <!-- User Name -->
            <h3 style="font-size: 1rem; font-weight: 600; margin-bottom: 0; color: #2b2b2b;">{{ session('account_name', 'User') }}</h3>
        </div>
        <div style="display: flex; flex-direction: column; gap: 6px; margin-top: 12px;">

                        <a href="{{ route('user.dashboard') }}" class="{{ request()->routeIs('user.dashboard') ? 'active' : '' }}">
                <i class="bi bi-house-door me-2 icon"></i> <span>Home</span>
            </a>
            <a href="{{ route('user.health-screening') }}" class="{{ request()->routeIs('user.health-screening') ? 'active' : '' }}">
                <i class="bi bi-clipboard-pulse me-2 icon"></i> <span>Health Screening</span>
            </a>

                           <a href="{{ route('user.donate') }}" class="{{ request()->routeIs('user.donate') ? 'active' : '' }}">
                <i class="bi bi-droplet-half me-2 icon"></i> <span>Donate</span>
            </a>
            <a href="{{ route('user.pending') }}" class="{{ request()->routeIs('user.pending') ? 'active' : '' }}">
                <i class="bi bi-hourglass-split me-2 icon"></i> <span>Pending Donation</span>
            </a>
            <a href="{{ route('user.history') }}" class="{{ request()->routeIs('user.history') ? 'active' : '' }}">
                <i class="bi bi-clock-history me-2 icon"></i> <span>My Donation History</span>
            </a>
            <a href="{{ route('user.breastmilk-request') }}" class="{{ request()->routeIs('user.breastmilk-request') ? 'active' : '' }}">
                <i class="bi bi-envelope-paper me-2 icon"></i> <span>Breastmilk Request</span>
            </a>

                           <a href="{{ route('user.my-requests') }}" class="{{ request()->routeIs('user.my-requests') ? 'active' : '' }}">
                <i class="bi bi-list-check me-2 icon"></i> <span>My Requests</span>
            </a>

                           <a href="{{ route('user.settings') }}" class="{{ request()->routeIs('user.settings') ? 'active' : '' }}">
                <i class="bi bi-gear me-2 icon"></i> <span>Settings</span>
            </a>
        </div>
        <hr>
    </div>

        <!-- Main content -->
        <div class="content">
    @yield('content')
    </div>

    <!-- Mobile Menu Toggle Button -->
    <button class="menu-toggle" id="mobileMenuToggle" aria-label="Toggle Menu">
        <i class="bi bi-list" style="font-size: 1.5rem;"></i>
    </button>

    <!-- Sidebar Overlay for Mobile -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Mobile menu toggle functionality
        document.addEventListener('DOMContentLoaded', function() {
            const menuToggle = document.getElementById('mobileMenuToggle');
            const sidebar = document.querySelector('.sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            
            if (menuToggle && sidebar && overlay) {
                menuToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('active');
                    overlay.classList.toggle('active');
                });
                
                overlay.addEventListener('click', function() {
                    sidebar.classList.remove('active');
                    overlay.classList.remove('active');
                });
                
                // Close sidebar when a link is clicked (mobile only)
                const sidebarLinks = sidebar.querySelectorAll('a');
                sidebarLinks.forEach(link => {
                    link.addEventListener('click', function() {
                        if (window.innerWidth < 768) {
                            sidebar.classList.remove('active');
                            overlay.classList.remove('active');
                        }
                    });
                });
            }
        });
    </script>
    <!-- Responsive Tables JavaScript -->
    <script src="{{ asset('js/responsive-tables.js') }}?v={{ time() }}"></script>
    @yield('scripts')
    {{-- logout script now provided by header partial --}}
</body>

</html>
