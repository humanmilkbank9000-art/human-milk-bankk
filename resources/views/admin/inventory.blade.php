@extends('layouts.admin-layout')


@section('title', 'Inventory Management')

@section('styles')
    <style>
        .stats-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            border: none;
            border-radius: 12px;
            padding: 20px 15px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s, box-shadow 0.2s;
            height: 100%;
            min-width: 150px;
            flex: 1 1 auto;
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
                flex: 1 1 calc(50% - 15px);
                min-width: 120px;
            }
        }

        .stats-icon {
            font-size: 2.5rem;
            margin-bottom: 10px;
            opacity: 0.8;
        }

        .stats-number {
            font-size: 1.8rem;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .stats-label {
            color: #6c757d;
            font-size: 0.85rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
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

        /* Responsive tabs - keep them in a single row */
        .nav-tabs {
            flex-wrap: nowrap;
            width: 100%;
            display: flex;
        }

        .nav-tabs .nav-item {
            flex: 1 1 33.333%;
            max-width: 33.333%;
            margin-right: 0;
        }

        .nav-tabs .nav-link {
            margin-right: 0;
            width: 100%;
            white-space: normal;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            gap: 0.25rem;
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

            .nav-tabs .nav-link i {
                font-size: 0.9rem;
                margin-bottom: 0.15rem;
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

            .nav-tabs .nav-link i {
                font-size: 0.8rem;
            }

            .nav-tabs .badge {
                font-size: 0.6rem;
                padding: 0.15em 0.3em;
            }
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid px-2 px-md-4">

        <!-- Inventory Statistics -->
        <div class="stats-cards-container">
            <div class="stats-card-wrapper">
                <div class="stats-card warning">
                    <div class="stats-icon">
                        <i class="fas fa-flask"></i>
                    </div>
                    <div class="stats-number" id="unpasteurized-count">{{ $unpasteurizedDonations->count() }}</div>
                    <div class="stats-label">Unpasteurized Donations</div>
                </div>
            </div>
            <div class="stats-card-wrapper">
                <div class="stats-card info">
                    <div class="stats-icon">
                        <i class="fas fa-tint"></i>
                    </div>
                    <div class="stats-number" id="unpasteurized-volume">
                        {{ number_format($unpasteurizedDonations->sum('available_volume'), 0) }}ml
                    </div>
                    <div class="stats-label">Unpasteurized Volume</div>
                </div>
            </div>
            <div class="stats-card-wrapper">
                <div class="stats-card primary">
                    <div class="stats-icon">
                        <i class="fas fa-vial"></i>
                    </div>
                    <div class="stats-number" id="pasteurized-batches">{{ $pasteurizationBatches->count() }}</div>
                    <div class="stats-label">Active Batches</div>
                </div>
            </div>
            <div class="stats-card-wrapper">
                <div class="stats-card info">
                    <div class="stats-icon">
                        <i class="fas fa-fill-drip"></i>
                    </div>
                    <div class="stats-number" id="pasteurized-volume">
                        {{ number_format($pasteurizationBatches->sum('available_volume'), 0) }}ml
                    </div>
                    <div class="stats-label">Pasteurized Volume</div>
                </div>
            </div>
            <div class="stats-card-wrapper">
                <div class="stats-card success">
                    <div class="stats-icon">
                        <i class="fas fa-hand-holding-medical"></i>
                    </div>
                    <div class="stats-number" id="dispensed-records">{{ $dispensedMilk->count() }}</div>
                    <div class="stats-label">Dispensed Records</div>
                </div>
            </div>
            <div class="stats-card-wrapper">
                <div class="stats-card success">
                    <div class="stats-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stats-number" id="dispensed-volume">
                        {{ number_format($dispensedMilk->sum('volume_dispensed'), 0) }}ml
                    </div>
                    <div class="stats-label">Total Dispensed</div>
                </div>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <ul class="nav nav-tabs" id="inventoryTabs" role="tablist">
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
                <div class="card mt-3 shadow-sm rounded-lg border-0">
                    <div class="card-header bg-warning text-dark rounded-top">
                        <h5 class="mb-0"><i class="fas fa-flask"></i> Unpasteurized Breastmilk</h5>
                        <small class="text-dark">Successful home collection and walk-in donations automatically recorded
                            here.</small>
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

                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped align-middle shadow-sm rounded">
                                        <thead class="table-success">
                                            <tr>
                                                <th class="text-center align-middle px-2 py-2">
                                                    <input type="checkbox" id="selectAllCheckbox"
                                                        onchange="toggleAllDonations()">
                                                </th>
                                                <th class="text-center px-2 py-2">Donor</th>
                                                <th class="text-center px-2 py-2">Type</th>
                                                <th class="text-center px-2 py-2">Bags</th>
                                                <th class="text-center px-2 py-2">Volume/Bag</th>
                                                <th class="text-center px-2 py-2">Total</th>
                                                <th class="text-center px-2 py-2">Available</th>
                                                <th class="text-center px-2 py-2">Date</th>
                                                <th class="text-center px-2 py-2">Time</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($unpasteurizedDonations as $donation)
                                                <tr>
                                                    <td class="text-center align-middle">
                                                        <input type="checkbox" class="pasteurize-checkbox donation-checkbox"
                                                            value="{{ $donation->breastmilk_donation_id }}"
                                                            onchange="updatePasteurizeButton()">
                                                    </td>
                                                    <td style="white-space: normal;">
                                                        <strong>{{ $donation->user->first_name }}
                                                            {{ $donation->user->last_name }}</strong>
                                                    </td>
                                                    <td class="text-center">
                                                        <span
                                                            class="badge badge-{{ $donation->donation_method === 'walk_in' ? 'primary' : 'success' }} donation-type-badge">
                                                            {{ $donation->donation_method === 'walk_in' ? 'Walk-in' : 'Home Collection' }}
                                                        </span>
                                                    </td>
                                                    <td class="text-center">{{ $donation->number_of_bags }}</td>
                                                    <td class="text-center" style="white-space: normal; font-size: 0.85rem;">
                                                        <small>{{ $donation->formatted_bag_volumes }}</small>
                                                    </td>
                                                    <td class="text-center">
                                                        <span
                                                            class="badge badge-info volume-badge">{{ $donation->total_volume }}ml</span>
                                                    </td>
                                                    <td class="text-center">
                                                        <span
                                                            class="badge badge-success volume-badge">{{ $donation->available_volume }}ml</span>
                                                    </td>
                                                    <td class="text-center" style="white-space: nowrap;">
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
                                                    <td class="text-center" style="white-space: nowrap;">
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
                <div class="card mt-3 shadow-sm rounded-lg border-0">
                    <div class="card-header bg-primary text-white rounded-top">
                        <h5 class="mb-0"><i class="fas fa-vial"></i> Pasteurized Breastmilk</h5>
                        <small class="text-white">Batches of pasteurized donations with FIFO management.</small>
                    </div>
                    <div class="card-body">
                        @if($pasteurizationBatches->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped align-middle shadow-sm rounded">
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
                                        @foreach($pasteurizationBatches as $batch)
                                            <tr class="batch-row batch-expandable"
                                                onclick="toggleBatchDetails({{ $batch->batch_id }})">
                                                <td style="white-space: normal;">
                                                    <strong>{{ $batch->batch_number }}</strong>
                                                    <i class="fas fa-chevron-down batch-toggle-icon ms-1"
                                                        id="icon-{{ $batch->batch_id }}"></i>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge badge-info volume-badge">{{ $batch->total_volume }}ml</span>
                                                </td>
                                                <td class="text-center">
                                                    <span
                                                        class="badge badge-success volume-badge">{{ $batch->available_volume }}ml</span>
                                                </td>
                                                <td class="text-center" style="white-space: nowrap;">
                                                    <small>{{ $batch->formatted_date }}</small>
                                                </td>
                                                <td class="text-center" style="white-space: nowrap;">
                                                    <small>{{ $batch->formatted_time }}</small>
                                                </td>
                                                <td class="text-center"><small>{{ $batch->donations->count() }}</small></td>
                                                <td class="text-center" onclick="event.stopPropagation()">
                                                    <button class="btn btn-sm btn-outline-info" title="View Details"
                                                        onclick="viewBatchDetails({{ $batch->batch_id }})">
                                                        <i class="fas fa-eye"></i> View
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr class="batch-details d-none" id="details-{{ $batch->batch_id }}">
                                                <td colspan="7">
                                                    <div class="p-3">
                                                        <h6><i class="fas fa-list"></i> Donations in {{ $batch->batch_number }}</h6>
                                                        <div class="table-responsive">
                                                            <table class="table table-sm table-bordered align-middle">
                                                                <thead class="table-light">
                                                                    <tr>
                                                                        <th class="text-center px-2 py-2">Donor</th>
                                                                        <th class="text-center px-2 py-2">Type</th>
                                                                        <th class="text-center px-2 py-2">Bags</th>
                                                                        <th class="text-center px-2 py-2">Volume/Bag</th>
                                                                        <th class="text-center px-2 py-2">Total</th>
                                                                        <th class="text-center px-2 py-2">Date</th>
                                                                        <th class="text-center px-2 py-2">Time</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach($batch->donations as $donation)
                                                                        <tr>
                                                                            <td style="white-space: normal;">
                                                                                <small>{{ $donation->user->first_name }}
                                                                                    {{ $donation->user->last_name }}</small>
                                                                            </td>
                                                                            <td class="text-center">
                                                                                <span
                                                                                    class="badge badge-{{ $donation->donation_method === 'walk_in' ? 'primary' : 'success' }} donation-type-badge">
                                                                                    {{ $donation->donation_method === 'walk_in' ? 'Walk-in' : 'Home Collection' }}
                                                                                </span>
                                                                            </td>
                                                                            <td class="text-center">
                                                                                <small>{{ $donation->number_of_bags }}</small>
                                                                            </td>
                                                                            <td class="text-center"
                                                                                style="white-space: normal; font-size: 0.8rem;">
                                                                                <small>{{ $donation->formatted_bag_volumes }}</small>
                                                                            </td>
                                                                            <td class="text-center">
                                                                                <small>{{ $donation->total_volume }}ml</small>
                                                                            </td>
                                                                            <td class="text-center" style="white-space: nowrap;">
                                                                                <small>
                                                                                    @if($donation->donation_date)
                                                                                        {{ $donation->donation_date->format('M d, Y') }}
                                                                                    @elseif($donation->scheduled_pickup_date)
                                                                                        {{ $donation->scheduled_pickup_date->format('M d, Y') }}
                                                                                    @else
                                                                                        -
                                                                                    @endif
                                                                            </td>
                                                                            <td>
                                                                                @if($donation->availability)
                                                                                    {{ $donation->availability->formatted_time }}
                                                                                @elseif($donation->donation_time)
                                                                                    {{ \Carbon\Carbon::parse($donation->donation_time)->format('g:i A') }}
                                                                                @elseif($donation->scheduled_pickup_time)
                                                                                    {{ $donation->scheduled_pickup_time }}
                                                                                @else
                                                                                    -
                                                                                @endif
                                                                            </td>
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                        @if($batch->notes)
                                                            <div class="mt-3">
                                                                <h6>Notes:</h6>
                                                                <p class="text-muted">{{ $batch->notes }}</p>
                                                            </div>
                                                        @endif
                                                    </div>
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
                <div class="card mt-3 shadow-sm rounded-lg border-0">
                    <div class="card-header bg-success text-white rounded-top">
                        <h5 class="mb-0"><i class="fas fa-hand-holding-medical"></i> Dispensed Breastmilk</h5>
                        <small class="text-white">Records of all dispensed breastmilk to recipients.</small>
                    </div>
                    <div class="card-body">
                        @if($dispensedMilk->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped align-middle shadow-sm rounded">
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
                                        @foreach($dispensedMilk as $dispensed)
                                            <tr>
                                                <td style="white-space: normal;">
                                                    <strong>{{ $dispensed->guardian->first_name }}
                                                        {{ $dispensed->guardian->last_name }}</strong>
                                                </td>
                                                <td style="white-space: normal;">
                                                    <strong>{{ $dispensed->recipient->first_name }}
                                                        {{ $dispensed->recipient->last_name }}</strong><br>
                                                    <small class="text-muted">{{ $dispensed->recipient->getCurrentAgeInMonths() }}
                                                        mos</small>
                                                </td>
                                                <td style="white-space: normal; font-size: 0.85rem;">
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
                                                <td class="text-center">
                                                    <span
                                                        class="badge badge-success volume-badge">{{ $dispensed->volume_dispensed }}ml</span>
                                                </td>
                                                <td class="text-center" style="white-space: nowrap;">
                                                    <small>{{ $dispensed->formatted_date }}</small>
                                                </td>
                                                <td class="text-center" style="white-space: nowrap;">
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
                const volume = parseFloat(row.cells[5].textContent.replace('ml', ''));
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

        function toggleBatchDetails(batchId) {
            const detailsRow = document.getElementById('details-' + batchId);
            const icon = document.getElementById('icon-' + batchId);

            if (detailsRow.classList.contains('d-none')) {
                detailsRow.classList.remove('d-none');
                icon.classList.remove('fa-chevron-down');
                icon.classList.add('fa-chevron-up');
            } else {
                detailsRow.classList.add('d-none');
                icon.classList.remove('fa-chevron-up');
                icon.classList.add('fa-chevron-down');
            }
        }

        function viewBatchDetails(batchId) {
            fetch(`{{ url('/admin/inventory/batch') }}/${batchId}`)
                .then(response => response.json())
                .then(data => {
                    console.log('Batch details:', data);
                })
                .catch(error => {
                    alert('Error loading batch details: ' + error.message);
                });
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