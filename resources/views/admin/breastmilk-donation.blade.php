@extends('layouts.admin-layout')

@section('title', 'Breastmilk Donation Management')

@section('content')

    @section('styles')
        <style>
            .nav-tabs .nav-link {
                border-radius: 8px 8px 0 0;
                margin-right: 2px;
                font-weight: 500;
                transition: background 0.2s, color 0.2s;
            }

            .nav-tabs .nav-link.active {
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            }

            .table thead th {
                background: #f8fafc;
                font-weight: 600;
                font-size: 0.9rem;
                border-bottom: 2px solid #eaeaea;
                white-space: nowrap;
                padding: 0.75rem 0.5rem;
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
                font-size: 0.9rem;
            }

            .badge {
                font-size: 0.85rem;
                padding: 0.4em 0.6em;
                border-radius: 6px;
            }

            .card {
                border-radius: 12px;
                box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
            }

            .card-header {
                border-radius: 12px 12px 0 0;
                font-size: 1.1rem;
            }

            .table-responsive {
                border-radius: 8px;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            /* Table buttons - smaller font size for page buttons only */
            .table .btn,
            .card-body .btn,
            .tab-content .btn {
                font-size: 0.85rem;
                border-radius: 6px;
                padding: 0.375rem 0.75rem;
            }

            .btn-sm {
                padding: 0.25rem 0.5rem;
                font-size: 0.8rem;
            }

            /* CRITICAL: Preserve unified header icon sizes - DO NOT OVERRIDE */
            .unified-header .logout-btn i,
            .unified-header .bi-bell,
            .header-right .logout-btn i,
            .header-right .bi-bell {
                font-size: 1.1rem !important;
            }

            /* Keep logout button text at normal size */
            .unified-header .logout-btn,
            .unified-header .logout-btn span,
            .header-right .logout-btn,
            .header-right .logout-btn span {
                font-size: 1rem !important;
            }

            /* Ensure notification bell button maintains proper sizing */
            .notification-bell .btn {
                font-size: 1rem !important;
            }

            .tab-content>.tab-pane {
                padding-top: 0.5rem;
            }

            /* Ensure table fits within viewport */
            .table {
                margin-bottom: 0;
                table-layout: auto;
            }

            /* Better word wrapping for long text */
            .table td {
                word-wrap: break-word;
                overflow-wrap: break-word;
            }

            /* Responsive tabs - wrap on smaller screens */
            .nav-tabs {
                flex-wrap: wrap;
                border-bottom: 2px solid #dee2e6;
                gap: 0.5rem;
            }

            .nav-tabs .nav-item {
                margin-bottom: 0.5rem;
            }

            .nav-tabs .nav-link {
                display: flex;
                white-space: normal;
                text-align: center;
                min-height: 42px;
                align-items: center;
                justify-content: center;
                gap: 0.5rem;
            }

            /* Mobile and Tablet - 2 columns (2x2 grid) */
            @media (max-width: 991px) {
                .nav-tabs .nav-item {
                    flex: 0 0 calc(50% - 0.25rem);
                    max-width: calc(50% - 0.25rem);
                }

                .nav-tabs .nav-link {
                    border-radius: 8px;
                    border: 1px solid #dee2e6 !important;
                    word-break: break-word;
                }
            }

            @media (max-width: 768px) {
                .table-responsive {
                    font-size: 0.85rem;
                }

                .card-header {
                    font-size: 1rem;
                }

                .table thead th {
                    font-size: 0.8rem;
                    padding: 0.5rem 0.3rem;
                }

                .table tbody td {
                    font-size: 0.8rem;
                    padding: 0.5rem 0.3rem;
                }

                /* Compact tabs for mobile */
                .nav-tabs .nav-link {
                    font-size: 0.875rem;
                    padding: 0.625rem 0.75rem;
                }

                .nav-tabs .badge {
                    font-size: 0.75rem;
                    padding: 0.25em 0.4em;
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

    <!-- Navigation Tabs with status query for persistence -->
    @php
        $tabStatus = request('status', 'pending');
    @endphp
    <ul class="nav nav-tabs mb-3" role="tablist">
        <li class="nav-item">
            <a class="nav-link {{ $tabStatus == 'pending' ? 'active bg-warning text-dark' : 'text-dark' }}"
                href="?status=pending">
                Pending <span class="badge bg-warning">{{ $pendingDonations->count() }}</span>
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
    </ul>

    <div class="container-fluid px-2 px-md-4" style="max-width: 100%;">
        <div class="tab-content" id="donationTabContent" aria-live="polite">
            <!-- Unified Pending Donations Tab -->
            <div class="tab-pane fade show {{ $tabStatus == 'pending' ? 'active' : '' }}" id="pending-donations"
                role="tabpanel">
                <div class="card mt-3 shadow-sm rounded-lg border-0">
                    <div class="card-header bg-warning text-dark rounded-top">
                        <h5 class="mb-0">Pending Donations</h5>
                    </div>
                    <div class="card-body py-3">
                        @if($pendingDonations->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover align-middle shadow-sm rounded">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="text-center">Name</th>
                                            <th class="text-center">Type</th>
                                            <th class="text-center">Bags</th>
                                            <th class="text-center">Volume/Bag</th>
                                            <th class="text-center">Total</th>
                                            <th class="text-center">Address</th>
                                            <th class="text-center">Map</th>
                                            <th class="text-center">Date & Time Submitted</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($pendingDonations as $donation)
                                            <tr class="bg-white rounded shadow-sm">
                                                <td class="text-center" style="white-space: normal;">
                                                    <strong>{{ $donation->user->first_name ?? '' }}
                                                        {{ $donation->user->last_name ?? '' }}</strong>
                                                </td>
                                                <td class="text-center">
                                                    @if($donation->donation_method === 'walk_in')
                                                        <span class="badge px-2 py-1"
                                                            style="background-color:#4A90E2;color:#fff;font-size:0.85rem;">Walk-in</span>
                                                    @else
                                                        <span class="badge px-2 py-1"
                                                            style="background-color:#81C784;color:#fff;font-size:0.85rem;">Home</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    @if($donation->donation_method === 'walk_in')
                                                        <span class="text-muted">-</span>
                                                    @else
                                                        <strong>{{ $donation->number_of_bags }}</strong>
                                                    @endif
                                                </td>
                                                <td class="text-center" style="white-space: normal; font-size: 0.85rem;">
                                                    @if($donation->donation_method === 'walk_in')
                                                        <span class="text-muted">TBF</span>
                                                    @else
                                                        <small>{{ $donation->formatted_bag_volumes }}</small>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    @if($donation->donation_method === 'walk_in')
                                                        <span class="text-muted">TBC</span>
                                                    @else
                                                        <strong>{{ $donation->total_volume }}ml</strong>
                                                    @endif
                                                </td>
                                                <td class="text-center"
                                                    style="white-space: normal; font-size: 0.85rem; max-width: 150px;">
                                                    <small>{{ $donation->user->address ?? 'Not provided' }}</small>
                                                </td>
                                                <td class="text-center">
                                                    @if($donation->donation_method === 'home_collection' && $donation->user->latitude && $donation->user->longitude)
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
                                                <td class="text-center" style="white-space: nowrap;">
                                                    <small>{{ $donation->created_at->format('M d, Y g:i A') }}</small>
                                                </td>
                                                <td class="text-center">
                                                    @if($donation->donation_method === 'walk_in')
                                                        <button class="btn btn-success btn-sm px-2 validate-walkin" title="Validate"
                                                            data-id="{{ $donation->breastmilk_donation_id }}"
                                                            data-donor="{{ $donation->user->first_name }} {{ $donation->user->last_name }}">
                                                            <i class="fas fa-check"></i> Validate
                                                        </button>
                                                    @else
                                                        <button class="btn btn-primary btn-sm px-2 schedule-pickup" title="Schedule"
                                                            data-id="{{ $donation->breastmilk_donation_id }}"
                                                            data-donor="{{ $donation->user->first_name }} {{ $donation->user->last_name }}"
                                                            data-address="{{ $donation->user->address }}">
                                                            <i class="fas fa-calendar-plus"></i> Schedule
                                                        </button>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-heart fa-3x mb-3"></i>
                                <p>No pending donations</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Scheduled Home Collection Tab -->
            <div class="tab-pane fade show {{ $tabStatus == 'scheduled' ? 'active' : '' }}" id="scheduled-home"
                role="tabpanel">
                <div class="card mt-3 shadow-sm rounded-lg border-0">
                    <div class="card-header bg-primary text-white rounded-top">
                        <h5 class="mb-0">Scheduled Home Collection</h5>
                    </div>
                    <div class="card-body py-3">
                        @if($scheduledHomeCollection->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover align-middle shadow-sm rounded">
                                    <thead class="table-light">
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
                                            <tr class="bg-white rounded shadow-sm">
                                                <td class="text-center" style="white-space: normal;">
                                                    <strong>{{ $donation->user->first_name ?? '' }}
                                                        {{ $donation->user->last_name ?? '' }}</strong>
                                                </td>
                                                <td class="text-center"><strong>{{ $donation->number_of_bags }}</strong></td>
                                                <td class="text-center" style="white-space: normal; font-size: 0.85rem;">
                                                    <small>{{ $donation->formatted_bag_volumes }}</small>
                                                </td>
                                                <td class="text-center"><strong>{{ $donation->total_volume }}ml</strong></td>
                                                <td class="text-center"
                                                    style="white-space: normal; font-size: 0.85rem; max-width: 150px;">
                                                    <small>{{ $donation->user->address ?? 'Not provided' }}</small>
                                                </td>
                                                <td class="text-center">
                                                    @if($donation->user->latitude && $donation->user->longitude)
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
                                                <td class="text-center" style="white-space: nowrap;">
                                                    <small>{{ $donation->scheduled_pickup_date->format('M d, Y') }}</small>
                                                </td>
                                                <td class="text-center" style="white-space: nowrap;">
                                                    <small>{{ $donation->scheduled_pickup_time }}</small>
                                                </td>
                                                <td class="text-center">
                                                    <button class="btn btn-success btn-sm px-2 validate-home-collection"
                                                        title="Validate" data-id="{{ $donation->breastmilk_donation_id }}"
                                                        data-donor="{{ $donation->user->first_name }} {{ $donation->user->last_name }}"
                                                        data-bags="{{ $donation->number_of_bags }}"
                                                        data-volumes="{{ json_encode($donation->individual_bag_volumes) }}"
                                                        data-total="{{ $donation->total_volume }}">
                                                        <i class="fas fa-check"></i> Validate
                                                    </button>
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
                <div class="card mt-3 shadow-sm rounded-lg border-0">
                    <div class="card-header bg-success text-white rounded-top">
                        <h5 class="mb-0">Completed Walk-in Donations</h5>
                    </div>
                    <div class="card-body py-3">
                        @if($successWalkIn->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped align-middle shadow-sm rounded">
                                    <thead class="table-success">
                                        <tr>
                                            <th class="text-center px-3 py-2">Name</th>
                                            <th class="text-center px-3 py-2">Bags</th>
                                            <th class="text-center px-3 py-2">Volume/Bag</th>
                                            <th class="text-center px-3 py-2">Total</th>
                                            <th class="text-center px-3 py-2">Date</th>
                                            <th class="text-center px-3 py-2">Time</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($successWalkIn as $donation)
                                            <tr class="bg-white rounded shadow-sm">
                                                <td class="text-center align-middle px-3 py-2" style="white-space: normal;">
                                                    {{ $donation->user->first_name ?? '' }} {{ $donation->user->last_name ?? '' }}
                                                </td>
                                                <td class="text-center align-middle px-3 py-2">
                                                    {{ $donation->number_of_bags ?? 'N/A' }}
                                                </td>
                                                <td class="text-center align-middle px-3 py-2"
                                                    style="white-space: normal; font-size: 0.85rem;">
                                                    @if($donation->individual_bag_volumes)
                                                        {{ $donation->formatted_bag_volumes }}
                                                    @else
                                                        {{ $donation->volume_per_bag ?? 'N/A' }}ml each
                                                    @endif
                                                </td>
                                                <td class="text-center align-middle px-3 py-2">
                                                    {{ $donation->total_volume ?? 'N/A' }}ml
                                                </td>
                                                <td class="text-center align-middle px-3 py-2" style="white-space: nowrap;">
                                                    <small>{{ $donation->donation_date ? $donation->donation_date->format('M d, Y') : 'N/A' }}</small>
                                                </td>
                                                <td class="text-center align-middle px-3 py-2" style="white-space: nowrap;">
                                                    <small>
                                                        @if($donation->availability)
                                                            {{ $donation->availability->formatted_time }}
                                                        @elseif($donation->donation_time)
                                                            {{ $donation->donation_time }}
                                                        @else
                                                            N/A
                                                        @endif
                                                    </small>
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
                <div class="card mt-3 shadow-sm rounded-lg border-0">
                    <div class="card-header bg-success text-white rounded-top">
                        <h5 class="mb-0">Completed Home Collection</h5>
                    </div>
                    <div class="card-body py-3">
                        @if($successHomeCollection->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped align-middle shadow-sm rounded">
                                    <thead class="table-success">
                                        <tr>
                                            <th class="text-center px-3 py-2">Name</th>
                                            <th class="text-center px-3 py-2">Address</th>
                                            <th class="text-center px-3 py-2">Bags</th>
                                            <th class="text-center px-3 py-2">Volume/Bag</th>
                                            <th class="text-center px-3 py-2">Total</th>
                                            <th class="text-center px-3 py-2">Map</th>
                                            <th class="text-center px-3 py-2">Date</th>
                                            <th class="text-center px-3 py-2">Time</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($successHomeCollection as $donation)
                                            <tr class="bg-white rounded shadow-sm">
                                                <td class="text-center align-middle px-3 py-2" style="white-space: normal;">
                                                    <strong>{{ $donation->user->first_name ?? '' }}
                                                        {{ $donation->user->last_name ?? '' }}</strong>
                                                </td>
                                                <td class="text-center align-middle px-3 py-2"
                                                    style="white-space: normal; font-size: 0.85rem; max-width: 150px;">
                                                    <small>{{ $donation->user->address ?? 'Not provided' }}</small>
                                                </td>
                                                <td class="text-center align-middle px-3 py-2">
                                                    <strong>{{ $donation->number_of_bags }}</strong>
                                                </td>
                                                <td class="text-center align-middle px-3 py-2"
                                                    style="white-space: normal; font-size: 0.85rem;">
                                                    <small>{{ $donation->formatted_bag_volumes }}</small>
                                                </td>
                                                <td class="text-center align-middle px-3 py-2">
                                                    <strong>{{ $donation->total_volume }}ml</strong>
                                                </td>
                                                <td class="text-center align-middle px-3 py-2">
                                                    @if($donation->user->latitude && $donation->user->longitude)
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
                                                <td class="text-center align-middle px-3 py-2" style="white-space: nowrap;">
                                                    <small>{{ $donation->scheduled_pickup_date->format('M d, Y') }}</small>
                                                </td>
                                                <td class="text-center align-middle px-3 py-2" style="white-space: nowrap;">
                                                    <small>{{ $donation->scheduled_pickup_time }}</small>
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
                                    <strong id="walkin-total">0.00 ml</strong>
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
                            <input type="hidden" id="home-bags" name="number_of_bags" value="">

                            {{-- Inline error area --}}
                            <div id="home-form-error" class="alert alert-danger" role="alert" style="display:none;"
                                aria-live="polite"></div>

                            <!-- Info Message -->
                            <div class="alert alert-info mb-4">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Edit the actual volumes collected</strong> - Update each bag volume to reflect the
                                actual amount collected during pickup.
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
                                    <h2 class="mb-0 text-info fw-bold" id="home-total">0.00 ml</h2>
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
@endsection

    @section('scripts')
        <script>
            let currentDonationId = null;
            let currentOriginalVolumes = []; // Store original volumes globally

            // Initialize modal triggers
            $(document).ready(function () {
                // Walk-in validation modal
                $('.validate-walkin').click(function () {
                    currentDonationId = $(this).data('id');
                    const donorName = $(this).data('donor');

                    $('#walkin-donor-name').text(donorName);
                    $('#walkin-donation-id').val(currentDonationId);
                    $('#validateWalkInForm').attr('action', `/admin/donations/${currentDonationId}/validate-walkin`);
                    // reset form state
                    $('#walkin-form-error').hide().text('');
                    $('#walkin-volume-fields').html('');
                    $('#walkin-bags').val('');
                    $('#walkin-total').text('0.00 ml');
                    $('#validateWalkInModal').modal('show');
                });

                // Schedule pickup modal  
                $('.schedule-pickup').click(function () {
                    currentDonationId = $(this).data('id');
                    const donorName = $(this).data('donor');
                    const donorAddress = $(this).data('address');

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

                    $('#walkin-total').text(total.toFixed(2) + ' ml');
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
                $('#walkin-total').text('0.00 ml');
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
                $('#home-total').text('0.00 ml');
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

                $('#walkin-total').text(total.toFixed(2) + ' ml');
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
                    $('#home-total').text('0.00 ml');
                    return;
                }

                let fieldsHTML = '<div class="row g-3">';
                for (let i = 1; i <= bagCount; i++) {
                    // Pre-populate with existing values if available
                    const existingValue = currentOriginalVolumes && currentOriginalVolumes[i - 1] ? currentOriginalVolumes[i - 1] : '';

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

                $('#home-total').text(total.toFixed(2) + ' ml');
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

                if (latitude && longitude) {
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
    @endsection