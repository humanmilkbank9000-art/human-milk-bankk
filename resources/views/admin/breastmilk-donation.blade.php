@extends('layouts.admin-layout')

@section('title', 'Breastmilk Donation Management')

@section('content')

    @section('styles')
        <link rel="stylesheet" href="{{ asset('css/table-layout-standard.css') }}">
        <link rel="stylesheet" href="{{ asset('css/responsive-tables.css') }}">
        <!-- Using global styles from ui-components.css for filter and header alignment -->
        <style>
            /* Ensure consistent compact header height across all tabs */
            .card-header.bg-primary,
            .card-header.bg-success,
            .card-header.bg-warning {
                padding: 0.75rem 1.25rem !important;
                min-height: 52px;
                display: flex;
                align-items: center;
            }

            /* Compact header on mobile */
            @media (max-width: 767.98px) {

                .card-header.bg-primary,
                .card-header.bg-success {
                    padding: 0.5rem 0.75rem !important;
                    min-height: 45px;
                }
            }

            .card-header h5 {
                font-size: 1.1rem;
                margin-bottom: 0 !important;
            }

            /* Dropdown filter alignment - always horizontal and compact */
            .card-header .form-select-sm {
                font-size: 0.8rem;
                padding: 0.25rem 0.5rem;
                margin-left: auto;
                height: auto;
                line-height: 1.2;
                flex-shrink: 0;
            }

            /* Keep header horizontal on all screen sizes - compact */
            .card-header.bg-warning.d-flex {
                flex-direction: row !important;
                align-items: center !important;
                justify-content: space-between;
                gap: 0.5rem !important;
                flex-wrap: nowrap !important;
            }

            .card-header h5 {
                flex-shrink: 1;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }

            /* Mobile optimization - extra compact */
            @media (max-width: 767.98px) {
                .card-header.bg-warning.d-flex {
                    padding: 0.5rem 0.75rem !important;
                    min-height: 45px !important;
                }

                .card-header h5 {
                    font-size: 0.95rem;
                }

                .card-header .form-select-sm {
                    font-size: 0.7rem;
                    padding: 0.2rem 0.3rem;
                    min-width: 100px !important;
                    max-width: 130px;
                }
            }

            @media (max-width: 575.98px) {
                .card-header.bg-warning.d-flex {
                    padding: 0.4rem 0.6rem !important;
                    min-height: 42px !important;
                }

                .card-header h5 {
                    font-size: 0.85rem;
                }

                .card-header .form-select-sm {
                    font-size: 0.65rem;
                    padding: 0.15rem 0.25rem;
                    min-width: 90px !important;
                    max-width: 110px;
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

            @media (max-width: 400px) {
                .card-header h5 {
                    font-size: 0.8rem;
                }

                .card-header .form-select-sm {
                    font-size: 0.6rem;
                    padding: 0.1rem 0.2rem;
                    min-width: 85px !important;
                    max-width: 100px;
                }
            }

            /* Tab navigation consistency - more compact */
            .nav-tabs-standard .nav-link {
                padding: 0.65rem 1rem;
                font-size: 0.95rem;
            }

            .nav-tabs-standard .badge {
                font-size: 0.75rem;
                padding: 0.25em 0.5em;
            }

            /* ============================================
                       RESPONSIVE LAYOUT - NO HORIZONTAL SCROLL
                       ============================================ */
            
            /* Card-based layout for smaller screens */
            @media (max-width: 1400px) {
                #pending-donations .table-responsive table {
                    display: none !important;
                }
                
                .donation-card {
                    display: block !important;
                    border: 1px solid #dee2e6;
                    border-radius: 8px;
                    padding: 1rem;
                    margin-bottom: 1rem;
                    background: white;
                    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
                }
                
                .donation-card .card-header-row {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    padding-bottom: 0.75rem;
                    margin-bottom: 0.75rem;
                    border-bottom: 2px solid #f8f9fa;
                }
                
                .donation-card .card-row {
                    display: flex;
                    justify-content: space-between;
                    padding: 0.4rem 0;
                    border-bottom: 1px solid #f0f0f0;
                }
                
                .donation-card .card-row:last-of-type {
                    border-bottom: none;
                }
                
                .donation-card .card-label {
                    font-weight: 600;
                    color: #495057;
                    font-size: 0.85rem;
                }
                
                .donation-card .card-value {
                    text-align: right;
                    color: #212529;
                    font-size: 0.85rem;
                }
                
                .donation-card .card-actions {
                    margin-top: 0.75rem;
                    padding-top: 0.75rem;
                    border-top: 2px solid #e9ecef;
                    display: flex;
                    gap: 0.5rem;
                    flex-wrap: wrap;
                }
                
                .donation-card .card-actions .btn {
                    flex: 1;
                    min-width: 120px;
                }
            }
            
            @media (min-width: 1401px) {
                .donation-card {
                    display: none !important;
                }
            }
            
            /* Extra compact for tablets */
            @media (min-width: 600px) and (max-width: 1024px) {

                /* Make pending donations table even more compact */
                #pending-donations .table thead th,
                #pending-donations .table tbody td {
                    font-size: 0.6rem !important;
                    padding: 0.35rem 0.15rem !important;
                    line-height: 1.1 !important;
                }

                /* Ultra-compact buttons for 9-column table */
                #pending-donations .table .btn {
                    font-size: 0.55rem !important;
                    padding: 0.15rem 0.3rem !important;
                    white-space: nowrap !important;
                }

                #pending-donations .table .btn i {
                    font-size: 0.65rem !important;
                }

                /* Ultra-compact badges */
                #pending-donations .table .badge {
                    font-size: 0.55rem !important;
                    padding: 0.15rem 0.3rem !important;
                }

                /* Compact name display */
                #pending-donations .table td[data-label="Name"] strong {
                    font-size: 0.6rem !important;
                    font-weight: 600;
                }

                /* Ensure text wraps in narrow columns */
                #pending-donations .table td[data-label="Address"] small,
                #pending-donations .table td[data-label="Volume/Bag"] small,
                #pending-donations .table td[data-label="Date & Time"] small {
                    font-size: 0.55rem !important;
                    line-height: 1.1 !important;
                }

                /* Make location button more compact */
                #pending-donations .table .view-location {
                    padding: 0.15rem 0.25rem !important;
                }

                /* Specific column width optimization */
                #pending-donations .table {
                    table-layout: fixed !important;
                }

                /* Allocate width percentages for better distribution */
                #pending-donations .table thead th:nth-child(1) {
                    width: 11%;
                }

                /* Name */
                #pending-donations .table thead th:nth-child(2) {
                    width: 7%;
                }

                /* Type */
                #pending-donations .table thead th:nth-child(3) {
                    width: 13%;
                }

                /* Address */
                #pending-donations .table thead th:nth-child(4) {
                    width: 6%;
                }

                /* Location */
                #pending-donations .table thead th:nth-child(5) {
                    width: 6%;
                }

                /* Bags */
                #pending-donations .table thead th:nth-child(6) {
                    width: 11%;
                }

                /* Volume/Bag */
                #pending-donations .table thead th:nth-child(7) {
                    width: 8%;
                }

                /* Total */
                #pending-donations .table thead th:nth-child(8) {
                    width: 12%;
                }

                /* Date & Time */
                #pending-donations .table thead th:nth-child(9) {
                    width: 18%;
                }

                /* Actions - increased from 11% to 18% */
                
                /* Make table horizontally scrollable as fallback */
                #pending-donations .table-container {
                    overflow-x: auto;
                    -webkit-overflow-scrolling: touch;
                }
                
                /* Ensure action buttons stay visible */
                #pending-donations .table thead th:nth-child(9),
                #pending-donations .table tbody td:nth-child(9) {
                    position: sticky;
                    right: 0;
                    background-color: white;
                    box-shadow: -2px 0 4px rgba(0,0,0,0.05);
                    z-index: 2;
                    vertical-align: middle;
                    min-width: 140px;
                }

                #pending-donations .table thead th:nth-child(9) {
                    background-color: #f8f9fa;
                }

                /* Fallback sticky for last column at other sizes (helps with 90% zoom) */
                #pending-donations .table thead th:last-child,
                #pending-donations .table tbody td:last-child {
                    position: sticky;
                    right: 0;
                    background: white;
                    z-index: 3;
                    box-shadow: -2px 0 4px rgba(0,0,0,0.04);
                }

                /* Action buttons container: allow wrapping so buttons remain visible rather than overflow */
                #pending-donations .table .table-actions {
                    display: inline-flex;
                    gap: 0.4rem;
                    justify-content: center;
                    align-items: center;
                    flex-wrap: wrap;
                }

                /* Make buttons more compact when space is constrained */
                #pending-donations .table .table-actions .btn {
                    white-space: nowrap;
                    padding-left: 0.45rem;
                    padding-right: 0.45rem;
                }
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
    @endsection

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="container-fluid page-container-standard">
        <!-- Navigation Tabs with status query for persistence -->
        @php
            $tabStatus = request('status', 'pending');
        @endphp
        <ul class="nav nav-tabs nav-tabs-standard mb-3" role="tablist">
            <li class="nav-item">
                <a class="nav-link {{ $tabStatus == 'pending' ? 'active bg-warning text-dark' : 'text-warning' }}"
                    href="?status=pending">
                    Pending Donations <span class="badge bg-warning text-dark">{{ $pendingDonations->count() }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $tabStatus == 'scheduled' ? 'active bg-primary text-white' : 'text-primary' }}"
                    href="?status=scheduled">
                    Scheduled <span class="badge bg-primary">{{ $scheduledHomeCollection->count() }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $tabStatus == 'success_walk_in' ? 'active bg-success text-white' : 'text-success' }}"
                    href="?status=success_walk_in">
                    Walk-in Success <span class="badge bg-success">{{ $successWalkIn->count() }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $tabStatus == 'success_home_collection' ? 'active bg-success text-white' : 'text-success' }}"
                    href="?status=success_home_collection">
                    Home Collection Success <span class="badge bg-success">{{ $successHomeCollection->count() }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $tabStatus == 'archived' ? 'active bg-secondary text-white' : 'text-secondary' }}" href="?status=archived">Archived <span class="badge bg-secondary text-white">{{ $archivedCount ?? 0 }}</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $tabStatus == 'declined' ? 'active bg-danger text-white' : 'text-danger' }}" href="?status=declined">Declined <span class="badge bg-danger text-white">{{ $declinedCount ?? 0 }}</span></a>
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
                       placeholder="Search by donor name, address, contact..."
                       aria-label="Search donations">
                <button class="btn btn-outline-secondary" type="button" id="clearSearch" style="display: none;">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <small class="text-muted d-block mt-1">
                <span id="searchResults"></span>
            </small>
        </div>

        <div class="tab-content" id="donationTabContent" aria-live="polite">
            <!-- Pending Donations Tab -->
            <div class="tab-pane fade show {{ $tabStatus == 'pending' ? 'active' : '' }}" id="pending-donations"
                role="tabpanel">
                <div class="card card-standard">
                    <div
                        class="card-header bg-warning text-dark py-3 d-flex flex-row justify-content-between align-items-center gap-2">
                        <h5 class="mb-0">Pending Donations</h5>
                        <select id="donation-type-filter" class="form-select form-select-sm"
                            style="width: auto; min-width: 150px;">
                            <option value="all" {{ request('donation_type', 'all') == 'all' ? 'selected' : '' }}>All Donations
                            </option>
                            <option value="walk_in" {{ request('donation_type') == 'walk_in' ? 'selected' : '' }}>Walk-in Only
                            </option>
                            <option value="home_collection" {{ request('donation_type') == 'home_collection' ? 'selected' : '' }}>Home Collection Only</option>
                        </select>
                    </div>
                    <div class="card-body">
                        @if($pendingDonations->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover" style="min-width: 900px; width:100%;">
                                    <thead>
                                        <tr>
                                            <th class="text-center">Name</th>
                                            <th class="text-center">Type</th>
                                            <th class="text-center">Contact</th>
                                            <th class="text-center">Address</th>
                                            <th class="text-center">Location</th>
                                            <th class="text-center">Date</th>
                                            <th class="text-center">Total Volume</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $pendingOrdered = $pendingDonations instanceof \Illuminate\Pagination\LengthAwarePaginator
                                                ? $pendingDonations->getCollection()->sortByDesc('created_at')
                                                : collect($pendingDonations)->sortByDesc('created_at');
                                        @endphp
                                        @foreach($pendingOrdered as $donation)
                                            <tr>
                                                <td data-label="Name" class="text-center">
                                                    <strong>{{ $donation->user->first_name ?? '' }} {{ $donation->user->last_name ?? '' }}</strong>
                                                </td>
                                                <td data-label="Type" class="text-center">
                                                    @if($donation->donation_method === 'walk_in')
                                                        <span class="badge bg-info">Walk-in</span>
                                                    @else
                                                        <span class="badge bg-primary">Home Collection</span>
                                                    @endif
                                                </td>
                                                <td data-label="Contact" class="text-center">
                                                    {{ $donation->user->contact_number ?? $donation->user->phone ?? '-' }}
                                                </td>
                                                <td data-label="Address" class="text-center">
                                                    <small>
                                                        @if($donation->donation_method === 'home_collection')
                                                            {{ $donation->user->address ?? 'Not provided' }}
                                                        @else
                                                            <span class="text-muted">Walk-in</span>
                                                        @endif
                                                    </small>
                                                </td>
                                                <td data-label="Location" class="text-center">
                                                    @if($donation->donation_method === 'home_collection')
                                                        @php
                                                            // Prefer donation-specific coordinates if available; fallback to user's profile
                                                            $lat = $donation->latitude ?? $donation->user->latitude ?? null;
                                                            $lng = $donation->longitude ?? $donation->user->longitude ?? null;
                                                        @endphp
                                                        @if(!is_null($lat) && $lat !== '' && !is_null($lng) && $lng !== '')
                                                            <button class="btn btn-info btn-sm view-location" title="View on Map"
                                                                data-donor-name="{{ $donation->user->first_name }} {{ $donation->user->last_name }}"
                                                                data-donor-address="{{ $donation->user->address }}"
                                                                data-latitude="{{ $lat }}"
                                                                data-longitude="{{ $lng }}">
                                                                <i class="fas fa-map-marked-alt"></i>
                                                            </button>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td data-label="Date" class="text-center">
                                                    <small>
                                                        @if($donation->donation_method === 'walk_in')
                                                            {{ $donation->donation_date ? $donation->donation_date->format('M d, Y') : 'N/A' }}
                                                            @if($donation->availability)
                                                                <br>{{ $donation->availability->formatted_time }}
                                                            @elseif($donation->donation_time)
                                                                <br>{{ \Carbon\Carbon::parse($donation->donation_time)->format('g:i A') }}
                                                            @endif
                                                        @else
                                                            {{ $donation->created_at->format('M d, Y') }}<br>{{ $donation->created_at->format('g:i A') }}
                                                        @endif
                                                    </small>
                                                </td>
                                                <td data-label="Total Volume" class="text-center">
                                                    <strong>{{ $donation->formatted_total_volume ?? '-' }}ml</strong>
                                                </td>
                                                <td data-label="Action" class="text-center">
                                                    <div class="table-actions d-inline-flex align-items-center gap-2 flex-nowrap" style="display:inline-flex;flex-wrap:nowrap;align-items:center;gap:0.5rem;">
                                                        @if($donation->donation_method === 'walk_in')
                                                            <button class="btn btn-success btn-sm px-2 validate-walk-in"
                                                                title="Validate Walk-in" data-id="{{ $donation->breastmilk_donation_id }}"
                                                                data-donor="{{ $donation->user->first_name }} {{ $donation->user->last_name }}">
                                                                <i class="fas fa-check"></i>
                                                                <span class="d-none d-md-inline"> Validate</span>
                                                            </button>
                                                            <button class="btn btn-outline-danger btn-sm px-2"
                                                                title="Decline Donation"
                                                                onclick="declineDonation({{ $donation->breastmilk_donation_id }})">
                                                                <i class="fas fa-times"></i>
                                                                <span class="d-none d-md-inline"> Decline</span>
                                                            </button>
                                                        @else
                                                            <button class="btn btn-primary btn-sm px-2 schedule-pickup"
                                                                title="Schedule Pickup" data-id="{{ $donation->breastmilk_donation_id }}"
                                                                data-donor="{{ $donation->user->first_name }} {{ $donation->user->last_name }}"
                                                                data-address="{{ $donation->user->address ?? 'Not provided' }}"
                                                                data-first-expression="{{ $donation->first_expression_date ? $donation->first_expression_date->format('M d, Y') : '' }}"
                                                                data-last-expression="{{ $donation->last_expression_date ? $donation->last_expression_date->format('M d, Y') : '' }}"
                                                                data-bag-details="{{ json_encode($donation->bag_details) }}"
                                                                data-total="{{ $donation->total_volume }}">
                                                                <i class="fas fa-calendar-alt"></i>
                                                                <span class="d-none d-md-inline"> Schedule</span>
                                                            </button>
                                                            <button class="btn btn-outline-danger btn-sm px-2"
                                                                title="Decline Donation"
                                                                onclick="declineDonation({{ $donation->breastmilk_donation_id }})">
                                                                <i class="fas fa-times"></i>
                                                                <span class="d-none d-md-inline"> Decline</span>
                                                            </button>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            {{-- Card Layout for Smaller Screens --}}
                            @foreach($pendingOrdered as $donation)
                                <div class="donation-card" style="display: none;">
                                    <div class="card-header-row">
                                        <div>
                                            <strong style="font-size: 1rem;">{{ $donation->user->first_name ?? '' }} {{ $donation->user->last_name ?? '' }}</strong>
                                        </div>
                                        <div>
                                            @if($donation->donation_method === 'walk_in')
                                                <span class="badge bg-info">Walk-in</span>
                                            @else
                                                <span class="badge bg-primary">Home Collection</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="card-row">
                                        <span class="card-label">Contact:</span>
                                        <span class="card-value">{{ $donation->user->contact_number ?? $donation->user->phone ?? '-' }}</span>
                                    </div>

                                    @if($donation->donation_method === 'home_collection')
                                        <div class="card-row">
                                            <span class="card-label">Address:</span>
                                            <span class="card-value">{{ $donation->user->address ?? 'Not provided' }}</span>
                                        </div>
                                        @php
                                            $latCard = $donation->latitude ?? $donation->user->latitude ?? null;
                                            $lngCard = $donation->longitude ?? $donation->user->longitude ?? null;
                                        @endphp
                                        @if(!is_null($latCard) && $latCard !== '' && !is_null($lngCard) && $lngCard !== '')
                                            <div class="card-row">
                                                <span class="card-label">Location:</span>
                                                <span class="card-value">
                                                    <button class="btn btn-info btn-sm view-location"
                                                        data-donor-name="{{ $donation->user->first_name }} {{ $donation->user->last_name }}"
                                                        data-donor-address="{{ $donation->user->address }}"
                                                        data-latitude="{{ $latCard }}"
                                                        data-longitude="{{ $lngCard }}">
                                                        <i class="fas fa-map-marked-alt"></i> View Map
                                                    </button>
                                                </span>
                                            </div>
                                        @endif
                                    @endif

                                    <div class="card-row">
                                        <span class="card-label">Date:</span>
                                        <span class="card-value">
                                            @if($donation->donation_method === 'walk_in')
                                                {{ $donation->donation_date ? $donation->donation_date->format('M d, Y') : 'N/A' }}
                                                @if($donation->availability)
                                                    <br>{{ $donation->availability->formatted_time }}
                                                @elseif($donation->donation_time)
                                                    <br>{{ \Carbon\Carbon::parse($donation->donation_time)->format('g:i A') }}
                                                @endif
                                            @else
                                                {{ $donation->created_at->format('M d, Y') }}<br>{{ $donation->created_at->format('g:i A') }}
                                            @endif
                                        </span>
                                    </div>

                                    <div class="card-row">
                                        <span class="card-label">Total Volume:</span>
                                        <span class="card-value"><strong>{{ $donation->formatted_total_volume ?? '-' }}ml</strong></span>
                                    </div>

                                    <div class="card-actions">
                                        @if($donation->donation_method === 'walk_in')
                                            <button class="btn btn-success validate-walk-in"
                                                data-id="{{ $donation->breastmilk_donation_id }}"
                                                data-donor="{{ $donation->user->first_name }} {{ $donation->user->last_name }}">
                                                <i class="fas fa-check"></i> Validate Walk-in
                                            </button>
                                            <button class="btn btn-outline-danger"
                                                onclick="declineDonation({{ $donation->breastmilk_donation_id }})">
                                                <i class="fas fa-times"></i> Decline
                                            </button>
                                        @else
                                            <button class="btn btn-primary schedule-pickup"
                                                data-id="{{ $donation->breastmilk_donation_id }}"
                                                data-donor="{{ $donation->user->first_name }} {{ $donation->user->last_name }}"
                                                data-address="{{ $donation->user->address ?? 'Not provided' }}">
                                                <i class="fas fa-calendar-alt"></i> Schedule Pickup
                                            </button>
                                            <button class="btn btn-outline-danger"
                                                onclick="declineDonation({{ $donation->breastmilk_donation_id }})">
                                                <i class="fas fa-times"></i> Decline
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-3x mb-3"></i>
                                <p>No pending donations</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Scheduled Home Collection Tab -->
            <div class="tab-pane fade show {{ $tabStatus == 'scheduled' ? 'active' : '' }}" id="scheduled-home"
                role="tabpanel">
                <div class="card card-standard">
                    <div class="card-header bg-primary text-white py-3">
                        <h5 class="mb-0">Scheduled Home Collection</h5>
                    </div>
                    <div class="card-body">
                            @if($scheduledHomeCollection->count() > 0)
                            <div class="table-container table-wide">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th class="text-center">Name</th>
                                            <th class="text-center">Contact</th>
                                            <th class="text-center">Address</th>
                                            <th class="text-center">Location</th>
                                            <th class="text-center">Date</th>
                                            <th class="text-center">Time</th>
                                            <th class="text-center">Total volume</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $scheduledOrdered = $scheduledHomeCollection instanceof \Illuminate\Pagination\LengthAwarePaginator
                                                ? $scheduledHomeCollection->getCollection()->sortByDesc('created_at')
                                                : collect($scheduledHomeCollection)->sortByDesc('created_at');
                                        @endphp
                                        @foreach($scheduledOrdered as $donation)
                                            <tr>
                                                <td data-label="Name" class="text-center">
                                                    <strong>{{ $donation->user->first_name ?? '' }} {{ $donation->user->last_name ?? '' }}</strong>
                                                </td>
                                                <td data-label="Contact" class="text-center">
                                                    {{ $donation->user->contact_number ?? $donation->user->phone ?? '-' }}
                                                </td>
                                                <td data-label="Address" class="text-center">
                                                    <small>{{ $donation->user->address ?? 'Not provided' }}</small>
                                                </td>
                                                <td data-label="Location" class="text-center">
                                                    @if($donation->user->latitude !== null && $donation->user->latitude !== '' && $donation->user->longitude !== null && $donation->user->longitude !== '')
                                                        <button class="btn btn-info btn-sm view-location" title="View on Map"
                                                            data-donor-name="{{ $donation->user->first_name }} {{ $donation->user->last_name }}"
                                                            data-donor-address="{{ $donation->user->address }}"
                                                            data-latitude="{{ $donation->user->latitude }}"
                                                            data-longitude="{{ $donation->user->longitude }}">
                                                            <i class="fas fa-map-marked-alt"></i> Map
                                                        </button>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td data-label="Date" class="text-center">
                                                    <small>{{ $donation->scheduled_pickup_date->format('M d, Y') }}</small>
                                                </td>
                                                <td data-label="Time" class="text-center">
                                                    <small>{{ $donation->scheduled_pickup_time }}</small>
                                                </td>
                                                <td data-label="Total volume" class="text-center">
                                                    <strong>{{ $donation->formatted_total_volume }}ml</strong>
                                                </td>
                                                <td data-label="Action" class="text-center">
                                                    <div class="table-actions d-inline-flex align-items-center gap-2 flex-nowrap" style="display:inline-flex;flex-wrap:nowrap;align-items:center;gap:0.5rem;">
                                                        <button class="btn btn-success btn-sm px-2 validate-home-collection"
                                                            title="Validate" data-id="{{ $donation->breastmilk_donation_id }}"
                                                            data-donor="{{ $donation->user->first_name }} {{ $donation->user->last_name }}"
                                                            data-address="{{ $donation->user->address ?? 'Not provided' }}"
                                                            data-date="{{ $donation->scheduled_pickup_date ? $donation->scheduled_pickup_date->format('M d, Y') : '' }}"
                                                            data-time="{{ $donation->scheduled_pickup_time ?? '' }}"
                                                            data-bags="{{ $donation->number_of_bags }}"
                                                            data-bag-details="{{ json_encode($donation->bag_details) }}"
                                                            data-total="{{ $donation->formatted_total_volume }}">
                                                            <i class="fas fa-check"></i>
                                                            <span class="d-none d-md-inline"> Validate</span>
                                                        </button>
                                                        
                                                        {{-- Reschedule button for scheduled pickups --}}
                                                        <button class="btn btn-outline-primary btn-sm px-2 reschedule-pickup"
                                                            title="Reschedule Pickup" data-id="{{ $donation->breastmilk_donation_id }}"
                                                            data-donor="{{ $donation->user->first_name }} {{ $donation->user->last_name }}"
                                                            data-address="{{ $donation->user->address ?? 'Not provided' }}"
                                                            data-date-iso="{{ $donation->scheduled_pickup_date ? $donation->scheduled_pickup_date->format('Y-m-d') : '' }}"
                                                            data-time="{{ $donation->scheduled_pickup_time ?? '' }}"
                                                            data-bags="{{ $donation->number_of_bags }}"
                                                            data-bag-details="{{ json_encode($donation->bag_details) }}"
                                                            data-total="{{ $donation->formatted_total_volume }}">
                                                            <i class="fas fa-calendar-alt"></i>
                                                            <span class="d-none d-md-inline"> Reschedule</span>
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
                                <i class="fas fa-calendar-alt fa-3x mb-3"></i>
                                <p>No scheduled home collections</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Success Walk-in Tab -->
            <div class="tab-pane fade show {{ $tabStatus == 'success_walk_in' ? 'active' : '' }}" id="success-walk-in"
                role="tabpanel">
                <div class="card card-standard">
                    <div class="card-header bg-success text-white py-3">
                        <h5 class="mb-0">Completed Walk-in Donations</h5>
                    </div>
                    <div class="card-body">
                            @if($successWalkIn->count() > 0)
                            <div class="table-container table-wide">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th class="text-center">Name</th>
                                            <th class="text-center">Address</th>
                                            <th class="text-center">Total</th>
                                            <th class="text-center">Date</th>
                                            <th class="text-center">Time</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $walkInOrdered = $successWalkIn instanceof \Illuminate\Pagination\LengthAwarePaginator
                                                ? $successWalkIn->getCollection()->sortByDesc('updated_at')
                                                : collect($successWalkIn)->sortByDesc('updated_at');
                                        @endphp
                                        @foreach($walkInOrdered as $donation)
                                            <tr>
                                                <td data-label="Name" class="text-center">
                                                    {{ $donation->user->first_name ?? '' }} {{ $donation->user->last_name ?? '' }}
                                                </td>
                                                <td data-label="Address" class="text-center">
                                                    <small>{{ $donation->user->address ?? 'Not provided' }}</small>
                                                </td>
                                                <td data-label="Total" class="text-center">
                                                    {{ $donation->formatted_total_volume ?? 'N/A' }}ml
                                                </td>
                                                <td data-label="Date" class="text-center">
                                                    <small>{{ $donation->updated_at ? $donation->updated_at->format('M d, Y') : ($donation->donation_date ? $donation->donation_date->format('M d, Y') : 'N/A') }}</small>
                                                </td>
                                                <td data-label="Time" class="text-center">
                                                    <small>{{ $donation->updated_at ? $donation->updated_at->format('g:i A') : (isset($donation->donation_time) ? \Carbon\Carbon::parse($donation->donation_time)->format('g:i A') : 'N/A') }}</small>
                                                </td>
                                                <td data-label="Action" class="text-center">
                                                    <div class="table-actions d-inline-flex align-items-center gap-2 flex-nowrap" style="display:inline-flex;flex-wrap:nowrap;align-items:center;gap:0.5rem;">
                                                        <button class="btn btn-sm btn-primary me-1 view-donation" data-id="{{ $donation->breastmilk_donation_id }}" title="View donation">
                                                            <i class="fas fa-eye"></i>
                                                            <span class="d-none d-md-inline"> View</span>
                                                        </button>
                                                        <button class="btn btn-sm btn-danger" onclick="archiveDonation({{ $donation->breastmilk_donation_id }})" title="Archive donation" aria-label="Archive donation">
                                                            <i class="fas fa-archive"></i>
                                                            <span class="d-none d-md-inline"> Archive</span>
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
                                <p>No completed walk-in donations yet</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Success Home Collection Tab -->
            <div class="tab-pane fade show {{ $tabStatus == 'success_home_collection' ? 'active' : '' }}" id="success-home"
                role="tabpanel">
                <div class="card card-standard">
                    <div class="card-header bg-success text-white py-3">
                        <h5 class="mb-0">Completed Home Collection</h5>
                    </div>
                    <div class="card-body">
                        @if($successHomeCollection->count() > 0)
                            <div class="table-container table-wide">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th class="text-center">Name</th>
                                            <th class="text-center">Address</th>
                                            <th class="text-center">Location</th>
                                            <th class="text-center">Total volume</th>
                                            <th class="text-center">Date</th>
                                            <th class="text-center">Time</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $homeSuccessOrdered = $successHomeCollection instanceof \Illuminate\Pagination\LengthAwarePaginator
                                                ? $successHomeCollection->getCollection()->sortByDesc('created_at')
                                                : collect($successHomeCollection)->sortByDesc('created_at');
                                        @endphp
                                        @foreach($homeSuccessOrdered as $donation)
                                            <tr>
                                                <td data-label="Name" class="text-center">
                                                    <strong>{{ $donation->user->first_name ?? '' }}
                                                        {{ $donation->user->last_name ?? '' }}</strong>
                                                </td>
                                                <td data-label="Address" class="text-center">
                                                    <small>{{ $donation->user->address ?? 'Not provided' }}</small>
                                                </td>
                                                <td data-label="Location" class="text-center">
                                                    @if($donation->user->latitude !== null && $donation->user->latitude !== '' && $donation->user->longitude !== null && $donation->user->longitude !== '')
                                                        <button class="btn btn-info btn-sm view-location" title="View on Map"
                                                            data-donor-name="{{ $donation->user->first_name }} {{ $donation->user->last_name }}"
                                                            data-donor-address="{{ $donation->user->address }}"
                                                            data-latitude="{{ $donation->user->latitude }}"
                                                            data-longitude="{{ $donation->user->longitude }}">
                                                            <i class="fas fa-map-marked-alt"></i>
                                                        </button>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td data-label="Total volume" class="text-center">
                                                    <strong>{{ $donation->formatted_total_volume }}ml</strong>
                                                </td>
                                                <td data-label="Date" class="text-center">
                                                    <small>{{ $donation->scheduled_pickup_date->format('M d, Y') }}</small>
                                                </td>
                                                <td data-label="Time" class="text-center">
                                                    <small>{{ $donation->scheduled_pickup_time }}</small>
                                                </td>
                                                <td data-label="Action" class="text-center">
                                                    <div class="table-actions d-inline-flex align-items-center gap-2 flex-nowrap" style="display:inline-flex;flex-wrap:nowrap;align-items:center;gap:0.5rem;">
                                                        <button class="btn btn-sm btn-primary me-1 view-donation" data-id="{{ $donation->breastmilk_donation_id }}" title="View donation">
                                                            <i class="fas fa-eye"></i>
                                                            <span class="d-none d-md-inline"> View</span>
                                                        </button>
                                                        <button class="btn btn-sm btn-danger" onclick="archiveDonation({{ $donation->breastmilk_donation_id }})" title="Archive donation" aria-label="Archive donation">
                                                            <i class="fas fa-archive"></i>
                                                            <span class="d-none d-md-inline"> Archive</span>
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
                                <i class="fas fa-home fa-3x mb-3"></i>
                                <p>No completed home collections yet</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Archived Tab -->
            <div class="tab-pane fade show {{ $tabStatus == 'archived' ? 'active' : '' }}" id="archived-donations"
                role="tabpanel">
                <div class="card card-standard">
                    <div class="card-header bg-secondary text-white py-3">
                        <h5 class="mb-0">Archived Donations</h5>
                    </div>
                    <div class="card-body">
                        @if(!empty($archived) && $archived->count() > 0)
                            <div class="table-container table-wide">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th class="text-center">Name</th>
                                            <th class="text-center">Type</th>
                                            <th class="text-center">Total</th>
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
                                        @foreach($archivedOrdered as $donation)
                                            <tr>
                                                <td class="text-center">{{ $donation->user->first_name ?? '' }} {{ $donation->user->last_name ?? '' }}</td>
                                                <td class="text-center">
                                                    @if($donation->donation_method === 'walk_in')
                                                        <span class="badge bg-info">Walk-in</span>
                                                    @else
                                                        <span class="badge bg-primary">Home Collection</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">{{ $donation->formatted_total_volume ?? '-' }}ml</td>
                                                <td class="text-center">{{ $donation->deleted_at ? $donation->deleted_at->format('M d, Y g:i A') : '-' }}</td>
                                                <td class="text-center">
                                                    <button class="btn btn-sm btn-outline-success" onclick="restoreDonation({{ $donation->breastmilk_donation_id }})">Restore</button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-archive fa-3x mb-3"></i>
                                <p>No archived donations</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Declined Tab -->
            <div class="tab-pane fade show {{ $tabStatus == 'declined' ? 'active' : '' }}" id="declined-donations" role="tabpanel">
                <div class="card card-standard">
                    <div class="card-header bg-danger text-white py-3">
                        <h5 class="mb-0">Declined Donations</h5>
                    </div>
                    <div class="card-body">
                        @if(isset($declinedDonations) && $declinedDonations->count() > 0)
                            <div class="table-container table-wide">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th class="text-center">Name</th>
                                            <th class="text-center">Type</th>
                                            <th class="text-center">Address</th>
                                            <th class="text-center">Reason</th>
                                            <th class="text-center">Declined At</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($declinedDonations as $donation)
                                            <tr>
                                                <td data-label="Name" class="text-center">
                                                    <strong>{{ $donation->user->first_name ?? '' }} {{ $donation->user->last_name ?? '' }}</strong>
                                                </td>
                                                <td data-label="Type" class="text-center">
                                                    @if($donation->donation_method === 'walk_in')
                                                        <span class="badge bg-info">Walk-in</span>
                                                    @else
                                                        <span class="badge bg-primary">Home Collection</span>
                                                    @endif
                                                </td>
                                                <td data-label="Address" class="text-center">
                                                    <small>{{ $donation->user->address ?? 'Not provided' }}</small>
                                                </td>
                                                <td data-label="Reason" class="text-center">
                                                    <small>{{ $donation->decline_reason ?? '-' }}</small>
                                                </td>
                                                <td data-label="Declined At" class="text-center">
                                                    <small>{{ $donation->declined_at ? \Carbon\Carbon::parse($donation->declined_at)->format('M d, Y g:i A') : '-' }}</small>
                                                </td>
                                                <td data-label="Action" class="text-center">
                                                    <button class="btn btn-sm btn-danger" onclick="archiveDonation({{ $donation->breastmilk_donation_id }})" title="Archive donation">
                                                        <i class="fas fa-archive"></i>
                                                        <span class="d-none d-md-inline"> Archive</span>
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
                                <p>No declined donations</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Walk-in Validation Modal -->
        <div class="modal fade" id="validateWalkInModal" tabindex="-1" aria-labelledby="validateWalkInModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="validateWalkInModalLabel">Validate Walk-in Donation</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="validateWalkInForm" method="POST" novalidate>
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label"><strong>Donor:</strong></label>
                                <div id="walkin-donor-name" class="form-control-plaintext"></div>
                            </div>

                            {{-- Hidden donation id --}}
                            <input type="hidden" id="walkin-donation-id" name="donation_id" value="">

                            {{-- Inline error area --}}
                            <div id="walkin-form-error" class="alert alert-danger" role="alert" style="display:none;"
                                aria-live="polite"></div>

                            <div class="mb-3">
                                <label for="walkin-bags" class="form-label">Number of Bags <span
                                        class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="walkin-bags" name="number_of_bags" min="1"
                                    max="20" required onchange="generateWalkinBagFields()">
                            </div>

                            <div id="walkin-volumes-container" style="display: none;">
                                <label class="form-label">Volume for each bag (ml):</label>
                                <div id="walkin-volume-fields">
                                    <!-- Individual bag volume inputs will be generated here -->
                                </div>
                            </div>

                            <div class="mb-3" id="walkin-total-display" style="display: none;">
                                <label class="form-label">Total Volume:</label>
                                <div class="alert alert-success">
                                    <strong id="walkin-total">0 ml</strong>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-success" id="walkin-validate-submit">
                                <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"
                                    style="display:none;" id="walkin-validate-spinner"></span>
                                <span id="walkin-validate-text">Validate Donation</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Schedule Pickup Modal -->
        <div class="modal fade" id="schedulePickupModal" tabindex="-1" aria-labelledby="schedulePickupModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="schedulePickupModalLabel">Schedule Home Collection Pickup</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="schedulePickupForm" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label"><strong>Donor:</strong></label>
                                    <div id="schedule-donor-name" class="form-control-plaintext"></div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label"><strong>Address:</strong></label>
                                    <div id="schedule-donor-address" class="form-control-plaintext"></div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label"><strong>First Expression Date:</strong></label>
                                    <div id="schedule-first-expression" class="form-control-plaintext"></div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label"><strong>Last Expression Date:</strong></label>
                                    <div id="schedule-last-expression" class="form-control-plaintext"></div>
                                </div>
                            </div>

                            <!-- Bag Details Table -->
                            <div class="mb-3">
                                <label class="form-label"><strong>Bag Details:</strong></label>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-sm" id="schedule-bag-details-table">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Bag #</th>
                                                <th>Time</th>
                                                <th>Date</th>
                                                <th>Volume (ml)</th>
                                                <th>Storage</th>
                                                <th>Temp (°C)</th>
                                                <th>Collection Method</th>
                                            </tr>
                                        </thead>
                                        <tbody id="schedule-bag-details-body">
                                            <!-- Rows will be generated here -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Total Volume -->
                            <div class="alert alert-info mb-3">
                                <strong>Total Volume:</strong> <span id="schedule-total-volume">0</span> ml
                            </div>

                            <hr>

                            <h6 class="mb-3">Schedule Pickup</h6>
                            <div class="mb-3">
                                <label for="pickup-date" class="form-label">Pickup Date <span
                                        class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="pickup-date" name="scheduled_pickup_date"
                                    min="{{ date('Y-m-d') }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="pickup-time" class="form-label">Pickup Time <span
                                        class="text-danger">*</span></label>
                                <input type="time" class="form-control" id="pickup-time" name="scheduled_pickup_time"
                                    required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Schedule Pickup</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- View Donation Details Modal -->
        <div class="modal fade" id="viewDonationModal" tabindex="-1" aria-labelledby="viewDonationModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header" style="background: linear-gradient(135deg, #f8a5c2 0%, #f48fb1 100%); color: #000;">
                        <h5 class="modal-title fw-bold" id="viewDonationModalLabel">
                            Home Collection Success
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <!-- Loading State -->
                        <div id="donation-loading" class="text-center py-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-3">Loading donation details...</p>
                        </div>

                        <!-- Donation Details Content -->
                        <div id="donation-details" style="display: none;">
                            <!-- Donor Information Box -->
                            <div class="border rounded p-3 mb-3" style="background-color: #f0f0f0;">
                                <div class="mb-2">
                                    <strong>Name:</strong> <span id="view-donor-name"></span>
                                </div>
                                <div class="mb-2">
                                    <strong>Contact:</strong> <span id="view-donor-contact"></span>
                                </div>
                                <div class="mb-2">
                                    <strong>Address:</strong> <span id="view-donor-address"></span>
                                </div>
                                <div>
                                    <strong>Location:</strong> 
                                    <button class="btn btn-info btn-sm ms-2" id="view-location-btn" style="display: none;">
                                        <i class="fas fa-map-marked-alt"></i> Map
                                    </button>
                                    <span id="view-location-none" class="text-muted" style="display: none;">-</span>
                                </div>
                            </div>

                            <!-- Total Bag and Volume Summary -->
                            <div class="row mb-3">
                                <div class="col-6">
                                    <strong>Total Bag:</strong> <span id="view-total-bags"></span>
                                </div>
                                <div class="col-6">
                                    <strong>Total Vol:</strong> <span id="view-total-volume"></span>
                                </div>
                            </div>

                            <!-- Bag Details Table -->
                            <div class="border rounded p-3" style="background-color: #ffffff;">
                                <div class="table-responsive">
                                    <table class="table table-bordered mb-0">
                                        <thead class="table-light">
                                            <tr class="text-center">
                                                <th>Bag</th>
                                                <th>Volume</th>
                                                <th>Date</th>
                                                <th>Time</th>
                                                <th>Storage Location</th>
                                                <th>Temp(°C)</th>
                                                <th>Milk Collection Method</th>
                                            </tr>
                                        </thead>
                                        <tbody id="view-bag-details-body">
                                            <!-- Bag details will be inserted here -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Error State -->
                        <div id="donation-error" class="alert alert-danger" style="display: none;" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <span id="error-message">Failed to load donation details.</span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Home Collection Validation Modal -->
        <div class="modal fade" id="validateHomeCollectionModal" tabindex="-1"
            aria-labelledby="validateHomeCollectionModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title" id="validateHomeCollectionModalLabel">
                            <i class="fas fa-check-circle me-2"></i>Validate Collected Milk
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <form id="validateHomeCollectionForm" method="POST" novalidate>
                        @csrf
                        <div class="modal-body">
                            <div class="row mb-3">
                                <div class="col-md-6 mb-2">
                                    <label class="form-label"><strong>Donor:</strong></label>
                                    <input type="text" id="validate-home-donor-name" class="form-control" readonly>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label class="form-label"><strong>Address:</strong></label>
                                    <input type="text" id="validate-home-donor-address" class="form-control" readonly>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6 mb-2">
                                    <label class="form-label"><strong>Date:</strong></label>
                                    <input type="text" id="validate-home-date" class="form-control" readonly>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label class="form-label"><strong>Time:</strong></label>
                                    <input type="text" id="validate-home-time" class="form-control" readonly>
                                </div>
                            </div>
                            {{-- Hidden donation id --}}
                            <input type="hidden" id="home-donation-id" name="donation_id" value="">

                            {{-- Inline error area --}}
                            <div id="home-form-error" class="alert alert-danger" role="alert" style="display:none;"
                                aria-live="polite"></div>

                            <!-- Info Message -->
                            <div class="alert alert-info mb-4">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Review Home Collection Details</strong> - Verify all bag information collected during pickup.
                            </div>

                            <!-- Bag Details Table -->
                            <div class="table-responsive mb-3">
                                <table class="table table-bordered table-hover" id="home-bag-details-table">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width:8%">Bag #</th>
                                            <th style="width:12%">Time</th>
                                            <th style="width:15%">Date</th>
                                            <th style="width:12%">Volume (ml)</th>
                                            <th style="width:12%">Storage</th>
                                            <th style="width:12%">Temp (°C)</th>
                                            <th style="width:29%">Collection Method</th>
                                        </tr>
                                    </thead>
                                    <tbody id="home-bag-details-body">
                                        <!-- Rows will be generated here -->
                                    </tbody>
                                </table>
                            </div>

                            <!-- Total Volume Display -->
                            <div class="card border-info">
                                <div class="card-body text-center py-4 bg-info bg-opacity-10">
                                    <label class="form-label fw-bold text-info mb-2">
                                        <i class="fas fa-tint me-2"></i>Total Volume
                                    </label>
                                    <h2 class="mb-0 text-info fw-bold" id="home-total">0 ml</h2>
                                </div>
                            </div>
                            
                            <!-- Hidden inputs for form submission -->
                            <input type="hidden" id="home-bags" name="number_of_bags" value="">
                            <div id="home-volume-fields" style="display:none;">
                                <!-- Hidden volume inputs for form submission -->
                            </div>
                        </div>
                        <div class="modal-footer bg-light">
                            <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">
                                <i class="fas fa-times me-1"></i>Cancel
                            </button>
                            <button type="submit" class="btn btn-success btn-sm px-3" id="home-validate-submit">
                                <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"
                                    style="display:none;" id="home-validate-spinner"></span>
                                <i class="fas fa-check-double me-2"></i>
                                <span id="home-validate-text">Validate Collection</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>{{-- Close container-fluid --}}
@endsection

@section('scripts')
    {{-- Real-time Search Functionality (improved) --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('searchInput');
            const clearBtn = document.getElementById('clearSearch');
            const searchResults = document.getElementById('searchResults');

            if (!searchInput) return;

            // Helpers to extract searchable text from table rows and donation cards
            function extractRowFields(row) {
                // Try to get specific labeled cells first (Name, Address)
                const nameCell = row.querySelector('[data-label="Name"]');
                const addressCell = row.querySelector('[data-label="Address"]');
                const nameText = nameCell ? nameCell.textContent.trim() : '';
                const addressText = addressCell ? addressCell.textContent.trim() : '';
                return (nameText + ' ' + addressText).toLowerCase();
            }

            function extractCardFields(card) {
                // Donation-card layout: name in .card-header-row strong; address in a card-row where .card-label contains "Address"
                const nameEl = card.querySelector('.card-header-row strong') || card.querySelector('strong');
                let nameText = nameEl ? nameEl.textContent.trim() : '';

                // Find card-row with label 'Address'
                let addressText = '';
                const rows = card.querySelectorAll('.card-row');
                rows.forEach(r => {
                    const label = r.querySelector('.card-label');
                    const value = r.querySelector('.card-value');
                    if (label && /address/i.test(label.textContent || '')) {
                        addressText = value ? value.textContent.trim() : '';
                    }
                });

                // Fallback: any text content
                if (!addressText) addressText = card.textContent.trim();

                return (nameText + ' ' + addressText).toLowerCase();
            }


            // Gather targets only within the active tab to avoid duplicates
            function getActivePane() {
                return document.querySelector('.tab-pane.show.active') || document.querySelector('.tab-pane.active') || document.querySelector('.tab-pane');
            }

            function isVisible(el) {
                if (!el) return false;
                // offsetParent is null for display:none; getClientRects ensures visibility for elements with no size
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
                return Array.from(pane.querySelectorAll('.donation-card'));
            }

            function performSearch() {
                const term = searchInput.value.trim().toLowerCase();
                let totalCount = 0;
                let visibleCount = 0;

                const rows = getAllTableRowsInActivePane();
                const cards = getAllCardsInActivePane();

                // If no term, restore all rows/cards (remove inline display overrides)
                if (!term) {
                    rows.forEach(row => { row.style.display = ''; });
                    cards.forEach(card => { card.style.display = ''; });

                    // Count only those that are actually visible in the current layout
                    const visibleRows = rows.filter(r => isVisible(r));
                    const visibleCards = cards.filter(c => isVisible(c));
                    totalCount = visibleRows.length + visibleCards.length;
                    visibleCount = totalCount;

                    // Clear UI
                    clearBtn.style.display = 'none';
                    searchResults.textContent = '';
                    searchResults.classList.remove('text-danger');
                    return;
                }

                // With a search term: limit targets to currently visible elements to avoid matching hidden duplicates
                const visibleRows = rows.filter(r => isVisible(r));
                const visibleCards = cards.filter(c => isVisible(c));
                totalCount = visibleRows.length + visibleCards.length;

                // Handle table rows (desktop)
                visibleRows.forEach(row => {
                    const hay = extractRowFields(row);
                    if (hay.indexOf(term) !== -1) {
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });

                // Handle donation-card blocks (mobile view)
                visibleCards.forEach(card => {
                    const hay = extractCardFields(card);
                    if (hay.indexOf(term) !== -1) {
                        card.style.display = '';
                        visibleCount++;
                    } else {
                        card.style.display = 'none';
                    }
                });

                // Update UI
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
            clearBtn.addEventListener('click', function() {
                searchInput.value = '';
                performSearch();
                searchInput.focus();
            });

            // Initial run to ensure correct state
            performSearch();
        });
    </script>

    <script>
        let currentDonationId = null;
        let currentOriginalVolumes = []; // Store original volumes globally

        // Initialize modal triggers
        $(document).ready(function () {
            // Donation type filter for pending donations tab
            $('#donation-type-filter').on('change', function () {
                const selectedType = $(this).val();
                const currentUrl = new URL(window.location.href);
                currentUrl.searchParams.set('status', 'pending');
                currentUrl.searchParams.set('donation_type', selectedType);
                window.location.href = currentUrl.toString();
            });

            // Walk-in validation modal (handles both .validate-walkin and .validate-walk-in)
            $(document).on('click', '.validate-walkin, .validate-walk-in', function () {
                currentDonationId = $(this).data('id');
                const donorName = $(this).data('donor');

                $('#walkin-donor-name').text(donorName);
                $('#walkin-donation-id').val(currentDonationId);
                $('#validateWalkInForm').attr('action', `/admin/donations/${currentDonationId}/validate-walkin`);
                // reset form state
                $('#walkin-form-error').hide().text('');
                $('#walkin-volume-fields').html('');
                $('#walkin-bags').val('');
                $('#walkin-total').text('0 ml');
                $('#validateWalkInModal').modal('show');
            });

            // Schedule pickup modal  
            $(document).on('click', '.schedule-pickup', function () {
                currentDonationId = $(this).data('id');
                const donorName = $(this).data('donor');
                const donorAddress = $(this).data('address') || 'Not provided';
                const firstExpression = $(this).data('first-expression') || '--';
                const lastExpression = $(this).data('last-expression') || '--';
                const bagDetailsRaw = $(this).attr('data-bag-details');
                const totalVolume = $(this).data('total') || 0;

                // Populate donor info
                $('#schedule-donor-name').text(donorName);
                $('#schedule-donor-address').text(donorAddress);
                $('#schedule-first-expression').text(firstExpression);
                $('#schedule-last-expression').text(lastExpression);

                // Parse bag details
                let bagDetails = [];
                try {
                    if (typeof bagDetailsRaw === 'string' && bagDetailsRaw.trim() !== '' && bagDetailsRaw !== 'null') {
                        bagDetails = JSON.parse(bagDetailsRaw);
                    } else if (Array.isArray(bagDetailsRaw)) {
                        bagDetails = bagDetailsRaw;
                    }
                } catch (err) {
                    console.warn('Failed to parse bag details for donation', currentDonationId, err);
                    console.log('Raw bag details:', bagDetailsRaw);
                    bagDetails = [];
                }

                console.log('Parsed bag details:', bagDetails);

                // Populate bag details table
                const tbody = $('#schedule-bag-details-body');
                tbody.empty();
                
                // Helper function to format time
                function formatTime12(t) {
                    if (!t) return '--';
                    if (/\b(am|pm)\b/i.test(t)) return t;
                    const m = t.toString().match(/^(\d{1,2}):(\d{2})(?::(\d{2}))?$/);
                    if (!m) return t;
                    let hh = parseInt(m[1], 10);
                    const mm = m[2];
                    const ampm = hh >= 12 ? 'PM' : 'AM';
                    hh = hh % 12; if (hh === 0) hh = 12;
                    return hh + ':' + mm + ' ' + ampm;
                }
                
                let total = 0;
                if (bagDetails && bagDetails.length > 0) {
                    bagDetails.forEach((bag, index) => {
                        const bagNum = bag.bag_number || (index + 1);
                        const time = formatTime12(bag.time) || '--';
                        const date = bag.date || '--';
                        const volume = bag.volume || 0;
                        const storage = bag.storage_location || '--';
                        const temp = bag.temperature || '--';
                        const method = bag.collection_method || '--';
                        
                        total += parseFloat(volume) || 0;
                        
                        const row = `
                            <tr>
                                <td class="text-center fw-bold">Bag ${bagNum}</td>
                                <td>${time}</td>
                                <td>${date}</td>
                                <td class="text-end">${volume}</td>
                                <td>${storage}</td>
                                <td class="text-end">${temp}</td>
                                <td><small>${method}</small></td>
                            </tr>
                        `;
                        tbody.append(row);
                    });
                } else {
                    tbody.append('<tr><td colspan="7" class="text-center text-muted"><i class="fas fa-info-circle me-2"></i>No bag details available (donation created before system update)</td></tr>');
                }
                
                // Update total volume - use passed total if bag details not available
                const displayTotal = total > 0 ? total : totalVolume;
                $('#schedule-total-volume').text(parseFloat(displayTotal).toFixed(2));

                $('#schedulePickupForm').attr('action', `/admin/donations/${currentDonationId}/schedule-pickup`);
                $('#schedulePickupModal').modal('show');
            });

            // Reschedule existing pickup - show only date/time in the schedule modal
            $(document).on('click', '.reschedule-pickup', function () {
                currentDonationId = $(this).data('id');
                const dateIso = $(this).data('date-iso') || '';
                const timeRaw = $(this).data('time') || '';

                // Prefill date/time inputs; #pickup-date expects ISO yyyy-mm-dd, #pickup-time expects 24hr HH:MM
                $('#pickup-date').val(dateIso || '');

                // Convert timeRaw (possibly 'g:i A') to 24-hour HH:MM for input if necessary
                function to24Hour(t) {
                    if (!t) return '';
                    if (/^\d{2}:\d{2}$/.test(t)) return t;
                    const m = t.match(/(\d{1,2}):(\d{2})\s*(am|pm)/i);
                    if (!m) return '';
                    let hh = parseInt(m[1], 10);
                    const mm = m[2];
                    const ampm = m[3].toLowerCase();
                    if (ampm === 'pm' && hh < 12) hh += 12;
                    if (ampm === 'am' && hh === 12) hh = 0;
                    return (hh < 10 ? '0' + hh : hh) + ':' + mm;
                }

                $('#pickup-time').val(to24Hour(timeRaw));

                // Hide other modal sections so only date/time are visible
                $('#schedule-donor-name').closest('.row').hide();
                $('#schedule-first-expression').closest('.row').hide();
                // hide bag details container and total volume alert
                $('#schedule-bag-details-body').closest('.table-responsive').closest('.mb-3').hide();
                $('#schedule-total-volume').closest('.alert').hide();
                // hide header/label for scheduling
                $('#schedulePickupModal').find('hr').hide();
                $('#schedulePickupModal').find('h6.mb-3').hide();

                // Set form action to reschedule endpoint and update modal title/button
                $('#schedulePickupForm').attr('action', `/admin/donations/${currentDonationId}/reschedule-pickup`);
                $('#schedulePickupModalLabel').text('Reschedule Home Collection Pickup');
                $('#schedulePickupForm button[type="submit"]').text('Reschedule Pickup');

                $('#schedulePickupModal').modal('show');
            });

            // Home collection validation modal
            $('.validate-home-collection').click(function () {
                currentDonationId = $(this).data('id');
                const donorName = $(this).data('donor');
                const numberOfBags = $(this).data('bags');
                const bagDetailsRaw = $(this).attr('data-bag-details');
                const totalVolume = $(this).data('total');

                // Optional fields sent via data-attributes
                const donorAddress = $(this).data('address') || '';
                const scheduledDate = $(this).data('date') || '';
                const scheduledTimeRaw = $(this).data('time') || '';

                // Try to parse bag details safely (data-bag-details may be JSON string)
                let bagDetails = [];
                try {
                    if (typeof bagDetailsRaw === 'string' && bagDetailsRaw.trim() !== '' && bagDetailsRaw !== 'null') {
                        bagDetails = JSON.parse(bagDetailsRaw);
                    } else if (Array.isArray(bagDetailsRaw)) {
                        bagDetails = bagDetailsRaw;
                    }
                } catch (err) {
                    console.warn('Failed to parse bag details for donation', currentDonationId, err);
                    console.log('Raw bag details:', bagDetailsRaw);
                    bagDetails = [];
                }

                console.log('Validate modal - Parsed bag details:', bagDetails);

                // Set donation id and number of bags
                $('#home-donation-id').val(currentDonationId);
                $('#home-bags').val(numberOfBags || '');

                // Populate donor info and schedule details in the modal (readonly inputs)
                $('#validate-home-donor-name').val(donorName || '');
                $('#validate-home-donor-address').val(donorAddress || '');
                $('#validate-home-date').val(scheduledDate || '');
                
                // Format time to 12-hour with AM/PM if possible
                function formatTime12(t) {
                    if (!t) return '';
                    // If already contains AM/PM, leave as-is
                    if (/\b(am|pm)\b/i.test(t)) return t;
                    // Match HH:MM or HH:MM:SS
                    const m = t.toString().match(/^(\d{1,2}):(\d{2})(?::(\d{2}))?$/);
                    if (!m) return t;
                    let hh = parseInt(m[1], 10);
                    const mm = m[2];
                    const ampm = hh >= 12 ? 'PM' : 'AM';
                    hh = hh % 12; if (hh === 0) hh = 12;
                    return hh + ':' + mm + ' ' + ampm;
                }

                const scheduledTime = formatTime12(scheduledTimeRaw);
                $('#validate-home-time').val(scheduledTime || '');

                // Set form action
                $('#validateHomeCollectionForm').attr('action', `/admin/donations/${currentDonationId}/validate-pickup`);

                // Populate bag details table
                const tbody = $('#home-bag-details-body');
                tbody.empty();
                
                let totalVol = 0;
                const volumeFieldsContainer = $('#home-volume-fields');
                volumeFieldsContainer.empty();

                if (bagDetails && bagDetails.length > 0) {
                    bagDetails.forEach((bag, index) => {
                        const bagNum = bag.bag_number || (index + 1);
                        const time = formatTime12(bag.time) || '--';
                        const date = bag.date || '--';
                        const volume = bag.volume || 0;
                        const storage = bag.storage_location || '--';
                        const temp = bag.temperature || '--';
                        const method = bag.collection_method || '--';
                        
                        totalVol += parseFloat(volume) || 0;
                        
                        // Add row to table
                        const row = `
                            <tr>
                                <td class="text-center fw-bold">Bag ${bagNum}</td>
                                <td>${time}</td>
                                <td>${date}</td>
                                <td class="text-end">${volume}</td>
                                <td>${storage}</td>
                                <td class="text-end">${temp}</td>
                                <td><small>${method}</small></td>
                            </tr>
                        `;
                        tbody.append(row);
                        
                        // Add hidden input for volume (for form submission)
                        volumeFieldsContainer.append(`<input type="hidden" name="bag_volumes[]" value="${volume}">`);
                    });
                } else {
                    tbody.append('<tr><td colspan="7" class="text-center text-muted"><i class="fas fa-info-circle me-2"></i>No bag details available (donation created before system update)</td></tr>');
                    // If no bag details, use total volume from data attribute
                    totalVol = totalVolume || 0;
                }
                
                // Update total display
                $('#home-total').text(totalVol.toFixed(2) + ' ml');

                $('#home-form-error').hide().text('');
                $('#validateHomeCollectionModal').modal('show');
            });

            // Calculate total volume for walk-in (will be updated to use individual volumes)
            $('#walkin-bags').on('input', function () {
                generateWalkinBagFields();
            });

            // Calculate total volume for home collection when bag count changes
            $('#home-bags').on('input change', function () {
                generateHomeBagFields();
            });



            // Generate individual bag volume fields for walk-in validation
            function generateWalkinBagFields() {
                const bagCount = parseInt($('#walkin-bags').val()) || 0;
                const container = $('#walkin-volume-fields');
                const volumesContainer = $('#walkin-volumes-container');
                const totalDisplay = $('#walkin-total-display');

                if (bagCount <= 0) {
                    volumesContainer.hide();
                    totalDisplay.hide();
                    container.html('');
                    return;
                }

                volumesContainer.show();
                totalDisplay.show();

                let fieldsHTML = '<div class="row">';
                for (let i = 1; i <= bagCount; i++) {
                    fieldsHTML += `
                                                                                                                                    <div class="col-md-6 mb-2">
                                                                                                                                        <label for="walkin_bag_volume_${i}" class="form-label">Bag ${i} Volume (ml):</label>
                                                                                                                                        <input type="number" 
                                                                                                                                               id="walkin_bag_volume_${i}" 
                                                                                                                                               name="bag_volumes[]" 
                                                                                                                                               class="form-control walkin-bag-volume-input" 
                                                                                                                                               step="0.01" 
                                                                                                                                               min="0.01" 
                                                                                                                                               required>
                                                                                                                                    </div>
                                                                                                                                `;
                }
                fieldsHTML += '</div>';

                container.html(fieldsHTML);
                calculateWalkinTotal();
            }



            // Calculate total from individual bag volumes for walk-in
            function calculateWalkinTotal() {
                const bagCount = parseInt($('#walkin-bags').val()) || 0;
                let total = 0;

                for (let i = 1; i <= bagCount; i++) {
                    const volumeInput = $(`#walkin_bag_volume_${i}`);
                    if (volumeInput.length && volumeInput.val()) {
                        // use parseFloat but guard against commas and spaces
                        const parsed = parseFloat(String(volumeInput.val()).replace(/,/g, '').trim());
                        total += isNaN(parsed) ? 0 : parsed;
                    }
                }

                // Remove .00 from whole numbers
                const displayTotal = total % 1 === 0 ? Math.round(total) : total.toFixed(2).replace(/\.?0+$/, '');
                $('#walkin-total').text(displayTotal + ' ml');
            }

            // Handle form submissions with loading states
            $('#validateWalkInForm').submit(function (e) {
                e.preventDefault();
                const submitBtn = $(this).find('button[type="submit"]');
                const spinner = $('#walkin-validate-spinner');
                const text = $('#walkin-validate-text');
                $('#walkin-form-error').hide().text('');

                // Basic front-end validation: ensure at least one bag volume entered when bags > 0
                const bagCount = parseInt($('#walkin-bags').val()) || 0;
                if (bagCount > 0) {
                    let hasValue = false;
                    for (let i = 1; i <= bagCount; i++) {
                        const v = $(`#walkin_bag_volume_${i}`).val();
                        if (v && parseFloat(v) > 0) { hasValue = true; break; }
                    }
                    if (!hasValue) {
                        $('#walkin-form-error').text('Please provide volumes for the bags.').show();
                        return;
                    }
                }

                submitBtn.prop('disabled', true);
                spinner.show();
                text.text('Validating...');

                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function (response) {
                        if (response && response.success) {
                            // Close modal first, then show SweetAlert
                            $('#validateWalkInModal').modal('hide');
                            setTimeout(() => {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Donation validated',
                                    text: response.message || 'Walk-in donation validated successfully.',
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    location.reload();
                                });
                            }, 300);
                        } else {
                            const msg = response && response.message ? response.message : 'An error occurred.';
                            $('#walkin-form-error').text(msg).show();
                            submitBtn.prop('disabled', false);
                            spinner.hide();
                            text.text('Validate Donation');
                        }
                    },
                    error: function (xhr) {
                        const msg = xhr && xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'An error occurred. Please try again.';
                        $('#walkin-form-error').text(msg).show();
                        submitBtn.prop('disabled', false);
                        spinner.hide();
                        text.text('Validate Donation');
                    }
                });
            });

            $('#schedulePickupForm').submit(function (e) {
                e.preventDefault();
                const submitBtn = $(this).find('button[type="submit"]');

                const action = $(this).attr('action') || '';
                const isReschedule = action.indexOf('/reschedule-pickup') !== -1;

                // Use different texts depending on scheduling vs rescheduling
                const busyText = isReschedule ? 'Rescheduling...' : 'Scheduling...';
                const idleText = isReschedule ? 'Reschedule Pickup' : 'Schedule Pickup';
                const successTitle = isReschedule ? 'Pickup rescheduled' : 'Pickup scheduled';
                const successDefault = isReschedule ? 'Pickup rescheduled successfully.' : 'Pickup scheduled successfully.';

                submitBtn.prop('disabled', true).text(busyText);

                $.ajax({
                    url: action,
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function (response) {
                        if (response && response.success) {
                            $('#schedulePickupModal').modal('hide');
                            setTimeout(() => {
                                Swal.fire({
                                    icon: 'success',
                                    title: successTitle,
                                    text: response.message || successDefault,
                                    timer: 1400,
                                    showConfirmButton: false
                                }).then(() => {
                                    location.reload();
                                });
                            }, 300);
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: (response && response.message) ? response.message : 'An error occurred.'
                            });
                            submitBtn.prop('disabled', false).text(idleText);
                        }
                    },
                    error: function (xhr) {
                        const msg = xhr && xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'An error occurred. Please try again.';
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: msg
                        });
                        submitBtn.prop('disabled', false).text(idleText);
                    }
                });
            });

            $('#validateHomeCollectionForm').submit(function (e) {
                e.preventDefault();
                const submitBtn = $(this).find('button[type="submit"]');
                const spinner = $('#home-validate-spinner');
                const text = $('#home-validate-text');
                $('#home-form-error').hide().text('');

                // Basic front-end validation
                const bagCount = parseInt($('#home-bags').val()) || 0;
                if (bagCount <= 0) {
                    $('#home-form-error').text('No bags to validate.').show();
                    return;
                }

                // Validate that all bag volumes are entered
                let hasAllVolumes = true;
                for (let i = 1; i <= bagCount; i++) {
                    const volumeInput = $(`#home_bag_volume_${i}`);
                    if (!volumeInput.val() || parseFloat(volumeInput.val()) <= 0) {
                        hasAllVolumes = false;
                        break;
                    }
                }

                if (!hasAllVolumes) {
                    $('#home-form-error').text('Please enter valid volumes for all bags.').show();
                    return;
                }

                submitBtn.prop('disabled', true);
                spinner.show();
                text.text('Validating...');

                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function (response) {
                        if (response && response.success) {
                            // Close modal first, then show SweetAlert
                            $('#validateHomeCollectionModal').modal('hide');
                            setTimeout(() => {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Collection validated',
                                    text: response.message || 'Home collection validated successfully.',
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    location.reload();
                                });
                            }, 300);
                        } else {
                            const msg = response && response.message ? response.message : 'An error occurred.';
                            $('#home-form-error').text(msg).show();
                            submitBtn.prop('disabled', false);
                            spinner.hide();
                            text.text('Complete Collection');
                        }
                    },
                    error: function (xhr) {
                        const msg = xhr && xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'An error occurred. Please try again.';
                        $('#home-form-error').text(msg).show();
                        submitBtn.prop('disabled', false);
                        spinner.hide();
                        text.text('Complete Collection');
                    }
                });
            });


        });

        // Reset modals when closed to avoid stale state
        $('#validateWalkInModal').on('hidden.bs.modal', function () {
            $('#validateWalkInForm')[0].reset();
            $('#walkin-volume-fields').html('');
            $('#walkin-total').text('0 ml');
            $('#walkin-form-error').hide().text('');
            $('#walkin-validate-spinner').hide();
            $('#walkin-validate-text').text('Validate Donation');
            $('#walkin-donation-id').val('');
            $('#validateWalkInForm').attr('action', '');
            $(this).find('button[type="submit"]').prop('disabled', false);
        });

        // Reset schedule modal to default when closed (restore title, button text, action)
        $('#schedulePickupModal').on('hidden.bs.modal', function () {
            $('#schedulePickupModalLabel').text('Schedule Home Collection Pickup');
            $('#schedulePickupForm button[type="submit"]').text('Schedule Pickup');
            $('#schedulePickupForm').attr('action', '');
            $('#pickup-date').val('');
            $('#pickup-time').val('');
            $('#schedule-bag-details-body').empty();
            $('#schedule-total-volume').text('0');
            // Ensure all sections are visible again (in case reschedule hid some)
            $('#schedule-donor-name').closest('.row').show();
            $('#schedule-first-expression').closest('.row').show();
            $('#schedule-bag-details-body').closest('.table-responsive').closest('.mb-3').show();
            $('#schedule-total-volume').closest('.alert').show();
            $('#schedulePickupModal').find('hr').show();
            $('#schedulePickupModal').find('h6.mb-3').show();
        });

        $('#validateHomeCollectionModal').on('hidden.bs.modal', function () {
            $('#validateHomeCollectionForm')[0].reset();
            $('#home-volume-fields').html('');
            $('#home-total').text('0 ml');
            $('#home-form-error').hide().text('');
            $('#home-validate-spinner').hide();
            $('#home-validate-text').text('Validate Collection');
            $('#home-donation-id').val('');
            $('#home-bags').val('');
            $('#validateHomeCollectionForm').attr('action', '');
            $(this).find('button[type="submit"]').prop('disabled', false);
            currentOriginalVolumes = [];
            // Clear donor and schedule details to avoid stale info
            $('#validate-home-donor-name').val('');
            $('#validate-home-donor-address').val('');
            $('#validate-home-date').val('');
            $('#validate-home-time').val('');
        });            // Global functions for individual bag volume management
        window.generateWalkinBagFields = function () {
            const bagCount = parseInt($('#walkin-bags').val()) || 0;
            const container = $('#walkin-volume-fields');
            const volumesContainer = $('#walkin-volumes-container');
            const totalDisplay = $('#walkin-total-display');

            if (bagCount <= 0) {
                volumesContainer.hide();
                totalDisplay.hide();
                container.html('');
                return;
            }

            // Store existing values before regenerating
            const existingValues = {};
            for (let i = 1; i <= 100; i++) { // Check up to 100 to capture any existing values
                const input = $(`#walkin_bag_volume_${i}`);
                if (input.length && input.val()) {
                    existingValues[i] = input.val();
                }
            }

            volumesContainer.show();
            totalDisplay.show();

            let fieldsHTML = '<div class="row">';
            for (let i = 1; i <= bagCount; i++) {
                // Restore existing value if it exists
                const existingValue = existingValues[i] || '';
                fieldsHTML += `
                                                                                                                                    <div class="col-md-6 mb-2">
                                                                                                                                        <label for="walkin_bag_volume_${i}" class="form-label">Bag ${i} Volume (ml):</label>
                                                                                                                                        <input type="number" 
                                                                                                                                               id="walkin_bag_volume_${i}" 
                                                                                                                                               name="bag_volumes[]" 
                                                                                                                                               class="form-control walkin-bag-volume-input" 
                                                                                                                                               step="0.01" 
                                                                                                                                               min="0.01" 
                                                                                                                                               value="${existingValue}"
                                                                                                                                               placeholder="Enter volume"
                                                                                                                                               required>
                                                                                                                                    </div>
                                                                                                                                `;
            }
            fieldsHTML += '</div>';

            container.html(fieldsHTML);
            calculateWalkinTotal();
        };



        window.calculateWalkinTotal = function () {
            const bagCount = parseInt($('#walkin-bags').val()) || 0;
            let total = 0;

            for (let i = 1; i <= bagCount; i++) {
                const volumeInput = $(`#walkin_bag_volume_${i}`);
                if (volumeInput.length && volumeInput.val()) {
                    const parsed = parseFloat(String(volumeInput.val()).replace(/,/g, '').trim());
                    total += isNaN(parsed) ? 0 : parsed;
                }
            }

            // Remove .00 from whole numbers
            const displayTotal = total % 1 === 0 ? Math.round(total) : total.toFixed(2).replace(/\.?0+$/, '');
            $('#walkin-total').text(displayTotal + ' ml');
        };

        // Delegated input listener for walkin volume inputs to update total in real-time
        $('#walkin-volume-fields').on('input', '.walkin-bag-volume-input', function () {
            calculateWalkinTotal();
        });

        // Global functions for home collection validation
        window.generateHomeBagFields = function () {
            const bagCount = parseInt($('#home-bags').val()) || 0;
            const container = $('#home-volume-fields');

            if (bagCount <= 0) {
                container.html('');
                $('#home-total').text('0 ml');
                return;
            }

            // Store existing values before regenerating
            const existingValues = {};
            for (let i = 1; i <= 100; i++) { // Check up to 100 to capture any existing values
                const input = $(`#home_bag_volume_${i}`);
                if (input.length && input.val()) {
                    existingValues[i] = input.val();
                }
            }

            let fieldsHTML = '<div class="row g-3">';
            for (let i = 1; i <= bagCount; i++) {
                // Pre-populate with existing values if available, or use original values from data
                let existingValue = existingValues[i] || '';
                if (!existingValue && currentOriginalVolumes && currentOriginalVolumes[i - 1]) {
                    existingValue = currentOriginalVolumes[i - 1];
                }

                fieldsHTML += `
                                                                                                        <div class="col-md-6">
                                                                                                            <div class="input-group input-group-lg">
                                                                                                                <span class="input-group-text bg-primary text-white fw-bold">
                                                                                                                    <i class="fas fa-flask me-2"></i>Bag ${i}
                                                                                                                </span>
                                                                                                                <input type="number" 
                                                                                                                       id="home_bag_volume_${i}" 
                                                                                                                       name="bag_volumes[]" 
                                                                                                                       class="form-control home-bag-volume-input" 
                                                                                                                       step="0.01" 
                                                                                                                       min="0.01" 
                                                                                                                       value="${existingValue}"
                                                                                                                       placeholder="Enter volume"
                                                                                                                       required>
                                                                                                                <span class="input-group-text">ml</span>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    `;
            }
            fieldsHTML += '</div>';

            container.html(fieldsHTML);
            calculateHomeTotal();
        }; window.calculateHomeTotal = function () {
            const bagCount = parseInt($('#home-bags').val()) || 0;
            let total = 0;

            for (let i = 1; i <= bagCount; i++) {
                const volumeInput = $(`#home_bag_volume_${i}`);
                if (volumeInput.length && volumeInput.val()) {
                    const parsed = parseFloat(String(volumeInput.val()).replace(/,/g, '').trim());
                    total += isNaN(parsed) ? 0 : parsed;
                }
            }

            // Remove .00 from whole numbers
            const displayTotal = total % 1 === 0 ? Math.round(total) : total.toFixed(2).replace(/\.?0+$/, '');
            $('#home-total').text(displayTotal + ' ml');
        };

        // Delegated input listener for home volume inputs to update total in real-time
        $('#home-volume-fields').on('input', '.home-bag-volume-input', function () {
            calculateHomeTotal();
        });

        // View Location button handler
        $(document).on('click', '.view-location', function () {
            const donorName = $(this).data('donor-name');
            const donorAddress = $(this).data('donor-address');
            const latitude = parseFloat($(this).data('latitude'));
            const longitude = parseFloat($(this).data('longitude'));

            // Use numeric validation to allow 0 coordinates
            if (Number.isFinite(latitude) && Number.isFinite(longitude)) {
                showLocationModal(donorName, donorAddress, latitude, longitude);
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'Location Unavailable',
                    text: 'No location data available for this donor.'
                });
            }
        });
    </script>

    <!-- Include Location Modal -->
    @include('partials.location-modal')
    
    <script>
        // View Donation Details Handler
        $(document).on('click', '.view-donation', function() {
            const donationId = $(this).data('id');
            
            // Show modal and reset states
            $('#viewDonationModal').modal('show');
            $('#donation-loading').show();
            $('#donation-details').hide();
            $('#donation-error').hide();
            $('#view-location-btn').hide();
            $('#view-location-none').hide();
            
            // Fetch donation details
            $.ajax({
                url: `/admin/donations/${donationId}`,
                method: 'GET',
                success: function(response) {
                    if (response.success && response.donation) {
                        const donation = response.donation;
                        
                        // Populate donor information
                        $('#view-donor-name').text(donation.donor_name || 'N/A');
                        $('#view-donor-contact').text(donation.contact || 'N/A');
                        $('#view-donor-address').text(donation.address || 'Not provided');
                        
                        // Handle location data
                        if (donation.latitude && donation.longitude) {
                            $('#view-location-btn').show().off('click').on('click', function() {
                                showLocationModal(
                                    donation.donor_name,
                                    donation.address,
                                    parseFloat(donation.latitude),
                                    parseFloat(donation.longitude)
                                );
                            });
                        } else {
                            $('#view-location-none').show();
                        }
                        
                        // Populate totals
                        $('#view-total-bags').text(donation.number_of_bags || 0);
                        $('#view-total-volume').text(donation.total_volume || 'N/A');
                        
                        // Populate bag details table
                        const bagDetailsBody = $('#view-bag-details-body');
                        bagDetailsBody.empty();
                        
                        if (donation.bag_details && donation.bag_details.length > 0) {
                            donation.bag_details.forEach((bag, index) => {
                                const bagNum = bag.bag_number || (index + 1);
                                const volume = bag.volume || 0;
                                const date = bag.date || donation.donation_date || 'N/A';
                                const time = bag.time || donation.donation_time || 'N/A';
                                const storage = bag.storage_location || '-';
                                const temp = bag.temperature || '-';
                                const method = bag.collection_method || '-';
                                
                                const row = `
                                    <tr class="text-center">
                                        <td>${bagNum}</td>
                                        <td>${volume}</td>
                                        <td>${date}</td>
                                        <td>${time}</td>
                                        <td>${storage}</td>
                                        <td>${temp}</td>
                                        <td>${method}</td>
                                    </tr>
                                `;
                                bagDetailsBody.append(row);
                            });
                        } else {
                            // If no bag details, create rows based on number of bags
                            const numBags = donation.number_of_bags || 0;
                            if (numBags > 0) {
                                for (let i = 1; i <= numBags; i++) {
                                    const row = `
                                        <tr class="text-center">
                                            <td>${i}</td>
                                            <td>120</td>
                                            <td>${donation.donation_date || 'N/A'}</td>
                                            <td>${donation.donation_time || 'N/A'}</td>
                                            <td>-</td>
                                            <td>-</td>
                                            <td>-</td>
                                        </tr>
                                    `;
                                    bagDetailsBody.append(row);
                                }
                            } else {
                                bagDetailsBody.append(`
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">No bag details available</td>
                                    </tr>
                                `);
                            }
                        }
                        
                        // Show details, hide loading
                        $('#donation-loading').hide();
                        $('#donation-details').show();
                    } else {
                        throw new Error('Invalid response format');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching donation details:', error);
                    $('#donation-loading').hide();
                    $('#error-message').text(xhr.responseJSON?.message || 'Failed to load donation details. Please try again.');
                    $('#donation-error').show();
                }
            });
        });
        
        // Reset modal when closed
        $('#viewDonationModal').on('hidden.bs.modal', function() {
            $('#donation-loading').show();
            $('#donation-details').hide();
            $('#donation-error').hide();
            $('#view-bag-details-body').empty();
            $('#view-location-btn').hide().off('click');
            $('#view-location-none').hide();
        });
    </script>
    
    <script>
        function archiveDonation(id) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Archive donation?',
                    text: 'This will archive (soft-delete) the donation record. You can restore it from the database if needed.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, archive',
                    preConfirm: () => {
                        return fetch(`/admin/donations/${id}/archive`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        }).then(r => r.json());
                    }
                }).then(result => {
                    if (result.isConfirmed) {
                        Swal.fire('Archived', 'Donation archived successfully.', 'success').then(()=> location.reload());
                    }
                }).catch(()=> Swal.fire('Error', 'Failed to archive donation', 'error'));
            } else {
                if (!confirm('Archive donation?')) return;
                fetch(`/admin/donations/${id}/archive`, { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } })
                    .then(()=> location.reload())
                    .catch(()=> alert('Failed to archive'));
            }
        }
    </script>
    <script>
        function restoreDonation(id) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Restore donation?',
                    text: 'This will restore the archived donation back to active lists.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, restore',
                    preConfirm: () => {
                        return fetch(`/admin/donations/${id}/restore`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        }).then(r => r.json());
                    }
                }).then(result => {
                    if (result.isConfirmed) {
                        Swal.fire('Restored', 'Donation restored successfully.', 'success').then(()=> location.reload());
                    }
                }).catch(()=> Swal.fire('Error', 'Failed to restore donation', 'error'));
            } else {
                if (!confirm('Restore donation?')) return;
                fetch(`/admin/donations/${id}/restore`, { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } })
                    .then(()=> location.reload())
                    .catch(()=> alert('Failed to restore'));
            }
        }
    </script>
    <script>
        function declineDonation(id) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Decline donation',
                    input: 'textarea',
                    inputLabel: 'Reason for decline',
                    inputPlaceholder: 'Enter reason/notes...',
                    inputAttributes: { 'aria-label': 'Reason for decline' },
                    inputValidator: (value) => {
                        if (!value || value.trim() === '') {
                            return 'Please enter a reason.';
                        }
                        return undefined;
                    },
                    showCancelButton: true,
                    confirmButtonText: 'Decline'
                }).then(result => {
                    if (result.isConfirmed) {
                        const reason = result.value.trim();
                        fetch(`/admin/donations/${id}/decline`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({ reason })
                        })
                        .then(r => r.json())
                        .then(data => {
                            if (data && data.success) {
                                Swal.fire('Declined', data.message || 'Donation declined successfully.', 'success')
                                    .then(()=> location.reload());
                            } else {
                                Swal.fire('Error', (data && data.message) || 'Failed to decline donation', 'error');
                            }
                        })
                        .catch(()=> Swal.fire('Error', 'Failed to decline donation', 'error'));
                    }
                });
            } else {
                const reason = prompt('Enter reason for declining:');
                if (!reason || reason.trim() === '') return;
                fetch(`/admin/donations/${id}/decline`, { 
                    method: 'POST', 
                    headers: { 
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ reason: reason.trim() })
                })
                .then(()=> location.reload())
                .catch(()=> alert('Failed to decline donation'));
            }
        }
    </script>
@endsection