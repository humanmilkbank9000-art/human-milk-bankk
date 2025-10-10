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
    'user.dashboard' => 'Dashboard',
    'user.health-screening' => 'Health Screening',
    'user.donate' => 'Donate',
    'user.breastmilk-request' => 'Breastmilk Request',
    'user.my-requests' => 'My Requests',
    'user.history' => 'My Donation History',
    'user.pending' => 'Pending Donation',
];
$defaultTitle = $titles[$routeName] ?? 'Dashboard';
// Expose an ID for the logout form so scripts can target it regardless of which layout includes the partial
$logoutFormId = 'logout-form-top';
$logoutBtnId = session('account_role', 'user') === 'admin' ? 'logout-btn-admin' : 'logout-btn-user';
@endphp
<!-- Unified header partial: fully responsive with proper alignment -->
<header class="unified-header">
    <div class="header-content">
        <div class="header-left">
            <!-- Hospital Logos -->
            <img src="{{ asset('jrbgh-logo.png') }}" alt="JRBGH Logo" class="header-logo header-logo-jrbgh">
            <img src="{{ asset('hmblsc-logo.jpg') }}" alt="HMBLSC Logo" class="header-logo header-logo-hmblsc">
            
            {{-- If a child view defines @section('suppressLayoutTitle', true) the layout title will be hidden to avoid duplication --}}
@unless(View::hasSection('suppressLayoutTitle'))
    <h1>@yield('pageTitle', $defaultTitle)</h1>


   @endunless
    </div>

            <div class="header-right">
            @include('partials.notification-bell')

            <form id="{{ $logoutFormId }}" action="{{ route('logout') }}" method="POST" style="display:inline;margin:0;">
                @csrf
                <button type="button" class="btn btn-danger logout-btn" id="{{ $logoutBtnId }}" aria-label="Logout">
                    <i class="bi bi-box-arrow-right" aria-hidden="true"></i>
                    <span>Logout</span>
                </button>
            </form>
        </div>
    </div>
</header>

<style>
    /* Header structure styles */
    .header-left,
    .header-right {
        display: flex;
        align-items: center;
        gap: 12px;
        min-width: 0;
    }
    
    /* Header logos - responsive sizing */
.header-logo {
        width: auto;
        transition: all 0.3s ease;
    }
    
    .header-logo-jrbgh {
    height: 61px;
        margin-right: 8px;
    }
    
    .header-logo-hmblsc {
    height: 50px;
        margin-right: 12px;
    }
    
    .header-left h1 {
    font-size: 1.125rem;
        margin: 0;
        padding: 0;
        user-select: none;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    .header-right {
    flex-shrink: 0;
        gap: 8px;
    }
    
    .logout-btn {
    display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        min-width: 120px;
        height: 40px;
        padding: 6px 16px;
        border-radius: 6px;
        font-weight: 600;
        font-size: 1rem;
        white-space: nowrap;
        transition: all 0.2s ease;
    }
    
    .logout-btn i {
    font-size: 1.1em;
    }
    
    /* Tablet optimizations (768px - 991px) */
@media (max-width: 991px) {
        .header-logo-jrbgh {
            height: 50px;
            margin-right: 6px;
        }
        
        .header-logo-hmblsc {
    height: 42px;
            margin-right: 10px;
        }
        
        .header-left {
    gap: 10px;
        }
        
        .header-left h1 {
    font-size: 1.05rem;
        }
    }
    
    /* Mobile optimizations (max-width: 767px) */
@media (max-width: 767px) {
        .header-logo-jrbgh {
            height: 38px;
            margin-right: 4px;
        }
        
        .header-logo-hmblsc {
    height: 32px;
            margin-right: 6px;
        }
        
        .header-left {
    gap: 6px;
        }
        
        .header-left h1 {
    font-size: 0.95rem;
        }
        
        .header-right {
    gap: 6px;
        }
        
        .logout-btn {
    min-width: 100px;
            height: 36px;
            font-size: 0.9rem;
            padding: 4px 12px;
        }
    }
    
    /* Extra small mobile (max-width: 480px) */
@media (max-width: 480px) {
        .header-logo-jrbgh {
            height: 32px;
            margin-right: 3px;
        }
        
        .header-logo-hmblsc {
    height: 28px;
            margin-right: 4px;
        }
        
        .header-left {
    gap: 4px;
        }
        
        .header-left h1 {
    font-size: 0.85rem;
        }
        
        .logout-btn {
    min-width: 85px;
            height: 34px;
            font-size: 0.85rem;
            padding: 4px 10px;
            gap: 4px;
        }
    }
</style>

<script>
    // Attach logout confirmation to whichever button exists on the page
    document.addEventListener('DOMContentLoaded', function () {
        const logoutBtnAdmin = document.getElementById('logout-btn-admin');
        const logoutBtnUser = document.getElementById('logout-btn-user');
        const logoutForm = document.getElementById('{{ $logoutFormId }}');

        function attach(btn) {
            if (!btn || !logoutForm) return;
            btn.addEventListener('click', function (e) {
                Swal.fire({
                    title: 'Are you sure you want to logout?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, logout',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        logoutForm.submit();
                    }
                });
            });
        }

        attach(logoutBtnAdmin);
        attach(logoutBtnUser);
    });
</script>
