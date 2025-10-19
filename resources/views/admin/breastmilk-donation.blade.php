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
                                            <th class="text-center">Address</th>
                                            <th class="text-center">Location</th>
                                            <th class="text-center">Bags</th>
                                            <th class="text-center">Volume/Bag</th>
                                            <th class="text-center">Total</th>
                                            <th class="text-center">Date & Time</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($pendingDonations as $donation)
                                            <tr>
                                                <td data-label="Name" class="text-center">
                                                    <strong>{{ $donation->user->first_name ?? '' }}
                                                        {{ $donation->user->last_name ?? '' }}</strong>
                                                </td>
                                                <td data-label="Type" class="text-center">
                                                    @if($donation->donation_method === 'walk_in')
                                                        <span class="badge bg-info">Walk-in</span>
                                                    @else
                                                        <span class="badge bg-primary">Home Collection</span>
                                                    @endif
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
                                                    @if($donation->donation_method === 'home_collection' && $donation->user->latitude !== null && $donation->user->latitude !== '' && $donation->user->longitude !== null && $donation->user->longitude !== '')
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
                                                <td data-label="Bags" class="text-center">
                                                    <strong>{{ $donation->number_of_bags ?? '-' }}</strong>
                                                </td>
                                                <td data-label="Volume/Bag" class="text-center">
                                                    <small>
                                                        @if($donation->individual_bag_volumes)
                                                            {{ $donation->formatted_bag_volumes }}
                                                        @elseif($donation->volume_per_bag)
                                                            {{ $donation->volume_per_bag }}ml each
                                                        @else
                                                            -
                                                        @endif
                                                    </small>
                                                </td>
                                                <td data-label="Total" class="text-center">
                                                    <strong>{{ $donation->formatted_total_volume ?? '-' }}ml</strong>
                                                </td>
                                                <td data-label="Date & Time" class="text-center">
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
                                                <td data-label="Actions" class="text-center">
                                                    <div class="table-actions">
                                                        @if($donation->donation_method === 'walk_in')
                                                            <button class="btn btn-success btn-sm px-2 validate-walk-in"
                                                                title="Validate Walk-in" data-id="{{ $donation->breastmilk_donation_id }}"
                                                                data-donor="{{ $donation->user->first_name }} {{ $donation->user->last_name }}">
                                                                <i class="fas fa-check"></i>
                                                                <span class="d-none d-md-inline"> Validate</span>
                                                            </button>
                                                        @else
                                                            <button class="btn btn-primary btn-sm px-2 schedule-pickup"
                                                                title="Schedule Pickup" data-id="{{ $donation->breastmilk_donation_id }}"
                                                                data-donor="{{ $donation->user->first_name }} {{ $donation->user->last_name }}"
                                                                data-address="{{ $donation->user->address ?? 'Not provided' }}">
                                                                <i class="fas fa-calendar-alt"></i>
                                                                <span class="d-none d-md-inline"> Schedule</span>
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
                            @foreach($pendingDonations as $donation)
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
                                    
                                    @if($donation->donation_method === 'home_collection')
                                        <div class="card-row">
                                            <span class="card-label">Address:</span>
                                            <span class="card-value">{{ $donation->user->address ?? 'Not provided' }}</span>
                                        </div>
                                        @if($donation->user->latitude && $donation->user->longitude)
                                            <div class="card-row">
                                                <span class="card-label">Location:</span>
                                                <span class="card-value">
                                                    <button class="btn btn-info btn-sm view-location"
                                                        data-donor-name="{{ $donation->user->first_name }} {{ $donation->user->last_name }}"
                                                        data-donor-address="{{ $donation->user->address }}"
                                                        data-latitude="{{ $donation->user->latitude }}"
                                                        data-longitude="{{ $donation->user->longitude }}">
                                                        <i class="fas fa-map-marked-alt"></i> View Map
                                                    </button>
                                                </span>
                                            </div>
                                        @endif
                                    @endif
                                    
                                    <div class="card-row">
                                        <span class="card-label">Number of Bags:</span>
                                        <span class="card-value"><strong>{{ $donation->number_of_bags ?? '-' }}</strong></span>
                                    </div>
                                    
                                    <div class="card-row">
                                        <span class="card-label">Volume per Bag:</span>
                                        <span class="card-value">
                                            @if($donation->individual_bag_volumes)
                                                {{ $donation->formatted_bag_volumes }}
                                            @elseif($donation->volume_per_bag)
                                                {{ $donation->volume_per_bag }}ml each
                                            @else
                                                -
                                            @endif
                                        </span>
                                    </div>
                                    
                                    <div class="card-row">
                                        <span class="card-label">Total Volume:</span>
                                        <span class="card-value"><strong>{{ $donation->formatted_total_volume ?? '-' }}ml</strong></span>
                                    </div>
                                    
                                    <div class="card-row">
                                        <span class="card-label">Date & Time:</span>
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
                                    
                                    <div class="card-actions">
                                        @if($donation->donation_method === 'walk_in')
                                            <button class="btn btn-success validate-walk-in"
                                                data-id="{{ $donation->breastmilk_donation_id }}"
                                                data-donor="{{ $donation->user->first_name }} {{ $donation->user->last_name }}">
                                                <i class="fas fa-check"></i> Validate Walk-in
                                            </button>
                                        @else
                                            <button class="btn btn-primary schedule-pickup"
                                                data-id="{{ $donation->breastmilk_donation_id }}"
                                                data-donor="{{ $donation->user->first_name }} {{ $donation->user->last_name }}"
                                                data-address="{{ $donation->user->address ?? 'Not provided' }}">
                                                <i class="fas fa-calendar-alt"></i> Schedule Pickup
                                            </button>
                                        @endif
                                            {{-- Archive disabled for pending donations --}}
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
                                            <th class="text-center">Bags</th>
                                            <th class="text-center">Volume/Bag</th>
                                            <th class="text-center">Total</th>
                                            <th class="text-center">Address</th>
                                            <th class="text-center">Map</th>
                                            <th class="text-center">Date</th>
                                            <th class="text-center">Time</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($scheduledHomeCollection as $donation)
                                            <tr>
                                                <td data-label="Name" class="text-center">
                                                    <strong>{{ $donation->user->first_name ?? '' }}
                                                        {{ $donation->user->last_name ?? '' }}</strong>
                                                </td>
                                                <td data-label="Bags" class="text-center">
                                                    <strong>{{ $donation->number_of_bags }}</strong>
                                                </td>
                                                <td data-label="Volume/Bag" class="text-center">
                                                    <small>{{ $donation->formatted_bag_volumes }}</small>
                                                </td>
                                                <td data-label="Total" class="text-center">
                                                    <strong>{{ $donation->formatted_total_volume }}ml</strong>
                                                </td>
                                                <td data-label="Address" class="text-center">
                                                    <small>{{ $donation->user->address ?? 'Not provided' }}</small>
                                                </td>
                                                <td data-label="Map" class="text-center">
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
                                                <td data-label="Actions" class="text-center">
                                                    <div class="table-actions">
                                                        <button class="btn btn-success btn-sm px-2 validate-home-collection"
                                                            title="Validate" data-id="{{ $donation->breastmilk_donation_id }}"
                                                            data-donor="{{ $donation->user->first_name }} {{ $donation->user->last_name }}"
                                                            data-bags="{{ $donation->number_of_bags }}"
                                                            data-volumes="{{ json_encode($donation->individual_bag_volumes) }}"
                                                            data-total="{{ $donation->formatted_total_volume }}">
                                                            <i class="fas fa-check"></i>
                                                            <span class="d-none d-md-inline"> Validate</span>
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
                                            <th class="text-center">Bags</th>
                                            <th class="text-center">Volume/Bag</th>
                                            <th class="text-center">Total</th>
                                            <th class="text-center">Date</th>
                                            <th class="text-center">Time</th>
                                            <th class="text-center">Archive</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($successWalkIn as $donation)
                                            <tr>
                                                <td data-label="Name" class="text-center">
                                                    {{ $donation->user->first_name ?? '' }} {{ $donation->user->last_name ?? '' }}
                                                </td>
                                                <td data-label="Bags" class="text-center">
                                                    {{ $donation->number_of_bags ?? 'N/A' }}
                                                </td>
                                                <td data-label="Volume/Bag" class="text-center">
                                                    @if($donation->individual_bag_volumes)
                                                        {{ $donation->formatted_bag_volumes }}
                                                    @else
                                                        {{ $donation->volume_per_bag ?? 'N/A' }}ml each
                                                    @endif
                                                </td>
                                                <td data-label="Total" class="text-center">
                                                    {{ $donation->formatted_total_volume ?? 'N/A' }}ml
                                                </td>
                                                <td data-label="Date" class="text-center">
                                                    <small>{{ $donation->donation_date ? $donation->donation_date->format('M d, Y') : 'N/A' }}</small>
                                                </td>
                                                <td data-label="Time" class="text-center">
                                                    <small>
                                                        @if($donation->availability)
                                                            {{ $donation->availability->formatted_time }}
                                                        @elseif($donation->donation_time)
                                                            {{ \Carbon\Carbon::parse($donation->donation_time)->format('g:i A') }}
                                                        @else
                                                            N/A
                                                        @endif
                                                    </small>
                                                </td>
                                                <td data-label="Archive" class="text-center">
                                                    <div class="table-actions">
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
                                            <th class="text-center">Bags</th>
                                            <th class="text-center">Volume/Bag</th>
                                            <th class="text-center">Total</th>
                                            <th class="text-center">Map</th>
                                            <th class="text-center">Date</th>
                                            <th class="text-center">Time</th>
                                            <th class="text-center">Archive</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($successHomeCollection as $donation)
                                            <tr>
                                                <td data-label="Name" class="text-center">
                                                    <strong>{{ $donation->user->first_name ?? '' }}
                                                        {{ $donation->user->last_name ?? '' }}</strong>
                                                </td>
                                                <td data-label="Address" class="text-center">
                                                    <small>{{ $donation->user->address ?? 'Not provided' }}</small>
                                                </td>
                                                <td data-label="Bags" class="text-center">
                                                    <strong>{{ $donation->number_of_bags }}</strong>
                                                </td>
                                                <td data-label="Volume/Bag" class="text-center">
                                                    <small>{{ $donation->formatted_bag_volumes }}</small>
                                                </td>
                                                <td data-label="Total" class="text-center">
                                                    <strong>{{ $donation->formatted_total_volume }}ml</strong>
                                                </td>
                                                <td data-label="Map" class="text-center">
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
                                                <td data-label="Archive" class="text-center">
                                                    <div class="table-actions">
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
                                        @foreach($archived as $donation)
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
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="schedulePickupModalLabel">Schedule Home Collection Pickup</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="schedulePickupForm" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label"><strong>Donor:</strong></label>
                                <div id="schedule-donor-name" class="form-control-plaintext"></div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label"><strong>Address:</strong></label>
                                <div id="schedule-donor-address" class="form-control-plaintext"></div>
                            </div>

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
                            {{-- Hidden donation id --}}
                            <input type="hidden" id="home-donation-id" name="donation_id" value="">

                            {{-- Inline error area --}}
                            <div id="home-form-error" class="alert alert-danger" role="alert" style="display:none;"
                                aria-live="polite"></div>

                            <!-- Info Message -->
                            <div class="alert alert-info mb-4">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Edit the number of bags and volumes collected</strong> - Update the number of bags
                                and each bag volume to reflect the actual amount collected during pickup.
                            </div>

                            <!-- Number of Bags Input -->
                            <div class="mb-3">
                                <label for="home-bags" class="form-label fw-bold">
                                    <i class="fas fa-shopping-bag me-2 text-primary"></i>Number of Bags <span
                                        class="text-danger">*</span>
                                </label>
                                <input type="number" class="form-control" id="home-bags" name="number_of_bags" min="1"
                                    max="20" required onchange="generateHomeBagFields()" oninput="generateHomeBagFields()">
                                <small class="text-muted">Change this number to add or remove bag volume fields</small>
                            </div>

                            <!-- Bag Volumes Section -->
                            <div class="mb-3">
                                <label class="form-label fw-bold fs-5">
                                    <i class="fas fa-flask me-2 text-primary"></i>Bag Volumes (ml)
                                </label>
                                <div id="home-volume-fields" class="mb-3">
                                    <!-- Individual bag volume inputs will be generated here -->
                                </div>
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
                        </div>
                        <div class="modal-footer bg-light">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                <i class="fas fa-times me-1"></i>Cancel
                            </button>
                            <button type="submit" class="btn btn-success btn-lg px-4" id="home-validate-submit">
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
    {{-- Real-time Search Functionality --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('searchInput');
            const clearBtn = document.getElementById('clearSearch');
            const searchResults = document.getElementById('searchResults');
            
            if (!searchInput) return;

            // Get all tables across all tabs
            const allTables = document.querySelectorAll('.tab-pane table tbody');
            
            // Real-time search function
            function performSearch() {
                const searchTerm = searchInput.value.toLowerCase();
                let totalCount = 0;
                let visibleCount = 0;

                // Process each tab's table
                allTables.forEach(tableBody => {
                    const rows = Array.from(tableBody.querySelectorAll('tr'));
                    totalCount += rows.length;
                    
                    if (searchTerm === '') {
                        // Reset to original order
                        rows.forEach(row => {
                            row.style.display = '';
                        });
                        visibleCount = totalCount;
                    } else {
                        // Separate matched and non-matched rows
                        const matchedRows = [];
                        const unmatchedRows = [];
                        
                        rows.forEach(row => {
                            // Search in all text content of the row
                            const rowText = row.textContent.toLowerCase();
                            
                            if (rowText.indexOf(searchTerm) !== -1) {
                                row.style.display = '';
                                matchedRows.push(row);
                                visibleCount++;
                            } else {
                                row.style.display = 'none';
                                unmatchedRows.push(row);
                            }
                        });
                        
                        // Reorder: matched rows first, then unmatched
                        matchedRows.forEach(row => tableBody.appendChild(row));
                        unmatchedRows.forEach(row => tableBody.appendChild(row));
                    }
                });

                // Update UI
                if (searchTerm === '') {
                    clearBtn.style.display = 'none';
                    searchResults.textContent = '';
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

                $('#schedule-donor-name').text(donorName);
                $('#schedule-donor-address').text(donorAddress);
                $('#schedulePickupForm').attr('action', `/admin/donations/${currentDonationId}/schedule-pickup`);
                $('#schedulePickupModal').modal('show');
            });

            // Home collection validation modal
            $('.validate-home-collection').click(function () {
                currentDonationId = $(this).data('id');
                const donorName = $(this).data('donor');
                const numberOfBags = $(this).data('bags');
                const bagVolumesRaw = $(this).attr('data-volumes');
                const totalVolume = $(this).data('total');

                // Try to parse bag volumes safely (data-volumes may be JSON string)
                let bagVolumes = [];
                try {
                    if (typeof bagVolumesRaw === 'string' && bagVolumesRaw.trim() !== '') {
                        bagVolumes = JSON.parse(bagVolumesRaw);
                    } else if (Array.isArray(bagVolumesRaw)) {
                        bagVolumes = bagVolumesRaw;
                    }
                } catch (err) {
                    console.warn('Failed to parse bag volumes for donation', currentDonationId, err);
                    bagVolumes = [];
                }

                // Set donation id and number of bags
                $('#home-donation-id').val(currentDonationId);
                $('#home-bags').val(numberOfBags || '');

                // Set form action
                $('#validateHomeCollectionForm').attr('action', `/admin/donations/${currentDonationId}/validate-pickup`);

                // Store original volumes for pre-population
                currentOriginalVolumes = bagVolumes || [];

                // Generate bag fields with existing data
                generateHomeBagFields();

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
                submitBtn.prop('disabled', true).text('Scheduling...');

                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function (response) {
                        if (response.success) {
                            // Close modal first, then show SweetAlert
                            $('#schedulePickupModal').modal('hide');
                            setTimeout(() => {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Pickup scheduled',
                                    text: response.message || 'Pickup scheduled successfully.',
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
                                text: response.message || 'An error occurred.'
                            });
                            submitBtn.prop('disabled', false).text('Schedule Pickup');
                        }
                    },
                    error: function () {
                        alert('An error occurred. Please try again.');
                        submitBtn.prop('disabled', false).text('Schedule Pickup');
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
@endsection