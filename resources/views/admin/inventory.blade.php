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

        .btn-dispose {
            background: linear-gradient(135deg, #dc2626, #c0262c);
            border: none;
            color: white;
            font-weight: 600;
        }

        .btn-dispose:hover {
            background: linear-gradient(135deg, #b91c1c, #991b1b);
            color: white;
        }

        .volume-badge {
            font-size: 0.85rem;
            padding: 4px 8px;
        }

        /* Per-bag vertical stack helper to ensure Date/Time/Volume lines align */
        .per-bag-list {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 0.25rem; /* consistent spacing between bag lines */
        }

        .selected-volume-info {
            font-size: 0.95rem;
            color: #343a40;
            min-width: 170px;
            text-align: right;
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
                /* Responsive table wrapper */
            }

            .table th,
            .table td {
                padding: 0.5rem 0.3rem;
            }

            .stats-number {
                font-size: 1.5rem;
            }

            .stats-icon {
                font-size: 2.2rem;
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
                                    <div class="d-flex align-items-center">
                                        <div id="selectedVolumeInfo" class="me-3 selected-volume-info">
                                            Selected: <span id="selectedVolumeValue">0</span> / 9000 ml
                                        </div>
                                        <button type="button" class="btn btn-dispose me-2" onclick="disposeSelected()" disabled id="disposeBtn">
                                            <i class="fas fa-trash-alt"></i> Dispose Selected
                                        </button>
                                        <button type="button" class="btn btn-pasteurize" onclick="pasteurizeSelected()" disabled
                                            id="pasteurizeBtn">
                                            <i class="fas fa-fire"></i> Pasteurize Selected
                                        </button>
                                    </div>
                                </div>

                                <div class="table-container-standard table-wide">
                                    <table class="table table-standard table-bordered table-striped align-middle">
                                        <thead class="table-success">
                                            <tr>
                                                <th class="text-center align-middle px-2 py-2" style="color: #000;">&nbsp;</th>
                                                <th class="text-center px-2 py-2" style="color: #000;">Donor</th>
                                                <th class="text-center px-2 py-2" style="color: #000;">Type</th>
                                                <th class="text-center px-2 py-2" style="color: #000;">Bags</th>
                                                <th class="text-center px-2 py-2" style="color: #000;">Volume/Bag</th>
                                                <th class="text-center px-2 py-2" style="color: #000;">Total</th>
                                                <th class="text-center px-2 py-2" style="color: #000;">Available</th>
                                                <th class="text-center px-2 py-2" style="color: #000;">Date</th>
                                                <th class="text-center px-2 py-2" style="color: #000;">Time</th>
                                                <th class="text-center px-2 py-2" style="color: #000;">Expires</th>
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
                                    onchange="toggleDonationBags(this)">
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
                                                    <td class="text-center align-top" data-label="Bags">
                                                        @php
                                                            $rawBagVolumes = $donation->bag_volumes ?? $donation->formatted_bag_volumes ?? '';
                                                            if (is_array($rawBagVolumes)) {
                                                                $bagVolumes = $rawBagVolumes;
                                                            } elseif (is_string($rawBagVolumes) && trim($rawBagVolumes) !== '') {
                                                                $bagVolumes = preg_split('/\s*,\s*/', trim($rawBagVolumes));
                                                            } else {
                                                                $bagVolumes = [];
                                                            }
                                                            $bagsCount = max(1, intval($donation->number_of_bags));
                                                        @endphp
                                                        <div class="bag-list per-bag-list text-start" data-bag-volumes='@json($bagVolumes)'>
                                                            @for ($i = 0; $i < $bagsCount; $i++)
                                                                <div class="d-flex align-items-center mb-1">
                                                                    <input class="form-check-input bag-checkbox me-2" type="checkbox"
                                                                        id="bagCheckbox{{ $donation->breastmilk_donation_id }}_{{ $i }}"
                                                                        data-donation-id="{{ $donation->breastmilk_donation_id }}"
                                                                        data-bag-index="{{ $i }}"
                                                                        onchange="updatePasteurizeButton()">
                                                                    <label class="form-check-label mb-0" for="bagCheckbox{{ $donation->breastmilk_donation_id }}_{{ $i }}" style="font-size:0.88rem;">
                                                                        Bag {{ $i + 1 }}
                                                                    </label>
                                                                </div>
                                                            @endfor
                                                        </div>
                                                    </td>
                                                    <td class="text-center align-top" style="white-space: normal; font-size: 0.85rem;" data-label="Volume/Bag">
                                                        <div class="volume-list per-bag-list text-start">
                                                            @for ($i = 0; $i < $bagsCount; $i++)
                                                                @php
                                                                    $vol = isset($bagVolumes[$i]) ? $bagVolumes[$i] : '';
                                                                @endphp
                                                                <div>
                                                                    @if($vol !== '')
                                                                        <small>{{ stripos($vol, 'ml') === false ? $vol . 'ml' : $vol }}</small>
                                                                    @else
                                                                        <span class="text-muted">-</span>
                                                                    @endif
                                                                </div>
                                                            @endfor
                                                        </div>
                                                    </td>
                                                    <td class="text-center" data-label="Total">
                                                        <span
                                                            class="badge badge-info volume-badge">{{ $donation->formatted_total_volume }}ml</span>
                                                    </td>
                                                    <td class="text-center" data-label="Available">
                                                        <span
                                                            class="badge badge-success volume-badge">{{ $donation->formatted_available_volume }}ml</span>
                                                    </td>
                                                    <td class="text-center align-top" style="white-space: nowrap;" data-label="Date">
                                                        <div class="per-bag-list text-start">
                                                            @for ($i = 0; $i < $bagsCount; $i++)
                                                                <div><small>
                                                                    @if($donation->donation_date)
                                                                        {{ $donation->donation_date->format('M d, Y') }}
                                                                    @elseif($donation->scheduled_pickup_date)
                                                                        {{ $donation->scheduled_pickup_date->format('M d, Y') }}
                                                                    @else
                                                                        -
                                                                    @endif
                                                                </small></div>
                                                            @endfor
                                                        </div>
                                                    </td>
                                                    <td class="text-center align-top" style="white-space: nowrap;" data-label="Time">
                                                        <div class="per-bag-list text-start">
                                                            @for ($i = 0; $i < $bagsCount; $i++)
                                                                <div><small>
                                                                    @if($donation->availability)
                                                                        {{ $donation->availability->formatted_time }}
                                                                    @elseif($donation->donation_time)
                                                                        {{ \Carbon\Carbon::parse($donation->donation_time)->format('g:i A') }}
                                                                    @elseif($donation->scheduled_pickup_time)
                                                                        {{ $donation->scheduled_pickup_time }}
                                                                    @else
                                                                        -
                                                                    @endif
                                                                </small></div>
                                                            @endfor
                                                        </div>
                                                    </td>
                                                    <td class="text-center" data-label="Expires">
                                                        @php
                                                            // Use donation_date if present, otherwise created_at; expiry = +6 months
                                                            $baseDate = $donation->donation_date ?? $donation->created_at;
                                                            try {
                                                                $expiry = \Carbon\Carbon::parse($baseDate)->addMonths(6)->setTimezone('Asia/Manila');
                                                            } catch (\Exception $e) {
                                                                $expiry = null;
                                                            }
                                                        @endphp
                                                        @if($expiry)
                                                            <small>{{ $expiry->format('M d, Y') }}</small>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
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
                                            <th class="text-center px-2 py-2">No. of Donation</th>
                                            <th class="text-center px-2 py-2">Total Volume</th>
                                            <th class="text-center px-2 py-2">Available</th>
                                            <th class="text-center px-2 py-2">Date</th>
                                            <th class="text-center px-2 py-2">Time</th>
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
                                                <td class="text-center" data-label="No. of Donation">
                                                    <small>{{ $batch->donations->count() }}</small>
                                                </td>
                                                <td class="text-center" data-label="Total Volume">
                                                    <span class="badge badge-info volume-badge">{{ $batch->formatted_total_volume }}ml</span>
                                                </td>
                                                <td class="text-center" data-label="Available">
                                                    <span class="badge badge-success volume-badge">{{ $batch->formatted_available_volume }}ml</span>
                                                </td>
                                                <td class="text-center" style="white-space: nowrap;" data-label="Date">
                                                    <small>{{ $batch->formatted_date }}</small>
                                                </td>
                                                <td class="text-center" style="white-space: nowrap;" data-label="Time">
                                                    <small>{{ $batch->formatted_time }}</small>
                                                </td>
                                                <td class="text-center" data-label="Action">
                                                    <button class="admin-review-btn btn-sm" title="Review Details" onclick="viewBatchDetails({{ $batch->batch_id }})">
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

    <!-- Disposal Modal -->
    <div class="modal fade" id="disposalModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-trash-alt"></i> Dispose Selected Bags
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Warning:</strong> Disposing a bag is permanent and will remove it from inventory.
                    </div>

                    <div id="selectedDisposalList"></div>

                    <div class="mb-3">
                        <label for="disposalNotes" class="form-label">Notes (Optional)</label>
                        <textarea class="form-control" id="disposalNotes" rows="3"
                            placeholder="Reason for disposal (e.g. contaminated, expired)"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-dispose" onclick="confirmDisposal()">
                        <i class="fas fa-trash-alt"></i> Confirm Disposal
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
            // Check all bag checkboxes and donation master checkboxes
            document.querySelectorAll('.bag-checkbox').forEach(cb => cb.checked = true);
            document.querySelectorAll('.donation-checkbox').forEach(cb => cb.checked = true);
            const headerCheckbox = document.getElementById('selectAllCheckbox');
            if (headerCheckbox) headerCheckbox.checked = true;
            updatePasteurizeButton();
        }

        function clearAllSelections() {
            document.querySelectorAll('.bag-checkbox').forEach(cb => cb.checked = false);
            document.querySelectorAll('.donation-checkbox').forEach(cb => cb.checked = false);
            const headerCheckbox = document.getElementById('selectAllCheckbox');
            if (headerCheckbox) headerCheckbox.checked = false;
            updatePasteurizeButton();
        }

        function toggleAllDonations() {
            const selectAll = document.getElementById('selectAllCheckbox');
            const checked = selectAll ? selectAll.checked : false;
            document.querySelectorAll('.bag-checkbox').forEach(cb => cb.checked = checked);
            document.querySelectorAll('.donation-checkbox').forEach(cb => cb.checked = checked);
            updatePasteurizeButton();
        }

        function toggleDonationBags(masterCheckbox) {
            // When a donation master checkbox is toggled, toggle its row's bag checkboxes
            const row = masterCheckbox.closest('tr');
            if (!row) return;
            row.querySelectorAll('.bag-checkbox').forEach(cb => cb.checked = masterCheckbox.checked);
            updatePasteurizeButton();
        }

        function updatePasteurizeButton() {
            // Count selected bags (we enable pasteurize when at least one bag is selected)
            const checkedBagBoxes = document.querySelectorAll('.bag-checkbox:checked');
            const pasteurizeBtn = document.getElementById('pasteurizeBtn');
            const disposeBtn = document.getElementById('disposeBtn');
            pasteurizeBtn.disabled = checkedBagBoxes.length === 0;
            if (disposeBtn) disposeBtn.disabled = checkedBagBoxes.length === 0;

            if (checkedBagBoxes.length > 0) {
                pasteurizeBtn.innerHTML = `<i class="fas fa-fire"></i> Pasteurize Selected (${checkedBagBoxes.length} bag${checkedBagBoxes.length > 1 ? 's' : ''})`;
                if (disposeBtn) disposeBtn.innerHTML = `<i class="fas fa-trash-alt"></i> Dispose Selected (${checkedBagBoxes.length} bag${checkedBagBoxes.length > 1 ? 's' : ''})`;
            } else {
                pasteurizeBtn.innerHTML = `<i class="fas fa-fire"></i> Pasteurize Selected`;
                if (disposeBtn) disposeBtn.innerHTML = `<i class="fas fa-trash-alt"></i> Dispose Selected`;
            }

            // Update the selected total volume display
            updateSelectedVolume();
        }

        // Maximum selectable total volume (ml)
        const MAX_SELECTABLE_VOLUME = 9000;

        function updateSelectedVolume() {
            const checkedBags = document.querySelectorAll('.bag-checkbox:checked');
            let totalVolume = 0;

            checkedBags.forEach(cb => {
                const donationId = cb.getAttribute('data-donation-id');
                const bagIndex = parseInt(cb.getAttribute('data-bag-index')) || 0;
                const donationRowEl = document.querySelector(`.bag-checkbox[data-donation-id="${donationId}"]`);
                if (!donationRowEl) return;
                const row = donationRowEl.closest('tr');
                if (!row) return;

                const bagContainer = row.querySelector('[data-bag-volumes]');
                let bagVolumesRaw = bagContainer ? bagContainer.getAttribute('data-bag-volumes') : '';
                let bagVolumes = [];
                try {
                    const parsed = JSON.parse(bagVolumesRaw);
                    if (Array.isArray(parsed)) bagVolumes = parsed;
                } catch (e) {
                    if (bagVolumesRaw && typeof bagVolumesRaw === 'string') {
                        bagVolumes = bagVolumesRaw.split(/\s*,\s*/).map(v => v.trim());
                    }
                }

                const rawVol = (bagVolumes[bagIndex] !== undefined) ? ('' + bagVolumes[bagIndex]) : '';
                const vol = parseFloat((rawVol + '').replace(/[^0-9.\-]+/g, '')) || 0;
                totalVolume += vol;
            });

            const displayEl = document.getElementById('selectedVolumeValue');
            if (displayEl) {
                // Format with thousand separators
                displayEl.textContent = Math.round(totalVolume).toLocaleString();
                // Optionally change color if exceeds max
                const parent = document.getElementById('selectedVolumeInfo');
                if (parent) {
                    if (totalVolume > MAX_SELECTABLE_VOLUME) {
                        parent.style.color = '#b91c1c'; // red
                    } else {
                        parent.style.color = '#343a40';
                    }
                }
            }
        }

        function pasteurizeSelected() {
            const checkedBags = document.querySelectorAll('.bag-checkbox:checked');

            if (checkedBags.length === 0) {
                alert('Please select at least one bag to pasteurize.');
                return;
            }

            // Build list of selected donations/bags for modal
            let donationsList = '<h6>Selected Bags:</h6><ul>';
            let totalVolume = 0;

            // Group by donation id
            const groups = {};
            checkedBags.forEach(cb => {
                const donationId = cb.getAttribute('data-donation-id');
                const bagIndex = parseInt(cb.getAttribute('data-bag-index')) || 0;
                if (!groups[donationId]) groups[donationId] = [];
                groups[donationId].push(bagIndex);
            });

            Object.keys(groups).forEach(donationId => {
                // Find the row for this donation
                const donationRow = document.querySelector(`.bag-checkbox[data-donation-id="${donationId}"]`).closest('tr');
                const donorName = donationRow.cells[1].textContent.trim().split('\n')[0];

                // Attempt to read bag volumes from the bag container data attribute
                const bagContainer = donationRow.querySelector('[data-bag-volumes]');
                let bagVolumesRaw = bagContainer ? bagContainer.getAttribute('data-bag-volumes') : '';
                let bagVolumes = [];
                try {
                    // If it's JSON array string
                    const parsed = JSON.parse(bagVolumesRaw);
                    if (Array.isArray(parsed)) bagVolumes = parsed;
                } catch (e) {
                    // Not JSON, fall back to splitting by comma
                    if (bagVolumesRaw && typeof bagVolumesRaw === 'string') {
                        bagVolumes = bagVolumesRaw.split(/\s*,\s*/).map(v => v.trim());
                    }
                }

                groups[donationId].forEach(bi => {
                    const rawVol = (bagVolumes[bi] !== undefined) ? ('' + bagVolumes[bi]) : '';
                    // Extract numeric portion
                    const vol = parseFloat((rawVol + '').replace(/[^0-9.\-]+/g, '')) || 0;
                    totalVolume += vol;
                    const label = rawVol ? `${rawVol}ml` : `Bag ${bi + 1}`;
                    donationsList += `<li>${donorName} - ${label}</li>`;
                });
            });

            donationsList += `</ul><p><strong>Total Volume: ${totalVolume}ml</strong></p>`;

            document.getElementById('selectedDonationsList').innerHTML = donationsList;

            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('pasteurizationModal'));
            modal.show();
        }

        function disposeSelected() {
            const checkedBags = document.querySelectorAll('.bag-checkbox:checked');

            if (checkedBags.length === 0) {
                alert('Please select at least one bag to dispose.');
                return;
            }

            // Build list of selected donations/bags for modal
            let donationsList = '<h6>Selected Bags for Disposal:</h6><ul>';
            let totalVolume = 0;

            // Group by donation id
            const groups = {};
            checkedBags.forEach(cb => {
                const donationId = cb.getAttribute('data-donation-id');
                const bagIndex = parseInt(cb.getAttribute('data-bag-index')) || 0;
                if (!groups[donationId]) groups[donationId] = [];
                groups[donationId].push(bagIndex);
            });

            Object.keys(groups).forEach(donationId => {
                const donationRow = document.querySelector(`.bag-checkbox[data-donation-id="${donationId}"]`).closest('tr');
                const donorName = donationRow.cells[1].textContent.trim().split('\n')[0];

                const bagContainer = donationRow.querySelector('[data-bag-volumes]');
                let bagVolumesRaw = bagContainer ? bagContainer.getAttribute('data-bag-volumes') : '';
                let bagVolumes = [];
                try {
                    const parsed = JSON.parse(bagVolumesRaw);
                    if (Array.isArray(parsed)) bagVolumes = parsed;
                } catch (e) {
                    if (bagVolumesRaw && typeof bagVolumesRaw === 'string') {
                        bagVolumes = bagVolumesRaw.split(/\s*,\s*/).map(v => v.trim());
                    }
                }

                groups[donationId].forEach(bi => {
                    const rawVol = (bagVolumes[bi] !== undefined) ? ('' + bagVolumes[bi]) : '';
                    const vol = parseFloat((rawVol + '').replace(/[^0-9.\-]+/g, '')) || 0;
                    totalVolume += vol;
                    const label = rawVol ? `${rawVol}ml` : `Bag ${bi + 1}`;
                    donationsList += `<li>${donorName} - ${label}</li>`;
                });
            });

            donationsList += `</ul><p><strong>Total Volume: ${totalVolume}ml</strong></p>`;

            document.getElementById('selectedDisposalList').innerHTML = donationsList;

            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('disposalModal'));
            modal.show();
        }

        function confirmDisposal() {
            const checkedBags = document.querySelectorAll('.bag-checkbox:checked');
            const notes = document.getElementById('disposalNotes').value;

            if (checkedBags.length === 0) {
                alert('No bags selected to dispose.');
                return;
            }

            const donationMap = {};
            checkedBags.forEach(cb => {
                const donationId = cb.getAttribute('data-donation-id');
                const bagIndex = parseInt(cb.getAttribute('data-bag-index')) || 0;
                if (!donationMap[donationId]) donationMap[donationId] = [];
                donationMap[donationId].push(bagIndex);
            });

            // Hide modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('disposalModal'));
            modal.hide();

            // Show loading indicator
            Swal.fire({
                title: 'Processing...',
                text: 'Disposing selected bags',
                icon: 'info',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch('{{ route("admin.inventory.dispose") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    donation_map: donationMap,
                    // include donation_ids array for compatibility with older callers
                    donation_ids: Object.keys(donationMap).map(k => parseInt(k)),
                    notes: notes
                })
            })
                .then(async response => {
                    const contentType = response.headers.get('content-type') || '';
                    const text = await response.text();

                    // Try to parse JSON if content-type indicates JSON
                    if (contentType.includes('application/json')) {
                        try {
                            const data = JSON.parse(text);
                            return { ok: response.ok, data };
                        } catch (e) {
                            return { ok: response.ok, data: null, text };
                        }
                    }

                    // Non-JSON response (probably HTML error page)
                    return { ok: response.ok, data: null, text };
                })
                .then(res => {
                    // Handle Laravel validation errors (422) which return { message, errors: { field: [msg,...] } }
                    if (res.data && res.data.errors) {
                        // Build a readable error message
                        const errs = [];
                        Object.keys(res.data.errors).forEach(k => {
                            const arr = res.data.errors[k] || [];
                            if (Array.isArray(arr)) arr.forEach(m => errs.push(m));
                        });
                        const msg = errs.join('\n') || res.data.message || 'Validation failed.';
                        Swal.fire({ title: 'Validation Error', html: `<pre style="text-align:left;white-space:pre-wrap;">${escapeHtml(msg)}</pre>`, icon: 'error', confirmButtonText: 'OK', confirmButtonColor: '#dc2626' });
                        return;
                    }

                    if (res.data && res.data.success) {
                        Swal.fire({
                            title: 'Disposed',
                            text: res.data.message || 'Selected bags have been disposed.',
                            icon: 'success',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#b91c1c'
                        }).then(() => {
                            location.reload();
                        });
                        return;
                    }

                    // If server returned JSON with an error
                    if (res.data && !res.data.success) {
                        Swal.fire({
                            title: 'Error',
                            text: res.data.error || (res.data.message || 'Failed to dispose selected bags.'),
                            icon: 'error',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#dc2626'
                        });
                        return;
                    }

                    // Non-JSON or unparsable response — show a helpful error including server body (trimmed)
                    const serverText = (res.text || '').toString();
                    const short = serverText.length > 1000 ? serverText.substring(0, 1000) + '... (truncated)' : serverText;
                    Swal.fire({
                        title: 'Server Error',
                        html: `<pre style="text-align:left;white-space:pre-wrap;">${escapeHtml(short)}</pre>`,
                        icon: 'error',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#dc2626'
                    });
                })
                .catch(error => {
                    Swal.fire({
                        title: 'Error',
                        text: error.message || 'An error occurred while disposing bags.',
                        icon: 'error',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#dc2626'
                    });
                });
        }

        function confirmPasteurization() {
            // Build payload based on selected bag-checkboxes grouped by donation
            const checkedBags = document.querySelectorAll('.bag-checkbox:checked');
            const notes = document.getElementById('pasteurizationNotes').value;

            if (checkedBags.length === 0) {
                alert('No bags selected to pasteurize.');
                return;
            }

            const donationMap = {};
            checkedBags.forEach(cb => {
                const donationId = cb.getAttribute('data-donation-id');
                const bagIndex = parseInt(cb.getAttribute('data-bag-index')) || 0;
                if (!donationMap[donationId]) donationMap[donationId] = [];
                donationMap[donationId].push(bagIndex);
            });

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
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    // donation_map: { donationId: [bagIndex, ...] }
                    donation_map: donationMap,
                    // include donation_ids array for compatibility with older callers
                    donation_ids: Object.keys(donationMap).map(k => parseInt(k)),
                    notes: notes
                })
            })
                .then(async response => {
                    const contentType = response.headers.get('content-type') || '';
                    const text = await response.text();

                    if (contentType.includes('application/json')) {
                        try {
                            const data = JSON.parse(text);
                            return { ok: response.ok, data };
                        } catch (e) {
                            return { ok: response.ok, data: null, text };
                        }
                    }

                    // Non-JSON response (probably HTML error page)
                    return { ok: response.ok, data: null, text };
                })
                .then(res => {
                    // Handle Laravel validation errors
                    if (res.data && res.data.errors) {
                        const errs = [];
                        Object.keys(res.data.errors).forEach(k => {
                            const arr = res.data.errors[k] || [];
                            if (Array.isArray(arr)) arr.forEach(m => errs.push(m));
                        });
                        const msg = errs.join('\n') || res.data.message || 'Validation failed.';
                        Swal.fire({ title: 'Validation Error', html: `<pre style="text-align:left;white-space:pre-wrap;">${escapeHtml(msg)}</pre>`, icon: 'error', confirmButtonText: 'OK', confirmButtonColor: '#dc2626' });
                        return;
                    }

                    if (res.data && res.data.success) {
                        // Show success SweetAlert
                        Swal.fire({
                            title: 'Success!',
                            html: `<strong>${res.data.simple_batch_name}</strong> created successfully!<br><br>` +
                                `<div style="font-size: 1.1em;">` +
                                `Processed <strong>${res.data.donations_count}</strong> donations<br>` +
                                `Totaling <strong>${res.data.total_volume}ml</strong>` +
                                `</div>`,
                            icon: 'success',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#10b981',
                            timer: 5000,
                            timerProgressBar: true
                        }).then(() => {
                            location.reload(); // Refresh page to show updated inventory
                        });
                        return;
                    }

                    // If server returned JSON with an error
                    if (res.data && !res.data.success) {
                        const msg = res.data.error || res.data.message || 'Failed to create pasteurization batch.';
                        Swal.fire({ title: 'Error', text: msg, icon: 'error', confirmButtonText: 'OK', confirmButtonColor: '#dc2626' });
                        return;
                    }

                    // Non-JSON or unparsable response — show a helpful error including server body (trimmed)
                    const serverText = (res.text || '').toString();
                    const short = serverText.length > 1000 ? serverText.substring(0, 1000) + '... (truncated)' : serverText;
                    Swal.fire({
                        title: 'Server Error',
                        html: `<pre style="text-align:left;white-space:pre-wrap;">${escapeHtml(short)}</pre>`,
                        icon: 'error',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#dc2626'
                    });
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
                    // Normalize payload: controller may return { batch, donations } or a flat batch object
                    const payload = data || {};
                    const batch = payload.batch ?? payload;
                    const donations = payload.donations ?? (Array.isArray(batch.donations) ? batch.donations : []);

                    // Determine batch number safely
                    const batchNumber = (batch && (batch.batch_number || batch.batchNumber || batch.batch_id || batch.id)) || 'Batch';
                    document.getElementById('batchModalTitle').textContent = `${batchNumber} - Details`;

                    // Build the content to mimic the unpasteurized table alignment with per-bag lists
                    let content = '';

                    if (batch && batch.notes) {
                        content += `
                        <div class="alert alert-info mb-4">
                            <h6><i class="fas fa-sticky-note"></i> Notes:</h6>
                            <p class="mb-0">${escapeHtml(batch.notes)}</p>
                        </div>`;
                    }

                    content += `
                        <h6 class="mb-3"><i class="fas fa-list"></i> Donations in this Batch (${(donations || []).length})</h6>
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
                                <tbody>`;

                    (donations || []).forEach(donation => {
                        const donorName = donation.donor_name || (donation.user ? `${donation.user.first_name} ${donation.user.last_name}` : 'Unknown');
                        const donationType = donation.donation_type || (donation.donation_method === 'walk_in' ? 'Walk-in' : 'Home Collection');
                        const badgeClass = (donation.donation_type && donation.donation_type.toLowerCase().includes('walk')) ? 'badge-primary' : 'badge-success';
                        const bags = donation.number_of_bags || 1;
                        const volumePerBag = donation.volume_per_bag || '-';
                        const totalVol = donation.total_volume || 0;
                        const date = donation.date || '-';
                        const time = donation.time || '-';

                        // Build per-bag volume list (volume_per_bag might be a comma-separated string)
                        let perBagHtml = '';
                        if (typeof volumePerBag === 'string' && volumePerBag.trim() !== '') {
                            const parts = volumePerBag.split(/\s*,\s*/);
                            parts.forEach(p => {
                                perBagHtml += `<div>${escapeHtml(p)}</div>`;
                            });
                        } else if (Array.isArray(volumePerBag)) {
                            volumePerBag.forEach(p => perBagHtml += `<div>${escapeHtml(p)}</div>`);
                        } else {
                            // Fallback: repeat dash per bag
                            for (let i = 0; i < bags; i++) perBagHtml += `<div>-</div>`;
                        }

                        // Repeat date/time per bag for consistent alignment
                        let dateHtml = '';
                        let timeHtml = '';
                        for (let i = 0; i < bags; i++) {
                            dateHtml += `<div><small>${escapeHtml(date)}</small></div>`;
                            timeHtml += `<div><small>${escapeHtml(time)}</small></div>`;
                        }

                        content += `
                            <tr>
                                <td style="white-space: normal;">${escapeHtml(donorName)}</td>
                                <td class="text-center"><span class="badge ${badgeClass}">${escapeHtml(donationType)}</span></td>
                                <td class="text-center">${bags}</td>
                                <td class="text-start" style="font-size:0.9rem;">${perBagHtml}</td>
                                <td class="text-center"><span class="badge badge-info">${totalVol}ml</span></td>
                                <td class="text-center">${dateHtml}</td>
                                <td class="text-center">${timeHtml}</td>
                            </tr>`;
                    });

                    content += `</tbody></table></div>`;

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
                const date = parseYMD(dateString);
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

        // Initialize selected volume display once page scripts have loaded
        try {
            updatePasteurizeButton();
        } catch (e) {
            // ignore if functions are not yet available
        }
    </script>
@endsection