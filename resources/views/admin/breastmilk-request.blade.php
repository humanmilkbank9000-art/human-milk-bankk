@extends('layouts.admin-layout')

@section('title', 'Breastmilk Request Management')

@section('styles')
    <style>
        .table thead th {
            background: #f8fafc;
            font-weight: 600;
            font-size: 0.9rem;
            border-bottom: 2px solid #eaeaea;
            text-align: center;
            padding: 0.75rem 0.5rem;
            white-space: nowrap;
        }

        .table tbody tr {
            transition: box-shadow 0.2s, background 0.2s;
        }

        .table tbody tr:hover {
            background: #f6f8ff;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        }

        .table tbody td {
            padding: 0.75rem 0.5rem;
            font-size: 0.875rem;
            vertical-align: middle;
        }

        /* Compact text in tables */
        .table td strong {
            font-size: 0.875rem;
        }

        .table td small {
            font-size: 0.75rem;
        }

        .badge {
            font-size: 0.95rem;
            padding: 0.5em 0.8em;
            border-radius: 6px;
        }
        /* Assist option badge (shared styling similar to donation view) */
        .assist-option-badge {
            display:inline-block;
            padding:0.25rem 0.5rem;
            font-size:0.6rem;
            line-height:1.1;
            font-weight:600;
            border-radius:0.35rem;
            background:#6c757d;
            color:#fff;
            white-space:nowrap;
            letter-spacing:.3px;
        }
        .assist-option-badge.option-direct { background:#0d6efd; }
        .assist-option-badge.option-existing { background:#198754; }
        .assist-option-badge.option-letting { background:#6610f2; }

        /* Ensure milk type badges have a uniform appearance and width and are vertically centered */
        .milk-type-badge {
            min-width: 110px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 0.35em 0.6em;
            height: 34px;
            /* match typical .btn height for visual parity */
            line-height: 1;
            box-sizing: border-box;
        }

        /* Target action buttons inside standard tables to align heights and visual weight */
        .table-standard td .admin-review-btn,
        .table-standard td .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            height: 34px;
            padding: 0.35rem 0.6rem;
        }

        /* Small adjustment to ensure spacing between buttons stays correct */
        .table-standard td .admin-review-btn+.btn,
        .table-standard td .btn+.btn {
            margin-left: 0.4rem;
        }

        .card {
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
        }

        .card-header {
            border-radius: 12px 12px 0 0;
            font-size: 1.1rem;
            padding: 1rem 1.5rem;
        }

        .table-responsive {
            border-radius: 8px;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        /* Remove fixed min-width for better mobile responsiveness */
        .table-responsive table {
            min-width: auto;
        }

        .btn {
            font-size: 0.85rem;
            border-radius: 6px;
            padding: 0.375rem 0.75rem;
        }

        /* Tablet and below */
        @media (max-width: 991px) {
            .table thead th {
                font-size: 0.8rem;
                padding: 0.6rem 0.4rem;
            }

            .table tbody td {
                font-size: 0.8rem;
                padding: 0.6rem 0.4rem;
            }

            .table td strong {
                font-size: 0.8rem;
            }

            .table td small {
                font-size: 0.7rem;
            }

            .btn {
                font-size: 0.75rem;
                padding: 0.3rem 0.5rem;
            }
        }

        /* Search Input Styling */
        .input-group-text {
            background-color: white;
            border-right: 0;
        }

        #searchInput {
            border-left: 0;
            padding-left: 0;
        }

        #searchInput:focus {
            box-shadow: none;
            border-color: #ced4da;
        }

        .input-group:focus-within .input-group-text {
            border-color: #86b7fe;
        }

        .input-group:focus-within #searchInput {
            border-color: #86b7fe;
        }

        #clearSearch {
            display: none;
        }

        @media (max-width: 768px) {
            #searchInput {
                font-size: 0.9rem;
            }
        }

        @media (max-width: 768px) {
            .table-responsive {
                font-size: 0.85rem;
            }

            .card-header {
                font-size: 1rem;
                padding: 0.75rem 1rem;
            }

            .card-header h5 {
                font-size: 1rem;
            }

            /* More compact table */
            .table thead th {
                font-size: 0.7rem;
                padding: 0.5rem 0.25rem;
                line-height: 1.2;
            }

            .table tbody td {
                font-size: 0.75rem;
                padding: 0.5rem 0.25rem;
            }

            .table td strong {
                font-size: 0.75rem;
                display: block;
            }

            .table td small {
                font-size: 0.65rem;
            }

            .badge {
                font-size: 0.7rem;
                padding: 0.25em 0.5em;
            }

            .btn {
                font-size: 0.7rem;
                padding: 0.3rem 0.4rem;
            }

            /* Hide user IDs and less critical info on mobile */
            .table td small.text-muted {
                display: none;
            }

            /* Stack content compactly */
            .table td {
                line-height: 1.3;
            }

        /* Assist button + search layout (reused from donation view) */
        .assist-btn {
            background: linear-gradient(90deg,#ff7eb6,#ff65a3) !important;
            color: #fff !important;
            border: none !important;
            border-radius: 10px !important;
            padding: 0.35rem 0.9rem !important;
            box-shadow: 0 1px 3px rgba(0,0,0,0.15);
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            white-space: nowrap;
        }
        .assist-btn:hover, .assist-btn:focus {
            filter: brightness(0.96);
            color: #fff !important;
        }

        .search-assist-row { width:100%; gap:0; }
        .search-assist-row .input-group { flex:1; min-width:260px; }
        .search-assist-row .assist-btn { flex-shrink:0; height:32px; line-height:1; }
        .search-assist-row .assist-btn i { font-size:0.9rem; }
        .search-assist-row .assist-btn span { font-size:0.78rem; font-weight:600; }
        .search-assist-row .input-group .form-control { height:32px; }
        .search-assist-row .input-group-text { height:32px; display:flex; align-items:center; }
        @media (max-width: 576px) {
            .search-assist-row { flex-direction:column; }
            .search-assist-row .assist-btn { width:100%; margin-left:0 !important; margin-top:6px; }
        }

            /* Compact tabs for mobile while keeping them horizontal */
            .nav-tabs {
                margin-left: 0;
                margin-right: 0;
                padding-left: 0;
                padding-right: 0;
            }

            .nav-tabs .nav-item {
                flex: 1 1 33.333%;
                max-width: 33.333%;
                padding: 0;
            }

            .nav-tabs .nav-link {
                font-size: 0.7rem;
                padding: 0.5rem 0.15rem;
                margin: 0;
                word-break: break-word;
                line-height: 1.2;
            }

            .nav-tabs .badge {
                font-size: 0.65rem;
                padding: 0.2em 0.35em;
                margin-top: 0.15rem;
            }
        }

        @media (max-width: 576px) {
            .nav-tabs .nav-link {
                font-size: 0.65rem;
                padding: 0.4rem 0.1rem;
            }

            .nav-tabs .badge {
                font-size: 0.6rem;
                padding: 0.15em 0.3em;
            }
        }

        /* Modal card styling - remove any background colors */
        .modal-body .card {
            background-color: transparent;
            border: 1px solid #e0e0e0;
            box-shadow: none;
        }

        .modal-body .card.p-3 {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
        }

        /* Ensure no pink or unwanted colors in modals */
        .modal-body {
            background-color: #ffffff;
        }

        /* Action buttons styling */
        .btn-success {
            background-color: #28a745;
            border-color: #28a745;
        }

        .btn-success:hover {
            background-color: #218838;
            border-color: #1e7e34;
        }

        .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
        }

        .btn-danger:hover {
            background-color: #c82333;
            border-color: #bd2130;
        }

        /* Gap utility for button spacing */
        .gap-2 {
            gap: 0.5rem !important;
        }

        /* SweetAlert2 z-index fix - ensure it appears above modals */
        .swal2-container {
            z-index: 10000 !important;
        }

        .swal2-popup {
            z-index: 10001 !important;
        }

        /* Ensure form controls in SweetAlert are interactive */
        .swal2-popup input,
        .swal2-popup textarea,
        .swal2-popup select {
            pointer-events: auto !important;
            user-select: text !important;
            -webkit-user-select: text !important;
            -moz-user-select: text !important;
            -ms-user-select: text !important;
        }

        /* Ensure modal backdrop doesn't cover SweetAlert */
        .modal-backdrop {
            z-index: 1040 !important;
        }

        .modal {
            z-index: 1050 !important;
        }

        /* Mobile Modal Fixes - Ensure buttons are visible and accessible */
        @media (max-width: 768px) {
            .modal-dialog {
                margin: 0.5rem;
                max-height: calc(100vh - 1rem);
            }

            .modal-content {
                max-height: calc(100vh - 1rem);
                display: flex;
                flex-direction: column;
            }

            .modal-body {
                overflow-y: auto;
                flex: 1 1 auto;
                max-height: calc(100vh - 200px);
                -webkit-overflow-scrolling: touch;
            }

            .modal-footer {
                position: sticky;
                bottom: 0;
                background: white;
                border-top: 1px solid #dee2e6;
                z-index: 1;
                flex-shrink: 0;
                padding: 0.75rem;
            }

            .modal-footer .d-flex {
                flex-wrap: wrap;
                gap: 0.5rem;
            }

            .modal-footer .btn {
                font-size: 0.875rem;
                padding: 0.5rem 0.75rem;
                white-space: nowrap;
            }

            .modal-footer .d-flex.gap-2 {
                flex-wrap: nowrap;
            }
        }

        /* Extra small devices - stack buttons vertically */
        @media (max-width: 576px) {
            .modal-dialog {
                margin: 0.25rem;
                max-width: calc(100vw - 0.5rem);
            }

            .modal-footer {
                flex-direction: column;
                gap: 0.5rem;
            }

            .modal-footer .d-flex {
                width: 100%;
                flex-direction: column;
            }

            .modal-footer .btn {
                width: 100%;
                margin: 0;
            }

            .modal-footer .d-flex.gap-2 {
                flex-direction: column;
                width: 100%;
            }
        }
    </style>
    <link rel="stylesheet" href="{{ asset('css/table-layout-standard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/responsive-tables.css') }}">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.27/dist/sweetalert2.min.css">
@endsection

@section('content')
    <div class="container-fluid page-container-standard">

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('warning'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                {{ session('warning') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Page Header (Action moved beside search for compact layout) -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="mb-0">Breastmilk Requests</h4>
            </div>
            {{-- The Assist button has been moved next to the search input for a compact header --}}
        </div>

        <!-- Navigation Tabs -->
        <ul class="nav nav-tabs nav-tabs-standard" id="requestTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link{{ request()->get('status', 'pending') == 'pending' ? ' active' : '' }}"
                    href="?status=pending" id="pending-tab" role="tab">
                    Pending Requests <span class="badge bg-warning">{{ $pendingRequests->count() }}</span>
                </a>
            </li>
            <!-- Approved Requests tab removed (redundant with Dispensed/Approved states) -->
            <li class="nav-item" role="presentation">
                <a class="nav-link{{ request()->get('status') == 'dispensed' ? ' active' : '' }}" href="?status=dispensed"
                    id="dispensed-tab" role="tab">
                    Dispensed Requests <span class="badge bg-info">{{ $dispensedRequests->count() }}</span>
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link{{ request()->get('status') == 'declined' ? ' active' : '' }}" href="?status=declined"
                    id="declined-tab" role="tab">
                    Declined Requests <span class="badge bg-danger">{{ $declinedRequests->count() }}</span>
                </a>
            </li>
        </ul>

        {{-- Search Input Below Tabs --}}
        <div class="mb-3">
            <form method="GET" action="{{ route('admin.request') }}" class="d-flex align-items-stretch search-assist-row">
                <input type="hidden" name="status" value="{{ request()->get('status', 'pending') }}">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" class="form-control form-control-sm border-start-0 ps-0" id="searchInput"
                        name="q" placeholder="Search guardian, infant, or contact" aria-label="Search requests" value="{{ request('q') }}">
                    <button class="btn btn-sm btn-outline-secondary" type="button" id="clearSearch" style="display: none;">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
                <button type="button" class="btn btn-sm assist-btn ms-2" data-bs-toggle="modal" data-bs-target="#assistedRequestModal" style="background: linear-gradient(90deg,#ff7eb6,#ff65a3) !important; color:#fff !important; border:none !important;">
                    <i class="fas fa-user-plus"></i>
                    <span>Assist Walk-in Request</span>
                </button>
            </form>
            <small class="text-muted d-block mt-1">
                <span id="searchResults"></span>
            </small>
        </div>

        <div class="tab-content" id="requestTabContent">
            <!-- Pending Requests Tab -->
            <div class="tab-pane fade{{ request()->get('status', 'pending') == 'pending' ? ' show active' : '' }}"
                id="pending-requests" role="tabpanel">
                <div class="card card-standard">
                    <div class="card-header bg-success text-white">
                        <h5>Pending Breastmilk Requests</h5>
                    </div>
                    <div class="card-body">
                        @if($pendingRequests->count() > 0)
                            <div class="table-container-standard">
                                <table
                                    class="table table-standard table-bordered table-striped align-middle table-standard-min-width">
                                    <thead class="table-success">
                                        <tr>
                                            <th class="text-center">Guardian</th>
                                            <th class="text-center">Infant</th>
                                            <th class="text-center">Contact</th>
                                            <th class="text-center">Submitted</th>
                                            <th class="text-center">Schedule date</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $pendingOrdered = $pendingRequests instanceof \Illuminate\Pagination\LengthAwarePaginator
                                                ? $pendingRequests->getCollection()->sortByDesc('created_at')
                                                : collect($pendingRequests)->sortByDesc('created_at');
                                        @endphp
                                        @foreach($pendingOrdered as $request)
                                            <tr>
                                                <td class="align-middle" data-label="Guardian">
                                                    <strong>{{ $request->user->first_name ?? '' }}
                                                        {{ $request->user->last_name ?? '' }}</strong>
                                                </td>
                                                <td class="align-middle" data-label="Infant">
                                                    <strong>{{ $request->infant?->first_name ?? 'Unknown' }}
                                                        {{ $request->infant?->last_name ?? '' }}{{ ($request->infant?->suffix) ? ' ' . $request->infant->suffix : '' }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ $request->infant->getFormattedAge() }}</small>
                                                </td>
                                                <td class="align-middle text-center" data-label="Contact">
                                                    {{ $request->user->contact_number ?? '-' }}
                                                </td>
                                                <td class="align-middle text-center" data-label="Submitted">
                                                    {{ $request->created_at->format('M d, Y g:i A') }}
                                                </td>
                                                <td class="align-middle text-center" data-label="Schedule date">
                                                    @if($request->availability)
                                                        {{ $request->availability->formatted_date }}
                                                        @if(!empty($request->availability->formatted_time))
                                                        {{ $request->availability->formatted_time }}@endif
                                                    @else
                                                        {{ Carbon\Carbon::parse($request->request_date)->format('M d, Y') }}
                                                        {{ Carbon\Carbon::parse($request->request_time)->format('g:i A') }}
                                                    @endif
                                                </td>
                                                <td class="align-middle text-center" data-label="Action">
                                                    {{-- Archive hidden for pending requests to prevent accidental archiving --}}
                                                    <button type="button" class="admin-review-btn btn-sm" data-bs-toggle="modal"
                                                        data-bs-target="#dispensingModal{{ $request->breastmilk_request_id }}">
                                                        Review
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-heart fa-3x mb-3"></i>
                                <p>No pending breastmilk requests</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Approved requests pane removed -->

            <!-- Dispensed Requests Tab -->
            <div class="tab-pane fade{{ request()->get('status') == 'dispensed' ? ' show active' : '' }}"
                id="dispensed-requests" role="tabpanel">
                <div class="card card-standard">
                    <div class="card-header bg-info text-white">
                        <h5>Dispensed Breastmilk Requests</h5>
                    </div>
                    <div class="card-body">
                        @if($dispensedRequests->count() > 0)
                            <div class="table-container-standard">
                                <table
                                    class="table table-standard table-bordered table-striped align-middle table-standard-min-width">
                                    <thead class="table-success">
                                        <tr>
                                            <th class="text-center">Guardian</th>
                                            <th class="text-center">Assist Option</th>
                                            <th class="text-center">Infant</th>
                                            <th class="text-center">Donor/Batch</th>
                                            <th class="text-center">Volume</th>
                                            <th class="text-center">Type</th>
                                            <th class="text-center">Date</th>
                                            <th class="text-center">Time</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $dispensedOrdered = $dispensedRequests instanceof \Illuminate\Pagination\LengthAwarePaginator
                                                ? $dispensedRequests->getCollection()->sortByDesc('created_at')
                                                : collect($dispensedRequests)->sortByDesc('created_at');
                                        @endphp
                                        @foreach($dispensedOrdered as $request)
                                            <tr>
                                                <td data-label="Guardian">
                                                    <strong>{{ $request->user->first_name ?? '' }}
                                                        {{ $request->user->last_name ?? '' }}</strong>
                                                </td>
                                                <td data-label="Assist Option" class="text-center">
                                                    @php
                                                        $assistMap = [
                                                            'no_account_direct_record' => 'Direct Record',
                                                            'record_to_existing_user' => 'Existing User',
                                                            'milk_letting_activity' => 'Milk Letting Activity',
                                                        ];
                                                        $assistKey = $request->assist_option ?? null;
                                                        $assistLabel = $assistKey ? ($assistMap[$assistKey] ?? $assistKey) : null;
                                                        $assistClass = match($assistKey) {
                                                            'no_account_direct_record' => 'option-direct',
                                                            'record_to_existing_user' => 'option-existing',
                                                            'milk_letting_activity' => 'option-letting',
                                                            default => ''
                                                        };
                                                    @endphp
                                                    @if($assistLabel)
                                                        <span class="assist-option-badge {{ $assistClass }}">{{ $assistLabel }}</span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td data-label="Infant">
                                                    <strong>{{ $request->infant?->first_name ?? 'Unknown' }}
                                                        {{ $request->infant?->last_name ?? '' }}{{ ($request->infant?->suffix) ? ' ' . $request->infant->suffix : '' }}</strong><br>
                                                    <small class="text-muted">{{ $request->infant->getFormattedAge() }}</small>
                                                </td>
                                                <td data-label="Donor/Batch">
                                                    @if($request->dispensedMilk)
                                                        @php
                                                            $sd = $request->dispensedMilk->source_display ?? '-';
                                                            // remove trailing volume in parentheses like " (200.00ml)"
                                                            $sd_clean = preg_replace('/\s*\(\d+(?:\.\d+)?ml\)$/', '', $sd);
                                                        @endphp
                                                        {{ $sd_clean }}
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td data-label="Volume">
                                                    @php
                                                        $vol = $request->volume_dispensed ?? $request->volume_requested;
                                                        $vol_display = is_numeric($vol) ? rtrim(rtrim(number_format((float) $vol, 2, '.', ''), '0'), '.') : $vol;
                                                    @endphp
                                                    <strong>{{ $vol_display }} ml</strong>
                                                </td>
                                                <td data-label="Type">
                                                    @if($request->dispensedMilk && $request->dispensedMilk->milk_type)
                                                        <span
                                                            class="badge milk-type-badge bg-{{ $request->dispensedMilk->milk_type === 'pasteurized' ? 'success' : 'warning' }}">
                                                            {{ ucfirst($request->dispensedMilk->milk_type) }}
                                                        </span>
                                                    @else
                                                        <span class="text-muted">Not specified</span>
                                                    @endif
                                                </td>
                                                <td data-label="Date">
                                                    {{ $request->dispensed_at ? \Carbon\Carbon::parse($request->dispensed_at)->format('M d, Y') : 'N/A' }}
                                                </td>
                                                <td data-label="Time">
                                                    {{ $request->dispensed_at ? \Carbon\Carbon::parse($request->dispensed_at)->format('g:i A') : 'N/A' }}
                                                </td>
                                                <td data-label="Action">
                                                    <div class="d-flex gap-2 align-items-center justify-content-center">
                                                        <button class="admin-review-btn btn-sm" data-bs-toggle="modal"
                                                            data-bs-target="#viewModal{{ $request->breastmilk_request_id }}">
                                                            Review
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-prescription-bottle fa-3x mb-3"></i>
                                <p>No dispensed requests yet</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Declined Requests Tab -->
            <div class="tab-pane fade{{ request()->get('status') == 'declined' ? ' show active' : '' }}"
                id="declined-requests" role="tabpanel">
                <div class="card card-standard">
                    <div class="card-header bg-danger text-white">
                        <h5>Declined Breastmilk Requests</h5>
                    </div>
                    <div class="card-body">
                        @if($declinedRequests->count() > 0)
                            <div class="table-container-standard">
                                <table class="table table-standard table-striped">
                                    <thead class="table-success">
                                        <tr>
                                            <th class="text-center">Guardian</th>
                                            <th class="text-center">Infant</th>
                                            <th class="text-center">Reason</th>
                                            <th class="text-center">Date</th>
                                            <th class="text-center">Time</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $declinedOrdered = $declinedRequests instanceof \Illuminate\Pagination\LengthAwarePaginator
                                                ? $declinedRequests->getCollection()->sortByDesc('created_at')
                                                : collect($declinedRequests)->sortByDesc('created_at');
                                        @endphp
                                        @foreach($declinedOrdered as $request)
                                            <tr>
                                                <td data-label="Guardian">
                                                    <strong>{{ $request->user->first_name ?? '' }}
                                                        {{ $request->user->last_name ?? '' }}</strong>
                                                </td>
                                                <td data-label="Infant">
                                                    <strong>{{ $request->infant?->first_name ?? 'Unknown' }}
                                                        {{ $request->infant?->last_name ?? '' }}{{ ($request->infant?->suffix) ? ' ' . $request->infant->suffix : '' }}</strong><br>
                                                    <small class="text-muted">{{ $request->infant->getFormattedAge() }}</small>
                                                </td>
                                                <td data-label="Reason">
                                                    @if($request->admin_notes)
                                                        <small>{{ Str::limit($request->admin_notes, 50) }}</small>
                                                    @else
                                                        <span class="text-muted">No reason provided</span>
                                                    @endif
                                                </td>
                                                <td data-label="Date" class="text-center">
                                                    {{ $request->declined_at ? $request->declined_at->format('M d, Y') : 'N/A' }}
                                                </td>
                                                <td data-label="Time" class="text-center">
                                                    {{ $request->declined_at ? $request->declined_at->format('g:i A') : 'N/A' }}
                                                </td>
                                                <td class="text-center" data-label="Action">
                                                    <button class="admin-review-btn btn-sm" data-bs-toggle="modal"
                                                        data-bs-target="#viewModal{{ $request->breastmilk_request_id }}">
                                                        Review
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-times-circle fa-3x mb-3"></i>
                                <p>No declined requests yet</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Dispensing Modals for Pending Requests -->
        @php
            // Ensure $pendingOrdered is available for modal generation regardless of tab rendering order
            $pendingOrdered = isset($pendingOrdered)
                ? $pendingOrdered
                : (($pendingRequests instanceof \Illuminate\Pagination\LengthAwarePaginator)
                    ? $pendingRequests->getCollection()->sortByDesc('created_at')
                    : collect($pendingRequests)->sortByDesc('created_at'));
        @endphp
        @foreach($pendingOrdered as $request)
            <!-- Dispensing Modal -->
            <div class="modal fade" id="dispensingModal{{ $request->breastmilk_request_id }}" tabindex="-1"
                aria-labelledby="dispensingModalLabel{{ $request->breastmilk_request_id }}" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title" id="dispensingModalLabel{{ $request->breastmilk_request_id }}">
                                <i class="fas fa-prescription-bottle"></i> Dispense Breastmilk Request
                                #{{ $request->breastmilk_request_id }}
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <!-- Request Information -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="card p-3">
                                        <h6 class="mb-2"><i class="fas fa-user"></i> Guardian Information</h6>
                                        <p class="mb-1"><strong>Name:</strong> {{ $request->user->first_name ?? '' }}
                                            {{ $request->user->last_name ?? '' }}
                                        </p>
                                        <p class="mb-0"><strong>Contact:</strong> {{ $request->user->contact_number ?? '-' }}
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card p-3">
                                        <h6 class="mb-2"><i class="fas fa-baby"></i> Infant Information</h6>
                                        <p class="mb-1"><strong>Name:</strong> {{ $request->infant?->first_name ?? 'Unknown' }}
                                            {{ $request->infant?->last_name ?? '' }}{{ ($request->infant?->suffix) ? ' ' . $request->infant->suffix : '' }}
                                        </p>
                                        <p class="mb-1"><strong>Age:</strong> {{ $request->infant->getFormattedAge() }}</p>
                                        <p class="mb-0"><strong>Sex:</strong> {{ ucfirst($request->infant->sex) }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Prescription -->
                            @if($request->hasPrescription())
                                <div class="row mb-3">
                                    <div class="col-12">
                                        <button type="button" class="admin-review-btn btn-sm force-blue" data-bs-toggle="modal"
                                            data-bs-target="#prescriptionModal{{ $request->breastmilk_request_id }}"
                                            onclick="viewPrescriptionModal({{ $request->breastmilk_request_id }})">
                                            Review Prescription
                                        </button>
                                    </div>
                                </div>
                            @endif

                            <!-- Dispensing Form -->
                            <form id="dispensingForm{{ $request->breastmilk_request_id }}">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="volumeToDispense{{ $request->breastmilk_request_id }}"
                                                class="form-label">
                                                <i class="fas fa-flask"></i> Volume to Dispense (ml) <span
                                                    class="text-danger">*</span>
                                            </label>
                                            <input type="text" inputmode="numeric" pattern="[0-9]*([.][0-9]+)?"
                                                class="form-control" id="volumeToDispense{{ $request->breastmilk_request_id }}"
                                                name="volume_dispensed"
                                                oninput="updateSelectedVolume({{ $request->breastmilk_request_id }})">
                                            <div class="form-text">Enter the amount of milk to dispense</div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="milkTypeSelect{{ $request->breastmilk_request_id }}" class="form-label">
                                                <i class="fas fa-vial"></i> Breastmilk Type <span class="text-danger">*</span>
                                            </label>
                                            <select class="form-select" id="milkTypeSelect{{ $request->breastmilk_request_id }}"
                                                name="milk_type"
                                                onchange="handleMilkTypeChange({{ $request->breastmilk_request_id }})">
                                                <option value="pasteurized" selected>Pasteurized Breastmilk</option>
                                            </select>
                                            <small class="text-muted">Only pasteurized breastmilk can be dispensed for safety.</small>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <!-- Inventory Section -->
                                        <div id="inventoryContainer{{ $request->breastmilk_request_id }}" style="display:none;">
                                            <div class="card p-3">
                                                <h6 class="mb-2"><i class="fas fa-warehouse"></i> Available Inventory</h6>
                                                <div id="loadingInventory{{ $request->breastmilk_request_id }}"
                                                    class="text-center py-3" style="display:none;">
                                                    <i class="fas fa-spinner fa-spin"></i> Loading inventory...
                                                </div>
                                                <div id="inventoryList{{ $request->breastmilk_request_id }}"
                                                    style="max-height: 300px; overflow-y: auto;">
                                                </div>
                                                <div class="mt-2" id="volumeTracker{{ $request->breastmilk_request_id }}"
                                                    style="display:none;">
                                                    <div class="alert alert-info mb-0 py-2">
                                                        <small>
                                                            <strong>Selected:</strong> <span
                                                                id="totalSelected{{ $request->breastmilk_request_id }}">0.00</span>
                                                            ml /
                                                            <strong>Required:</strong> <span
                                                                id="volumeRequired{{ $request->breastmilk_request_id }}">0.00</span>
                                                            ml
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Admin Notes - Moved to the end -->
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <div class="mb-3">
                                            <label for="adminNotes{{ $request->breastmilk_request_id }}" class="form-label">
                                                <i class="fas fa-sticky-note"></i> Admin Notes
                                            </label>
                                            <textarea class="form-control" id="adminNotes{{ $request->breastmilk_request_id }}"
                                                name="dispensing_notes" rows="3"
                                                placeholder="Enter any notes about this dispensing..."></textarea>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer d-flex justify-content-between">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                <i class="fas fa-times"></i> Close
                            </button>
                            <div class="d-flex gap-2">
                                {{-- Archive disabled for pending requests --}}
                                <button type="button" class="btn btn-danger"
                                    onclick="handleReject({{ $request->breastmilk_request_id }})">
                                    <i class="fas fa-ban"></i> Reject
                                </button>
                                <button type="button" class="btn btn-success"
                                    id="dispenseBtn{{ $request->breastmilk_request_id }}"
                                    onclick="handleDispense({{ $request->breastmilk_request_id }})">
                                    <i class="fas fa-check"></i> Dispense
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Prescription Modal for Pending Request -->
            <div class="modal fade" id="prescriptionModal{{ $request->breastmilk_request_id }}" tabindex="-1"
                aria-labelledby="prescriptionModalLabel{{ $request->breastmilk_request_id }}" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="prescriptionModalLabel{{ $request->breastmilk_request_id }}">
                                <i class="fas fa-file-medical"></i> Prescription
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div id="prescriptionModalContent{{ $request->breastmilk_request_id }}" class="text-center">
                                <div class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading prescription...</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        <!-- Modals for viewing, approving, and declining requests will be added here -->
        {{-- Temporarily excluding pending requests from modal generation --}}
        @foreach([$approvedRequests, $dispensedRequests, $declinedRequests] as $requestCollection)
            @foreach($requestCollection as $request)
                @component('partials.shared-modal', ['id' => 'viewModal' . $request->breastmilk_request_id, 'title' => 'Request #' . $request->breastmilk_request_id . ' Details', 'hideFooterButtons' => true])
                @slot('slot')
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="card p-3">
                                <h6 class="mb-2"><i class="fas fa-user"></i> Guardian Information</h6>
                                <p class="mb-1"><strong>Name:</strong> {{ $request->user->first_name ?? '' }}
                                    {{ $request->user->last_name ?? '' }}
                                </p>
                                <p class="mb-0"><strong>Contact Number:</strong> {{ $request->user->contact_number ?? '-' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="card p-3">
                                <h6 class="mb-2"><i class="fas fa-baby"></i> Infant Information</h6>
                                <p class="mb-1"><strong>Name:</strong> {{ $request->infant?->first_name ?? 'Unknown' }}
                                    {{ $request->infant?->last_name ?? '' }}{{ ($request->infant?->suffix) ? ' ' . $request->infant->suffix : '' }}
                                </p>
                                <p class="mb-1"><strong>Age:</strong> {{ $request->infant->getFormattedAge() }}</p>
                                <p class="mb-0"><strong>Sex:</strong> {{ ucfirst($request->infant->sex) }}</p>
                                <p class="mb-0"><strong>Birth Weight:</strong>
                                    {{ $request->infant->birth_weight ? $request->infant->birth_weight . ' kg' : '-' }}</p>
                            </div>
                        </div>
                    </div>

                    @if($request->hasPrescription())
                        <div class="row mt-2">
                            <div class="col-12">
                                <button type="button" class="admin-review-btn btn-sm force-blue" data-bs-toggle="modal"
                                    data-bs-target="#prescriptionModal{{ $request->breastmilk_request_id }}"
                                    onclick="viewPrescriptionModal({{ $request->breastmilk_request_id }})">
                                    Review Prescription
                                </button>
                            </div>
                        </div>
                    @endif

                    @if($request->status === 'pending')
                        <!-- Admin Notes Field for Accept/Decline -->
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="card p-3">
                                    <h6 class="mb-2"><i class="fas fa-sticky-note"></i> Admin Notes</h6>
                                    <div class="mb-3">
                                        <label for="viewModalNotes{{ $request->breastmilk_request_id }}" class="form-label">
                                            Notes / Remarks (Optional)
                                        </label>
                                        <textarea class="form-control" id="viewModalNotes{{ $request->breastmilk_request_id }}" rows="3"
                                            placeholder="Enter any notes or remarks about this request (optional)..."></textarea>
                                        <small class="form-text text-muted">
                                            These notes will be saved with the request when you accept or decline it.
                                        </small>
                                    </div>
                                    <div class="d-flex gap-2 justify-content-end">
                                        <button type="button" class="btn btn-success"
                                            onclick="handleAcceptFromViewModal({{ $request->breastmilk_request_id }})">
                                            <i class="fas fa-check"></i> Accept Request
                                        </button>
                                        <button type="button" class="btn btn-danger"
                                            onclick="handleDeclineFromViewModal({{ $request->breastmilk_request_id }})">
                                            <i class="fas fa-times"></i> Decline Request
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @elseif($request->admin_notes)
                        <!-- Display existing admin notes for non-pending requests -->
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="card p-3">
                                    <h6 class="mb-2"><i class="fas fa-sticky-note"></i> Admin Notes</h6>
                                    <div class="alert alert-info mb-0">
                                        {{ $request->admin_notes }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
                @endslot
                @endcomponent

                {{-- inline decline modal removed; use full decline modal below with admin notes backup shown --}}

                @component('partials.shared-modal', ['id' => 'prescriptionModal' . $request->breastmilk_request_id, 'title' => 'Prescription'])
                @slot('slot')
                <div id="prescriptionModalContent{{ $request->breastmilk_request_id }}" class="text-center">
                    <div class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading prescription...</div>
                </div>
                @endslot
                @endcomponent

                @if($request->status === 'pending')
                    @component('partials.shared-modal', ['id' => 'approveModal' . $request->breastmilk_request_id, 'title' => 'Approve & Dispense Request #' . $request->breastmilk_request_id])
                    @slot('slot')
                    <form action="{{ route('admin.request.approve', $request->breastmilk_request_id) }}" method="POST"
                        id="approveForm{{ $request->breastmilk_request_id }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="volume_requested" class="form-label">Volume to Dispense (ml) *</label>
                                    <input type="number" class="form-control" name="volume_requested" step="0.01" min="0"
                                        id="volumeRequested{{ $request->breastmilk_request_id }}"
                                        oninput="validateDispenseForm({{ $request->breastmilk_request_id }})">
                                    <div class="form-text">Specify the amount of breastmilk to be dispensed.</div>
                                </div>
                                <div class="mb-3">
                                    <label for="milk_type" class="form-label">Milk Type *</label>
                                    <select class="form-select" name="milk_type"
                                        id="milkType{{ $request->breastmilk_request_id }}"
                                        onchange="loadInventory({{ $request->breastmilk_request_id }}); validateDispenseForm({{ $request->breastmilk_request_id }})">
                                        <option value="pasteurized" selected>Pasteurized Breastmilk</option>
                                    </select>
                                    <small class="text-muted">Only pasteurized breastmilk can be dispensed for safety.</small>
                                </div>
                                <div class="mb-3">
                                    <label for="admin_notes" class="form-label">Notes (Optional)</label>
                                    <textarea class="form-control" name="admin_notes" rows="3"
                                        placeholder="Any additional notes about the dispensing..."></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div id="inventorySection{{ $request->breastmilk_request_id }}" style="display: none;">
                                    <h6>Available Inventory</h6>
                                    <div class="alert alert-info"><small><strong>Instructions:</strong> Select items and specify volume
                                            to deduct from each.</small></div>
                                    <div id="inventoryList{{ $request->breastmilk_request_id }}"
                                        style="max-height: 300px; overflow-y: auto;"></div>
                                    <div class="mt-3"><small class="text-muted">Selected Volume: <span
                                                id="selectedVolume{{ $request->breastmilk_request_id }}" class="fw-bold">0</span>
                                            ml</small></div>
                                </div>
                                <div id="loadingInventory{{ $request->breastmilk_request_id }}" style="display: none;">
                                    <div class="text-center py-3"><i class="fas fa-spinner fa-spin"></i> Loading inventory...</div>
                                </div>
                            </div>
                        </div>
                    </form>
                    @endslot
                    @slot('primary')
                    {{-- The default primary is not used; keep footer action inside the form for submit --}}
                    @endslot
                    @endcomponent

                    @component('partials.shared-modal', ['id' => 'declineModal' . $request->breastmilk_request_id, 'title' => 'Decline Request #' . $request->breastmilk_request_id])
                    @slot('slot')
                    <form action="{{ route('admin.request.decline', $request->breastmilk_request_id) }}" method="POST">
                        @csrf
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i> Are you sure you want to decline this request? This action cannot be undone.
                        </div>
                    </form>
                    @endslot
                    @slot('primary')
                    {{-- Primary action is the form submit inside the slot --}}
                    @endslot
                    @endcomponent
                @endif
            @endforeach
        @endforeach
    </div>{{-- Close container-fluid --}}

    

    <!-- Assisted Walk-in Request Modal -->
    <div class="modal fade" id="assistedRequestModal" tabindex="-1" aria-labelledby="assistedRequestModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="assistedRequestModalLabel">
                        <i class="fas fa-user-plus"></i> Assist Walk-in Breastmilk Request
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form id="assistedRequestForm" action="{{ route('admin.breastmilk-request.store-assisted') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> <strong>Note:</strong> Use this form to assist
                            mothers/guardians who do not have a device. Interview them and fill in their details below.
                        </div>
                        <div class="mb-3">
                            <label for="assist_option" class="form-label"><i class="fas fa-tag me-1"></i> Assist Option <span class="text-danger">*</span></label>
                            <select class="form-select" id="assist_option" name="assist_option">
                                <option value="">Select option</option>
                                <option value="no_account_direct_record">No account or direct record</option>
                                <option value="record_to_existing_user">Record to existing user</option>
                                <option value="milk_letting_activity">Milk letting activity</option>
                            </select>
                        </div>
                        <div id="assisted-existing-user" class="mb-3" style="display:none;">
                            <label class="form-label"><i class="fas fa-search me-1"></i> Find Existing User</label>
                            <input type="text" class="form-control" id="assisted_user_search" placeholder="Search by name or contact (min 2 chars)">
                            <div id="assisted_user_results" class="list-group mt-2" style="max-height:220px; overflow:auto;"></div>
                            <input type="hidden" name="existing_user_id" id="assisted_existing_user_id" value="">
                            <small class="text-muted">Selecting a user will auto-fill Guardian fields below.</small>
                        </div>

                        <h6 class="border-bottom pb-2 mb-3"><i class="fas fa-user"></i> Guardian Information</h6>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="guardian_first_name" class="form-label">First Name <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control auto-capitalize-words" id="guardian_first_name" name="guardian_first_name"
                                    value="{{ old('guardian_first_name') }}">
                            </div>
                            <div class="col-md-4">
                                <label for="guardian_last_name" class="form-label">Last Name <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control auto-capitalize-words" id="guardian_last_name" name="guardian_last_name"
                                    value="{{ old('guardian_last_name') }}">
                            </div>
                            <div class="col-md-4">
                                <label for="guardian_contact" class="form-label">Contact Number <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="guardian_contact" name="guardian_contact"
                                    value="{{ old('guardian_contact') }}" placeholder="09XXXXXXXXX">
                                <div id="guardian_contact_feedback" class="form-text text-danger" style="display:none;">
                                </div>
                            </div>
                        </div>

                        <h6 class="border-bottom pb-2 mb-3 mt-4"><i class="fas fa-baby"></i> Infant Information</h6>
                        <!-- Existing infant selector (auto-fill when assisting existing user) -->
                        <div id="assisted_infant_select_wrap" class="mb-3" style="display:none;">
                            <label for="assisted_infant_select" class="form-label"><i class="fas fa-child me-1"></i> Select Existing Infant (optional)</label>
                            <select id="assisted_infant_select" class="form-select"></select>
                            <small class="text-muted">Choosing an infant will auto-fill the fields below. You can still edit any value before submitting.</small>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="infant_first_name" class="form-label">Infant First Name <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control auto-capitalize-words" id="infant_first_name" name="infant_first_name"
                                    value="{{ old('infant_first_name') }}">
                            </div>
                            <div class="col-md-4">
                                <label for="infant_last_name" class="form-label">Infant Last Name <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control auto-capitalize-words" id="infant_last_name" name="infant_last_name"
                                    value="{{ old('infant_last_name') }}">
                            </div>
                            <div class="col-md-4">
                                <label for="infant_date_of_birth" class="form-label">Date of Birth <span
                                        class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="infant_date_of_birth"
                                    name="infant_date_of_birth" value="{{ old('infant_date_of_birth') }}"
                                    max="{{ date('Y-m-d') }}">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="infant_sex" class="form-label">Sex <span class="text-danger">*</span></label>
                                <select class="form-select" id="infant_sex" name="infant_sex">
                                    <option value="">Select Sex</option>
                                    <option value="Male" {{ old('infant_sex') === 'Male' ? 'selected' : '' }}>Male</option>
                                    <option value="Female" {{ old('infant_sex') === 'Female' ? 'selected' : '' }}>Female
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="infant_weight" class="form-label">Weight (kg) <span
                                        class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control" id="infant_weight"
                                    name="infant_weight" value="{{ old('infant_weight') }}" min="0.5" max="20"
                                    placeholder="e.g., 3.5">
                            </div>
                        </div>

                        <h6 class="border-bottom pb-2 mb-3 mt-4"><i class="fas fa-heartbeat"></i> Medical Information</h6>
                        <div class="mb-3">
                            <label for="medical_condition" class="form-label">Medical Condition / Reason for Request <span
                                    class="text-danger">*</span></label>
                            <textarea class="form-control" id="medical_condition" name="medical_condition" rows="3"
                                placeholder="Describe the infant's medical condition or reason for requesting breastmilk">{{ old('medical_condition') }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label for="prescription_upload" class="form-label">Prescription (Optional)</label>
                            <input type="file" class="form-control" id="prescription_upload" name="prescription"
                                accept="image/*,.pdf">
                            <small class="text-muted">Upload prescription if available. Images or PDF files only.</small>
                        </div>

                        <h6 class="border-bottom pb-2 mb-3 mt-4"><i class="fas fa-calendar-alt"></i> Request Details</h6>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="request_date" class="form-label">Requested Date <span
                                        class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="request_date" name="request_date"
                                    value="{{ old('request_date', date('Y-m-d')) }}">
                            </div>
                            <div class="col-md-6">
                                <label for="milk_type" class="form-label">Milk Type <span
                                        class="text-danger">*</span></label>
                                <select class="form-select" id="milk_type" name="milk_type">
                                    <option value="pasteurized" selected>Pasteurized Breastmilk</option>
                                </select>
                                <small class="text-muted">Only pasteurized breastmilk can be dispensed for safety.</small>
                            </div>
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" value="1" id="dispense_now_checkbox"
                                name="dispense_now" {{ old('dispense_now', '1') == '1' ? 'checked' : '' }}>
                            <label class="form-check-label" for="dispense_now_checkbox">
                                Dispense now from available inventory (admin-assisted immediate dispensing)
                            </label>
                        </div>

                        <div id="assistedInventorySection" style="display:none;" class="mb-3">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="assistedVolumeToDispense" class="form-label">
                                                    <i class="fas fa-flask"></i> Volume to Dispense (ml) <span
                                                        class="text-danger">*</span>
                                                </label>
                                                <input type="text" inputmode="numeric" pattern="[0-9]*([.][0-9]+)?"
                                                    class="form-control" id="assistedVolumeToDispense"
                                                    value="{{ old('assisted_volume') }}"
                                                    oninput="assistedUpdateSelectedVolume()">
                                                <div class="form-text">Enter the amount of milk to dispense.</div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-2" id="assistedVolumeTracker" style="display:none;">
                                                <div class="alert alert-info mb-0 py-2">
                                                    <small>
                                                        <strong>Selected:</strong> <span
                                                            id="assistedTotalSelected">0.00</span> ml /
                                                        <strong>Required:</strong> <span
                                                            id="assistedVolumeRequired">0.00</span> ml
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <h6 class="card-title mt-2"><i class="fas fa-warehouse"></i> Available Inventory</h6>
                                    <div id="assistedInventoryLoading" style="display:none">Loading inventory...</div>
                                    <div id="assistedInventoryList" style="max-height:300px; overflow-y:auto;"></div>
                                    <small class="text-muted d-block mt-2">Select bags in order. Volume will be automatically deducted based on your input above.</small>
                                </div>
                            </div>
                        </div>

                        <!-- Hidden input to carry selected sources as JSON -->
                        <input type="hidden" id="selected_sources_json" name="selected_sources_json" value="">

                        <div class="mb-3">
                            <label for="admin_notes" class="form-label">Staff Notes (Optional)</label>
                            <textarea class="form-control" id="admin_notes" name="admin_notes" rows="2"
                                placeholder="Additional notes or observations from the interview">{{ old('admin_notes') }}</textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-check"></i> Submit Request
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- JavaScript for prescription viewing and inventory selection -->
    {{-- Real-time Search Functionality --}}
    <script>
        // Assisted form: check for duplicate guardian contact numbers
        document.addEventListener('DOMContentLoaded', function () {
            const contactEl = document.getElementById('guardian_contact');
            const feedbackEl = document.getElementById('guardian_contact_feedback');
            const submitBtn = document.querySelector('#assistedRequestForm button[type="submit"]');

            if (contactEl) {
                let timeout = null;
                contactEl.addEventListener('input', function () {
                    // clear feedback while typing
                    if (feedbackEl) {
                        feedbackEl.style.display = 'none';
                        feedbackEl.textContent = '';
                    }
                    if (submitBtn) submitBtn.disabled = false;
                    // debounce
                    if (timeout) clearTimeout(timeout);
                    timeout = setTimeout(() => {
                        const val = (contactEl.value || '').trim();
                        if (!val) return;
                        // Simple format normalization (remove spaces)
                        const normalized = val.replace(/\s+/g, '');
                        fetch(`{{ route('admin.request.check-contact') }}?contact=${encodeURIComponent(normalized)}`, { headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' } })
                            .then(r => r.json())
                            .then(data => {
                                if (data && data.exists) {
                                    // Show warning and disable submit to prevent accidental duplicate
                                    if (feedbackEl) {
                                        feedbackEl.style.display = 'block';
                                        feedbackEl.textContent = 'This contact number already exists for user: ' + (data.user.first_name || '') + ' ' + (data.user.last_name || '') + ' — leave as is to link to existing user, or change the number to create a new walk-in user.';
                                    }
                                    if (submitBtn) submitBtn.disabled = false; // still allow submit; admin may intend to link to existing user
                                } else {
                                    if (feedbackEl) {
                                        feedbackEl.style.display = 'none';
                                        feedbackEl.textContent = '';
                                    }
                                    if (submitBtn) submitBtn.disabled = false;
                                }
                            })
                            .catch(err => {
                                // ignore errors silently
                            });
                    }, 450);
                });
            }
        });
        document.addEventListener('DOMContentLoaded', function () {
            // Toggle and search for Assisted Request existing user
            (function () {
                const optionSel = document.getElementById('assist_option');
                const wrap = document.getElementById('assisted-existing-user');
                const search = document.getElementById('assisted_user_search');
                const list = document.getElementById('assisted_user_results');
                const hiddenId = document.getElementById('assisted_existing_user_id');
                const gFirst = document.getElementById('guardian_first_name');
                const gLast = document.getElementById('guardian_last_name');
                const gContact = document.getElementById('guardian_contact');
                // Infant auto-fill elements
                const infantWrap = document.getElementById('assisted_infant_select_wrap');
                const infantSelect = document.getElementById('assisted_infant_select');
                const iFirst = document.getElementById('infant_first_name');
                const iLast = document.getElementById('infant_last_name');
                const iDob = document.getElementById('infant_date_of_birth');
                const iSex = document.getElementById('infant_sex');
                const iWeight = document.getElementById('infant_weight');

                const infantsUrlBase = "{{ url('/admin/users') }}/"; // /admin/users/{id}/infants

                function clearInfantSelector() {
                    if (infantSelect) infantSelect.innerHTML = '';
                    if (infantWrap) infantWrap.style.display = 'none';
                }

                function applyInfant(i) {
                    if (!i) return;
                    if (iFirst) iFirst.value = i.first_name || '';
                    if (iLast) iLast.value = i.last_name || '';
                    if (iDob) iDob.value = (i.date_of_birth || '').substring(0,10);
                    if (iSex) {
                        const val = String(i.sex || '').toLowerCase() === 'male' ? 'Male' : (String(i.sex || '').toLowerCase() === 'female' ? 'Female' : '');
                        if (val) iSex.value = val;
                    }
                    if (iWeight) iWeight.value = i.birth_weight != null ? i.birth_weight : '';
                }

                async function loadInfantsForUser(userId) {
                    clearInfantSelector();
                    if (!userId) return;
                    try {
                        const r = await fetch(`${infantsUrlBase}${encodeURIComponent(userId)}/infants`, { headers: { 'Accept': 'application/json' } });
                        if (!r.ok) return;
                        const data = await r.json();
                        const infants = Array.isArray(data) ? data : (data.data || []);
                        if (!infants || infants.length === 0) {
                            // nothing to select; keep fields as-is
                            return;
                        }
                        if (infants.length === 1) {
                            applyInfant(infants[0]);
                            return; // no need to show selector
                        }
                        // Populate selector
                        if (!infantSelect) return;
                        infantSelect.innerHTML = '';
                        const ph = document.createElement('option');
                        ph.value = '';
                        ph.textContent = 'Select an infant to auto-fill…';
                        infantSelect.appendChild(ph);
                        infants.forEach(i => {
                            const opt = document.createElement('option');
                            opt.value = i.infant_id;
                            const name = `${i.first_name || ''} ${i.last_name || ''}`.trim() || 'Unnamed infant';
                            const dob = (i.date_of_birth || '').substring(0,10);
                            const sex = (String(i.sex || '')).toLowerCase();
                            const sexLabel = sex === 'male' ? 'Male' : (sex === 'female' ? 'Female' : '');
                            const wt = (i.birth_weight != null && i.birth_weight !== '') ? `${i.birth_weight} kg` : '';
                            opt.textContent = [name, dob, sexLabel, wt].filter(Boolean).join(' • ');
                            opt.dataset.payload = JSON.stringify(i);
                            infantSelect.appendChild(opt);
                        });
                        if (infantWrap) infantWrap.style.display = 'block';
                    } catch (e) {
                        // fail silently
                    }
                }

                if (infantSelect) {
                    infantSelect.addEventListener('change', function () {
                        const sel = infantSelect.options[infantSelect.selectedIndex];
                        const payload = sel && sel.dataset && sel.dataset.payload ? JSON.parse(sel.dataset.payload) : null;
                        if (payload) applyInfant(payload);
                    });
                }
                function toggle() {
                    if (!optionSel) return;
                    wrap.style.display = optionSel.value === 'record_to_existing_user' ? 'block' : 'none';
                    if (optionSel.value !== 'record_to_existing_user') {
                        list.innerHTML = '';
                        hiddenId.value = '';
                        clearInfantSelector();
                    }
                }
                if (optionSel) {
                    optionSel.addEventListener('change', toggle);
                    toggle();
                }
                function render(items) {
                    list.innerHTML = '';
                    (items || []).forEach(u => {
                        const b = document.createElement('button');
                        b.type = 'button';
                        b.className = 'list-group-item list-group-item-action';
                        const name = `${u.first_name || ''} ${u.last_name || ''}`.trim();
                        b.innerHTML = `<div class=\"d-flex justify-content-between\"><strong>${name || 'Unnamed user'}</strong><span class=\"badge bg-secondary\">${u.user_type || ''}</span></div><div class=\"small text-muted\">${u.contact_number || ''} • ${u.address || ''}</div>`;
                        b.addEventListener('click', () => {
                            hiddenId.value = u.user_id;
                            if (gFirst) gFirst.value = u.first_name || '';
                            if (gLast) gLast.value = u.last_name || '';
                            if (gContact) gContact.value = u.contact_number || '';
                            list.innerHTML = '';
                            search.value = name || u.contact_number || '';
                            // Load infants for this user and auto-fill when possible
                            loadInfantsForUser(u.user_id);
                        });
                        list.appendChild(b);
                    });
                }
                let t = null;
                async function doFetch() {
                    const q = (search.value || '').trim();
                    if (q.length < 2) { list.innerHTML=''; return; }
                    try {
                        const r = await fetch(`{{ route('admin.users.search') }}?q=${encodeURIComponent(q)}`, { headers: { 'Accept': 'application/json' } });
                        if (!r.ok) return;
                        const data = await r.json();
                        render((data && data.data) || []);
                    } catch (e) { /* ignore */ }
                }
                if (search) {
                    search.addEventListener('input', () => { if (t) clearTimeout(t); t = setTimeout(doFetch, 300); });
                }
            })();
        });
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('searchInput');
            const clearBtn = document.getElementById('clearSearch');
            const searchResults = document.getElementById('searchResults');

            if (!searchInput) return;

            // Helpers: extract searchable text from rows (guardian-only for exact), and full haystack for partial
            function getGuardianText(row) {
                const guardianCell = row.querySelector('[data-label="Guardian"], td:nth-child(1)');
                const guardian = guardianCell ? guardianCell.textContent : '';
                return (guardian || '').replace(/\s+/g, ' ').trim().toLowerCase();
            }
            function extractRowFields(row) {
                // Guardian + Infant + Contact for partial matching fallback
                const guardianCell = row.querySelector('[data-label="Guardian"], td:nth-child(1)');
                const infantCell = row.querySelector('[data-label="Infant"], td:nth-child(2)');
                const contactCell = row.querySelector('[data-label="Contact"], td:nth-child(3)');
                const guardian = guardianCell ? guardianCell.textContent.trim() : '';
                const infant = infantCell ? infantCell.textContent.trim() : '';
                const contact = contactCell ? contactCell.textContent.trim() : '';
                return (guardian + ' ' + infant + ' ' + contact).toLowerCase();
            }

            function extractCardFields(card) {
                // If a mobile card layout exists in the future, adapt selectors here
                // Currently, return empty string so no cards are matched
                return '';
            }

            function getActivePane() {
                return document.querySelector('.tab-pane.show.active') || document.querySelector('.tab-pane.active') || document.querySelector('.tab-pane');
            }

            function isVisible(el) {
                if (!el) return false;
                return !!(el.offsetWidth || el.offsetHeight || el.getClientRects().length);
            }

            function getAllTableRowsInActivePane() {
                const pane = getActivePane();
                if (!pane) return [];
                const rows = [];
                pane.querySelectorAll('table tbody tr').forEach(r => rows.push(r));
                return rows;
            }

            function getAllCardsInActivePane() {
                const pane = getActivePane();
                if (!pane) return [];
                // No dedicated card layout currently; return empty array
                return [];
            }

            function performSearch() {
                const term = (searchInput.value || '').trim().toLowerCase();
                let totalCount = 0;
                let visibleCount = 0;

                const rows = getAllTableRowsInActivePane();
                const cards = getAllCardsInActivePane();

                if (!term) {
                    rows.forEach(row => { row.hidden = false; row.style.display = ''; });
                    cards.forEach(card => { card.style.display = ''; });
                    const visibleRows = rows.filter(r => isVisible(r));
                    const visibleCards = cards.filter(c => isVisible(c));
                    totalCount = visibleRows.length + visibleCards.length;
                    visibleCount = totalCount;
                    clearBtn.style.display = 'none';
                    searchResults.textContent = '';
                    searchResults.classList.remove('text-danger');
                    return;
                }

                const visibleRows = rows.filter(r => isVisible(r));
                const visibleCards = cards.filter(c => isVisible(c));
                totalCount = visibleRows.length + visibleCards.length;

                // Exact-first by guardian, then fallback to partial across guardian+infant+contact
                const exactRows = [];
                const partialRows = [];
                visibleRows.forEach(row => {
                    const g = getGuardianText(row);
                    if (g && g === term) {
                        exactRows.push(row);
                        return;
                    }
                    const hay = extractRowFields(row);
                    if (hay.indexOf(term) !== -1) partialRows.push(row);
                });

                const showRows = exactRows.length > 0 ? exactRows : partialRows;
                const hideRows = new Set(visibleRows);
                showRows.forEach(r => hideRows.delete(r));

                // Force hide using !important to beat any table-row styles
                showRows.forEach(row => {
                    row.removeAttribute('hidden');
                    row.style.removeProperty('display');
                    row.style.setProperty('display', 'table-row');
                    visibleCount++;
                });
                hideRows.forEach(row => {
                    row.setAttribute('hidden', 'hidden');
                    row.style.setProperty('display', 'none', 'important');
                });

                visibleCards.forEach(card => {
                    const hay = extractCardFields(card);
                    if (hay.indexOf(term) !== -1) {
                        card.style.display = '';
                        visibleCount++;
                    } else {
                        card.style.display = 'none';
                    }
                });

                if (!term) {
                    clearBtn.style.display = 'none';
                    searchResults.textContent = '';
                    searchResults.classList.remove('text-danger');
                } else {
                    clearBtn.style.display = 'inline-block';
                    searchResults.textContent = `Showing ${visibleCount} of ${totalCount} results`;
                    if (visibleCount === 0) {
                        searchResults.textContent = 'No results found';
                        searchResults.classList.add('text-danger');
                    } else {
                        searchResults.classList.remove('text-danger');
                    }
                }
            }

            searchInput.addEventListener('input', performSearch);
            clearBtn.addEventListener('click', function () {
                searchInput.value = '';
                performSearch();
                searchInput.focus();
            });

            performSearch();
        });
    </script>

    <script>
        // Display SweetAlert for flash messages after redirects (success/warning/error)
        document.addEventListener('DOMContentLoaded', function () {
            try {
                const hasSwal = (typeof Swal !== 'undefined');
                @if ($errors->any())
                    if (hasSwal) {
                        let html = '';
                        @foreach ($errors->all() as $err)
                            html += `<div>• {{ addslashes($err) }}</div>`;
                        @endforeach
                        Swal.fire({
                            icon: 'error',
                            title: 'Please fix the following:',
                            html: html,
                            confirmButtonColor: '#dc3545'
                        });
                    }
                    // Reopen the Assisted modal so the admin can correct inputs
                    try {
                        const modalEl = document.getElementById('assistedRequestModal');
                        if (modalEl && window.bootstrap && bootstrap.Modal) {
                            const m = new bootstrap.Modal(modalEl);
                            m.show();
                        }
                    } catch (_) { }
                @endif
                @if(session('success'))
                    if (hasSwal) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: @json(session('success')),
                            confirmButtonColor: '#28a745'
                        });
                    }
                @endif
                @if(session('warning'))
                    if (hasSwal) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Notice',
                            text: @json(session('warning')),
                            confirmButtonColor: '#f59e0b'
                        });
                    }
                @endif
                @if(session('error'))
                    if (hasSwal) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: @json(session('error')),
                            confirmButtonColor: '#dc3545'
                        });
                    }
                @endif
                    } catch (_) { }
        });
    </script>

    <script>
        // Ensure CSRF token is available for fetch
        const csrfToken = '{{ csrf_token() }}';

        // Load SweetAlert2 script dynamically (fallback if not included)
        (function loadSwal() {
            if (typeof Swal === 'undefined') {
                const s = document.createElement('script');
                s.src = 'https://cdn.jsdelivr.net/npm/sweetalert2@11.7.27/dist/sweetalert2.all.min.js';
                s.defer = true;
                document.head.appendChild(s);
            }
        })();

        // Helper toast
        const swalToast = (icon, title = '') => {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: icon,
                    title: title,
                    showConfirmButton: false,
                    timer: 3000
                });
            } else {
                // fallback
                if (icon === 'success') alert(title);
                else alert(title || (icon === 'error' ? 'Error' : ''));
            }
        };

        // Initialize dispensing modals - auto-load inventory when modal opens
        document.addEventListener('DOMContentLoaded', function () {
            // Find all dispensing modals and attach event listeners
            const dispensingModals = document.querySelectorAll('[id^="dispensingModal"]');
            dispensingModals.forEach(function(modalEl) {
                modalEl.addEventListener('shown.bs.modal', function () {
                    // Extract request ID from modal ID (e.g., "dispensingModal123" -> 123)
                    const requestId = modalEl.id.replace('dispensingModal', '');
                    if (requestId) {
                        // Auto-load inventory since "pasteurized" is pre-selected
                        handleMilkTypeChange(requestId);
                    }
                });
            });
        });

        // Sanitize manual typing into volumeToDispense inputs to prevent browser auto-rounding/snapping
        // Keep as text inputs (inputmode=numeric) and allow only digits and one decimal point.
        document.addEventListener('input', function (e) {
            const el = e.target;
            if (!el || !el.id || !el.id.startsWith('volumeToDispense')) return;

            let v = String(el.value || '');
            // Remove commas and any characters except digits and dot
            v = v.replace(/,/g, '').replace(/[^0-9.]/g, '');
            // Allow only first dot
            const parts = v.split('.');
            if (parts.length > 2) {
                v = parts[0] + '.' + parts.slice(1).join('');
            }
            // Limit to two decimal places (optional)
            if (v.indexOf('.') !== -1) {
                const [intPart, decPart] = v.split('.');
                v = intPart + '.' + (decPart || '').slice(0, 2);
            }

            if (el.value !== v) {
                el.value = v;
            }
        });

        // Prevent non-numeric keys on volumeToDispense and block mouse wheel changes
        document.addEventListener('keydown', function (e) {
            const el = e.target;
            if (!el || !el.id || !el.id.startsWith('volumeToDispense')) return;

            // Allow navigation and control keys
            const allowedKeys = ['Backspace', 'Delete', 'ArrowLeft', 'ArrowRight', 'ArrowUp', 'ArrowDown', 'Tab', 'Enter', 'Home', 'End'];
            if (allowedKeys.indexOf(e.key) !== -1) return;

            // Allow one dot
            if (e.key === '.') {
                if ((el.value || '').indexOf('.') === -1) return;
                e.preventDefault();
                return;
            }

            // Allow digits
            if (/^[0-9]$/.test(e.key)) return;

            // Prevent anything else
            e.preventDefault();
        }, true);

        // Prevent mouse wheel from changing focused input value (some browsers change number inputs)
        document.addEventListener('wheel', function (e) {
            const el = document.activeElement;
            if (!el || !el.id || !el.id.startsWith('volumeToDispense')) return;
            e.preventDefault();
        }, { passive: false });

        // Sanitize paste into volumeToDispense
        document.addEventListener('paste', function (e) {
            const el = e.target;
            if (!el || !el.id || !el.id.startsWith('volumeToDispense')) return;
            e.preventDefault();
            const text = (e.clipboardData || window.clipboardData).getData('text') || '';
            let v = text.replace(/,/g, '').replace(/[^0-9.]/g, '');
            const parts = v.split('.');
            if (parts.length > 2) v = parts[0] + '.' + parts.slice(1).join('');
            if (v.indexOf('.') !== -1) {
                const [intPart, decPart] = v.split('.');
                v = intPart + '.' + (decPart || '').slice(0, 2);
            }
            el.value = v;
            // trigger input handlers
            el.dispatchEvent(new Event('input', { bubbles: true }));
        });

        // Input validation: for non-multiples of 10, show inline warning and require user to accept or apply rounded value
        function removeVolumeWarning(requestId) {
            const warn = document.getElementById('volumeWarning' + requestId);
            if (warn) warn.remove();
        }

        // Auto-apply rounding on blur and show a transient notice that value was considered as roundedDown
        function showVolumeNotice(requestId, original, roundedDown) {
            // remove existing notice
            const existing = document.getElementById('volumeNotice' + requestId);
            if (existing) existing.remove();

            const input = document.getElementById(`volumeToDispense${requestId}`);
            if (!input) return;

            const container = document.createElement('div');
            container.id = 'volumeNotice' + requestId;
            container.className = 'mt-2';
            container.innerHTML = `
                        <div class="alert alert-info d-flex align-items-center justify-content-between">
                            <div>
                                You entered <strong>${original}</strong> ml — recorded as <strong>${roundedDown}</strong> ml.
                            </div>
                            <div class="ms-3">
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="document.getElementById('volumeNotice${requestId}').remove()">Dismiss</button>
                            </div>
                        </div>
                    `;

            const parent = input.parentElement || input.closest('.mb-3');
            if (parent) parent.appendChild(container);

            // auto-remove after 4 seconds
            setTimeout(() => {
                const el = document.getElementById('volumeNotice' + requestId);
                if (el) el.remove();
            }, 4000);
        }

        document.addEventListener('blur', function (e) {
            const el = e.target;
            if (!el || !el.id) return;
            const isDispense = el.id.startsWith('volumeToDispense');
            const isRequested = el.id.startsWith('volumeRequested');
            const isAssisted = el.id === 'assistedVolumeToDispense';
            const isItemVolume = /^volume_\d+_\d+$/.test(el.id);
            if (!(isDispense || isRequested || isAssisted || isItemVolume)) return;

            const raw = String(el.value || '').trim();
            if (!raw) return;
            const num = parseFloat(raw.replace(/,/g, ''));
            if (isNaN(num) || num <= 0) return;

            if (num >= 10 && Math.round(num) % 10 !== 0) {
                const rounded = Math.round(num / 10) * 10;
                if (rounded <= 0) return;
                el.value = String(rounded);
                el.dispatchEvent(new Event('input', { bubbles: true }));
                if (isDispense) {
                    const requestId = el.id.replace('volumeToDispense', '');
                    showVolumeNotice(requestId, num, rounded);
                    updateSelectedVolume(requestId);
                } else if (isRequested) {
                    const requestId = el.id.replace('volumeRequested', '');
                    validateDispenseForm(requestId);
                } else if (isItemVolume) {
                    // id format: volume_{requestId}_{itemId}
                    const parts = el.id.split('_');
                    const requestId = parts[1];
                    updateSelectedVolume(requestId);
                    validateDispenseForm(requestId);
                } else if (isAssisted) {
                    assistedUpdateSelectedVolume();
                }
            }
        }, true);

        // Also snap on change to give immediate feedback
        document.addEventListener('change', function (e) {
            const el = e.target;
            if (!el || !el.id) return;
            const isDispense = el.id.startsWith('volumeToDispense');
            const isRequested = el.id.startsWith('volumeRequested');
            const isAssisted = el.id === 'assistedVolumeToDispense';
            const isItemVolume = /^volume_\d+_\d+$/.test(el.id);
            if (!(isDispense || isRequested || isAssisted || isItemVolume)) return;

            const raw = String(el.value || '').trim();
            if (!raw) return;
            const num = parseFloat(raw.replace(/,/g, ''));
            if (isNaN(num) || num <= 0) return;

            if (num >= 10 && Math.round(num) % 10 !== 0) {
                const rounded = Math.round(num / 10) * 10;
                if (rounded <= 0) return;
                el.value = String(rounded);
                el.dispatchEvent(new Event('input', { bubbles: true }));
                if (isDispense) {
                    const requestId = el.id.replace('volumeToDispense', '');
                    showVolumeNotice(requestId, num, rounded);
                    updateSelectedVolume(requestId);
                } else if (isRequested) {
                    const requestId = el.id.replace('volumeRequested', '');
                    validateDispenseForm(requestId);
                } else if (isItemVolume) {
                    const parts = el.id.split('_');
                    const requestId = parts[1];
                    updateSelectedVolume(requestId);
                    validateDispenseForm(requestId);
                } else if (isAssisted) {
                    assistedUpdateSelectedVolume();
                }
            }
        }, true);

        function viewPrescription(requestId) {
            const container = document.getElementById('prescriptionImageContainer' + requestId);
            const img = document.getElementById('prescriptionImage' + requestId);

            if (container.style.display === 'none') {
                // Show loading
                container.innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading prescription...</div>';
                container.style.display = 'block';

                // Fetch prescription image
                fetch(`{{ url('/admin/breastmilk-request') }}/${requestId}/prescription`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.error) {
                            container.innerHTML = '<div class="alert alert-danger">' + data.error + '</div>';
                        } else {
                            container.innerHTML = `
                                                                                                                                                                            <div class="d-flex flex-column align-items-center justify-content-center">
                                                                                                                                                                                <h6 class="mb-3">Prescription: ${data.filename}</h6>
                                                                                                                                                                                <div class="d-flex justify-content-center align-items-center" style="min-height: 400px;">
                                                                                                                                                                                    <img src="${data.image}" alt="Prescription" class="img-fluid rounded border" 
                                                                                                                                                                                        style="max-width:100%; max-height:70vh; object-fit:contain;">
                                                                                                                                                                                </div>
                                                                                                                                                                            </div>
                                                                                                                                                                        `;
                        }
                    })
                    .catch(error => {
                        container.innerHTML = '<div class="alert alert-danger">Failed to load prescription image.</div>';
                    });
            } else {
                container.style.display = 'none';
            }
        }

        // New minimal modal view: fetch prescription and display image or PDF
        function viewPrescriptionModal(requestId) {
            const containerId = 'prescriptionModalContent' + requestId;
            const container = document.getElementById(containerId);

            // show spinner while loading
            container.innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading prescription...</div>';

            fetch(`{{ url('/admin/breastmilk-request') }}/${requestId}/prescription`)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        container.innerHTML = '<div class="alert alert-danger">' + data.error + '</div>';
                        return;
                    }

                    // Build UI showing user details alongside the prescription image
                    const user = data.user || null;
                    let userHtml = '';
                    if (user) {
                        userHtml = `
                                    <div class="card p-3 mb-3" style="min-width:250px;">
                                        <h6 class="mb-2"><i class="fas fa-user"></i> Requester</h6>
                                        <p class="mb-1"><strong>Name:</strong> ${escapeHtml(user.full_name || '-')}</p>
                                        <p class="mb-1"><strong>Contact:</strong> ${escapeHtml(user.contact_number || '-')}</p>
                                        <p class="mb-0"><strong>Address:</strong> ${escapeHtml(user.address || '-')}</p>
                                    </div>
                                `;
                    }

                    const isPdf = typeof data.image === 'string' && /^data:application\/pdf/i.test(data.image);
                    const filenameSafe = escapeHtml(data.filename || 'Prescription');
                    let viewerHtml = '';
                    if (isPdf) {
                        viewerHtml = `
                                    <div class="ratio ratio-16x9 w-100">
                                        <iframe src="${data.image}" title="${filenameSafe}" style="border:1px solid #dee2e6; border-radius: .25rem;"></iframe>
                                    </div>
                                    <div class="mt-2 text-center">
                                        <a class="btn btn-sm btn-outline-secondary" href="${data.image}" download="${filenameSafe}"><i class="fas fa-download"></i> Download PDF</a>
                                    </div>
                                `;
                    } else {
                        viewerHtml = `
                                    <div class="d-flex justify-content-center align-items-center" style="min-height: 320px; width:100%;">
                                        <img src="${data.image}" alt="Prescription" class="img-fluid rounded border" style="max-width:100%; max-height:70vh; object-fit:contain;" />
                                    </div>
                                    <div class="mt-2 text-center">
                                        <a class="btn btn-sm btn-outline-secondary" href="${data.image}" download="${filenameSafe}"><i class="fas fa-download"></i> Download</a>
                                    </div>
                                `;
                    }

                    container.innerHTML = `
                                <div class="row">
                                    <div class="col-md-4 d-flex justify-content-center align-items-start">
                                        ${userHtml}
                                    </div>
                                    <div class="col-md-8 d-flex flex-column align-items-center">
                                        <h6 class="mb-3">Prescription: ${filenameSafe}</h6>
                                        ${viewerHtml}
                                    </div>
                                </div>
                            `;
                })
                .catch(err => {
                    container.innerHTML = '<div class="alert alert-danger">Failed to load prescription image.</div>';
                });
        }

        function loadInventory(requestId) {
            const milkType = document.getElementById('milkType' + requestId).value;
            const inventorySection = document.getElementById('inventorySection' + requestId);
            const loadingDiv = document.getElementById('loadingInventory' + requestId);
            const inventoryList = document.getElementById('inventoryList' + requestId);

            if (!milkType) {
                inventorySection.style.display = 'none';
                return;
            }

            // Show loading
            inventorySection.style.display = 'block';
            loadingDiv.style.display = 'block';
            inventoryList.innerHTML = '';

            // Fetch available inventory
            fetch(`{{ route('admin.request.inventory') }}?milk_type=${milkType}`)
                .then(response => response.json())
                .then(data => {
                    loadingDiv.style.display = 'none';

                    if (data.error) {
                        inventoryList.innerHTML = '<div class="alert alert-danger">' + data.error + '</div>';
                        return;
                    }

                    if (data.inventory.length === 0) {
                        inventoryList.innerHTML = '<div class="alert alert-warning">No ' + milkType + ' milk available in inventory.</div>';
                        return;
                    }

                    // Display inventory items
                    let html = '';
                    data.inventory.forEach(item => {
                        const itemId = milkType === 'unpasteurized' ? item.id : item.id;
                        html += `
                                                                                                                                                                        <div class="card mb-2">
                                                                                                                                                                            <div class="card-body p-2">
                                                                                                                                                                                <div class="form-check">
                                                                                                                                                                                    <input class="form-check-input" type="checkbox" 
                                                                                                                                                                                        id="item_${requestId}_${itemId}" 
                                                                                                                                                                                        onchange="toggleInventoryItem(${requestId}, ${itemId}, ${item.volume})">
                                                                                                                                                                                    <label class="form-check-label" for="item_${requestId}_${itemId}">
                                                                                                                                                                                        <small>
                                                                                                                                                                                            ${milkType === 'unpasteurized' ?
                                `<strong>Donation #${item.id}</strong><br>
                                                                                                                                                                                                 ${item.donor_name} - ${item.donation_type}<br>
                                                                                                                                                                                                 <span class="text-primary">${item.volume}ml</span> (${item.date} ${item.time})` :
                                `<strong>Batch ${item.batch_number}</strong><br>
                                                                                                                                                                                                 Pasteurized by: ${item.admin_name}<br>
                                                                                                                                                                                                 <span class="text-primary">${item.volume}ml available</span> of ${item.original_volume}ml (${item.pasteurized_date})`
                            }
                                                                                                                                                                                        </small>
                                                                                                                                                                                    </label>
                                                                                                                                                                                </div>
                                                                                                                                                                                <div id="volumeInput_${requestId}_${itemId}" style="display: none;" class="mt-2">
                                                                                                                                                                                    <label class="form-label">Volume to deduct (ml):</label>
                                                                                                                                                                                    <input type="number" class="form-control form-control-sm" 
                                                                                                                                                                                        id="volume_${requestId}_${itemId}" 
                                                                                                                                                                                        step="0.01" min="0.01" max="${item.volume}" 
                                                                                                                                                                                        value="${item.volume}"
                                                                                                                                                                                        onchange="updateSelectedVolume(${requestId})">
                                                                                                                                                                                </div>
                                                                                                                                                                            </div>
                                                                                                                                                                        </div>
                                                                                                                                                                    `;
                    });

                    inventoryList.innerHTML = html;
                })
                .catch(error => {
                    loadingDiv.style.display = 'none';
                    inventoryList.innerHTML = '<div class="alert alert-danger">Failed to load inventory.</div>';
                });
        }

        function toggleInventoryItem(requestId, itemId, maxVolume) {
            const checkbox = document.getElementById(`item_${requestId}_${itemId}`);
            const volumeDiv = document.getElementById(`volumeInput_${requestId}_${itemId}`);

            if (checkbox.checked) {
                volumeDiv.style.display = 'block';
            } else {
                volumeDiv.style.display = 'none';
            }

            updateSelectedVolume(requestId);
        }

        function updateSelectedVolume(requestId) {
            const checkboxes = document.querySelectorAll(`input[id^="item_${requestId}_"]:checked`);
            let totalVolume = 0;

            checkboxes.forEach(checkbox => {
                const itemId = checkbox.id.split('_')[2];
                const volumeInput = document.getElementById(`volume_${requestId}_${itemId}`);
                if (volumeInput && volumeInput.value) {
                    totalVolume += parseFloat(volumeInput.value);
                }
            });

            // Remove .00 from whole numbers
            const displayVolume = totalVolume % 1 === 0 ? Math.round(totalVolume) : totalVolume.toFixed(2).replace(/\.?0+$/, '');
            document.getElementById(`selectedVolume${requestId}`).textContent = displayVolume;

            // Validate the form
            validateDispenseForm(requestId);

            // Update form with selected items data
            updateFormData(requestId);
        }

        function validateDispenseForm(requestId) {
            const requestedVolume = parseFloat(document.getElementById(`volumeRequested${requestId}`).value || 0);
            const milkType = document.getElementById(`milkType${requestId}`).value;
            const approveBtn = document.getElementById(`approveBtn${requestId}`);

            // Calculate total selected volume
            let totalVolume = 0;
            const checkboxes = document.querySelectorAll(`input[id^="item_${requestId}_"]:checked`);
            checkboxes.forEach(checkbox => {
                const itemId = checkbox.id.split('_')[2];
                const volumeInput = document.getElementById(`volume_${requestId}_${itemId}`);
                if (volumeInput && volumeInput.value) {
                    totalVolume += parseFloat(volumeInput.value);
                }
            });

            // Check if all required fields are filled and valid
            const hasRequestedVolume = requestedVolume > 0;
            const hasMilkType = milkType !== '';
            const hasSelectedItems = totalVolume > 0;
            const hasSufficientVolume = totalVolume >= requestedVolume;

            if (hasRequestedVolume && hasMilkType && hasSelectedItems && hasSufficientVolume) {
                approveBtn.disabled = false;
            } else {
                approveBtn.disabled = true;
            }
        }

        function updateFormData(requestId) {
            const form = document.getElementById(`approveForm${requestId}`);
            const checkboxes = document.querySelectorAll(`input[id^="item_${requestId}_"]:checked`);

            // Remove existing hidden inputs
            const existingInputs = form.querySelectorAll('input[name^="selected_items"]');
            existingInputs.forEach(input => input.remove());

            // Add new hidden inputs for selected items
            checkboxes.forEach((checkbox, index) => {
                const itemId = checkbox.id.split('_')[2];
                const volumeInput = document.getElementById(`volume_${requestId}_${itemId}`);

                if (volumeInput && volumeInput.value) {
                    // Add item id
                    const idInput = document.createElement('input');
                    idInput.type = 'hidden';
                    idInput.name = `selected_items[${index}][id]`;
                    idInput.value = itemId;
                    form.appendChild(idInput);

                    // Add volume
                    const volumeHiddenInput = document.createElement('input');
                    volumeHiddenInput.type = 'hidden';
                    volumeHiddenInput.name = `selected_items[${index}][volume]`;
                    volumeHiddenInput.value = volumeInput.value;
                    form.appendChild(volumeHiddenInput);
                }
            });
        }

        // ---- Accept / Decline workflow helpers ----

        /**
         * Handle Accept action from View Modal
         * Shows the Approve/Dispense modal for full workflow
         */
        function handleAcceptFromViewModal(requestId) {
            const notesField = document.getElementById('viewModalNotes' + requestId);
            const notes = notesField ? notesField.value.trim() : '';

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Accept Request',
                    html: `
                                                                                            <p>To accept this request, you need to specify the dispensing details.</p>
                                                                                            <p class="text-muted">Click "Continue" to open the dispensing form.</p>
                                                                                        `,
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonText: 'Continue to Dispense Form',
                    cancelButtonText: 'Cancel',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Close the view modal
                        const viewModal = bootstrap.Modal.getInstance(document.getElementById('viewModal' + requestId));
                        if (viewModal) viewModal.hide();

                        // Store notes temporarily if provided
                        if (notes) {
                            const approveNotesField = document.querySelector(`#approveForm${requestId} textarea[name="admin_notes"]`);
                            if (approveNotesField) {
                                approveNotesField.value = notes;
                            }
                        }

                        // Open the approve modal
                        const approveModal = new bootstrap.Modal(document.getElementById('approveModal' + requestId));
                        approveModal.show();
                    }
                });
            } else {
                // Fallback without SweetAlert
                const viewModal = bootstrap.Modal.getInstance(document.getElementById('viewModal' + requestId));
                if (viewModal) viewModal.hide();

                if (notes) {
                    const approveNotesField = document.querySelector(`#approveForm${requestId} textarea[name="admin_notes"]`);
                    if (approveNotesField) {
                        approveNotesField.value = notes;
                    }
                }

                const approveModal = new bootstrap.Modal(document.getElementById('approveModal' + requestId));
                approveModal.show();
            }
        }

        /**
         * Handle Decline action from View Modal
         * Shows SweetAlert confirmation and processes the decline
         */
        function handleDeclineFromViewModal(requestId) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Decline Request',
                    html: `
                        <div class="text-start">
                            <p>Are you sure you want to decline this request?</p>
                            <p class="text-muted">This action cannot be undone.</p>
                        </div>
                    `,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Decline Request',
                    cancelButtonText: 'Cancel',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Show loading state
                        Swal.fire({
                            title: 'Processing...',
                            text: 'Declining request...',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        // Submit the decline request without notes
                        fetch(`{{ url('/admin/breastmilk-request') }}/${requestId}/decline`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            },
                            body: JSON.stringify({})
                        })
                            .then(response => response.json())
                            .then(data => {
                                if (data.error) {
                                    Swal.fire({
                                        title: 'Error',
                                        text: data.error,
                                        icon: 'error'
                                    });
                                    return;
                                }

                                Swal.fire({
                                    title: 'Success!',
                                    text: 'Request has been declined successfully.',
                                    icon: 'success',
                                    timer: 2000,
                                    showConfirmButton: false
                                }).then(() => {
                                    location.reload();
                                });
                            })
                            .catch(error => {
                                console.error(error);
                                Swal.fire({
                                    title: 'Error',
                                    text: 'Failed to decline request. Please try again.',
                                    icon: 'error'
                                });
                            });
                    }
                });
            } else {
                // Fallback without SweetAlert
                const reason = prompt('Please provide a reason for declining this request:', notes);
                if (!reason || !reason.trim()) {
                    alert('Please provide a reason for declining.');
                    return;
                }

                if (!confirm('Are you sure you want to decline this request?')) {
                    return;
                }

                fetch(`{{ url('/admin/breastmilk-request') }}/${requestId}/decline`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ admin_notes: reason.trim() })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.error) {
                            alert('Error: ' + data.error);
                            return;
                        }
                        alert('Request has been declined successfully.');
                        location.reload();
                    })
                    .catch(error => {
                        console.error(error);
                        alert('Failed to decline request. Please try again.');
                    });
            }
        }

        // ---- Legacy functions (kept for compatibility) ----

        function confirmAccept(requestId) {
            // Read requested volume and milk type from the accept section inputs
            const volumeEl = document.getElementById('volumeRequested' + requestId);
            const typeEl = document.getElementById('milkType' + requestId);
            const volumeRequested = volumeEl ? parseFloat(volumeEl.value) : 0;
            const milkType = typeEl ? typeEl.value : '';

            if (!volumeRequested || volumeRequested <= 0) {
                alert('Please enter a valid volume to dispense.');
                return;
            }
            if (!milkType) {
                alert('Please select a milk type.');
                return;
            }

            // Collect selected inventory items and volumes
            const selectedCheckboxes = Array.from(document.querySelectorAll(`input[id^="item_${requestId}_"]:checked`));
            const selectedItems = [];
            let totalSelectedVolume = 0;

            selectedCheckboxes.forEach(cb => {
                const parts = cb.id.split('_');
                const itemId = parts[2];
                const volumeInput = document.getElementById(`volume_${requestId}_${itemId}`);
                const vol = volumeInput && volumeInput.value ? parseFloat(volumeInput.value) : 0;
                if (vol > 0) {
                    selectedItems.push({ id: itemId, volume: vol });
                    totalSelectedVolume += vol;
                }
            });

            if (selectedItems.length === 0) {
                alert('Please select at least one inventory item to deduct from.');
                return;
            }

            if (totalSelectedVolume < volumeRequested) {
                alert('Selected inventory volume is less than the requested dispense volume. Please adjust selections.');
                return;
            }

            // Use SweetAlert2 for confirmation and loading
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Confirm Accept',
                    text: 'Are you sure you want to accept and record this milk dispensing transaction?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, accept',
                    reverseButtons: true,
                    preConfirm: () => {
                        return fetch(`{{ url('/admin/breastmilk-request') }}/${requestId}/approve`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            },
                            body: JSON.stringify({ volume_requested: volumeRequested, milk_type: milkType, selected_items: selectedItems })
                        }).then(response => response.json()).catch(err => {
                            Swal.showValidationMessage('Network error');
                            throw err;
                        });
                    }
                }).then(result => {
                    if (result.isConfirmed && result.value) {
                        if (result.value.error) {
                            if (typeof Swal !== 'undefined') Swal.fire('Error', result.value.error, 'error');
                            else alert(result.value.error);
                            return;
                        }
                        swalToast('success', 'Request accepted and milk volume successfully recorded.');
                        location.reload();
                    }
                });
            } else {
                if (!confirm('Are you sure you want to accept and record this milk dispensing transaction?')) return;
                // fallback to previous fetch
                fetch(`{{ url('/admin/breastmilk-request') }}/${requestId}/approve`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ volume_requested: volumeRequested, milk_type: milkType, selected_items: selectedItems })
                })
                    .then(resp => resp.json())
                    .then(data => {
                        if (data.error) {
                            alert(data.error);
                            return;
                        }
                        alert('Request accepted and milk volume successfully recorded.');
                        location.reload();
                    })
                    .catch(err => {
                        console.error(err);
                        alert('Failed to accept request.');
                    });
            }
        }

        function confirmDecline(requestId) {
            const reasonEl = document.getElementById('declineReason' + requestId);
            const reason = reasonEl ? reasonEl.value.trim() : '';
            if (!reason) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire('Missing reason', 'Please provide a reason for declining.', 'warning');
                } else {
                    alert('Please provide a reason for declining.');
                }
                return;
            }
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Confirm Decline',
                    text: 'Are you sure you want to decline this request?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, decline',
                    reverseButtons: true,
                    preConfirm: () => {
                        return fetch(`{{ url('/admin/breastmilk-request') }}/${requestId}/decline`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            },
                            body: JSON.stringify({ admin_notes: reason })
                        }).then(response => response.json()).catch(err => {
                            Swal.showValidationMessage('Network error');
                            throw err;
                        });
                    }
                }).then(result => {
                    if (result.isConfirmed && result.value) {
                        if (result.value.error) {
                            Swal.fire('Error', result.value.error, 'error');
                            return;
                        }
                        swalToast('success', 'Request has been declined.');
                        location.reload();
                    }
                });
            } else {
                if (!confirm('Are you sure you want to decline this request?')) return;
                fetch(`{{ url('/admin/breastmilk-request') }}/${requestId}/decline`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ admin_notes: reason })
                })
                    .then(resp => resp.json())
                    .then(data => {
                        if (data.error) {
                            alert(data.error);
                            return;
                        }
                        alert('Request has been declined.');
                        location.reload();
                    })
                    .catch(err => {
                        console.error(err);
                        alert('Failed to decline request.');
                    });
            }
        }

        // Archive and restore functionality removed per requirements

        // New functions for the dispensing modal

        /**
         * Handle milk type change - load inventory based on selection
         */
        function handleMilkTypeChange(requestId) {
            const milkTypeSelect = document.getElementById(`milkTypeSelect${requestId}`);
            const milkType = milkTypeSelect.value;
            const inventoryContainer = document.getElementById(`inventoryContainer${requestId}`);
            const inventoryList = document.getElementById(`inventoryList${requestId}`);
            const loadingIndicator = document.getElementById(`loadingInventory${requestId}`);

            if (!milkType) {
                inventoryContainer.style.display = 'none';
                return;
            }

            // Show container and loading
            inventoryContainer.style.display = 'block';
            loadingIndicator.style.display = 'block';
            inventoryList.innerHTML = '';

            // Fetch available inventory
            fetch(`{{ route('admin.request.inventory') }}?type=${milkType}`, {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            })
                .then(response => response.json())
                .then(data => {
                    loadingIndicator.style.display = 'none';

                    if (data.error) {
                        inventoryList.innerHTML = `<div class="alert alert-danger">${data.error}</div>`;
                        return;
                    }

                    if (milkType === 'pasteurized') {
                        displayPasteurizedInventory(requestId, data.batches || []);
                    }

                    // Show volume tracker
                    document.getElementById(`volumeTracker${requestId}`).style.display = 'block';
                })
                .catch(error => {
                    loadingIndicator.style.display = 'none';
                    console.error('Error fetching inventory:', error);
                    inventoryList.innerHTML = '<div class="alert alert-danger">Failed to load inventory</div>';
                });
        }

        /**
         * Display pasteurized batch inventory
         */
        function displayPasteurizedInventory(requestId, batches) {
            const inventoryList = document.getElementById(`inventoryList${requestId}`);

            if (!batches || batches.length === 0) {
                inventoryList.innerHTML = '<div class="alert alert-warning">No pasteurized batches available</div>';
                return;
            }

            let html = '';
            batches.forEach(batch => {
                html += `
                    <div class="card mb-2">
                        <div class="card-body p-2">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" 
                                    id="batch_${requestId}_${batch.batch_id}" 
                                    value="${batch.batch_id}"
                                    data-volume="${batch.available_volume}"
                                    onchange="updateSelectedVolume(${requestId})">
                                <label class="form-check-label" for="batch_${requestId}_${batch.batch_id}">
                                    <small>
                                        <strong>Batch #${batch.batch_number}</strong><br>
                                        <span class="text-primary">${batch.available_volume} ml available</span><br>
                                        <span class="text-muted">Date: ${batch.date_pasteurized}</span>
                                    </small>
                                </label>
                            </div>
                        </div>
                    </div>
                `;
            });
            html += '<div class="mt-2"><small class="text-muted"><i class="fas fa-info-circle"></i> Select batches in order. Volume will be automatically deducted based on your input above.</small></div>';
            inventoryList.innerHTML = html;
        }

        /**
         * Display unpasteurized donation inventory
         */
        function displayUnpasteurizedInventory(requestId, donations) {
            const inventoryList = document.getElementById(`inventoryList${requestId}`);

            if (!donations || donations.length === 0) {
                inventoryList.innerHTML = '<div class="alert alert-warning">No unpasteurized donations available</div>';
                return;
            }

            let html = '';
            donations.forEach(donation => {
                const donorName = donation.donor_name || 'Anonymous';
                html += `
                    <div class="card mb-2">
                        <div class="card-body p-2">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" 
                                    id="donation_${requestId}_${donation.breastmilk_donation_id}" 
                                    value="${donation.breastmilk_donation_id}"
                                    data-volume="${donation.available_volume}"
                                    onchange="updateSelectedVolume(${requestId})">
                                <label class="form-check-label" for="donation_${requestId}_${donation.breastmilk_donation_id}">
                                    <small>
                                        <strong>Donation #${donation.breastmilk_donation_id}</strong><br>
                                        <span class="text-muted">Donor: ${donorName}</span><br>
                                        <span class="text-primary">${donation.available_volume} ml available</span><br>
                                        <span class="text-muted">Date: ${donation.donation_date}</span>
                                    </small>
                                </label>
                            </div>
                        </div>
                    </div>
                `;
            });
            html += '<div class="mt-2"><small class="text-muted"><i class="fas fa-info-circle"></i> Select donations in order. Volume will be automatically deducted based on your input above.</small></div>';
            inventoryList.innerHTML = html;
        }

        /**
         * Update the selected volume tracker - calculate automatic distribution
         */
        function updateSelectedVolume(requestId) {
            const milkTypeSelect = document.getElementById(`milkTypeSelect${requestId}`);
            const milkType = milkTypeSelect.value;
            const volumeRequired = parseFloat(document.getElementById(`volumeToDispense${requestId}`).value) || 0;

            let totalAvailable = 0;
            let selectedCheckboxes = [];

            if (milkType === 'pasteurized') {
                // Get all checked batch checkboxes
                selectedCheckboxes = Array.from(document.querySelectorAll(`input[id^="batch_${requestId}_"]:checked`));
            } else if (milkType === 'unpasteurized') {
                // Get all checked donation checkboxes
                selectedCheckboxes = Array.from(document.querySelectorAll(`input[id^="donation_${requestId}_"]:checked`));
            }

            // Calculate total available from selected sources
            selectedCheckboxes.forEach(checkbox => {
                const available = parseFloat(checkbox.dataset.volume) || 0;
                totalAvailable += available;
            });

            // Update display
            const displaySelected = totalAvailable % 1 === 0 ? Math.round(totalAvailable) : totalAvailable.toFixed(2).replace(/\.?0+$/, '');
            const displayRequired = volumeRequired % 1 === 0 ? Math.round(volumeRequired) : volumeRequired.toFixed(2).replace(/\.?0+$/, '');
            document.getElementById(`totalSelected${requestId}`).textContent = displaySelected;
            document.getElementById(`volumeRequired${requestId}`).textContent = displayRequired;
        }

        /**
         * Handle dispense action
         */
        function handleDispense(requestId) {
            const volumeToDispense = parseFloat(document.getElementById(`volumeToDispense${requestId}`).value);
            const milkType = document.getElementById(`milkTypeSelect${requestId}`).value;
            const adminNotes = document.getElementById(`adminNotes${requestId}`).value;

            // Validation
            if (!volumeToDispense || volumeToDispense <= 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Invalid Volume',
                    text: 'Please enter a valid volume to dispense.',
                    confirmButtonColor: '#3085d6'
                });
                return;
            }

            if (!milkType) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Missing Milk Type',
                    text: 'Please select a milk type.',
                    confirmButtonColor: '#3085d6'
                });
                return;
            }

            // Collect selected sources and automatically distribute volume
            let selectedSources = [];
            let selectedCheckboxes = [];
            let totalAvailable = 0;

            if (milkType === 'pasteurized') {
                selectedCheckboxes = Array.from(document.querySelectorAll(`input[id^="batch_${requestId}_"]:checked`));

                if (selectedCheckboxes.length === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'No Batch Selected',
                        text: 'Please select at least one pasteurized batch.',
                        confirmButtonColor: '#3085d6'
                    });
                    return;
                }
            } else if (milkType === 'unpasteurized') {
                selectedCheckboxes = Array.from(document.querySelectorAll(`input[id^="donation_${requestId}_"]:checked`));

                if (selectedCheckboxes.length === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'No Donation Selected',
                        text: 'Please select at least one unpasteurized donation.',
                        confirmButtonColor: '#3085d6'
                    });
                    return;
                }
            }

            // Calculate total available volume
            selectedCheckboxes.forEach(checkbox => {
                totalAvailable += parseFloat(checkbox.dataset.volume) || 0;
            });

            // Check if we have enough volume
            if (totalAvailable < volumeToDispense) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Insufficient Volume',
                    text: `Selected bags have ${totalAvailable.toFixed(2)} ml total, but you need ${volumeToDispense} ml. Please select more bags.`,
                    confirmButtonColor: '#3085d6'
                });
                return;
            }

            // Automatically distribute the volume across selected bags in order
            let remainingVolume = volumeToDispense;
            selectedCheckboxes.forEach(checkbox => {
                if (remainingVolume <= 0) return;
                
                const availableVolume = parseFloat(checkbox.dataset.volume) || 0;
                const volumeToTake = Math.min(remainingVolume, availableVolume);
                
                if (volumeToTake > 0) {
                    selectedSources.push({
                        type: milkType,
                        id: checkbox.value,
                        volume: volumeToTake
                    });
                    remainingVolume -= volumeToTake;
                }
            });

            // Validate that we collected the sources
            if (selectedSources.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'No Sources Selected',
                    text: 'Please select at least one source.',
                    confirmButtonColor: '#3085d6'
                });
                return;
            }

            // Confirm and dispense
            const displayVolumeToDispense = volumeToDispense % 1 === 0 ? Math.round(volumeToDispense) : volumeToDispense.toFixed(2).replace(/\.?0+$/, '');
            Swal.fire({
                title: 'Confirm Dispensing',
                html: `
                                                                                <p>Are you sure you want to dispense <strong>${displayVolumeToDispense} ml</strong> of <strong>${milkType}</strong> breastmilk?</p>
                                                                                <p class="text-muted mb-0">This action cannot be undone.</p>
                                                                            `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-check"></i> Yes, Dispense',
                cancelButtonText: '<i class="fas fa-times"></i> Cancel',
                reverseButtons: true,
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    return fetch(`{{ url('/admin/breastmilk-request') }}/${requestId}/dispense`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            volume_dispensed: volumeToDispense,
                            milk_type: milkType,
                            sources: selectedSources,
                            dispensing_notes: adminNotes
                        })
                    })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.json();
                        })
                        .catch(error => {
                            Swal.showValidationMessage(`Request failed: ${error}`);
                        });
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                if (result.isConfirmed) {
                    if (result.value.error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Dispensing Failed',
                            text: result.value.error,
                            confirmButtonColor: '#3085d6'
                        });
                    } else {
                        Swal.fire({
                            icon: 'success',
                            title: 'Successfully Dispensed!',
                            text: result.value.message || 'Breastmilk has been dispensed successfully.',
                            confirmButtonColor: '#28a745'
                        }).then(() => {
                            location.reload();
                        });
                    }
                }
            });
        }

        /**
         * Handle reject action
         */
        function handleReject(requestId) {
            // Close the dispensing modal first to prevent backdrop interference
            const modal = bootstrap.Modal.getInstance(document.getElementById(`dispensingModal${requestId}`));
            if (modal) {
                modal.hide();
            }

            // Wait a moment for the modal to close before showing SweetAlert
            setTimeout(() => {
                Swal.fire({
                    title: 'Reject Request',
                    text: 'Are you sure you want to reject this request? This action cannot be undone.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="fas fa-ban"></i> Reject Request',
                    cancelButtonText: '<i class="fas fa-times"></i> Cancel',
                    reverseButtons: true,
                    showLoaderOnConfirm: true,
                    preConfirm: () => {
                        return fetch(`{{ url('/admin/breastmilk-request') }}/${requestId}/reject`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({})
                        })
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error('Network response was not ok');
                                }
                                return response.json();
                            })
                            .catch(error => {
                                Swal.showValidationMessage(`Request failed: ${error}`);
                            });
                    },
                    allowOutsideClick: () => !Swal.isLoading()
                }).then((result) => {
                    if (result.isConfirmed) {
                        if (result.value.error) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Rejection Failed',
                                text: result.value.error,
                                confirmButtonColor: '#3085d6'
                            });
                        } else {
                            Swal.fire({
                                icon: 'success',
                                title: 'Request Rejected',
                                text: result.value.message || 'The request has been rejected.',
                                confirmButtonColor: '#28a745'
                            }).then(() => {
                                location.reload();
                            });
                        }
                    }
                });
            }, 300); // Wait 300ms for modal to close
        }

        // --- Assisted request inventory helpers ---
        document.addEventListener('DOMContentLoaded', function () {
            const dispenseNowCheckbox = document.getElementById('dispense_now_checkbox');
            const milkTypeSelect = document.getElementById('milk_type');
            if (dispenseNowCheckbox) {
                dispenseNowCheckbox.addEventListener('change', function () {
                    const section = document.getElementById('assistedInventorySection');
                    if (this.checked) {
                        section.style.display = 'block';
                        // load inventory for currently selected milk type
                        assistedLoadInventory();
                    } else {
                        section.style.display = 'none';
                        document.getElementById('assistedInventoryList').innerHTML = '';
                        document.getElementById('selected_sources_json').value = '';
                    }
                });
            }

            if (milkTypeSelect) {
                milkTypeSelect.addEventListener('change', function () {
                    if (dispenseNowCheckbox && dispenseNowCheckbox.checked) {
                        assistedLoadInventory();
                    }
                });
            }

            // On initial load, if checkbox is checked, show inventory section when milk type is selected
            (function initAssistedInventory() {
                const section = document.getElementById('assistedInventorySection');
                if (!section) return;
                if (dispenseNowCheckbox && dispenseNowCheckbox.checked) {
                    section.style.display = 'block';
                    if (milkTypeSelect && milkTypeSelect.value) {
                        assistedLoadInventory();
                    }
                } else {
                    section.style.display = 'none';
                }
            })();

            // Prepare selected_sources_json before form submit
            const assistedForm = document.getElementById('assistedRequestForm');
            if (assistedForm) {
                assistedForm.addEventListener('submit', function (e) {
                    const dispenseNow = document.getElementById('dispense_now_checkbox').checked;
                    if (!dispenseNow) return; // nothing to do
                    
                    const milkType = (document.getElementById('milk_type') || {}).value;
                    const volumeStr = (document.getElementById('assistedVolumeToDispense') || {}).value || '';
                    const volume = parseFloat(volumeStr);
                    
                    if (!milkType) {
                        e.preventDefault();
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Missing Milk Type',
                                text: 'Please select a milk type.',
                                confirmButtonColor: '#3085d6'
                            });
                        } else {
                            alert('Please select a milk type.');
                        }
                        return false;
                    }
                    if (!volume || volume <= 0) {
                        e.preventDefault();
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Invalid Volume',
                                text: 'Please enter a valid volume to dispense.',
                                confirmButtonColor: '#3085d6'
                            });
                        } else {
                            alert('Please enter a valid volume to dispense.');
                        }
                        return false;
                    }
                    
                    // Get all checked checkboxes
                    let selectedCheckboxes = [];
                    if (milkType === 'unpasteurized') {
                        selectedCheckboxes = Array.from(document.querySelectorAll('input[id^="assisted_donation_"]:checked'));
                    } else {
                        selectedCheckboxes = Array.from(document.querySelectorAll('input[id^="assisted_batch_"]:checked'));
                    }
                    
                    if (selectedCheckboxes.length === 0) {
                        e.preventDefault();
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'warning',
                                title: 'No Source Selected',
                                text: 'Please select at least one source from inventory.',
                                confirmButtonColor: '#3085d6'
                            });
                        } else {
                            alert('Please select at least one source from inventory.');
                        }
                        return false;
                    }
                    
                    // Calculate total available
                    let totalAvailable = 0;
                    selectedCheckboxes.forEach(cb => {
                        totalAvailable += parseFloat(cb.dataset.volume || '0') || 0;
                    });
                    
                    if (volume > totalAvailable + 1e-6) {
                        e.preventDefault();
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Insufficient Volume',
                                text: `Selected bags have ${totalAvailable.toFixed(2)} ml total, but you need ${volume} ml. Please select more bags.`,
                                confirmButtonColor: '#3085d6'
                            });
                        } else {
                            alert(`Selected bags have ${totalAvailable.toFixed(2)} ml total, but you need ${volume} ml. Please select more bags.`);
                        }
                        return false;
                    }
                    
                    // Automatically distribute volume across selected bags in order
                    const sources = [];
                    let remainingVolume = volume;
                    selectedCheckboxes.forEach(cb => {
                        if (remainingVolume <= 0) return;
                        const availableVolume = parseFloat(cb.dataset.volume || '0') || 0;
                        const volumeToTake = Math.min(remainingVolume, availableVolume);
                        if (volumeToTake > 0) {
                            sources.push({ type: milkType, id: cb.value, volume: volumeToTake });
                            remainingVolume -= volumeToTake;
                        }
                    });
                    
                    document.getElementById('selected_sources_json').value = JSON.stringify(sources);
                });
            }
        });
        function assistedLoadInventory() {
            const milkType = document.getElementById('milk_type').value;
            const loading = document.getElementById('assistedInventoryLoading');
            const list = document.getElementById('assistedInventoryList');
            const tracker = document.getElementById('assistedVolumeTracker');
            list.innerHTML = '';
            if (!milkType) return;
            loading.style.display = 'block';
            tracker.style.display = 'block';
            fetch(`{{ route('admin.request.inventory') }}?type=${milkType}`, { headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' } })
                .then(r => r.json())
                .then(data => {
                    loading.style.display = 'none';
                    if (data.error) {
                        list.innerHTML = `<div class="alert alert-danger">${data.error}</div>`;
                        return;
                    }

                    if (milkType === 'unpasteurized') {
                        const donations = data.donations || [];
                        if (donations.length === 0) {
                            list.innerHTML = '<div class="alert alert-warning">No unpasteurized donations available.</div>';
                            return;
                        }
                        let html = '';
                        donations.forEach(d => {
                            const avail = d.available_volume || 0;
                            html += `
                                <div class="card mb-2">
                                    <div class="card-body p-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="assisted_donation_${d.breastmilk_donation_id}" value="${d.breastmilk_donation_id}" data-volume="${avail}" onchange="assistedUpdateSelectedVolume()">
                                            <label class="form-check-label" for="assisted_donation_${d.breastmilk_donation_id}">
                                                <small>
                                                    <strong>Donation #${d.breastmilk_donation_id}</strong><br>
                                                    <span class="text-muted">Donor: ${d.donor_name || 'Anonymous'}</span><br>
                                                    <span class="text-primary">${avail} ml available</span>
                                                </small>
                                            </label>
                                        </div>
                                    </div>
                                </div>`;
                        });
                        list.innerHTML = html;
                    } else {
                        const batches = data.batches || [];
                        if (batches.length === 0) {
                            list.innerHTML = '<div class="alert alert-warning">No pasteurized batches available.</div>';
                            return;
                        }
                        let html = '';
                        batches.forEach(b => {
                            const avail = b.available_volume || 0;
                            html += `
                                <div class="card mb-2">
                                    <div class="card-body p-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="assisted_batch_${b.batch_id}" value="${b.batch_id}" data-volume="${avail}" onchange="assistedUpdateSelectedVolume()">
                                            <label class="form-check-label" for="assisted_batch_${b.batch_id}">
                                                <small>
                                                    <strong>Batch #${b.batch_number}</strong><br>
                                                    <span class="text-primary">${avail} ml available</span><br>
                                                    <span class="text-muted">Date: ${b.date_pasteurized}</span>
                                                </small>
                                            </label>
                                        </div>
                                    </div>
                                </div>`;
                        });
                        list.innerHTML = html;
                    }

                    assistedUpdateSelectedVolume();
                })
                .catch(err => {
                    loading.style.display = 'none';
                    list.innerHTML = '<div class="alert alert-danger">Failed to load inventory.</div>';
                });
        }

        function assistedUpdateSelectedVolume() {
            const milkType = (document.getElementById('milk_type') || {}).value;
            const volumeRequired = parseFloat((document.getElementById('assistedVolumeToDispense') || {}).value || '0') || 0;
            let totalAvailable = 0;
            
            // Get all checked checkboxes and sum available volumes
            if (milkType === 'unpasteurized') {
                const checked = document.querySelectorAll('input[id^="assisted_donation_"]:checked');
                checked.forEach(cb => {
                    totalAvailable += parseFloat(cb.dataset.volume || '0') || 0;
                });
            } else if (milkType === 'pasteurized') {
                const checked = document.querySelectorAll('input[id^="assisted_batch_"]:checked');
                checked.forEach(cb => {
                    totalAvailable += parseFloat(cb.dataset.volume || '0') || 0;
                });
            }
            
            const selEl = document.getElementById('assistedTotalSelected');
            const reqEl = document.getElementById('assistedVolumeRequired');
            if (selEl && reqEl) {
                const displaySel = totalAvailable % 1 === 0 ? Math.round(totalAvailable) : totalAvailable.toFixed(2).replace(/\.?0+$/, '');
                const displayReq = volumeRequired % 1 === 0 ? Math.round(volumeRequired) : volumeRequired.toFixed(2).replace(/\.?0+$/, '');
                selEl.textContent = displaySel;
                reqEl.textContent = displayReq;
            }
            const tracker = document.getElementById('assistedVolumeTracker');
            if (tracker) tracker.style.display = (milkType ? 'block' : 'none');
        }

        // Safe HTML escape helper (guarded define)
        if (typeof window.escapeHtml !== 'function') {
            window.escapeHtml = function (text) {
                if (text === null || text === undefined) return '';
                const div = document.createElement('div');
                div.textContent = String(text);
                return div.innerHTML;
            };
        }

        // Auto-capitalize first letter of each word
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.auto-capitalize-words').forEach(function(input) {
                input.addEventListener('input', function(e) {
                    const cursorPosition = e.target.selectionStart;
                    const originalLength = e.target.value.length;
                    
                    // Capitalize first letter of each word
                    e.target.value = e.target.value.replace(/\b\w/g, function(char) {
                        return char.toUpperCase();
                    });
                    
                    // Restore cursor position
                    const newLength = e.target.value.length;
                    const newPosition = cursorPosition + (newLength - originalLength);
                    e.target.setSelectionRange(newPosition, newPosition);
                });
            });
        });
    </script>
@endsection