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

        <!-- Page Header with Action Button -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0"><i class="fas fa-heart"></i> Breastmilk Request Management</h4>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#assistedRequestModal">
                <i class="fas fa-user-plus"></i> Assist Walk-in Request
            </button>
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
            <li class="nav-item" role="presentation">
                <a class="nav-link{{ request()->get('status') == 'archived' ? ' active' : '' }}" href="?status=archived" id="archived-tab" role="tab">
                    Archived <span class="badge bg-secondary">{{ $archivedCount ?? 0 }}</span>
                </a>
            </li>
        </ul>

        {{-- Search Input Below Tabs --}}
        <div class="mb-3">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0">
                    <i class="bi bi-search"></i>
                </span>
                <input type="text" 
                       class="form-control border-start-0 ps-0" 
                       id="searchInput" 
                       placeholder="Search by guardian name, infant name, contact..."
                       aria-label="Search requests">
                <button class="btn btn-outline-secondary" type="button" id="clearSearch" style="display: none;">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
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
                                                    <strong>{{ $request->infant->first_name }}
                                                        {{ $request->infant->last_name }}{{ $request->infant->suffix ? ' ' . $request->infant->suffix : '' }}</strong>
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
                                                        {{ $request->availability->formatted_date }} @if(!empty($request->availability->formatted_time)) {{ $request->availability->formatted_time }}@endif
                                                    @else
                                                        {{ Carbon\Carbon::parse($request->request_date)->format('M d, Y') }} {{ Carbon\Carbon::parse($request->request_time)->format('g:i A') }}
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
                                                <td data-label="Infant">
                                                    <strong>{{ $request->infant->first_name }}
                                                        {{ $request->infant->last_name }}{{ $request->infant->suffix ? ' ' . $request->infant->suffix : '' }}</strong><br>
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
                                                    <strong>{{ $request->volume_dispensed ?? $request->volume_requested }}
                                                        ml</strong>
                                                </td>
                                                <td data-label="Type">
                                                    @if($request->dispensedMilk && $request->dispensedMilk->milk_type)
                                                        <span
                                                            class="badge bg-{{ $request->dispensedMilk->milk_type === 'pasteurized' ? 'success' : 'warning' }}">
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
                                                    <button class="admin-review-btn btn-sm" data-bs-toggle="modal"
                                                        data-bs-target="#viewModal{{ $request->breastmilk_request_id }}">
                                                        Review
                                                    </button>
                                                    <button class="btn btn-sm btn-danger ms-1" onclick="archiveRequest({{ $request->breastmilk_request_id }})" title="Archive request" aria-label="Archive request">Archive</button>
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
                                                    <strong>{{ $request->infant->first_name }}
                                                        {{ $request->infant->last_name }}{{ $request->infant->suffix ? ' ' . $request->infant->suffix : '' }}</strong><br>
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
                                                    <button class="btn btn-sm btn-danger ms-1" onclick="archiveRequest({{ $request->breastmilk_request_id }})" title="Archive request" aria-label="Archive request">Archive</button>
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
                                        <p class="mb-1"><strong>Name:</strong> {{ $request->infant->first_name }}
                                            {{ $request->infant->last_name }}{{ $request->infant->suffix ? ' ' . $request->infant->suffix : '' }}
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
                                            <input type="text" inputmode="numeric" pattern="[0-9]*([.][0-9]+)?" class="form-control"
                                                id="volumeToDispense{{ $request->breastmilk_request_id }}"
                                                name="volume_dispensed" required
                                                oninput="updateSelectedVolume({{ $request->breastmilk_request_id }})">
                                            <div class="form-text">Enter the amount of milk to dispense</div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="milkTypeSelect{{ $request->breastmilk_request_id }}" class="form-label">
                                                <i class="fas fa-vial"></i> Breastmilk Type <span class="text-danger">*</span>
                                            </label>
                                            <select class="form-select" id="milkTypeSelect{{ $request->breastmilk_request_id }}"
                                                name="milk_type" required
                                                onchange="handleMilkTypeChange({{ $request->breastmilk_request_id }})">
                                                <option value="">Select milk type</option>
                                                <option value="unpasteurized">Unpasteurized Breastmilk</option>
                                                <option value="pasteurized">Pasteurized Breastmilk</option>
                                            </select>
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
                                <button type="button" class="btn btn-success" id="dispenseBtn{{ $request->breastmilk_request_id }}"
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
                                <p class="mb-1"><strong>Name:</strong> {{ $request->infant->first_name }}
                                    {{ $request->infant->last_name }}{{ $request->infant->suffix ? ' ' . $request->infant->suffix : '' }}
                                </p>
                                <p class="mb-1"><strong>Age:</strong> {{ $request->infant->getFormattedAge() }}</p>
                                <p class="mb-0"><strong>Sex:</strong> {{ ucfirst($request->infant->sex) }}</p>
                                <p class="mb-0"><strong>Birth Weight:</strong>
                                    {{ $request->infant->birth_weight ? $request->infant->birth_weight . ' kg' : '-' }}</p>
                            </div>
                        </div>
                    </div>

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
                                            <input type="number" class="form-control" name="volume_requested" step="0.01" min="0" required
                                                id="volumeRequested{{ $request->breastmilk_request_id }}"
                                                oninput="validateDispenseForm({{ $request->breastmilk_request_id }})">
                                            <div class="form-text">Specify the amount of breastmilk to be dispensed.</div>
                                        </div>
                                <div class="mb-3">
                                    <label for="milk_type" class="form-label">Milk Type *</label>
                                    <select class="form-select" name="milk_type" required
                                        id="milkType{{ $request->breastmilk_request_id }}"
                                        onchange="loadInventory({{ $request->breastmilk_request_id }}); validateDispenseForm({{ $request->breastmilk_request_id }})">
                                        <option value="">Select milk type</option>
                                        <option value="unpasteurized">Unpasteurized Breastmilk</option>
                                        <option value="pasteurized">Pasteurized Breastmilk</option>
                                    </select>
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
                        @if($request->admin_notes)
                            <div class="mb-3">
                                <label class="form-label">Previous Admin Note (readonly)</label>
                                <div class="form-control" style="white-space:pre-wrap;">{{ $request->admin_notes }}</div>
                            </div>
                        @endif
                        <div class="mb-3">
                            <label for="admin_notes" class="form-label">Reason for Declining *</label>
                            <textarea class="form-control" name="admin_notes" rows="4" required
                                placeholder="Please provide a reason for declining this request..."></textarea>
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

        <!-- Archived Requests Tab Pane -->
        <div class="tab-pane fade{{ request()->get('status') == 'archived' ? ' show active' : '' }}" id="archived-requests" role="tabpanel">
            <div class="card card-standard">
                <div class="card-header bg-secondary text-white">
                    <h5>Archived Requests</h5>
                </div>
                <div class="card-body">
                    @if(!empty($archived) && $archived->count() > 0)
                        <div class="table-container-standard">
                            <table class="table table-standard table-striped">
                                <thead>
                                    <tr>
                                        <th class="text-center">Guardian</th>
                                        <th class="text-center">Infant</th>
                                        <th class="text-center">Submitted</th>
                                        <th class="text-center">Archived At</th>
                                        <th class="text-center">Restore</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $archivedOrdered = $archived instanceof \Illuminate\Pagination\LengthAwarePaginator
                                            ? $archived->getCollection()->sortByDesc('created_at')
                                            : collect($archived)->sortByDesc('created_at');
                                    @endphp
                                    @foreach($archivedOrdered as $req)
                                        <tr>
                                            <td class="align-middle">{{ $req->user->first_name ?? '' }} {{ $req->user->last_name ?? '' }}</td>
                                            <td class="align-middle">{{ $req->infant->first_name ?? '' }} {{ $req->infant->last_name ?? '' }}</td>
                                            <td class="align-middle">{{ $req->created_at->format('M d, Y g:i A') }}</td>
                                            <td class="align-middle">{{ $req->deleted_at ? $req->deleted_at->format('M d, Y g:i A') : '-' }}</td>
                                            <td class="align-middle text-center">
                                                <button class="btn btn-sm btn-outline-success" onclick="restoreRequest({{ $req->breastmilk_request_id }})">Restore</button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-archive fa-3x mb-3"></i>
                            <p>No archived requests</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

    <!-- Assisted Walk-in Request Modal -->
    <div class="modal fade" id="assistedRequestModal" tabindex="-1" aria-labelledby="assistedRequestModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="assistedRequestModalLabel">
                        <i class="fas fa-user-plus"></i> Assist Walk-in Breastmilk Request
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="assistedRequestForm" action="{{ route('admin.breastmilk-request.store-assisted') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> <strong>Note:</strong> Use this form to assist mothers/guardians who do not have a device. Interview them and fill in their details below.
                        </div>

                        <h6 class="border-bottom pb-2 mb-3"><i class="fas fa-user"></i> Guardian Information</h6>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="guardian_first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="guardian_first_name" name="guardian_first_name" required>
                            </div>
                            <div class="col-md-4">
                                <label for="guardian_last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="guardian_last_name" name="guardian_last_name" required>
                            </div>
                            <div class="col-md-4">
                                <label for="guardian_contact" class="form-label">Contact Number <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="guardian_contact" name="guardian_contact" required placeholder="09XXXXXXXXX">
                                <div id="guardian_contact_feedback" class="form-text text-danger" style="display:none;"></div>
                            </div>
                        </div>

                        <h6 class="border-bottom pb-2 mb-3 mt-4"><i class="fas fa-baby"></i> Infant Information</h6>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="infant_first_name" class="form-label">Infant First Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="infant_first_name" name="infant_first_name" required>
                            </div>
                            <div class="col-md-4">
                                <label for="infant_last_name" class="form-label">Infant Last Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="infant_last_name" name="infant_last_name" required>
                            </div>
                            <div class="col-md-4">
                                <label for="infant_date_of_birth" class="form-label">Date of Birth <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="infant_date_of_birth" name="infant_date_of_birth" required max="{{ date('Y-m-d') }}">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="infant_sex" class="form-label">Sex <span class="text-danger">*</span></label>
                                <select class="form-select" id="infant_sex" name="infant_sex" required>
                                    <option value="">Select Sex</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="infant_weight" class="form-label">Weight (kg) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control" id="infant_weight" name="infant_weight" required min="0.5" max="20" placeholder="e.g., 3.5">
                            </div>
                        </div>

                        <h6 class="border-bottom pb-2 mb-3 mt-4"><i class="fas fa-heartbeat"></i> Medical Information</h6>
                        <div class="mb-3">
                            <label for="medical_condition" class="form-label">Medical Condition / Reason for Request <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="medical_condition" name="medical_condition" rows="3" required placeholder="Describe the infant's medical condition or reason for requesting breastmilk"></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="prescription_upload" class="form-label">Prescription (Optional)</label>
                            <input type="file" class="form-control" id="prescription_upload" name="prescription" accept="image/*,.pdf">
                            <small class="text-muted">Upload prescription if available. Images or PDF files only.</small>
                        </div>

                        <h6 class="border-bottom pb-2 mb-3 mt-4"><i class="fas fa-calendar-alt"></i> Request Details</h6>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="request_date" class="form-label">Requested Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="request_date" name="request_date" required value="{{ date('Y-m-d') }}">
                            </div>
                            <div class="col-md-4">
                                <label for="volume_needed" class="form-label">Volume Needed (ml) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="volume_needed" name="volume_needed" required min="1" step="1" placeholder="e.g., 500">
                            </div>
                            <div class="col-md-4">
                                <label for="milk_type" class="form-label">Milk Type <span class="text-danger">*</span></label>
                                <select class="form-select" id="milk_type" name="milk_type" required>
                                    <option value="">Select milk type</option>
                                    <option value="unpasteurized">Unpasteurized Breastmilk</option>
                                    <option value="pasteurized">Pasteurized Breastmilk</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" value="1" id="dispense_now_checkbox" name="dispense_now">
                            <label class="form-check-label" for="dispense_now_checkbox">
                                Dispense now from available inventory (admin-assisted immediate dispensing)
                            </label>
                        </div>

                        <div id="assistedInventorySection" style="display:none;" class="mb-3">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title">Select inventory sources</h6>
                                    <div id="assistedInventoryLoading" style="display:none">Loading inventory...</div>
                                    <div id="assistedInventoryList"></div>
                                    <small class="text-muted d-block mt-2">Select one or more sources and specify the volume to use from each. Total selected volume must be greater or equal to requested volume.</small>
                                </div>
                            </div>
                        </div>

                        <!-- Hidden input to carry selected sources as JSON -->
                        <input type="hidden" id="selected_sources_json" name="selected_sources_json" value="">

                        <div class="mb-3">
                            <label for="admin_notes" class="form-label">Staff Notes (Optional)</label>
                            <textarea class="form-control" id="admin_notes" name="admin_notes" rows="2" placeholder="Additional notes or observations from the interview"></textarea>
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
                        fetch(`{{ route('admin.request.check-contact') }}?contact=${encodeURIComponent(normalized)}`, { headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }})
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
            const searchInput = document.getElementById('searchInput');
            const clearBtn = document.getElementById('clearSearch');
            const searchResults = document.getElementById('searchResults');
            
            if (!searchInput) return;

            // Get all tables across all tabs
            const allTables = document.querySelectorAll('.tab-pane table tbody');

            // Record original order for each row so we can restore it when the search is cleared
            allTables.forEach((tableBody, tIndex) => {
                Array.from(tableBody.querySelectorAll('tr')).forEach((row, rIndex) => {
                    // store a per-table original index
                    if (!row.dataset.originalOrder) {
                        row.dataset.originalOrder = rIndex;
                    }
                });
            });
            
            // Real-time search function
            function performSearch() {
                const raw = searchInput.value || '';
                const searchTerm = raw.trim().toLowerCase();
                let totalCount = 0;
                let visibleCount = 0;

                // Process each tab's table
                allTables.forEach(tableBody => {
                    const rows = Array.from(tableBody.querySelectorAll('tr'));
                    totalCount += rows.length;

                    if (searchTerm === '') {
                        // Restore original order and show all rows
                        rows.sort((a, b) => (parseInt(a.dataset.originalOrder || 0, 10) - parseInt(b.dataset.originalOrder || 0, 10)));
                        rows.forEach(row => {
                            row.style.display = '';
                            tableBody.appendChild(row);
                        });
                        visibleCount += rows.length;
                    } else {
                        // Separate matched and non-matched rows
                        const matchedRows = [];
                        const unmatchedRows = [];

                        rows.forEach(row => {
                            // Get all cell content for comprehensive search
                            let rowText = '';
                            const cells = row.querySelectorAll('td');
                            cells.forEach(cell => {
                                rowText += (cell.textContent || '') + ' ';
                            });
                            rowText = rowText.toLowerCase();

                            // Check if search term matches anywhere in the row
                            if (rowText.indexOf(searchTerm) !== -1) {
                                row.style.display = '';
                                matchedRows.push(row);
                                visibleCount++;
                            } else {
                                row.style.display = 'none';
                                unmatchedRows.push(row);
                            }
                        });

                        // Reorder: matched rows first, then unmatched (keep relative original order)
                        matchedRows.sort((a, b) => parseInt(a.dataset.originalOrder || 0, 10) - parseInt(b.dataset.originalOrder || 0, 10));
                        unmatchedRows.sort((a, b) => parseInt(a.dataset.originalOrder || 0, 10) - parseInt(b.dataset.originalOrder || 0, 10));
                        matchedRows.forEach(row => tableBody.appendChild(row));
                        unmatchedRows.forEach(row => tableBody.appendChild(row));
                    }
                });

                // Update UI
                if (searchTerm === '') {
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

            // Event listeners
            searchInput.addEventListener('input', performSearch);
            
            clearBtn.addEventListener('click', function() {
                searchInput.value = '';
                performSearch();
                searchInput.focus();
            });

            // Initial state
            performSearch();
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
            const allowedKeys = ['Backspace','Delete','ArrowLeft','ArrowRight','ArrowUp','ArrowDown','Tab','Enter','Home','End'];
            if (allowedKeys.indexOf(e.key) !== -1) return;

            // Allow one dot
            if (e.key === '.' ) {
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
            if (!el || !el.id || !el.id.startsWith('volumeToDispense')) return;
            const raw = String(el.value || '').trim();
            if (!raw) return;
            const num = parseFloat(raw.replace(/,/g, ''));
            if (isNaN(num) || num <= 0) return;

            if (num >= 10 && Math.round(num) % 10 !== 0) {
                const roundedDown = Math.floor(num / 10) * 10;
                if (roundedDown <= 0) return;
                // auto-apply rounded value
                el.value = String(roundedDown);
                el.dispatchEvent(new Event('input', { bubbles: true }));
                const requestId = el.id.replace('volumeToDispense', '');
                showVolumeNotice(requestId, num, roundedDown);
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

        // New minimal modal view: fetch prescription and display image only
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

                    container.innerHTML = `
                        <div class="row">
                            <div class="col-md-4 d-flex justify-content-center align-items-start">
                                ${userHtml}
                            </div>
                            <div class="col-md-8 d-flex flex-column align-items-center">
                                <h6 class="mb-3">Prescription: ${escapeHtml(data.filename || 'Prescription')}</h6>
                                <div class="d-flex justify-content-center align-items-center" style="min-height: 320px; width:100%;">
                                    <img src="${data.image}" alt="Prescription" class="img-fluid rounded border" style="max-width:100%; max-height:70vh; object-fit:contain;" />
                                </div>
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
            const notesField = document.getElementById('viewModalNotes' + requestId);
            const notes = notesField ? notesField.value.trim() : '';

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Decline Request',
                    html: `
                                                                                    <div class="text-start">
                                                                                        <p>Are you sure you want to decline this request?</p>
                                                                                        <div class="mb-3">
                                                                                            <label for="swalDeclineReason" class="form-label">Reason for Declining <span class="text-danger">*</span></label>
                                                                                            <textarea 
                                                                                                id="swalDeclineReason" 
                                                                                                class="form-control" 
                                                                                                rows="4" 
                                                                                                placeholder="Please provide a reason for declining this request...">${notes}</textarea>
                                                                                            <small class="text-muted">This reason will be sent to the guardian.</small>
                                                                                        </div>
                                                                                    </div>
                                                                                `,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Decline Request',
                    cancelButtonText: 'Cancel',
                    reverseButtons: true,
                    preConfirm: () => {
                        const reason = document.getElementById('swalDeclineReason').value.trim();
                        if (!reason) {
                            Swal.showValidationMessage('Please provide a reason for declining');
                            return false;
                        }
                        return reason;
                    }
                }).then((result) => {
                    if (result.isConfirmed && result.value) {
                        const reason = result.value;

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

                        // Submit the decline request
                        fetch(`{{ url('/admin/breastmilk-request') }}/${requestId}/decline`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            },
                            body: JSON.stringify({ admin_notes: reason })
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

        // Archive request (soft-delete)
        function archiveRequest(requestId) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Archive request?',
                    text: 'This will archive (soft-delete) the request. You can restore it from the database if needed.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, archive',
                    preConfirm: () => {
                        return fetch(`{{ url('/admin/breastmilk-request') }}/${requestId}/archive`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        }).then(r => r.json());
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire('Archived', 'Request archived successfully.', 'success').then(()=> location.reload());
                    }
                }).catch(() => {
                    Swal.fire('Error', 'Failed to archive request', 'error');
                });
            } else {
                if (!confirm('Archive request?')) return;
                fetch(`{{ url('/admin/breastmilk-request') }}/${requestId}/archive`, { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } })
                    .then(()=> location.reload())
                    .catch(()=> alert('Failed to archive'));
            }
        }

        // Restore archived request
        function restoreRequest(requestId) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Restore request?',
                    text: 'This will restore the archived request back to active lists.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, restore',
                    preConfirm: () => {
                        return fetch(`{{ url('/admin/breastmilk-request') }}/${requestId}/restore`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        }).then(r => r.json());
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire('Restored', 'Request restored successfully.', 'success').then(()=> location.reload());
                    }
                }).catch(() => {
                    Swal.fire('Error', 'Failed to restore request', 'error');
                });
            } else {
                if (!confirm('Restore request?')) return;
                fetch(`{{ url('/admin/breastmilk-request') }}/${requestId}/restore`, { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } })
                    .then(()=> location.reload())
                    .catch(()=> alert('Failed to restore'));
            }
        }

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
                    } else if (milkType === 'unpasteurized') {
                        displayUnpasteurizedInventory(requestId, data.donations || []);
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

            let html = '<div class="list-group">';
            batches.forEach(batch => {
                html += `
                                                                        <div class="list-group-item">
                                                                            <div class="form-check">
                                                                                <input class="form-check-input" type="radio" 
                                                                                    name="batch_${requestId}" 
                                                                                    id="batch_${requestId}_${batch.batch_id}" 
                                                                                    value="${batch.batch_id}"
                                                                                    data-volume="${batch.available_volume}"
                                                                                    onchange="handleBatchSelection(${requestId}, ${batch.batch_id}, ${batch.available_volume})">
                                                                                <label class="form-check-label w-100" for="batch_${requestId}_${batch.batch_id}">
                                                                                    <div class="d-flex justify-content-between align-items-center">
                                                                                        <div>
                                                                                            <strong>Batch #${batch.batch_number}</strong><br>
                                                                                            <small class="text-muted">Available: ${batch.available_volume} ml</small><br>
                                                                                            <small class="text-muted">Date: ${batch.date_pasteurized}</small>
                                                                                        </div>
                                                                                    </div>
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                    `;
            });
            html += '</div>';
            html += '<div class="mt-2"><small class="text-info"><i class="fas fa-info-circle"></i> For pasteurized milk, the entire batch volume will be used.</small></div>';
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

            let html = '<div class="list-group">';
            donations.forEach(donation => {
                const donorName = donation.donor_name || 'Anonymous';
                html += `
                                                                        <div class="list-group-item">
                                                                            <div class="form-check">
                                                                                <input class="form-check-input" type="radio" 
                                                                                    name="donation_${requestId}" 
                                                                                    id="donation_${requestId}_${donation.breastmilk_donation_id}" 
                                                                                    value="${donation.breastmilk_donation_id}"
                                                                                    data-volume="${donation.available_volume}"
                                                                                    onchange="handleDonationSelection(${requestId}, ${donation.breastmilk_donation_id}, ${donation.available_volume})">
                                                                                <label class="form-check-label w-100" for="donation_${requestId}_${donation.breastmilk_donation_id}">
                                                                                    <div class="d-flex justify-content-between align-items-center">
                                                                                        <div>
                                                                                            <strong>Donation #${donation.breastmilk_donation_id}</strong><br>
                                                                                            <small class="text-muted">Donor: ${donorName}</small><br>
                                                                                            <small class="text-muted">Available: ${donation.available_volume} ml</small><br>
                                                                                            <small class="text-muted">Date: ${donation.donation_date}</small>
                                                                                        </div>
                                                                                    </div>
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                    `;
            });
            html += '</div>';
            html += '<div class="mt-2"><small class="text-info"><i class="fas fa-info-circle"></i> For unpasteurized milk, the entire donation volume will be used.</small></div>';
            inventoryList.innerHTML = html;
        }

        /**
         * Handle unpasteurized donation selection
         */
        function handleDonationSelection(requestId, donationId, availableVolume) {
            // Auto-fill the volume to dispense with the donation's total volume
            const volumeInput = document.getElementById(`volumeToDispense${requestId}`);
            if (volumeInput && !volumeInput.value) {
                volumeInput.value = availableVolume;
            }
            updateSelectedVolume(requestId);
        }

        /**
         * Handle pasteurized batch selection
         */
        function handleBatchSelection(requestId, batchId, availableVolume) {
            // Auto-fill the volume to dispense with the batch's total volume
            const volumeInput = document.getElementById(`volumeToDispense${requestId}`);
            if (volumeInput && !volumeInput.value) {
                volumeInput.value = availableVolume;
            }
            updateSelectedVolume(requestId);
        }

        /**
         * Update the selected volume tracker
         */
        function updateSelectedVolume(requestId) {
            const milkTypeSelect = document.getElementById(`milkTypeSelect${requestId}`);
            const milkType = milkTypeSelect.value;
            const volumeRequired = parseFloat(document.getElementById(`volumeToDispense${requestId}`).value) || 0;

            let totalSelected = 0;

            if (milkType === 'pasteurized') {
                // Get selected batch volume (radio button)
                const selectedBatch = document.querySelector(`input[name="batch_${requestId}"]:checked`);
                if (selectedBatch) {
                    totalSelected = parseFloat(selectedBatch.dataset.volume) || 0;
                }
            } else if (milkType === 'unpasteurized') {
                // Get selected donation volume
                const selectedDonation = document.querySelector(`input[name="donation_${requestId}"]:checked`);
                if (selectedDonation) {
                    totalSelected = parseFloat(selectedDonation.dataset.volume) || 0;
                }
            }

            // Update display
            const displaySelected = totalSelected % 1 === 0 ? Math.round(totalSelected) : totalSelected.toFixed(2).replace(/\.?0+$/, '');
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

            // Collect selected sources
            let selectedSources = [];
            let totalSelected = 0;

            if (milkType === 'pasteurized') {
                const selectedBatch = document.querySelector(`input[name="batch_${requestId}"]:checked`);

                if (!selectedBatch) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'No Batch Selected',
                        text: 'Please select a pasteurized batch.',
                        confirmButtonColor: '#3085d6'
                    });
                    return;
                }

                // Use the volume entered by the user, not the full batch volume
                selectedSources.push({
                    type: 'pasteurized',
                    id: selectedBatch.value,
                    volume: volumeToDispense  // ← Fixed: use the amount entered by user
                });
                totalSelected = volumeToDispense;

            } else if (milkType === 'unpasteurized') {
                const selectedDonation = document.querySelector(`input[name="donation_${requestId}"]:checked`);

                if (!selectedDonation) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'No Donation Selected',
                        text: 'Please select an unpasteurized donation.',
                        confirmButtonColor: '#3085d6'
                    });
                    return;
                }

                // Use the volume entered by the user, not the full donation volume
                selectedSources.push({
                    type: 'unpasteurized',
                    id: selectedDonation.value,
                    volume: volumeToDispense  // ← Fixed: use the amount entered by user
                });
                totalSelected = volumeToDispense;
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
                    text: 'Please provide a reason for rejecting this request',
                    input: 'textarea',
                    inputPlaceholder: 'Enter your reason here...',
                    inputAttributes: {
                        'aria-label': 'Reason for rejection',
                        'rows': 4
                    },
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="fas fa-ban"></i> Reject Request',
                    cancelButtonText: '<i class="fas fa-times"></i> Cancel',
                    reverseButtons: true,
                    showLoaderOnConfirm: true,
                    inputValidator: (value) => {
                        if (!value || !value.trim()) {
                            return 'You need to provide a reason for rejection!';
                        }
                    },
                    preConfirm: (reason) => {
                        return fetch(`{{ url('/admin/breastmilk-request') }}/${requestId}/reject`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                admin_notes: reason.trim()
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
                    // If admin already entered a desired volume, auto-enable "Dispense now" and load inventory
                    const volNeededEl = document.getElementById('volume_needed');
                    if (volNeededEl && volNeededEl.value && volNeededEl.value.trim() !== '') {
                        try {
                            const v = parseFloat(volNeededEl.value);
                            if (!isNaN(v) && v > 0) {
                                if (dispenseNowCheckbox && !dispenseNowCheckbox.checked) {
                                    dispenseNowCheckbox.checked = true;
                                    const section = document.getElementById('assistedInventorySection');
                                    if (section) section.style.display = 'block';
                                    assistedLoadInventory();
                                }
                            }
                        } catch (err) {
                            // ignore parse errors
                        }
                    }
                });
            }

            // Auto-toggle inventory when admin types a desired volume
            const volNeededInput = document.getElementById('volume_needed');
            if (volNeededInput) {
                volNeededInput.addEventListener('input', function () {
                    const milkType = (document.getElementById('milk_type') || {}).value;
                    const v = parseFloat(this.value || 0);
                    if (!isNaN(v) && v > 0 && milkType) {
                        if (dispenseNowCheckbox && !dispenseNowCheckbox.checked) {
                            dispenseNowCheckbox.checked = true;
                        }
                        const section = document.getElementById('assistedInventorySection');
                        if (section) section.style.display = 'block';
                        assistedLoadInventory();
                    } else {
                        // hide when invalid
                        if (dispenseNowCheckbox && !dispenseNowCheckbox.checked) {
                            const section = document.getElementById('assistedInventorySection');
                            if (section) section.style.display = 'none';
                        }
                    }
                });
            }

            // Prepare selected_sources_json before form submit
            const assistedForm = document.getElementById('assistedRequestForm');
            if (assistedForm) {
                assistedForm.addEventListener('submit', function (e) {
                    const dispenseNow = document.getElementById('dispense_now_checkbox').checked;
                    if (!dispenseNow) return; // nothing to do

                    // collect selected sources
                    const list = document.querySelectorAll('#assistedInventoryList .inventory-item');
                    const selected = [];
                    let total = 0;
                    list.forEach(item => {
                        const cb = item.querySelector('input[type="checkbox"]');
                        if (cb && cb.checked) {
                            const type = cb.dataset.type;
                            const id = cb.dataset.id;
                            const bagIndex = cb.dataset.bagIndex !== undefined ? cb.dataset.bagIndex : null;
                            // Get planned use from visible label (auto-selection) or fall back to numeric input if present
                            let vol = 0;
                            const plannedSpan = item.querySelector('span[id^="planned_use_"]');
                            if (plannedSpan) {
                                vol = parseFloat(plannedSpan.textContent || 0) || 0;
                            } else {
                                const volInput = item.querySelector('input[type="number"]');
                                vol = volInput ? parseFloat(volInput.value) : 0;
                            }
                            if (vol > 0) {
                                const src = { type: type, id: id, volume: vol };
                                if (bagIndex !== null) src.bag_index = parseInt(bagIndex, 10);
                                selected.push(src);
                                total += vol;
                            }
                        }
                    });

                    const requested = parseFloat(document.getElementById('volume_needed').value || 0);
                    if (selected.length === 0) {
                        e.preventDefault();
                        alert('You selected "Dispense now" but no inventory sources were selected. Please select at least one source or uncheck Dispense now.');
                        return false;
                    }
                    if (total < requested) {
                        e.preventDefault();
                        alert('Selected total volume (' + total + 'ml) is less than requested volume (' + requested + 'ml). Please adjust selected sources.');
                        return false;
                    }

                    document.getElementById('selected_sources_json').value = JSON.stringify(selected);
                    // allow submit to continue
                });
            }
        });

        function assistedLoadInventory() {
            const milkType = document.getElementById('milk_type').value;
            const loading = document.getElementById('assistedInventoryLoading');
            const list = document.getElementById('assistedInventoryList');
            list.innerHTML = '';
            if (!milkType) return;
            loading.style.display = 'block';
            fetch(`{{ route('admin.request.inventory') }}?type=${milkType}`, { headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }})
                .then(r => r.json())
                .then(data => {
                    loading.style.display = 'none';
                    if (data.error) {
                        list.innerHTML = `<div class="alert alert-danger">${data.error}</div>`;
                        return;
                    }

                    const requested = parseFloat(document.getElementById('volume_needed').value || 0) || 0;
                    let html = '';

                    if (milkType === 'unpasteurized') {
                        const donations = data.donations || [];
                        if (donations.length === 0) {
                            list.innerHTML = '<div class="alert alert-warning">No unpasteurized donations available.</div>';
                            return;
                        }

                        // Build bag-level list (read-only volumes). We'll auto-select bags to meet the requested volume.
                        donations.forEach(d => {
                            const bagVolumes = d.individual_bag_volumes || [];
                            html += `<div class="card mb-2 p-2"><div><strong>Donation #${d.breastmilk_donation_id}</strong> — ${d.donor_name} — Total Available: ${d.available_volume}ml</div>`;

                            if (Array.isArray(bagVolumes) && bagVolumes.length > 0) {
                                bagVolumes.forEach((bv, idx) => {
                                    const displayVol = bv % 1 === 0 ? Math.round(bv) : bv;
                                    // checkbox has data-volume placeholder; actual chosen volume will be set by auto-selection logic
                                    html += `<div class="inventory-item mt-2 card p-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="assisted_item_${d.breastmilk_donation_id}_bag_${idx}" data-type="unpasteurized" data-id="${d.breastmilk_donation_id}" data-bag-index="${idx}" data-available="${bv}" disabled>
                                            <label class="form-check-label" for="assisted_item_${d.breastmilk_donation_id}_bag_${idx}">Bag ${idx + 1} — ${displayVol}ml</label>
                                        </div>
                                        <div class="mt-2"><small class="text-muted">Planned use: <span id="planned_use_${d.breastmilk_donation_id}_bag_${idx}">0</span> ml</small></div>
                                    </div>`;
                                });
                            } else {
                                // donation-level single item
                                html += `<div class="inventory-item mb-2 card p-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="assisted_item_${d.breastmilk_donation_id}" data-type="unpasteurized" data-id="${d.breastmilk_donation_id}" data-available="${d.available_volume}" disabled>
                                        <label class="form-check-label" for="assisted_item_${d.breastmilk_donation_id}"><strong>Donation #${d.breastmilk_donation_id}</strong> — ${d.donor_name} — Available: ${d.available_volume}ml</label>
                                    </div>
                                    <div class="mt-2"><small class="text-muted">Planned use: <span id="planned_use_${d.breastmilk_donation_id}">0</span> ml</small></div>
                                </div>`;
                            }

                            html += `</div>`;
                        });

                        list.innerHTML = html;

                        // Auto-select bags/batches to meet requested volume (FIFO across donations and bags)
                        autoSelectSourcesForAssisted(donations, 'unpasteurized', requested);

                    } else {
                        const batches = data.batches || [];
                        if (batches.length === 0) {
                            list.innerHTML = '<div class="alert alert-warning">No pasteurized batches available.</div>';
                            return;
                        }

                        batches.forEach(b => {
                            html += `<div class="inventory-item mb-2 card p-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="assisted_batch_${b.batch_id}" data-type="pasteurized" data-id="${b.batch_id}" data-available="${b.available_volume}" disabled>
                                    <label class="form-check-label" for="assisted_batch_${b.batch_id}"><strong>Batch #${b.batch_number}</strong> — Available: ${b.available_volume}ml</label>
                                </div>
                                <div class="mt-2"><small class="text-muted">Planned use: <span id="planned_use_batch_${b.batch_id}">0</span> ml</small></div>
                            </div>`;
                        });

                        list.innerHTML = html;

                        autoSelectSourcesForAssisted(batches, 'pasteurized', requested);
                    }
                })
                .catch(err => {
                    loading.style.display = 'none';
                    list.innerHTML = '<div class="alert alert-danger">Failed to load inventory.</div>';
                });
        }

        // Auto-selection algorithm: choose full bags/batches FIFO until requested met; last selected item may be partial
        function autoSelectSourcesForAssisted(items, milkType, requested) {
            const selected = [];
            let remaining = Math.max(0, requested || 0);

            if (milkType === 'unpasteurized') {
                for (const d of items) {
                    const bags = d.individual_bag_volumes || [];
                    if (Array.isArray(bags) && bags.length > 0) {
                        for (let i = 0; i < bags.length; i++) {
                            if (remaining <= 0) break;
                            const avail = parseFloat(bags[i]) || 0;
                            if (avail <= 0) continue;
                            const take = Math.min(avail, remaining);
                            // mark checkbox and planned use
                            const cbId = `assisted_item_${d.breastmilk_donation_id}_bag_${i}`;
                            const cb = document.getElementById(cbId);
                            if (cb) cb.checked = true;
                            const plannedEl = document.getElementById(`planned_use_${d.breastmilk_donation_id}_bag_${i}`);
                            if (plannedEl) plannedEl.textContent = (take % 1 === 0 ? Math.round(take) : parseFloat(take).toFixed(2));
                            selected.push({ type: 'unpasteurized', id: d.breastmilk_donation_id, bag_index: i, volume: take });
                            remaining -= take;
                        }
                    } else {
                        if (remaining <= 0) break;
                        const avail = parseFloat(d.available_volume) || 0;
                        if (avail <= 0) continue;
                        const take = Math.min(avail, remaining);
                        const cbId = `assisted_item_${d.breastmilk_donation_id}`;
                        const cb = document.getElementById(cbId);
                        if (cb) cb.checked = true;
                        const plannedEl = document.getElementById(`planned_use_${d.breastmilk_donation_id}`);
                        if (plannedEl) plannedEl.textContent = (take % 1 === 0 ? Math.round(take) : parseFloat(take).toFixed(2));
                        selected.push({ type: 'unpasteurized', id: d.breastmilk_donation_id, volume: take });
                        remaining -= take;
                    }
                    if (remaining <= 0) break;
                }
            } else {
                for (const b of items) {
                    if (remaining <= 0) break;
                    const avail = parseFloat(b.available_volume) || 0;
                    if (avail <= 0) continue;
                    const take = Math.min(avail, remaining);
                    const cbId = `assisted_batch_${b.batch_id}`;
                    const cb = document.getElementById(cbId);
                    if (cb) cb.checked = true;
                    const plannedEl = document.getElementById(`planned_use_batch_${b.batch_id}`);
                    if (plannedEl) plannedEl.textContent = (take % 1 === 0 ? Math.round(take) : parseFloat(take).toFixed(2));
                    selected.push({ type: 'pasteurized', id: b.batch_id, volume: take });
                    remaining -= take;
                }
            }

            // Store selected_sources_json with the computed volumes and bag_index where applicable
            document.getElementById('selected_sources_json').value = JSON.stringify(selected);
        }
    </script>
@endsection