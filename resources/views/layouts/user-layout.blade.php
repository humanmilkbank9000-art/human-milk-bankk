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
    <!-- Load Quicksand (body) from Google Fonts -->
    <link
        href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    <!-- Global Typography System -->
    <link href="{{ asset('css/typography.css') }}" rel="stylesheet">
    <!-- UI Components & Utilities -->
    <link href="{{ asset('css/ui-components.css') }}" rel="stylesheet">
    <!-- Responsive & Adaptive Styles -->
    <link href="{{ asset('css/responsive.css') }}?v={{ time() }}" rel="stylesheet">
    <!-- Global Tab Styles - Horizontal Alignment -->
    <link href="{{ asset('css/global-tabs.css') }}" rel="stylesheet">
    <!-- Pagination Styles -->
    <link href="{{ asset('css/pagination.css') }}" rel="stylesheet">
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            /* Typography - Correct assignments */
            --heading-font: 'Segoe UI', 'Helvetica Neue', Arial, sans-serif;
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
            /* Dashboard palette */
            --blue-400: #3b82f6;
            --blue-600: #2563eb;
            --pink-400: #ff6fa8;
            --pink-600: #e83e8c;
            --green-400: #34d399;
            --green-600: #16a34a;
        }

        body {
            margin: 0;
            font-family: var(--body-font);
            color: #222;
            line-height: var(--line-height-relaxed);
        }

    /* Headings use Segoe UI (sans-serif) */
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

        /* User-scoped admin-style pink theme for card headers and tables */
        /* Card headers: gradient like admin */
        .content .card-header {
            background: linear-gradient(180deg, #ff93c1 0%, #ff7fb3 100%) !important;
            color: #ffffff !important;
            border-bottom: 1px solid rgba(0,0,0,0.06) !important;
        }
        /* Ensure titles inside card headers are white */
        .content .card-header h1,
        .content .card-header h2,
        .content .card-header h3,
        .content .card-header h4,
        .content .card-header h5,
        .content .card-header h6,
        .content .card-header .card-title {
            color: #ffffff !important;
        }
        /* Ensure utility bg classes don't override the gradient */
        .content .card-header.bg-primary,
        .content .card-header.bg-success,
        .content .card-header.bg-warning,
        .content .card-header.bg-secondary,
        .content .card-header.bg-info {
            background: linear-gradient(180deg, #ff93c1 0%, #ff7fb3 100%) !important;
            color: #ffffff !important;
        }

        /* Table header: pink gradient with white titles (admin-like) */
        .content .table-standard thead th {
            background: linear-gradient(180deg, #ff93c1 0%, #ff7fb3 100%) !important;
            color: #ffffff !important;
            font-weight: 700;
            border-bottom: 0 !important;
        }

        .content .table-standard {
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
        }

        .content .table-standard tbody td {
            padding: 0.7rem 0.8rem;
        }

        /* Keep subtle pink zebra and hover inside user tables */
        .content .table-standard tbody tr:nth-child(even) {
            background: rgba(255, 223, 234, 0.40) !important; /* subtle pink */
        }

        .content .table-standard tbody tr:hover {
            background: rgba(255, 207, 224, 0.90) !important;
        }

        /* Modals on user pages: match admin pink gradient header */
        .modal .modal-header {
            background: linear-gradient(180deg, #ff93c1 0%, #ff7fb3 100%) !important;
            color: #ffffff !important;
            border-bottom: 1px solid rgba(0,0,0,0.06) !important;
        }
        .modal .modal-header .btn-close { filter: invert(1) brightness(1.2) !important; }

        /* Optional: apply pink accent to small badges inside user tables */
        .content .badge-status.accepted { background: rgba(25,135,84,0.12); color: #198754; }


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

        /* Accessible focus ring utility */
        .focus-ring,
        .focus-ring:focus,
        .focus-ring:focus-visible {
            outline: 3px solid var(--blue-400);
            outline-offset: 2px;
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

        /* Sidebar - theme updated: pink gradient, black text, active pill + left indicator
           Keep original size/positioning behavior (no fixed width/height overrides) */
        .sidebar {
            background: linear-gradient(180deg, #ffd9e8 0%, #ff93c1 50%, #ff6fa6 100%);
            color: #222222;
            padding: 20px;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
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
            margin-bottom: 18px;
            font-size: 1rem;
            color: #000; /* ensure user name is black */
            font-weight: 700;
            text-align: center;
            /* Use system font stack for a native/formal appearance */
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            letter-spacing: 0.2px;
            text-transform: none;
        }
        
            .sidebar-title {
                font-size: 0.95rem;
                color: #000; /* ensure sidebar title (name) is black */
                font-weight: 700;
                margin: 0;
                padding: 0;
                text-align: center;
                /* Use system font stack for the sidebar title */
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
                letter-spacing: 0.15px;
            }

        .sidebar a {
            color: #222222;
            text-decoration: none;
            padding: 10px 12px;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: background 0.18s, color 0.12s;
            border-radius: 10px;
            position: relative;
            width: 100%;
        }
        
            .sidebar a span {
                display: inline-block;
                max-width: 150px;
                white-space: normal;
                color: inherit;
                font-weight: 600;
            }

            .dynamic-sidebar-badge {
                position: absolute;
                right: 18px;
                top: 50%;
                transform: translateY(-50%);
                width: 18px;
                height: 18px;
                line-height: 18px;
                font-size: 0.7rem;
                text-align: center;
                padding: 0;
                border-radius: 50%;
                background: #d63031;
                color: #fff;
                display: inline-flex;
                align-items: center;
                justify-content: center;
            }

        .sidebar .icon { color: #222222; font-size: 1.05rem; }

        .sidebar a:hover {
            background: rgba(255,255,255,0.6);
        }

        .sidebar a.active {
            background: #ffffff;
            color: #222222;
            box-shadow: 0 8px 20px rgba(0,0,0,0.06);
        }

        .sidebar a.active .icon,
        .sidebar a.active span { color: #222222 !important; }

        .sidebar a.active::before {
            content: '';
            position: absolute;
            left: 6px;
            top: 50%;
            transform: translateY(-50%);
            width: 6px;
            height: 56%;
            background: #ff3478;
            border-radius: 4px;
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
            <img src="{{ asset('hmblsc-logo.jpg') }}" alt="HMBLSC Logo" width="95" height="95" loading="eager" style="width: 95px; height: 95px; object-fit: cover; margin-bottom: 12px; border-radius: 50%; border: 3px solid #ecf0f1; display: block; opacity: 0; animation: fadeInSidebarLogo 0.4s ease-in 0.1s forwards;">
            <!-- User Name -->
            <h3 class="sidebar-title">{{ session('account_name', 'User') }}</h3>
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
    <!-- Global date utils: parse YYYY-MM-DD into a local Date to avoid TZ shifts -->
    <script>
        function parseYMD(s) {
            if (!s || typeof s !== 'string') return new Date(NaN);
            const parts = s.split('-');
            if (parts.length !== 3) return new Date(s);
            const y = parseInt(parts[0], 10);
            const m = parseInt(parts[1], 10) - 1;
            const d = parseInt(parts[2], 10);
            return new Date(y, m, d);
        }
    </script>
    @yield('scripts')
    {{-- logout script now provided by header partial --}}
</body>

</html>
