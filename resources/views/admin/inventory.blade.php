@extends('layouts.admin-layout')


@section('title', 'Inventory Management')

@section('styles')
    <style>
        .stats-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            border: none;
            border-radius: 12px;
            padding: 12px 14px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s, box-shadow 0.2s;
            height: 100%;
            min-width: 150px;
            flex: 1 1 auto;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .stats-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .stats-cards-container {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 1.5rem;
        }

        .stats-card-wrapper {
            flex: 1 1 auto;
            min-width: 150px;
        }

        @media (max-width: 992px) {
            .stats-card-wrapper {
                flex: 1 1 calc(33.333% - 15px);
                min-width: 150px;
            }
        }

        @media (max-width: 576px) {
            .stats-card-wrapper {
                flex: 1 1 calc(33.333% - 10px);
                max-width: calc(33.333% - 10px);
                min-width: unset;
            }

            .stats-cards-container {
                gap: 10px;
            }

            .stats-card {
                padding: 15px 10px;
                min-width: unset;
            }
        }

        .stats-icon {
            font-size: 2.5rem;
            margin-bottom: 10px;
            opacity: 0.8;
        }

        .stats-number {
            font-size: 1.6rem;
            font-weight: 700;
            line-height: 1;
        }

        .stats-text {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            min-width: 0;
        }

        .stats-label {
            color: #6c757d;
            font-size: 0.78rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 4px;
        }

        /* Color variants for different stats */
        .stats-card.warning .stats-number {
            color: #ffc107;
        }

        .stats-card.warning .stats-icon {
            color: #ffc107;
        }

        .stats-card.info .stats-number {
            color: #17a2b8;
        }

        .stats-card.info .stats-icon {
            color: #17a2b8;
        }

        .stats-card.primary .stats-number {
            color: #007bff;
        }

        .stats-card.primary .stats-icon {
            color: #007bff;
        }

        .stats-card.success .stats-number {
            color: #28a745;
        }

        .stats-card.success .stats-icon {
            color: #28a745;
        }

        .table thead th {
            background: #f8fafc;
            font-weight: 600;
            font-size: 0.9rem;
            border-bottom: 2px solid #eaeaea;
            padding: 0.75rem 0.5rem;
            text-align: center;
            white-space: nowrap;
            color: #212529 !important;
        }

        .batch-expandable {
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .batch-expandable:hover {
            background-color: #f8f9fa;
        }

        .batch-details {
            background-color: #f8f9fa;
            border-left: 4px solid #007bff;
        }

        .pasteurize-checkbox {
            transform: scale(1.2);
        }

        .btn-pasteurize {
            background: linear-gradient(135deg, #28a745, #20c997);
            border: none;
            color: white;
            font-weight: 600;
        }

        .btn-pasteurize:hover {
            background: linear-gradient(135deg, #218838, #1ea486);
            color: white;
        }

        .volume-badge {
            font-size: 0.85rem;
            padding: 4px 8px;
        }

        .donation-type-badge {
            font-size: 0.8rem;
        }

        /* Fix badge text colors - make them dark instead of white */
        .badge-info {
            background-color: #d1ecf1 !important;
            color: #0c5460 !important;
        }

        .badge-success {
            background-color: #d4edda !important;
            color: #155724 !important;
        }

        .badge-primary {
            background-color: #cfe2ff !important;
            color: #084298 !important;
        }

        /* Responsive table tweaks */
        .table th,
        .table td {
            padding: 0.65rem 0.4rem;
            vertical-align: middle;
            white-space: normal;
            font-size: 0.9rem;
        }

        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        /* Responsive adjustments */
        @media (max-width: 1200px) {
            .table {
                font-size: 0.85rem;
            }

            .table th,
            .table td {
                padding: 0.5rem 0.3rem;
            }
        }

        @media (max-width: 768px) {
            .stats-number {
                font-size: 1.5rem;
            }

            .stats-icon {
                font-size: 2rem;
            }

            .stats-label {
                font-size: 0.75rem;
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
    <link rel="stylesheet" href="{{ asset('css/table-layout-standard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/responsive-tables.css') }}">
@endsection

@section('content')
    <div class="container-fluid page-container-standard">

        <!-- Inventory Statistics -->
        <div class="stats-cards-container">
            <div class="stats-card-wrapper">
                <div class="stats-card warning">
                    <div class="stats-icon">
                        <i class="fas fa-flask"></i>
                    </div>
                    <div class="stats-text">
                        <div class="stats-number" id="unpasteurized-count">{{ $unpasteurizedDonations->count() }}</div>
                        <div class="stats-label">Unpasteurized Donations</div>
                    </div>
                </div>
            </div>
            <div class="stats-card-wrapper">
                <div class="stats-card info">
                    <div class="stats-icon">
                        <i class="fas fa-tint"></i>
                    </div>
                    <div class="stats-text">
                        <div class="stats-number" id="unpasteurized-volume">
                            {{ number_format($unpasteurizedDonations->where('available_volume', '>', 0)->sum('available_volume'), 0) }}ml
                        </div>
                        <div class="stats-label">Available Volume</div>
                    </div>
                </div>
            </div>
            <div class="stats-card-wrapper">
                <div class="stats-card primary">
                    <div class="stats-icon">
                        <i class="fas fa-vial"></i>
                    </div>
                    <div class="stats-text">
                        <div class="stats-number" id="pasteurized-batches">{{ $pasteurizationBatches->count() }}</div>
                        <div class="stats-label">Active Batches</div>
                    </div>
                </div>
            </div>
            <div class="stats-card-wrapper">
                <div class="stats-card info">
                    <div class="stats-icon">
                        <i class="fas fa-fill-drip"></i>
                    </div>
                    <div class="stats-text">
                        <div class="stats-number" id="pasteurized-volume">
                            {{ number_format($pasteurizationBatches->sum('available_volume'), 0) }}ml
                        </div>
                        <div class="stats-label">Pasteurized Volume</div>
                    </div>
                </div>
            </div>
            <div class="stats-card-wrapper">
                <div class="stats-card success">
                    <div class="stats-icon">
                        <i class="fas fa-hand-holding-medical"></i>
                    </div>
                    <div class="stats-text">
                        <div class="stats-number" id="dispensed-records">{{ $dispensedMilk->count() }}</div>
                        <div class="stats-label">Dispensed Records</div>
                    </div>
                </div>
            </div>
            <div class="stats-card-wrapper">
                <div class="stats-card success">
                    <div class="stats-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stats-text">
                        <div class="stats-number" id="dispensed-volume">
                            {{ number_format($dispensedMilk->sum('volume_dispensed'), 0) }}ml
                        </div>
                        <div class="stats-label">Total Dispensed</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <ul class="nav nav-tabs nav-tabs-standard" id="inventoryTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link{{ request()->get('status', 'unpasteurized') == 'unpasteurized' ? ' active' : '' }}"
                    href="?status=unpasteurized" id="unpasteurized-tab" role="tab">
                    <i class="fas fa-flask"></i> Unpasteurized Breastmilk
                    <span class="badge bg-warning">{{ $unpasteurizedDonations->count() }}</span>
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link{{ request()->get('status') == 'pasteurized' ? ' active' : '' }}"
                    href="?status=pasteurized" id="pasteurized-tab" role="tab">
                    <i class="fas fa-vial"></i> Pasteurized Breastmilk
                    <span class="badge bg-primary">{{ $pasteurizationBatches->count() }}</span>
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link{{ request()->get('status') == 'dispensed' ? ' active' : '' }}" href="?status=dispensed"
                    id="dispensed-tab" role="tab">
                    <i class="fas fa-hand-holding-medical"></i> Dispensed Breastmilk
                    <span class="badge bg-success">{{ $dispensedMilk->count() }}</span>
                </a>
            </li>
        </ul>

        <div class="tab-content" id="inventoryTabContent" aria-live="polite">
            <!-- Section 1: Unpasteurized Breastmilk -->
            <div class="tab-pane fade{{ request()->get('status', 'unpasteurized') == 'unpasteurized' ? ' show active' : '' }}"
                id="unpasteurized" role="tabpanel">
                <div class="card card-standard">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0"><i class="fas fa-flask"></i> Unpasteurized Breastmilk</h5>
                    </div>
                    <div class="card-body">
                        @if($unpasteurizedDonations->count() > 0)
                            <form id="pasteurizationForm">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div>
                                        <button type="button" class="btn btn-sm btn-outline-secondary"
                                            onclick="selectAllDonations()">
                                            <i class="fas fa-check-square"></i> Select All
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary ms-2"
                                            onclick="clearAllSelections()">
                                            <i class="fas fa-square"></i> Clear All
                                        </button>
                                    </div>
                                    <button type="button" class="btn btn-pasteurize" onclick="pasteurizeSelected()" disabled
                                        id="pasteurizeBtn">
                                        <i class="fas fa-fire"></i> Pasteurize Selected
                                    </button>
                                </div>

                                <div class="table-container-standard table-wide">
                                    <table class="table table-standard table-bordered table-striped align-middle">
                                        <thead class="table-success">
                                            <tr>
                                                <th class="text-center align-middle px-2 py-2" style="color: #000;">
                                                    <input type="checkbox" id="selectAllCheckbox"
                                                        onchange="toggleAllDonations()">
                                                </th>
                                                <th class="text-center px-2 py-2" style="color: #000;">Donor</th>
                                                <th class="text-center px-2 py-2" style="color: #000;">Type</th>
                                                <th class="text-center px-2 py-2" style="color: #000;">Bags</th>
                                                <th class="text-center px-2 py-2" style="color: #000;">Volume/Bag</th>
                                                <th class="text-center px-2 py-2" style="color: #000;">Total</th>
                                                <th class="text-center px-2 py-2" style="color: #000;">Available</th>
                                                <th class="text-center px-2 py-2" style="color: #000;">Date</th>
                                                <th class="text-center px-2 py-2" style="color: #000;">Time</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $unpasteurizedOrdered = $unpasteurizedDonations instanceof \Illuminate\Pagination\LengthAwarePaginator
                                                    ? $unpasteurizedDonations->getCollection()->sortBy('created_at')
                                                    : collect($unpasteurizedDonations)->sortBy('created_at');
                                            @endphp
                                            @foreach($unpasteurizedOrdered as $donation)
                                                <tr>
                                                    <td class="text-center align-middle" data-label="Select">
                                                        <input type="checkbox" class="pasteurize-checkbox donation-checkbox"
                                                            value="{{ $donation->breastmilk_donation_id }}"
                                                            onchange="updatePasteurizeButton()">
                                                    </td>
                                                    <td style="white-space: normal;" data-label="Donor">
                                                        <strong>{{ $donation->user->first_name }}
                                                            {{ $donation->user->last_name }}</strong>
                                                    </td>
                                                    <td class="text-center" data-label="Type">
                                                        <span
                                                            class="badge badge-{{ $donation->donation_method === 'walk_in' ? 'primary' : 'success' }} donation-type-badge">
                                                            {{ $donation->donation_method === 'walk_in' ? 'Walk-in' : 'Home Collection' }}
                                                        </span>
                                                    </td>
                                                    <td class="text-center" data-label="Bags">{{ $donation->number_of_bags }}</td>
                                                    <td class="text-center" style="white-space: normal; font-size: 0.85rem;"
                                                        data-label="Volume/Bag">
                                                        <small>{{ $donation->formatted_bag_volumes }}</small>
                                                    </td>
                                                    <td class="text-center" data-label="Total">
                                                        <span
                                                            class="badge badge-info volume-badge">{{ $donation->formatted_total_volume }}ml</span>
                                                    </td>
                                                    <td class="text-center" data-label="Available">
                                                        <span
                                                            class="badge badge-success volume-badge">{{ $donation->formatted_available_volume }}ml</span>
                                                    </td>
                                                    <td class="text-center" style="white-space: nowrap;" data-label="Date">
                                                        <small>
                                                            @if($donation->donation_date)
                                                                {{ $donation->donation_date->format('M d, Y') }}
                                                            @elseif($donation->scheduled_pickup_date)
                                                                {{ $donation->scheduled_pickup_date->format('M d, Y') }}
                                                            @else
                                                                -
                                                            @endif
                                                        </small>
                                                    </td>
                                                    <td class="text-center" style="white-space: nowrap;" data-label="Time">
                                                        <small>
                                                            @if($donation->availability)
                                                                {{ $donation->availability->formatted_time }}
                                                            @elseif($donation->donation_time)
                                                                {{ \Carbon\Carbon::parse($donation->donation_time)->format('g:i A') }}
                                                            @elseif($donation->scheduled_pickup_time)
                                                                {{ $donation->scheduled_pickup_time }}
                                                            @else
                                                                -
                                                            @endif
                                                        </small>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </form>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No unpasteurized donations available</h5>
                                <p class="text-muted">Successful donations will appear here automatically</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Section 2: Pasteurized Breastmilk -->
            <div class="tab-pane fade{{ request()->get('status') == 'pasteurized' ? ' show active' : '' }}" id="pasteurized"
                role="tabpanel">
                <div class="card card-standard">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-vial"></i> Pasteurized Breastmilk</h5>
                    </div>
                    <div class="card-body">
                        @if($pasteurizationBatches->count() > 0)
                            <div class="table-container-standard">
                                <table class="table table-standard table-bordered table-striped align-middle">
                                    <thead class="table-success">
                                        <tr>
                                            <th class="text-center px-2 py-2">Batch</th>
                                            <th class="text-center px-2 py-2">Total</th>
                                            <th class="text-center px-2 py-2">Available</th>
                                            <th class="text-center px-2 py-2">Date</th>
                                            <th class="text-center px-2 py-2">Time</th>
                                            <th class="text-center px-2 py-2">Count</th>
                                            <th class="text-center px-2 py-2">Actions</th>
                                        </tr>
                                    </thead>
                                        <tbody>
                                        @php
                                            $batchesOrdered = $pasteurizationBatches instanceof \Illuminate\Pagination\LengthAwarePaginator
                                                ? $pasteurizationBatches->getCollection()->sortBy('created_at')
                                                : collect($pasteurizationBatches)->sortBy('created_at');
                                        @endphp
                                        @foreach($batchesOrdered as $batch)
                                            <tr class="batch-row">
                                                <td style="white-space: normal;" data-label="Batch">
                                                    <strong>{{ $batch->batch_number }}</strong>
                                                </td>
                                                <td class="text-center" data-label="Total">
                                                    <span
                                                        class="badge badge-info volume-badge">{{ $batch->formatted_total_volume }}ml</span>
                                                </td>
                                                <td class="text-center" data-label="Available">
                                                    <span
                                                        class="badge badge-success volume-badge">{{ $batch->formatted_available_volume }}ml</span>
                                                </td>
                                                <td class="text-center" style="white-space: nowrap;" data-label="Date">
                                                    <small>{{ $batch->formatted_date }}</small>
                                                </td>
                                                <td class="text-center" style="white-space: nowrap;" data-label="Time">
                                                    <small>{{ $batch->formatted_time }}</small>
                                                </td>
                                                <td class="text-center" data-label="Count">
                                                    <small>{{ $batch->donations->count() }}</small>
                                                </td>
                                                <td class="text-center" data-label="Actions">
                                                    <button class="admin-review-btn btn-sm" title="Review Details"
                                                        onclick="viewBatchDetails({{ $batch->batch_id }})">
                                                        Review
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-vial fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No pasteurized batches available</h5>
                                <p class="text-muted">Pasteurize unpasteurized donations to create batches</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Section 3: Dispensed Breastmilk -->
            <div class="tab-pane fade{{ request()->get('status') == 'dispensed' ? ' show active' : '' }}" id="dispensed"
                role="tabpanel">
                <div class="card card-standard">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-hand-holding-medical"></i> Dispensed Breastmilk</h5>
                    </div>
                    <div class="card-body">
                        @if($dispensedMilk->count() > 0)
                            <div class="table-container-standard">
                                <table class="table table-standard table-bordered table-striped align-middle">
                                    <thead class="table-success">
                                        <tr>
                                            <th class="text-center px-2 py-2">Guardian</th>
                                            <th class="text-center px-2 py-2">Recipient</th>
                                            <th class="text-center px-2 py-2">Source</th>
                                            <th class="text-center px-2 py-2">Volume</th>
                                            <th class="text-center px-2 py-2">Date</th>
                                            <th class="text-center px-2 py-2">Time</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $dispensedOrdered = $dispensedMilk instanceof \Illuminate\Pagination\LengthAwarePaginator
                                                ? $dispensedMilk->getCollection()->sortBy('created_at')
                                                : collect($dispensedMilk)->sortBy('created_at');
                                        @endphp
                                        @foreach($dispensedOrdered as $dispensed)
                                            <tr>
                                                <td style="white-space: normal;" data-label="Guardian">
                                                    <strong>{{ $dispensed->guardian->first_name }}
                                                        {{ $dispensed->guardian->last_name }}</strong>
                                                </td>
                                                <td style="white-space: normal;" data-label="Recipient">
                                                    <strong>{{ $dispensed->recipient->first_name }}
                                                        {{ $dispensed->recipient->last_name }}</strong><br>
                                                    <small class="text-muted">{{ $dispensed->recipient->getFormattedAge() }}</small>
                                                </td>
                                                <td style="white-space: normal; font-size: 0.85rem;" data-label="Source">
                                                    @php
                                                        // Prefer donor name(s) from sourceDonations (unpasteurized),
                                                        // otherwise use batch numbers from sourceBatches (pasteurized).
                                                        $sourceLabel = '-';

                                                        // If there are any related sourceDonations (could be multiple),
                                                        // build a unique list of donor full names.
                                                        if (!empty($dispensed->sourceDonations) && $dispensed->sourceDonations->count() > 0) {
                                                            $donorNames = $dispensed->sourceDonations->map(function ($sd) {
                                                                // Some donations may have a related user, or only donor_name
                                                                $name = trim((($sd->user->first_name ?? '') . ' ' . ($sd->user->last_name ?? '')));
                                                                if (empty($name)) {
                                                                    $name = $sd->donor_name ?? ('Donation #' . ($sd->breastmilk_donation_id ?? '-'));
                                                                }
                                                                return $name;
                                                            })->filter()->unique()->values()->all();

                                                            if (!empty($donorNames)) {
                                                                $sourceLabel = implode(', ', $donorNames);
                                                            }

                                                            // Otherwise, use batch numbers from related sourceBatches
                                                        } elseif (!empty($dispensed->sourceBatches) && $dispensed->sourceBatches->count() > 0) {
                                                            $batchNumbers = $dispensed->sourceBatches->pluck('batch_number')->filter()->all();
                                                            if (!empty($batchNumbers)) {
                                                                $sourceLabel = implode(', ', $batchNumbers);
                                                            } else {
                                                                $sourceLabel = 'Pasteurized batch';
                                                            }
                                                        }
                                                    @endphp

                                                    <small><strong>{{ $dispensed->source_name }}</strong></small>
                                                </td>
                                                <td class="text-center" data-label="Volume">
                                                    <span
                                                        class="badge badge-success volume-badge">{{ $dispensed->formatted_volume_dispensed }}ml</span>
                                                </td>
                                                <td class="text-center" style="white-space: nowrap;" data-label="Date">
                                                    <small>{{ $dispensed->formatted_date }}</small>
                                                </td>
                                                <td class="text-center" style="white-space: nowrap;" data-label="Time">
                                                    <small>{{ $dispensed->formatted_time }}</small>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-hand-holding-medical fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No dispensed records</h5>
                                <p class="text-muted">Dispensed breastmilk records will appear here</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pasteurization Modal -->
    <div class="modal fade" id="pasteurizationModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-fire"></i> Pasteurize Selected Donations
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>FIFO Rule:</strong> Donations will be processed in First-In-First-Out order to ensure proper
                        inventory rotation.
                    </div>

                    <div class="alert alert-success">
                        <i class="fas fa-flask"></i>
                        <strong>Batch Name:</strong> <span id="nextBatchName">Batch
                            {{ \App\Models\PasteurizationBatch::count() + 1 }}</span>
                    </div>

                    <div id="selectedDonationsList"></div>

                    <div class="mb-3">
                        <label for="pasteurizationNotes" class="form-label">Notes (Optional)</label>
                        <textarea class="form-control" id="pasteurizationNotes" rows="3"
                            placeholder="Any notes about the pasteurization process..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-pasteurize" onclick="confirmPasteurization()">
                        <i class="fas fa-fire"></i> Start Pasteurization
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Batch Details Modal -->
    <div class="modal fade" id="batchDetailsModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-vial"></i> <span id="batchModalTitle">Batch Details</span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="batchDetailsContent">
                        <div class="text-center py-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-3 text-muted">Loading batch details...</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Close container-fluid --}}

@endsection

@section('scripts')
    <script>
        function selectAllDonations() {
            document.querySelectorAll('.donation-checkbox').forEach(cb => {
                cb.checked = true;
            });
            document.getElementById('selectAllCheckbox').checked = true;
            updatePasteurizeButton();
        }

        function clearAllSelections() {
            document.querySelectorAll('.donation-checkbox').forEach(cb => {
                cb.checked = false;
            });
            document.getElementById('selectAllCheckbox').checked = false;
            updatePasteurizeButton();
        }

        function toggleAllDonations() {
            const selectAll = document.getElementById('selectAllCheckbox');
            document.querySelectorAll('.donation-checkbox').forEach(cb => {
                cb.checked = selectAll.checked;
            });
            updatePasteurizeButton();
        }

        function updatePasteurizeButton() {
            const checkedBoxes = document.querySelectorAll('.donation-checkbox:checked');
            const pasteurizeBtn = document.getElementById('pasteurizeBtn');

            pasteurizeBtn.disabled = checkedBoxes.length === 0;

            if (checkedBoxes.length > 0) {
                pasteurizeBtn.innerHTML = `<i class="fas fa-fire"></i> Pasteurize Selected (${checkedBoxes.length})`;
            } else {
                pasteurizeBtn.innerHTML = `<i class="fas fa-fire"></i> Pasteurize Selected`;
            }
        }

        function pasteurizeSelected() {
            const checkedBoxes = document.querySelectorAll('.donation-checkbox:checked');

            if (checkedBoxes.length === 0) {
                alert('Please select at least one donation to pasteurize.');
                return;
            }

            // Build list of selected donations for modal
            let donationsList = '<h6>Selected Donations:</h6><ul>';
            let totalVolume = 0;

            checkedBoxes.forEach((checkbox, index) => {
                const row = checkbox.closest('tr');
                const donorName = row.cells[1].textContent.trim().split('\n')[0];
                // Use the Available column (cell index 6) which contains remaining volume
                // Strip non-numeric characters (commas, 'ml') before parsing
                const rawAvailable = row.cells[6].textContent || '';
                const volume = parseFloat(rawAvailable.replace(/[^0-9.-]+/g, '')) || 0;
                totalVolume += volume;

                donationsList += `<li>${donorName} - ${volume}ml</li>`;
            });

            donationsList += `</ul><p><strong>Total Volume: ${totalVolume}ml</strong></p>`;

            document.getElementById('selectedDonationsList').innerHTML = donationsList;

            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('pasteurizationModal'));
            modal.show();
        }

        function confirmPasteurization() {
            const checkedBoxes = document.querySelectorAll('.donation-checkbox:checked');
            const donationIds = Array.from(checkedBoxes).map(cb => cb.value);
            const notes = document.getElementById('pasteurizationNotes').value;

            // Hide modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('pasteurizationModal'));
            modal.hide();

            // Show loading indicator
            Swal.fire({
                title: 'Processing...',
                text: 'Creating pasteurization batch',
                icon: 'info',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch('{{ route("admin.inventory.pasteurize") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    donation_ids: donationIds,
                    notes: notes
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Show success SweetAlert
                        Swal.fire({
                            title: 'Success!',
                            html: `<strong>${data.simple_batch_name}</strong> created successfully!<br><br>` +
                                `<div style="font-size: 1.1em;">` +
                                `Processed <strong>${data.donations_count}</strong> donations<br>` +
                                `Totaling <strong>${data.total_volume}ml</strong>` +
                                `</div>`,
                            icon: 'success',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#10b981',
                            timer: 5000,
                            timerProgressBar: true
                        }).then(() => {
                            location.reload(); // Refresh page to show updated inventory
                        });
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: data.error || 'Unknown error occurred',
                            icon: 'error',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#dc2626'
                        });
                    }
                })
                .catch(error => {
                    Swal.fire({
                        title: 'Error!',
                        text: error.message || 'An error occurred while processing',
                        icon: 'error',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#dc2626'
                    });
                });
        }

        function viewBatchDetails(batchId) {
            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('batchDetailsModal'));
            modal.show();

            // Fetch batch details
            fetch(`{{ url('/admin/inventory/batch') }}/${batchId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Failed to load batch details');
                    }
                    return response.json();
                })
                .then(data => {
                    // Update modal title
                    document.getElementById('batchModalTitle').textContent = `${data.batch_number} - Details`;

                    // Build the content
                    let content = '';

                    if (data.notes) {
                        content += `
                                                <div class="alert alert-info mb-4">
                                                    <h6><i class="fas fa-sticky-note"></i> Notes:</h6>
                                                    <p class="mb-0">${escapeHtml(data.notes)}</p>
                                                </div>
                                            `;
                    }

                    content += `
                                            <h6 class="mb-3"><i class="fas fa-list"></i> Donations in this Batch (${data.donations.length})</h6>
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-striped align-middle">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th class="text-center">Donor</th>
                                                            <th class="text-center">Type</th>
                                                            <th class="text-center">Bags</th>
                                                            <th class="text-center">Volume/Bag</th>
                                                            <th class="text-center">Total Volume</th>
                                                            <th class="text-center">Date</th>
                                                            <th class="text-center">Time</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                        `;

                    data.donations.forEach(donation => {
                        const donorName = donation.user ? `${donation.user.first_name} ${donation.user.last_name}` : 'Unknown';
                        const donationType = donation.donation_method === 'walk_in' ? 'Walk-in' : 'Home Collection';
                        const badgeClass = donation.donation_method === 'walk_in' ? 'badge-primary' : 'badge-success';
                        const date = donation.donation_date || donation.scheduled_pickup_date || '-';
                        const time = donation.donation_time || donation.scheduled_pickup_time || '-';

                        content += `
                                                <tr>
                                                    <td>${escapeHtml(donorName)}</td>
                                                    <td class="text-center">
                                                        <span class="badge ${badgeClass}">${donationType}</span>
                                                    </td>
                                                    <td class="text-center">${donation.number_of_bags}</td>
                                                    <td class="text-center" style="font-size: 0.85rem;">${escapeHtml(donation.bag_volumes || '-')}</td>
                                                    <td class="text-center">
                                                        <span class="badge badge-info">${donation.total_volume}ml</span>
                                                    </td>
                                                    <td class="text-center">${formatDate(date)}</td>
                                                    <td class="text-center">${formatTime(time)}</td>
                                                </tr>
                                            `;
                    });

                    content += `
                                                    </tbody>
                                                </table>
                                            </div>
                                        `;

                    document.getElementById('batchDetailsContent').innerHTML = content;
                })
                .catch(error => {
                    document.getElementById('batchDetailsContent').innerHTML = `
                                            <div class="alert alert-danger">
                                                <i class="fas fa-exclamation-triangle"></i> Error loading batch details: ${escapeHtml(error.message)}
                                            </div>
                                        `;
                });
        }

        function formatDate(dateString) {
            if (!dateString || dateString === '-') return '-';
            try {
                const date = new Date(dateString);
                return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
            } catch (e) {
                return dateString;
            }
        }

        function formatTime(timeString) {
            if (!timeString || timeString === '-') return '-';
            try {
                // Handle both full datetime and time-only strings
                let date;
                if (timeString.includes('T') || timeString.includes(' ')) {
                    date = new Date(timeString);
                } else {
                    date = new Date(`2000-01-01T${timeString}`);
                }
                return date.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });
            } catch (e) {
                return timeString;
            }
        }

        function escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function refreshInventoryStats() {
            fetch('{{ route("admin.inventory.stats") }}')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('unpasteurized-count').textContent = data.unpasteurized_donations_count;
                    document.getElementById('unpasteurized-volume').textContent = Math.round(data.unpasteurized_total_volume) + 'ml';
                    document.getElementById('pasteurized-batches').textContent = data.pasteurized_batches_count;
                    document.getElementById('pasteurized-volume').textContent = Math.round(data.pasteurized_total_volume) + 'ml';
                    document.getElementById('dispensed-records').textContent = data.dispensed_records_count;
                    document.getElementById('dispensed-volume').textContent = Math.round(data.total_dispensed_volume) + 'ml';
                })
                .catch(error => {
                    console.error('Error refreshing stats:', error);
                });
        }
    </script>
@endsection