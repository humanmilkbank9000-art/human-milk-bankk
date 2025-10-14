@extends('layouts.admin-layout')

@section('title', 'Monthly Reports')
@section('pageTitle', 'Monthly Reports')
@section('styles')
    <style>
        .table thead th {
            background: #f8fafc;
            font-weight: 600;
            font-size: 1rem;
            border-bottom: 2px solid #eaeaea;
            text-align: center;
            padding: 1rem 1.5rem;
        }

        .table tbody td {
            text-align: center;
            vertical-align: middle;
            padding: 1rem 1.5rem;
        }

        .table tbody tr {
            transition: box-shadow 0.2s, background 0.2s;
        }

        .table tbody tr:hover {
            background: #f6f8ff;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
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
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        .table-responsive {
            border-radius: 8px;
            overflow: hidden;
            min-width: 900px;
        }

        .btn {
            font-size: 0.95rem;
            border-radius: 6px;
        }

        /* Filter form styling - always horizontal */
        .filter-form {
            display: flex;
            flex-wrap: nowrap;
            align-items: end;
            gap: 0.75rem;
            margin-bottom: 1rem;
            padding: 0.75rem;
            background: #f8f9fa;
            border-radius: 8px;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            min-width: 0;
            flex: 0 0 auto;
        }

        .filter-group label {
            font-size: 0.875rem;
            font-weight: 500;
            margin-bottom: 0.25rem;
            white-space: nowrap;
        }

        .filter-group select {
            min-width: 120px;
        }

        .filter-actions {
            margin-left: auto;
            display: flex;
            align-items: flex-end;
        }

        .filter-actions .btn {
            white-space: nowrap;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .table-responsive {
                font-size: 0.95rem;
            }

            .card-header {
                font-size: 1rem;
            }

            .filter-form {
                gap: 0.5rem;
                padding: 0.5rem;
            }

            .filter-group {
                min-width: 100px;
            }

            .filter-group label {
                font-size: 0.75rem;
            }

            .filter-group select {
                min-width: 100px;
                font-size: 0.875rem;
                padding: 0.375rem 0.5rem;
            }

            .filter-actions .btn {
                font-size: 0.875rem;
                padding: 0.375rem 0.75rem;
            }

            .filter-actions .btn i {
                margin-right: 0.25rem;
            }
        }

        @media (max-width: 576px) {
            .filter-form {
                gap: 0.375rem;
                padding: 0.5rem;
            }

            .filter-group {
                min-width: 85px;
            }

            .filter-group select {
                min-width: 85px;
                font-size: 0.8125rem;
                padding: 0.25rem 0.375rem;
            }

            .filter-group label {
                font-size: 0.7rem;
            }

            .filter-actions .btn {
                font-size: 0.8125rem;
                padding: 0.25rem 0.5rem;
            }
        }
    </style>
    <link rel="stylesheet" href="{{ asset('css/responsive-tables.css') }}">
@endsection

@section('content')
    @php
        $currentYear = date('Y');
        $currentMonth = date('n'); // 1-12
        $year = $year ?? request('year', $currentYear);
        $month = $month ?? request('month', $currentMonth);
        $activeTab = $activeTab ?? request('tab', 'request');

        // Controller-provided stats (single-month aggregates)
        $requestStats = $requestStats ?? [];
        $donationStats = $donationStats ?? [];
        $inventoryStats = $inventoryStats ?? [];

        $years = range($currentYear, $currentYear - 5);
        $months = [1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'];
    @endphp

    <div class="row">
        <div class="col-12">

            <ul class="nav nav-tabs mb-3" role="tablist">
                <li class="nav-item">
                    <a class="nav-link {{ $activeTab === 'request' ? 'active bg-primary text-white' : 'text-primary' }}"
                        href="?tab=request&year={{ $year }}&month={{ $month }}">
                        <i class="fas fa-file-medical tab-icon"></i>
                        <span class="tab-text-full">Breastmilk Request Reports</span>
                        <span class="tab-text-short">Requests</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $activeTab === 'donation' ? 'active bg-success text-white' : 'text-success' }}"
                        href="?tab=donation&year={{ $year }}&month={{ $month }}">
                        <i class="fas fa-hand-holding-heart tab-icon"></i>
                        <span class="tab-text-full">Breastmilk Donation Reports</span>
                        <span class="tab-text-short">Donations</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $activeTab === 'inventory' ? 'active bg-info text-white' : 'text-info' }}"
                        href="?tab=inventory&year={{ $year }}&month={{ $month }}">
                        <i class="fas fa-boxes tab-icon"></i>
                        <span class="tab-text-full">Inventory Reports</span>
                        <span class="tab-text-short">Inventory</span>
                    </a>
                </li>
            </ul>

            <div class="tab-content mt-3" id="reportsTabContent">
                <div class="tab-pane fade {{ $activeTab === 'request' ? 'show active' : '' }}" id="request" role="tabpanel"
                    aria-labelledby="request-tab">
                    <form method="GET" class="filter-form" id="requestFilters">
                        <input type="hidden" name="tab" value="request">
                        <div class="filter-group">
                            <label>Year</label>
                            <select name="year" class="form-select" onchange="this.form.submit()">
                                @foreach($years as $y)
                                    <option value="{{ $y }}" {{ $y == $year ? ' selected' : '' }}>{{ $y }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="filter-group">
                            <label>Month</label>
                            <select name="month" class="form-select" onchange="this.form.submit()">
                                @foreach($months as $num => $label)
                                    <option value="{{ $num }}" {{ $num == $month ? ' selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="filter-actions">
                            <a class="btn btn-primary"
                                href="{{ route('admin.reports.preview', ['type' => 'requests', 'year' => $year, 'month' => $month]) }}"
                                target="_blank" rel="noopener">
                                <i class="fas fa-print"></i> Print
                            </a>
                        </div>
                    </form>

                    <div class="card">
                        <div class="card-body p-0">
                            <table class="table table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th>Month</th>
                                        <th>Total Request</th>
                                        <th>Approved</th>
                                        <th>Declined</th>
                                        <th>Total Dispensed (ml)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $selectedRequestRow = null;
                                        if (!empty($requestReportRows)) {
                                            $selectedRequestRow = collect($requestReportRows)->first(function ($r) use ($month) {
                                                return isset($r['month']) && (int) $r['month'] === (int) $month;
                                            });
                                        }
                                    @endphp

                                    @if($selectedRequestRow)
                                        <tr>
                                            <td>{{ $selectedRequestRow['month_label'] ?? $selectedRequestRow['month'] ?? '' }}
                                            </td>
                                            <td>{{ $selectedRequestRow['total'] ?? 0 }}</td>
                                            <td>{{ $selectedRequestRow['approved'] ?? 0 }}</td>
                                            <td>{{ $selectedRequestRow['declined'] ?? 0 }}</td>
                                            <td>{{ $selectedRequestRow['dispensed_volume'] ?? 0 }}</td>
                                        </tr>
                                    @elseif(!empty($requestStats))
                                        <tr>
                                            <td>{{ $requestStats['month'] ?? (\Carbon\Carbon::create()->month($month)->format('F')) }}
                                            </td>
                                            <td>{{ $requestStats['total'] ?? 0 }}</td>
                                            <td>{{ $requestStats['approved'] ?? 0 }}</td>
                                            <td>{{ $requestStats['declined'] ?? 0 }}</td>
                                            <td>{{ $requestStats['dispensed_volume'] ?? 0 }}</td>
                                        </tr>
                                    @else
                                        <tr>
                                            <td colspan="5" class="text-center">No data for selected month/year.</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade {{ $activeTab === 'donation' ? 'show active' : '' }}" id="donation"
                    role="tabpanel" aria-labelledby="donation-tab">
                    <form method="GET" class="filter-form" id="donationFilters">
                        <input type="hidden" name="tab" value="donation">
                        <div class="filter-group">
                            <label>Year</label>
                            <select name="year" class="form-select" onchange="this.form.submit()">
                                @foreach($years as $y)
                                    <option value="{{ $y }}" {{ $y == $year ? ' selected' : '' }}>{{ $y }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="filter-group">
                            <label>Month</label>
                            <select name="month" class="form-select" onchange="this.form.submit()">
                                @foreach($months as $num => $label)
                                    <option value="{{ $num }}" {{ $num == $month ? ' selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="filter-actions">
                            <a class="btn btn-primary"
                                href="{{ route('admin.reports.preview', ['type' => 'donations', 'year' => $year, 'month' => $month]) }}"
                                target="_blank" rel="noopener">
                                <i class="fas fa-print"></i> Print
                            </a>
                        </div>
                    </form>

                    <div class="card">
                        <div class="card-body p-0">
                            <table class="table table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th>Month</th>
                                        <th>Total Walk-in</th>
                                        <th>Total Home Collection</th>
                                        <th>Total Donations</th>
                                        <th>Total Volume</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $selectedDonationRow = null;
                                        if (!empty($donationReportRows)) {
                                            $selectedDonationRow = collect($donationReportRows)->first(function ($r) use ($month) {
                                                return isset($r['month']) && (int) $r['month'] === (int) $month;
                                            });
                                        }
                                    @endphp

                                    @if($selectedDonationRow)
                                        <tr>
                                            <td>{{ $selectedDonationRow['month_label'] ?? $selectedDonationRow['month'] ?? '' }}
                                            </td>
                                            <td>{{ $selectedDonationRow['walk_in'] ?? $selectedDonationRow['walkin'] ?? 0 }}
                                            </td>
                                            <td>{{ $selectedDonationRow['home_collection'] ?? 0 }}</td>
                                            <td>{{ $selectedDonationRow['total'] ?? 0 }}</td>
                                            <td>{{ $selectedDonationRow['total_volume'] ?? 0 }}</td>
                                        </tr>
                                    @elseif(!empty($donationStats))
                                        <tr>
                                            <td>{{ $donationStats['month'] ?? (\Carbon\Carbon::create()->month($month)->format('F')) }}
                                            </td>
                                            <td>{{ $donationStats['walk_in'] ?? $donationStats['walkin'] ?? 0 }}</td>
                                            <td>{{ $donationStats['home_collection'] ?? 0 }}</td>
                                            <td>{{ $donationStats['total'] ?? 0 }}</td>
                                            <td>{{ $donationStats['total_volume'] ?? 0 }}</td>
                                        </tr>
                                    @else
                                        <tr>
                                            <td colspan="5" class="text-center">No data for selected month/year.</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade {{ $activeTab === 'inventory' ? 'show active' : '' }}" id="inventory"
                    role="tabpanel" aria-labelledby="inventory-tab">
                    <form method="GET" class="filter-form" id="inventoryFilters">
                        <input type="hidden" name="tab" value="inventory">
                        <div class="filter-group">
                            <label>Year</label>
                            <select name="year" class="form-select" onchange="this.form.submit()">
                                @foreach($years as $y)
                                    <option value="{{ $y }}" {{ $y == $year ? ' selected' : '' }}>{{ $y }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="filter-group">
                            <label>Month</label>
                            <select name="month" class="form-select" onchange="this.form.submit()">
                                @foreach($months as $num => $label)
                                    <option value="{{ $num }}" {{ $num == $month ? ' selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="filter-actions">
                            <a class="btn btn-primary"
                                href="{{ route('admin.reports.preview', ['type' => 'inventory', 'year' => $year, 'month' => $month]) }}"
                                target="_blank" rel="noopener">
                                <i class="fas fa-print"></i> Print
                            </a>
                        </div>
                    </form>

                    <div class="card">
                        <div class="card-body p-0">
                            <table class="table table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th>Month</th>
                                        <th>Currently Unpasteurized Breastmilk</th>
                                        <th>Pasteurized Breastmilk</th>
                                        <th>Dispensed Volume</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $selectedInventoryRow = null;
                                        if (!empty($inventoryReportRows)) {
                                            $selectedInventoryRow = collect($inventoryReportRows)->first(function ($r) use ($month) {
                                                return isset($r['month']) && (int) $r['month'] === (int) $month;
                                            });
                                        }
                                    @endphp

                                    @if($selectedInventoryRow)
                                        <tr>
                                            <td>{{ $selectedInventoryRow['month_label'] ?? $selectedInventoryRow['month'] ?? '' }}
                                            </td>
                                            <td>{{ $selectedInventoryRow['unpasteurized'] ?? 0 }}</td>
                                            <td>{{ $selectedInventoryRow['pasteurized'] ?? 0 }}</td>
                                            <td>{{ $selectedInventoryRow['dispensed'] ?? 0 }}</td>
                                        </tr>
                                    @elseif(!empty($inventoryStats))
                                        <tr>
                                            <td>{{ $inventoryStats['month'] ?? (\Carbon\Carbon::create()->month($month)->format('F')) }}
                                            </td>
                                            <td>{{ $inventoryStats['unpasteurized'] ?? 0 }}</td>
                                            <td>{{ $inventoryStats['pasteurized'] ?? 0 }}</td>
                                            <td>{{ $inventoryStats['dispensed'] ?? 0 }}</td>
                                        </tr>
                                    @else
                                        <tr>
                                            <td colspan="4" class="text-center">No data for selected month/year.</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection