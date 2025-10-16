<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes, viewport-fit=cover">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <title>@yield('title', 'Admin Dashboard')</title>
    
    <!-- Preload critical images to prevent FOUC (Flash of Unstyled Content) -->
    <link rel="preload" as="image" href="{{ asset('hmblsc-logo.jpg') }}" fetchpriority="high">
    <link rel="preload" as="image" href="{{ asset('jrbgh-logo.png') }}" fetchpriority="high">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>

    <!-- Leaflet.js for OpenStreetMap -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
        crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
        crossorigin=""></script>

    <!-- Cally calendar component -->
    <script type="module" src="https://unpkg.com/cally"></script>

    <!-- Load Quicksand (body) and Merriweather (headings) from Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700&family=Merriweather:wght@400;700;900&display=swap" rel="stylesheet">
    <!-- Global Typography System -->
    <link href="{{ asset('css/typography.css') }}" rel="stylesheet">
    <!-- UI Components & Utilities -->
    <link href="{{ asset('css/ui-components.css') }}" rel="stylesheet">
    <!-- Responsive & Adaptive Styles -->
    <link href="{{ asset('css/responsive.css') }}?v={{ time() }}" rel="stylesheet">
    <!-- Global Tab Styles - Horizontal Alignment -->
    <link href="{{ asset('css/global-tabs.css') }}" rel="stylesheet">
    <link href="{{ asset('css/sidebar-theme.css') }}?v={{ time() }}" rel="stylesheet">
    <style>
        /* Design system typography */
        :root{
            /* Typography - Correct assignments */
            --heading-font: 'Merriweather', Georgia, 'Times New Roman', serif;
            --body-font: 'Quicksand', system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            --line-height: 1.6;
            --line-height-relaxed: 1.6;
            --line-height-normal: 1.5;
            --muted: #6c757d;
            --table-header-bg: #f8fafc;
            --table-border: #e9ecef;
            --primary-color: #0d6efd;
            --success-color: #198754;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
        }

        body {
            margin: 0;
            /* Use Quicksand for body text */
            font-family: var(--body-font);
            line-height: var(--line-height-relaxed);
            padding-left: 260px;
            color: #212529;
            font-weight: 400;
        }

        /* Headings use Merriweather (serif) */
        h1, h2, h3, h4, h5, h6 {
            font-family: var(--heading-font);
            font-weight: 700;
            line-height: var(--line-height-normal);
        }

        /* Sidebar - pink theme (matches provided design) */
        .sidebar {
            background: linear-gradient(180deg, #ffe6f3 0%, #ffb3dd 14%, #e851a9 60%, #c22e8f 100%);
            color: #2b2b2b;
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
            margin-bottom: 20px;
            font-size: 1.2rem;
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
        }

        .sidebar a:hover {
            background: rgba(0,0,0,0.04);
            color: #111;
        }

        .sidebar a.active {
            background: rgba(232,81,169,0.12);
            color: #111;
            box-shadow: 0 6px 18px rgba(0,0,0,0.06);
            border-radius: 12px;
            padding-left: 18px;
        }

        .sidebar hr {
            border: 0.5px solid #7f8c8d;
            margin: 20px 0;
        }

        .logout {
            margin-top: auto;
            color: #e74c3c;
        }
        }
        /* Ensure logout button style always wins over local .btn styles */
        .btn.logout-btn {
            min-width: 120px !important;
            height: 40px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 6px !important;
            padding: 6px 16px !important;
            border-radius: 6px !important;
            font-weight: 600 !important;
            font-size: 1rem !important;
        }

        /* Content - responsive margins handled by responsive.css */
        body,
        .content {
            min-width: 0;
        }

        .content {
            flex: 1;
            padding: 12px; /* reduced from 20px for compactness */
            min-height: 100vh;
            box-sizing: border-box;
            overflow-x: auto;
            overflow-y: visible;
        }

        /* Additional admin-specific styles */
        .slot-btn.active {
            background-color: #0d6efd;
            color: white;
        }

        th,
        td {
            font-size: 12px;
        }

        /* Use Quicksand for headings inside admin pages */
        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            font-family: var(--heading-font);
            font-weight: 600;
            line-height: 1.4;
            margin: 0 0 0.5rem 0;
        }

        /* Standardized table styles for admin UI */
        .table-standard thead th {
            font-family: var(--heading-font);
            font-weight: 600;
            background: var(--table-header-bg);
            border-bottom: 2px solid var(--table-border);
            color: #212529;
            padding: 0.65rem 0.6rem;
            text-align: left; /* default left alignment for headers */
            vertical-align: middle;
        }

        .table-standard tbody td {
            font-family: var(--body-font);
            font-weight: 400;
            padding: 0.6rem 0.6rem;
            vertical-align: middle;
            color: #2b2b2b;
        }

        /* Alignment helpers */
        .table-standard td.text-left { text-align: left; }
        .table-standard td.text-center { text-align: center; }
        .table-standard td.text-right { text-align: right; }

        /* Status badge standards */
        .badge-status { font-family: var(--body-font); font-weight:500; padding: 0.35rem 0.55rem; border-radius: 0.375rem; }
        .badge-status.pending { background:#ffc107; color:#000; }
        .badge-status.accepted { background:var(--success-color); color:#fff; }
        .badge-status.completed { background:#0dcaf0; color:#fff; }
        .badge-status.danger { background:var(--danger-color); color:#fff; }

        /* Action button standard */
        .action-btn { min-width:44px; min-height:44px; display:inline-flex; align-items:center; justify-content:center; gap:6px; padding:6px 10px; border-radius:6px; }

        /* Zebra striping & hover */
        .table-standard tbody tr:nth-child(odd) { background: #ffffff; }
        .table-standard tbody tr:nth-child(even) { background: #fbfcfd; }
        .table-standard tbody tr:hover { background: #eef6ff; }

        /* Modal shared styles */
        .shared-modal .modal-header { font-family: var(--heading-font); font-weight:700; background: #fff; border-bottom:1px solid var(--table-border); }
        .shared-modal .modal-title { font-family: var(--heading-font); font-weight:700; }
        .shared-modal .modal-body { font-family: var(--body-font); font-weight:400; line-height:1.6; }
        .shared-modal .modal-footer { background: #fff; border-top:1px solid var(--table-border); }

        /* Ensure modal backdrop and z-index override */
        .modal-backdrop.show { opacity: 0.55; }
        .modal { z-index: 12000; }

        /* Global Modal Responsive Styles */
        .modal-dialog {
            max-width: 90%;
            margin: 1.75rem auto;
        }

        .modal-dialog-centered {
            min-height: calc(100% - 3.5rem);
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

        /* Accessibility helpers */
        .sr-only { position: absolute !important; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0,0,0,0); border: 0; }

        /* Keep tables responsive but avoid forcing horizontal scroll unless necessary */
        table { width: 100%; table-layout: auto; }

        /* Ensure Bootstrap dropdowns and popovers show above the fixed header/sidebar */
        .dropdown-menu,
        .popover,
        .tooltip {
            z-index: 11000 !important;
        }

        .screening-card {
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 20px;
            background: #f9f9f9;
        }

        .screening-card h5 {
            margin-bottom: 15px;
        }

        .field-label {
            font-weight: bold;
        }

        .question {
            font-style: italic;
            color: #555;
            font-size: 0.9rem;
        }

        .translation {
            font-size: 0.85rem;
            color: #777;
            margin-bottom: 3px;
        }

        @yield('styles')
    </style>
</head>

<body>
    <!-- Fixed admin header -->
    @php
// Small route->title map; views can override with @section('pageTitle', 'My Title')
$routeName = request()->route() ? request()->route()->getName() : null;
$titles = [
    'admin.dashboard' => 'Dashboard',
    'admin.health-screening' => 'Health Screening',
    'admin.donation' => 'Breastmilk Donation',
    'admin.request' => 'Breastmilk Request',
    'admin.inventory' => 'Inventory',
    'admin.reports' => 'Monthly Reports',
    'admin.settings' => 'Settings',
];
$defaultTitle = $titles[$routeName] ?? 'Admin';
    @endphp
    @include('partials.header')

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Bootstrap Icons CDN -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

        <div style="margin-bottom: 16px; text-align: center; display: flex; flex-direction: column; align-items: center;">
            <!-- HMBLSC Logo -->
            <img src="{{ asset('hmblsc-logo.jpg') }}" alt="HMBLSC Logo" width="95" height="95" loading="eager" style="width: 95px; height: 95px; object-fit: cover; margin-bottom: 12px; border-radius: 50%; border: 3px solid #ecf0f1; display: block; opacity: 0; animation: fadeInSidebarLogo 0.4s ease-in 0.1s forwards;">
            <!-- Admin Name -->
            <h3 style="font-size: 1rem; font-weight: 600; margin-bottom: 0; color: #ecf0f1;">{{ session('account_name', 'Admin') }}</h3>
        </div>
        <div style="display: flex; flex-direction: column; gap: 6px; margin-top: 12px;">
                <a href="{{ route('admin.dashboard') }}"
                    class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-house-door me-2 icon"></i> <span>Dashboard</span>
                </a>
            <a href="{{ route('admin.health-screening') }}"
                class="{{ request()->routeIs('admin.health-screening') ? 'active' : '' }}"
                style="position:relative; padding-right:1.6rem;">
                <i class="bi bi-clipboard-pulse me-2 icon"></i> <span>Health Screening</span>
                {{-- badge created dynamically by JS when count > 0 to avoid empty red circle --}}
            </a>
            <a href="{{ route('admin.donation') }}"
                class="{{ request()->routeIs('admin.donation') ? 'active' : '' }}"
                style="position:relative; padding-right:1.6rem;">
                <i class="bi bi-droplet-half me-2 icon"></i> <span>Breastmilk Donation</span>
                {{-- badge created dynamically by JS when count > 0 to avoid empty red circle --}}
            </a>
            <a href="{{ route('admin.request') }}"
                class="{{ request()->routeIs('admin.request') ? 'active' : '' }}"
                style="position:relative; padding-right:1.6rem;">
                <i class="bi bi-envelope-paper me-2 icon"></i> <span>Breastmilk Request</span>
                {{-- badge created dynamically by JS when count > 0 to avoid empty red circle --}}
            </a>

            <a href="{{ route('admin.inventory') }}"
                class="{{ request()->routeIs('admin.inventory') ? 'active' : '' }}">
                <i class="bi bi-box-seam me-2 icon"></i> <span>Inventory</span>
            </a>
            <a href="{{ route('admin.reports') }}" class="{{ request()->routeIs('admin.reports') ? 'active' : '' }}">
                <i class="bi bi-bar-chart-line me-2 icon"></i> <span>Monthly Reports</span>
            </a>
            <a href="{{ route('admin.settings') }}"
                class="{{ request()->routeIs('admin.settings') ? 'active' : '' }}">
                <i class="bi bi-gear me-2 icon"></i> <span>Settings</span>
            </a>
        </div>

        <hr>
    </div>

    <!-- Main content -->
    <div class="content">
        <div class="container-fluid" style="padding-top:8px;">
            @yield('content')
        </div>
    </div>

    <!-- Mobile Menu Toggle Button -->
    <button class="menu-toggle" id="mobileMenuToggle" aria-label="Toggle Menu">
        <i class="bi bi-list" style="font-size: 1.5rem;"></i>
    </button>

    <!-- Sidebar Overlay for Mobile -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        (function(){
            // Only run for admins - blade provides $accountRole in some layouts; fall back to session
            const accountRole = typeof window.accountRole !== 'undefined'
                ? window.accountRole
                : @json(session('account_role', ''));

            if (accountRole !== 'admin') return;

            const pages = [
                { url: @json(route('admin.health-screening')), selector: 'span.badge.bg-warning.text-dark.ms-1', target: 'sidebar-health-badge' },
                { url: @json(route('admin.donation')), selector: 'span.badge.bg-warning.text-dark', target: 'sidebar-donation-badge' },
                { url: @json(route('admin.request')), selector: 'span.badge.bg-warning', target: 'sidebar-request-badge' }
            ];

            function parseCount(html, selector){
                try{
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    if(selector){
                        const el = doc.querySelector(selector);
                        if(el){ const m = el.textContent.match(/(\d+)/); if(m) return parseInt(m[1],10); }
                    }
                    const candidates = ['span.pending-count-badge','span.badge.bg-warning.text-dark','span.badge.bg-warning','span.badge.ms-1','span.badge'];
                    for(const s of candidates){ const e = doc.querySelector(s); if(e){ const m = e.textContent.match(/(\d+)/); if(m) return parseInt(m[1],10); } }
                    const any = Array.from(doc.querySelectorAll('span,div')).find(n => /\d+/.test(n.textContent));
                    if(any){ const m = any.textContent.match(/(\d+)/); if(m) return parseInt(m[1],10); }
                }catch(e){ console && console.error && console.error('parse error', e); }
                return 0;
            }

            async function updateBadges(){
                for(const p of pages){
                    try{
                        const res = await fetch(p.url, { credentials: 'same-origin' });
                        if(!res.ok) continue;
                        const html = await res.text();
                        const count = parseCount(html, p.selector);
                        // find the sidebar link for this target
                        const link = document.querySelector('[href]');
                        // more robust: look for element by route URL
                        const sidebarLink = Array.from(document.querySelectorAll('.sidebar a')).find(a => a.getAttribute('href') === p.url || a.href === p.url || a.getAttribute('href') === p.url.replace(window.location.origin, ''));
                        if(!sidebarLink) continue;
                        let badge = sidebarLink.querySelector('.dynamic-sidebar-badge');
                        if(count > 0){
                            if(!badge){
                                badge = document.createElement('span');
                                badge.className = 'badge bg-danger text-white dynamic-sidebar-badge';
                                badge.setAttribute('aria-hidden', 'true');
                                // style to avoid shifting text
                                badge.style.fontSize = '0.65rem';
                                badge.style.position = 'absolute';
                                badge.style.right = '10px';
                                badge.style.top = '50%';
                                badge.style.transform = 'translateY(-50%)';
                                badge.style.minWidth = '1.4rem';
                                badge.style.height = '1.4rem';
                                badge.style.lineHeight = '1.1rem';
                                badge.style.textAlign = 'center';
                                badge.style.padding = '0 0.35rem';
                                badge.style.borderRadius = '50%';
                                sidebarLink.style.position = sidebarLink.style.position || 'relative';
                                sidebarLink.appendChild(badge);
                            }
                            badge.style.display = 'inline-block';
                            badge.textContent = String(count);
                        } else {
                            if(badge){ badge.remove(); }
                        }
                    }catch(err){ console && console.error && console.error('badge fetch err', err); }
                }
            }

            document.addEventListener('DOMContentLoaded', function(){
                updateBadges();
                setInterval(updateBadges, 60000); // refresh every minute
            });
        })();
    </script>
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
    <script src="{{ asset('js/sidebar.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('js/responsive-tables.js') }}?v={{ time() }}"></script>
    @yield('scripts')
    {{-- logout confirmation now handled in header partial --}}
</body>

</html>
