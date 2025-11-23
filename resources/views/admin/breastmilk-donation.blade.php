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
            }

            /* Clean, leveled header style for the Bag Details editable table (validation modal) */
            #home-bag-details-table {
                border-collapse: separate;
                border-spacing: 0;
                --header-bg: #f6f7f8;
            }

            #home-bag-details-table thead th {
                background: var(--header-bg) !important;
                font-weight: 700;
                color: #222;
                padding: 0.7rem 0.9rem;
                text-align: left;
                vertical-align: middle;
                border-right: 1px solid #e9ecef;
                border-bottom: 1px solid #e9ecef;
                white-space: nowrap;
            }

            #home-bag-details-table thead th:first-child {
                border-top-left-radius: 6px;
            }

            #home-bag-details-table thead th:last-child {
                border-top-right-radius: 6px;
                border-right: none;
            }

            #home-bag-details-table tbody td {
                vertical-align: middle;
                padding: 0.55rem 0.9rem;
            }

            /* small muted placeholder for empty cells to keep layout neat */
            #home-bag-details-table tbody td:empty::after {
                content: "-";
                color: #6c757d;
            }

            else {

                // Fallback: use parsed data-bag-details already available
                if (bagDetails && bagDetails.length > 0) {
                    bagDetails.forEach((bag, index)=> {
                            const bagNum=bag.bag_number || (index + 1);
                            const time=formatTime12(bag.time) || '--';
                            const date=bag.date || '--';
                            const volume=bag.volume || 0;
                            const storageLabel=mapStorage(bag.storage_location || '--');
                            const temp=bag.temperature || '--';
                            const method=bag.collection_method || '--';

                            const row=`\n <tr>\n <td class="text-center fw-bold" >Bag $ {
                                bagNum
                            }

                            </td>\n <td>$ {
                                time
                            }

                            </td>\n <td>$ {
                                date
                            }

                            </td>\n <td>\n <div class="input-group input-group-sm" >\n <input type="number" id="home_bag_volume_${index + 1}" name="bag_volumes[]" class="form-control home-bag-volume-input" step="0.01" min="0.01" value="${volume}" placeholder="ml" required>\n <span class="input-group-text" >ml</span>\n </div>\n </td>\n <td>$ {
                                storageLabel
                            }

                            </td>\n <td class="text-end" >$ {
                                temp
                            }

                            </td>\n <td><small>$ {
                                method
                            }

                            </small></td>\n </tr>`;
                            tbody.append(row);
                            tbody.closest('.table-responsive').show();
                            totalVol +=parseFloat(volume) || 0;
                        });
                    $('#home-total').text(totalVol.toFixed(2) + ' ml');

                    // Live update total when editing volumes
                    $('#home-bag-details-body').off('input.homeVol').on('input.homeVol', '.home-bag-volume-input', function () {
                            let sum=0;

                            $('#home-bag-details-body .home-bag-volume-input').each(function () {
                                    const v=parseFloat($(this).val());
                                    if ( !isNaN(v)) sum +=v;
                                });
                            $('#home-total').text(sum.toFixed(2) + ' ml');
                        });

                    $('#home-form-error').hide().text('');
                }

                else {
                    $('#home-bag-details-body').append('<tr><td colspan="7" class="text-center text-muted">No bag details available</td></tr>');
                    $('#home-total').text(parseFloat(totalVolume || 0).toFixed(2) + ' ml');
                    $('#home-form-error').hide().text('');
                }
            }
            }

            // Update total display
            $('#home-total').text(runningTotal.toFixed(2) + ' ml');

            // Live update total when editing volumes
            $('#home-bag-details-body').off('input.homeVol').on('input.homeVol', '.home-bag-volume-input', function () {
                    let sum=0;

                    $('#home-bag-details-body .home-bag-volume-input').each(function () {
                            const v=parseFloat($(this).val());
                            if ( !isNaN(v)) sum +=v;
                        });
                    $('#home-total').text(sum.toFixed(2) + ' ml');
                });
            }

            // If parsed bagDetails exist from data- attribute, render them immediately for snappy UI
            // Helper to render bag details into the editable table
            function renderBagTables(bags) {
                console.log('renderBagTables called with', bags);
                const tbody=$('#home-bag-details-body');
                tbody.empty();

                if ( !bags || !Array.isArray(bags) || bags.length===0) {
                    return;
                }

                function fmtTime(t) {
                    if (!t) return '--';
                    if (/\b(am|pm)\b/i.test(t)) return t;
                    
                    // Match time patterns like "16:49" or "16:49:30"
                    const m = t.toString().match(/^(\d{1,2}):(\d{2})(?::(\d{2}))?$/);
                    if (!m) return t;
                    
                    let hh = parseInt(m[1], 10);
                    const mm = m[2];
                    const ampm = hh >= 12 ? 'PM' : 'AM';
                    hh = hh % 12;
                    if (hh === 0) hh = 12;
                    return hh + ':' + mm + ' ' + ampm;
                }

                function mapStorageLabel(s) {
                    if ( !s) return '--';
                    const key=String(s).toLowerCase();
                    if (key.indexOf('refrig') !==-1 || key.indexOf('ref') !==-1) return 'Refrigerator';
                    if (key.indexOf('freez') !==-1) return 'Freezer';
                    if (key.indexOf('room') !==-1 || key.indexOf('ambient') !==-1) return 'Room temperature';
                    return String(s).replace(/_/g, ' ').replace(/\b\w/g, c=> c.toUpperCase());
                }

                let total=0;

                bags.forEach((bag, index)=> {
                        const bagNum=bag.bag_number || (index + 1);
                        const time=fmtTime(bag.time) || '--';
                        const date=bag.date || '--';
                        const volume=(typeof bag.volume !=='undefined' && bag.volume !==null) ? bag.volume : '';
                        const storageLabel=mapStorageLabel(bag.storage_location || bag.storage || '--');
                        const temp=bag.temperature || '--';
                        const method=bag.collection_method || bag.method || '--';

                        const row=`<tr>
                            <td class="text-center fw-bold">Bag ${bagNum}</td>
                            <td>${time}</td>
                            <td>${date}</td>
                            <td>
                                <div class="input-group input-group-sm">
                                    <input type="number" id="home_bag_volume_${index + 1}" name="bag_volumes[]" 
                                           class="form-control home-bag-volume-input" step="0.01" min="0.01" 
                                           value="${volume}" placeholder="ml" required>
                                    <span class="input-group-text">ml</span>
                                </div>
                            </td>
                            <td>${storageLabel}</td>
                            <td class="text-end">${temp}</td>
                            <td><small>${method}</small></td>
                        </tr>`;

                        tbody.append(row);
                        total +=parseFloat(volume) || 0;
                    });

                $('#home-total').text(total.toFixed(2) + ' ml');

                // Live update total when editing volumes
                $('#home-bag-details-body').off('input.homeVol').on('input.homeVol', '.home-bag-volume-input', function () {
                        let sum=0;

                        $('#home-bag-details-body .home-bag-volume-input').each(function () {
                                const v=parseFloat($(this).val());
                                if ( !isNaN(v)) sum +=v;
                            });
                        $('#home-total').text(sum.toFixed(2) + ' ml');
                    });
            }

            if (bagDetails && bagDetails.length > 0) {
                renderBagTables(bagDetails);
            }

            // Request donation details from server to populate both original (readonly) and editable tables
            $.ajax({
                url: `/admin/donations/$ {
                    currentDonationId
                }

                `,
                method: 'GET',
                success: function (response) {
                    console.log('AJAX Response:', response);
                    const donationData=(response && response.donation) ? response.donation : null;
                    console.log('Donation Data:', donationData);

                    // Populate donor info - prioritize server data, fallback to button data
                    if (donationData) {
                        $('#validate-home-donor-name').text(donationData.donor_name || donorName || 'N/A');
                        $('#validate-home-donor-address').text(donationData.address || donorAddress || 'Not provided');
                        $('#validate-home-date').text(donationData.donation_date || scheduledDate || 'N/A');
                        $('#validate-home-time').text(donationData.donation_time || formatTime12(scheduledTimeRaw) || 'N/A');
                    }

                    const effectiveBags=donationData?.bag_details && donationData.bag_details.length > 0 ? donationData.bag_details : (bagDetails && bagDetails.length > 0 ? bagDetails : []);

                    console.log('Effective Bags:', effectiveBags);
                    renderBagTables(effectiveBags);
                    $('#home-form-error').hide().text('');
                }

                ,
                error: function (xhr, status, error) {
                    console.error('Failed to fetch donation for validation:', error);
                    // Keep any pre-rendered bagDetails (we already rendered them above)
                    $('#home-form-error').hide().text('');
                }
            });

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

            /* Ensure the donation type badge displays on one line */
            #pending-donations .donation-type-badge {
                display: inline-block;
                white-space: nowrap;
                line-height: 1.05;
                padding: 0.25rem 0.45rem;
                font-size: 0.75rem;
                vertical-align: middle;
            }

            /* Ensure the Type cell can expand vertically and show wrapped text */
            #pending-donations .table td[data-label="Type"] {
                white-space: normal;
                vertical-align: middle;
                overflow: visible;
            }

            /* Specific column width optimization (keep fixed for consistency) */
            #pending-donations .table {
                table-layout: fixed !important;
            }

            /* Keep table headers aligned on a single horizontal line
                                               - prevent wrapping of header labels
                                               - use ellipsis when a header is too long
                                               - ensure consistent vertical alignment and padding */
            #pending-donations .table thead th {
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
                vertical-align: middle;
                padding: 0.5rem 0.6rem;
                font-size: 0.85rem;
                line-height: 1.1;
                text-align: center;
            }

            /* Allocate width percentages for better distribution (sum ~100%) */
            /* 1: Name */
            #pending-donations .table thead th:nth-child(1) {
                width: 14%;
            }

            /* 2: Type */
            #pending-donations .table thead th:nth-child(2) {
                width: 12%;
            }

            /* 3: Contact */
            #pending-donations .table thead th:nth-child(3) {
                width: 12%;
            }

            /* 4: Address (wider to prevent excessive wrapping) */
            #pending-donations .table thead th:nth-child(4) {
                width: 30%;
            }

            /* 5: Date */
            #pending-donations .table thead th:nth-child(5) {
                width: 12%;
            }

            /* 6: Total Volume */
            #pending-donations .table thead th:nth-child(6) {
                width: 8%;
            }

            /* 7: Action (sticky last column) */
            #pending-donations .table thead th:nth-child(7) {
                width: 12%;
            }

            /* Actions column is last now; widths defined above */

            /* Make table horizontally scrollable as fallback */
            #pending-donations .table-container {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            /* Ensure action buttons stay visible */
            #pending-donations .table thead th:nth-child(7),
            #pending-donations .table tbody td:nth-child(7) {
                position: sticky;
                right: 0;
                background-color: white;
                box-shadow: -2px 0 4px rgba(0, 0, 0, 0.05);
                z-index: 2;
                vertical-align: middle;
                min-width: 140px;
            }

            #pending-donations .table thead th:nth-child(7) {
                background-color: #f8f9fa;
            }

            /* Address cell should wrap naturally and break long words */
            #pending-donations .table tbody td[data-label="Address"] {
                white-space: normal !important;
                word-break: break-word;
            }

            /* Fallback sticky for last column at other sizes (helps with 90% zoom) */
            #pending-donations .table thead th:last-child,
            #pending-donations .table tbody td:last-child {
                position: sticky;
                right: 0;
                background: white;
                z-index: 3;
                box-shadow: -2px 0 4px rgba(0, 0, 0, 0.04);
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

            /* ---- Volume column sizing and inputs (Schedule/Validate modals) ---- */
            #schedule-bag-details-table th:nth-child(4),
            #schedule-bag-details-table td:nth-child(4) {
                min-width: 150px !important;
                width: 150px !important;
                white-space: nowrap;
            }

            #home-bag-details-table th:nth-child(4),
            #home-bag-details-table td:nth-child(4) {
                min-width: 160px !important;
                width: 160px !important;
                white-space: nowrap;
            }

            #schedule-bag-details-table .input-group,
            #home-bag-details-table .input-group {
                max-width: 160px;
                width: 100%;
            }

            #schedule-bag-details-table .form-control.schedule-bag-volume,
            #home-bag-details-table .form-control.home-bag-volume-input {
                text-align: right;
            }

            @media (max-width: 576px) {

                #schedule-bag-details-table th:nth-child(4),
                #schedule-bag-details-table td:nth-child(4),
                #home-bag-details-table th:nth-child(4),
                #home-bag-details-table td:nth-child(4) {
                    min-width: 130px !important;
                    width: 130px !important;
                }

                #schedule-bag-details-table .input-group,
                #home-bag-details-table .input-group {
                    max-width: none;
                    /* allow full cell width */
                    width: 100%;
                }

                /* Compact table paddings for mobile inside modals */
                #schedule-bag-details-table.table> :not(caption)>*>*,
                #home-bag-details-table.table> :not(caption)>*>* {
                    padding: 0.35rem 0.4rem !important;
                }

                /* Improve input tap targets but keep compact visuals */
                .modal .form-control.home-bag-volume-input,
                .modal .form-control.schedule-bag-volume {
                    height: 36px;
                    font-size: 16px;
                    /* prevent iOS zoom on focus */
                    line-height: 1.2;
                }

                .modal .input-group-text {
                    padding: 0 8px;
                }

                .modal .input-group {
                    gap: 0;
                }

                .modal .input-group .form-control {
                    padding-right: 6px;
                }

                /* Ensure volume group uses the available cell width */
                #schedule-bag-details-table .input-group,
                #home-bag-details-table .input-group {
                    width: 100%;
                }

                /* Hide trailing "ml" addon on mobile to free space; keep placeholder */
                #schedule-bag-details-table td:nth-child(4) .input-group-text,
                #home-bag-details-table td:nth-child(4) .input-group-text {
                    display: none !important;
                }

                #schedule-bag-details-table .form-control.schedule-bag-volume,
                #home-bag-details-table .form-control.home-bag-volume-input {
                    width: 100%;
                    min-width: 80px;
                    /* ensure at least 3-4 digits visible */
                }

                /* Permanently style the Decline button in the Schedule Pickup modal to
                                                                                                                                                                                                                           match the hovered look of the Cancel (.btn-secondary:hover) button */
                #schedule-decline-btn {
                    background-color: #5c636a !important;
                    /* darkened secondary */
                    border-color: #545b62 !important;
                    color: #fff !important;
                    box-shadow: none !important;
                    transform: none !important;
                }

                /* Keep same appearance on hover/focus to avoid jump */
                #schedule-decline-btn:hover,
                #schedule-decline-btn:focus {
                    background-color: #5c636a !important;
                    border-color: #545b62 !important;
                    color: #fff !important;
                }
            }
        </style>
    @endsection

    <style>
        /* Uniform badge sizing for donation type labels */
        .donation-type-badge {
            display: inline-block;
            min-width: 110px;
            text-align: center;
            padding: 0.25rem 0.6rem;
            font-size: 0.85rem;
            font-weight: 700;
            border-radius: 0.375rem;
        }
        /* Assist option badge */
        .assist-option-badge {
            display:inline-block;
            padding:0.25rem 0.5rem;
            font-size:0.65rem;
            line-height:1.1;
            font-weight:600;
            border-radius:0.35rem;
            background:#6c757d;
            color:#fff;
            white-space:nowrap;
        }
        .assist-option-badge.option-direct { background:#0d6efd; }
        .assist-option-badge.option-existing { background:#198754; }
        .assist-option-badge.option-letting { background:#6610f2; }

        /* Assist button styling to match screenshot */
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

        /* Search + Assist layout refinement */
        .search-assist-row { width:100%; gap:0; }
        .search-assist-row .donation-search-wrap { flex:1; min-width:260px; position: relative; }
        .search-assist-row .assist-btn { flex-shrink:0; height:32px; line-height:1; }
        .search-assist-row .assist-btn i { font-size:0.9rem; }
        .search-assist-row .assist-btn span { font-size:0.78rem; font-weight:600; }
        .search-assist-row .donation-search-input { height:32px; }

        /* New donation searchbox (no input-group) */
        .donation-search-wrap { width:100%; }
        .donation-search-input {
            width: 100%;
            border: 1px solid #ced4da;
            border-radius: 8px;
            background: #fff;
            padding-left: 2.9rem !important; /* ensure space for icon, override any global padding */
            padding-right: 2rem; /* clear button space */
            transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out;
        }
        .donation-search-input::placeholder { color:#9aa0a6; opacity:.95; }
        .donation-search-input:focus { border-color:#0d6efd; box-shadow:0 0 0 3px rgba(13,110,253,.12); }
        .donation-search-icon {
            position: absolute; left: 14px; top: 50%; transform: translateY(-50%);
            color:#6c757d; pointer-events:none; font-size:1rem;
            width: 18px; height: 18px; display: inline-flex; align-items: center; justify-content: center;
        }
        .donation-search-clear { position:absolute; right:10px; top:50%; transform:translateY(-50%);
            display:none; border:none; background:transparent; color:#6c757d; padding:0; line-height:1; }
        .donation-search-clear:hover { color:#495057; }
        .donation-search-clear:focus { outline:none; box-shadow:0 0 0 3px rgba(13,110,253,.2); border-radius:50%; }
        @media (max-width: 576px) {
            .search-assist-row { flex-direction:column; }
            .search-assist-row .assist-btn { width:100%; margin-left:0 !important; margin-top:6px; }
        }
    </style>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="container-fluid page-container-standard">
        <!-- Assist button moved beside searchbar (right aligned) -->
        <!-- Navigation Tabs with status query for persistence -->
        @php
            $tabStatus = request('status', 'pending');
        @endphp
        <ul class="nav nav-tabs nav-tabs-standard mb-3" role="tablist">
            <li class="nav-item">
                <a class="nav-link {{ $tabStatus == 'pending' ? 'active bg-warning text-dark' : 'text-warning' }}"
                    href="?status=pending">
                    Pending Donations <span class="badge bg-warning text-dark">{{ $pendingDonations->total() }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $tabStatus == 'scheduled' ? 'active bg-primary text-white' : 'text-primary' }}"
                    href="?status=scheduled">
                    Scheduled <span class="badge bg-primary">{{ $scheduledHomeCollection->total() }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $tabStatus == 'success_walk_in' ? 'active bg-success text-white' : 'text-success' }}"
                    href="?status=success_walk_in">
                    Walk-in Success <span class="badge bg-success">{{ $successWalkIn->total() }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $tabStatus == 'success_home_collection' ? 'active bg-success text-white' : 'text-success' }}"
                    href="?status=success_home_collection">
                    Home Collection Success <span class="badge bg-success">{{ $successHomeCollection->total() }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $tabStatus == 'declined' ? 'active bg-danger text-white' : 'text-danger' }}"
                    href="?status=declined">
                    Declined <span class="badge bg-danger">{{ $declinedCount }}</span>
                </a>
            </li>
        </ul>

        {{-- Search Input Below Tabs --}}
        {{-- Searchbar with Assist button on the right --}}
        <div class="mb-3">
            <form method="GET" action="{{ route('admin.donation') }}" class="d-flex align-items-stretch search-assist-row">
                <input type="hidden" name="status" value="{{ $tabStatus }}">
                @if(request('donation_type'))
                    <input type="hidden" name="donation_type" value="{{ request('donation_type') }}">
                @endif
                <div class="donation-search-wrap flex-grow-1" role="search" aria-label="Search donations by name or contact number">
                    <i class="bi bi-search donation-search-icon" aria-hidden="true"></i>
                    <input type="text" class="form-control form-control-sm donation-search-input" id="searchInput" name="q"
                        placeholder="Search by name, contact number..." aria-describedby="searchResults" value="{{ request('q') }}" autocomplete="off">
                    <button type="button" class="donation-search-clear" id="clearSearch" aria-label="Clear search" style="display:none;">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
                <button type="button" class="btn btn-sm assist-btn ms-2" data-bs-toggle="modal" data-bs-target="#assistWalkInDonationModal">
                    <i class="fas fa-user-plus"></i>
                    <span>Assist Walk-in Donation</span>
                </button>
            </form>
            <small class="text-muted d-block mt-1"><span id="searchResults"></span></small>
        </div>

        <div class="tab-content" id="donationTabContent" aria-live="polite">
            <!-- Pending Donations Tab -->
            <div class="tab-pane fade {{ $tabStatus == 'pending' ? 'show active' : '' }}" id="pending-donations"
                role="tabpanel">
                <div class="card card-standard">
                    <div
                        class="card-header bg-warning text-dark py-3 d-flex flex-row justify-content-between align-items-center gap-2">
                        <h5 class="mb-0">Pending Donations</h5>
                        <div class="ms-auto d-flex align-items-center gap-2">
                            <select id="donation-type-filter" class="form-select form-select-sm"
                                style="width: auto; min-width: 150px;">
                                <option value="all" {{ request('donation_type', 'all') == 'all' ? 'selected' : '' }}>All
                                    Donations
                                </option>
                                <option value="walk_in" {{ request('donation_type') == 'walk_in' ? 'selected' : '' }}>Walk-in
                                    Only
                                </option>
                                <option value="home_collection" {{ request('donation_type') == 'home_collection' ? 'selected' : '' }}>Home Collection Only</option>
                            </select>
                        </div>
                    </div>
                    <div class="card-body">
                        <style>
                            /* Mobile card improvements for Pending Donations */
                            @media (max-width: 767.98px) {
                                #pending-donations .donation-card {
                                    background: #fff;
                                    border: 1px solid #f1f3f5;
                                    border-radius: 10px;
                                    padding: 10px 12px;
                                    margin-bottom: 12px;
                                    box-shadow: 0 1px 2px rgba(16, 24, 40, 0.04);
                                }

                                #pending-donations .card-header-row {
                                    display: flex;
                                    align-items: center;
                                    justify-content: space-between;
                                    margin-bottom: 6px;
                                }

                                #pending-donations .card-row {
                                    display: grid;
                                    grid-template-columns: 96px 1fr;
                                    gap: 6px;
                                    padding: 6px 0;
                                    border-bottom: 1px dashed #eef2f7;
                                }

                                #pending-donations .card-row:last-of-type {
                                    border-bottom: none;
                                }

                                #pending-donations .card-label {
                                    color: #6c757d;
                                    font-size: 0.86rem;
                                }

                                #pending-donations .card-value {
                                    font-size: 0.95rem;
                                    word-break: break-word;
                                }

                                #pending-donations .card-actions {
                                    margin-top: 8px;
                                }

                                #pending-donations .card-actions .btn {
                                    width: 100%;
                                }

                                #pending-donations .donation-type-badge {
                                    font-size: 0.7rem;
                                    padding: 0.2rem 0.45rem;
                                }

                                #pending-donations .card-row .btn.view-location {
                                    padding: 0.3rem 0.45rem;
                                }
                            }
                        </style>
                        @if($pendingDonations->count() > 0)
                            <div class="table-responsive d-none d-md-block">
                                <table class="table table-hover" style="min-width: 900px; width:100%;">
                                    <thead>
                                        <tr>
                                            <th class="text-center">Name</th>
                                            <th class="text-center">Type</th>
                                            <th class="text-center">Contact</th>
                                            <th class="text-center">Address</th>
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
                                                    <strong>{{ trim(data_get($donation, 'user.first_name', '') . ' ' . data_get($donation, 'user.last_name', '')) }}</strong>
                                                </td>
                                                <td data-label="Type" class="text-center">
                                                    @if($donation->donation_method === 'walk_in')
                                                        <span class="badge donation-type-badge bg-info">Walk-in</span>
                                                    @else
                                                        <span class="badge donation-type-badge bg-primary">Home Collection</span>
                                                    @endif
                                                </td>
                                                <td data-label="Contact" class="text-center">
                                                    {{ data_get($donation, 'user.contact_number') ?: (data_get($donation, 'user.phone') ?: '-') }}
                                                </td>
                                                <td data-label="Address" class="text-center">
                                                    <small>
                                                        {{ data_get($donation, 'user.address', 'Not provided') }}
                                                    </small>
                                                </td>
                                                <td data-label="Date" class="text-center">
                                                    <small>
                                                        @if($donation->donation_method === 'walk_in')
                                                            {{ $donation->donation_date ? $donation->donation_date->format('M d, Y') : 'N/A' }}
                                                        @else
                                                            {{ $donation->created_at->format('M d, Y') }}<br>{{ $donation->created_at->format('g:i A') }}
                                                        @endif
                                                    </small>
                                                </td>
                                                <td data-label="Total Volume" class="text-center">
                                                    <strong>{{ $donation->formatted_total_volume ?? '-' }}ml</strong>
                                                </td>
                                                <td data-label="Action" class="text-center">
                                                    <div class="table-actions d-inline-flex align-items-center gap-2 flex-nowrap"
                                                        style="display:inline-flex;flex-wrap:nowrap;align-items:center;gap:0.5rem;">
                                                        @if($donation->donation_method === 'walk_in')
                                                            <button type="button" class="btn btn-success btn-sm px-2 validate-walk-in"
                                                                title="Validate Walk-in"
                                                                data-id="{{ $donation->breastmilk_donation_id }}" data-bs-toggle="modal"
                                                                data-bs-target="#validateWalkInModal"
                                                                data-donor="{{ trim(data_get($donation, 'user.first_name', '') . ' ' . data_get($donation, 'user.last_name', '')) }}">
                                                                <i class="fas fa-check"></i>
                                                                <span class="d-none d-md-inline"> Validate</span>
                                                            </button>
                                                        @else
                                                            @php
                                                                // Prepare coordinates for modal use
                                                                $lat = $donation->latitude ?? optional($donation->user)->latitude ?? null;
                                                                $lng = $donation->longitude ?? optional($donation->user)->longitude ?? null;
                                                            @endphp
                                                            <button class="btn btn-primary btn-sm px-2 schedule-pickup"
                                                                title="Schedule Pickup"
                                                                data-id="{{ $donation->breastmilk_donation_id }}"
                                                                data-donor="{{ trim(data_get($donation, 'user.first_name', '') . ' ' . data_get($donation, 'user.last_name', '')) }}"
                                                                data-address="{{ data_get($donation, 'user.address', 'Not provided') }}"
                                                                data-first-expression="{{ $donation->first_expression_date ? $donation->first_expression_date->format('M d, Y') : '' }}"
                                                                data-last-expression="{{ $donation->last_expression_date ? $donation->last_expression_date->format('M d, Y') : '' }}"
                                                                data-bag-details='@json($donation->bag_details, JSON_HEX_APOS | JSON_HEX_QUOT)'
                                                                data-bags="{{ $donation->number_of_bags }}"
                                                                data-total="{{ $donation->total_volume }}" data-latitude="{{ $lat }}"
                                                                data-longitude="{{ $lng }}">
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
                            @foreach($pendingOrdered as $donation)
                                <div class="donation-card d-block d-md-none">
                                    <div class="card-header-row">
                                        <div>
                                            <strong
                                                style="font-size: 1rem;">{{ trim(data_get($donation, 'user.first_name', '') . ' ' . data_get($donation, 'user.last_name', '')) }}</strong>
                                        </div>
                                        <div>
                                            @if($donation->donation_method === 'walk_in')
                                                <span class="badge donation-type-badge bg-info">Walk-in</span>
                                            @else
                                                <span class="badge donation-type-badge bg-primary">Home Collection</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="card-row">
                                        <span class="card-label">Contact:</span>
                                        <span class="card-value">
                                            @php
                                                $contactRaw = data_get($donation, 'user.contact_number') ?: (data_get($donation, 'user.phone') ?: '');
                                                $telHref = $contactRaw ? preg_replace('/[^0-9\+]/', '', $contactRaw) : '';
                                            @endphp
                                            @if($telHref)
                                                <a href="tel:{{ $telHref }}" class="text-decoration-none">{{ $contactRaw }}</a>
                                            @else
                                                -
                                            @endif
                                        </span>
                                    </div>

                                    <div class="card-row">
                                        <span class="card-label">Address:</span>
                                        <span class="card-value">{{ data_get($donation, 'user.address', 'Not provided') }}</span>
                                    </div>
                                    @php
                                        $latCard = $donation->latitude ?? optional($donation->user)->latitude ?? null;
                                        $lngCard = $donation->longitude ?? optional($donation->user)->longitude ?? null;
                                    @endphp
                                    @if(!is_null($latCard) && $latCard !== '' && !is_null($lngCard) && $lngCard !== '')
                                        <div class="card-row">
                                            <span class="card-label">Location:</span>
                                            <span class="card-value">
                                                <button class="btn btn-info btn-sm view-location"
                                                    data-donor-name="{{ trim(data_get($donation, 'user.first_name', '') . ' ' . data_get($donation, 'user.last_name', '')) }}"
                                                    data-donor-address="{{ data_get($donation, 'user.address', '') }}"
                                                    data-latitude="{{ $latCard }}" data-longitude="{{ $lngCard }}">
                                                    <i class="fas fa-map-marked-alt"></i>
                                                </button>
                                            </span>
                                        </div>
                                    @endif

                                    <div class="card-row">
                                        <span class="card-label">Date:</span>
                                        <span class="card-value">
                                            @if($donation->donation_method === 'walk_in')
                                                {{ $donation->donation_date ? $donation->donation_date->format('M d, Y') : 'N/A' }}
                                            @else
                                                {{ $donation->created_at->format('M d, Y') }} •
                                                {{ $donation->created_at->format('g:i A') }}
                                            @endif
                                        </span>
                                    </div>

                                    <div class="card-row">
                                        <span class="card-label">Total Volume:</span>
                                        <span
                                            class="card-value"><strong>{{ $donation->formatted_total_volume ?? '-' }}ml</strong></span>
                                    </div>

                                    <div class="card-actions">
                                        @if($donation->donation_method === 'walk_in')
                                            <button type="button" class="btn btn-success w-100 validate-walk-in"
                                                data-id="{{ $donation->breastmilk_donation_id }}" data-bs-toggle="modal"
                                                data-bs-target="#validateWalkInModal"
                                                data-donor="{{ trim(data_get($donation, 'user.first_name', '') . ' ' . data_get($donation, 'user.last_name', '')) }}">
                                                <i class="fas fa-check"></i> Validate Walk-in
                                            </button>
                                        @else
                                            <button class="btn btn-primary w-100 schedule-pickup"
                                                data-id="{{ $donation->breastmilk_donation_id }}"
                                                data-donor="{{ trim(data_get($donation, 'user.first_name', '') . ' ' . data_get($donation, 'user.last_name', '')) }}"
                                                data-address="{{ data_get($donation, 'user.address', 'Not provided') }}"
                                                data-bag-details='@json($donation->bag_details, JSON_HEX_APOS | JSON_HEX_QUOT)'
                                                data-bags="{{ $donation->number_of_bags }}" data-total="{{ $donation->total_volume }}">
                                                <i class="fas fa-calendar-alt"></i> Schedule Pickup
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            @endforeach

                            {{-- Pagination --}}
                            @if($pendingDonations instanceof \Illuminate\Pagination\LengthAwarePaginator)
                                <div class="d-flex justify-content-center mt-4">
                                    {{ $pendingDonations->links() }}
                                </div>
                            @endif
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
            <div class="tab-pane fade {{ $tabStatus == 'scheduled' ? 'show active' : '' }}" id="scheduled-home"
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
                                                    <strong>{{ trim(data_get($donation, 'user.first_name', '') . ' ' . data_get($donation, 'user.last_name', '')) }}</strong>
                                                </td>
                                                <td data-label="Contact" class="text-center">
                                                    {{ data_get($donation, 'user.contact_number') ?: (data_get($donation, 'user.phone') ?: '-') }}
                                                </td>
                                                <td data-label="Address" class="text-center">
                                                    <small>{{ data_get($donation, 'user.address', 'Not provided') }}</small>
                                                </td>
                                                <td data-label="Location" class="text-center">
                                                    @php
                                                        // Prefer donation-specific coordinates if available; fallback to user's profile
                                                        $latSched = $donation->latitude ?? optional($donation->user)->latitude ?? null;
                                                        $lngSched = $donation->longitude ?? optional($donation->user)->longitude ?? null;
                                                    @endphp
                                                    @if(!is_null($latSched) && $latSched !== '' && !is_null($lngSched) && $lngSched !== '')
                                                        <button class="btn btn-info btn-sm view-location" title="View on Map"
                                                            data-donor-name="{{ trim(data_get($donation, 'user.first_name', '') . ' ' . data_get($donation, 'user.last_name', '')) }}"
                                                            data-donor-address="{{ data_get($donation, 'user.address', '') }}"
                                                            data-latitude="{{ $latSched }}" data-longitude="{{ $lngSched }}">
                                                            <i class="fas fa-map-marked-alt"></i>
                                                        </button>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td data-label="Date" class="text-center">
                                                    <small>{{ $donation->scheduled_pickup_date ? $donation->scheduled_pickup_date->format('M d, Y') : 'N/A' }}</small>
                                                </td>
                                                <td data-label="Time" class="text-center">
                                                    <small>
                                                        {{ isset($donation->scheduled_pickup_time) && $donation->scheduled_pickup_time ? \Carbon\Carbon::parse($donation->scheduled_pickup_time)->format('g:i A') : 'N/A' }}
                                                    </small>
                                                </td>
                                                <td data-label="Total volume" class="text-center">
                                                    <strong>{{ $donation->formatted_total_volume }}ml</strong>
                                                </td>
                                                <td data-label="Action" class="text-center">
                                                    <div class="table-actions d-inline-flex align-items-center gap-2 flex-nowrap"
                                                        style="display:inline-flex;flex-wrap:nowrap;align-items:center;gap:0.5rem;">
                                                        <button type="button"
                                                            class="btn btn-success btn-sm px-2 validate-home-collection"
                                                            title="Validate" data-id="{{ $donation->breastmilk_donation_id }}"
                                                            data-bs-toggle="modal" data-bs-target="#validateHomeCollectionModal"
                                                            data-donor="{{ trim(data_get($donation, 'user.first_name', '') . ' ' . data_get($donation, 'user.last_name', '')) }}"
                                                            data-address="{{ data_get($donation, 'user.address', 'Not provided') }}"
                                                            data-date="{{ $donation->scheduled_pickup_date ? $donation->scheduled_pickup_date->format('M d, Y') : '' }}"
                                                            data-time="{{ $donation->scheduled_pickup_time ?? '' }}"
                                                            data-bags="{{ $donation->number_of_bags }}"
                                                            data-bag-details='@json($donation->bag_details, JSON_HEX_APOS | JSON_HEX_QUOT)'
                                                            data-total="{{ $donation->formatted_total_volume }}">
                                                            <i class="fas fa-check"></i>
                                                            <span class="d-none d-md-inline"> Validate</span>
                                                        </button>

                                                        {{-- Reschedule button for scheduled pickups --}}
                                                        <button class="btn btn-outline-primary btn-sm px-2 reschedule-pickup"
                                                            title="Reschedule Pickup"
                                                            data-id="{{ $donation->breastmilk_donation_id }}"
                                                            data-donor="{{ trim(data_get($donation, 'user.first_name', '') . ' ' . data_get($donation, 'user.last_name', '')) }}"
                                                            data-address="{{ data_get($donation, 'user.address', 'Not provided') }}"
                                                            data-date-iso="{{ $donation->scheduled_pickup_date ? $donation->scheduled_pickup_date->format('Y-m-d') : '' }}"
                                                            data-time="{{ $donation->scheduled_pickup_time ?? '' }}"
                                                            data-bags="{{ $donation->number_of_bags }}"
                                                            data-bag-details='@json($donation->bag_details, JSON_HEX_APOS | JSON_HEX_QUOT)'
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

                            {{-- Pagination --}}
                            @if($scheduledHomeCollection instanceof \Illuminate\Pagination\LengthAwarePaginator)
                                <div class="d-flex justify-content-center mt-4">
                                    {{ $scheduledHomeCollection->links() }}
                                </div>
                            @endif
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
            <div class="tab-pane fade {{ $tabStatus == 'success_walk_in' ? 'show active' : '' }}" id="success-walk-in"
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
                                            <th class="text-center">Assist Option</th>
                                            <th class="text-center">Contact</th>
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
                                                    {{ trim(data_get($donation, 'user.first_name', '') . ' ' . data_get($donation, 'user.last_name', '')) }}
                                                </td>
                                                <td data-label="Assist Option" class="text-center">
                                                    @php
                                                        $assistMap = [
                                                            'no_account_direct_record' => 'Direct Record',
                                                            'record_to_existing_user' => 'Existing User',
                                                            'milk_letting_activity' => 'Milk Letting Activity',
                                                        ];
                                                        $assistKey = $donation->assist_option ?? null;
                                                        $assistLabel = $assistKey ? ($assistMap[$assistKey] ?? $assistKey) : null;
                                                        $assistClass = match($assistKey) {
                                                            'no_account_direct_record' => 'option-direct',
                                                            'record_to_existing_user' => 'option-existing',
                                                            'milk_letting_activity' => 'option-letting',
                                                            default => ''
                                                        };
                                                    @endphp
                                                    @if($assistLabel)
                                                        <span class="assist-option-badge {{ $assistClass }}" title="Assist Option">{{ $assistLabel }}</span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td data-label="Contact" class="text-center">
                                                    {{ data_get($donation, 'user.contact_number') ?: (data_get($donation, 'user.phone') ?: '-') }}
                                                </td>
                                                <td data-label="Address" class="text-center">
                                                    <small>{{ data_get($donation, 'user.address', 'Not provided') }}</small>
                                                </td>
                                                <td data-label="Total" class="text-center">
                                                    {{ $donation->formatted_total_volume ?? 'N/A' }}ml
                                                </td>
                                                <td data-label="Date" class="text-center">
                                                    <small>{{ $donation->updated_at ? $donation->updated_at->format('M d, Y') : ($donation->donation_date ? $donation->donation_date->format('M d, Y') : 'N/A') }}</small>
                                                </td>
                                                <td data-label="Time" class="text-center">
                                                    <small>
                                                        {{ isset($donation->donation_time) && $donation->donation_time ? \Carbon\Carbon::parse($donation->donation_time)->format('g:i A') : ($donation->updated_at ? $donation->updated_at->format('g:i A') : 'N/A') }}
                                                    </small>
                                                </td>
                                                <td data-label="Action" class="text-center">
                                                    <div class="table-actions d-inline-flex align-items-center gap-2 flex-nowrap"
                                                        style="display:inline-flex;flex-wrap:nowrap;align-items:center;gap:0.5rem;">
                                                        <button class="btn btn-sm btn-primary me-1 view-donation"
                                                            data-id="{{ $donation->breastmilk_donation_id }}"
                                                            data-donor-name="{{ trim(data_get($donation, 'user.first_name', '') . ' ' . data_get($donation, 'user.last_name', '')) }}"
                                                            data-donor-contact="{{ data_get($donation, 'user.contact_number') ?: (data_get($donation, 'user.phone') ?: '') }}"
                                                            data-donor-address="{{ data_get($donation, 'user.address', 'Not provided') }}"
                                                            data-donation-method="{{ $donation->donation_method ?? 'walk_in' }}"
                                                            data-bags="{{ $donation->number_of_bags ?? (is_array($donation->bag_details ?? null) ? count($donation->bag_details) : '') }}"
                                                            data-total="{{ $donation->total_volume ?? $donation->formatted_total_volume ?? '' }}"
                                                            data-bag-details='@json($donation->bag_details ?? [], JSON_HEX_APOS | JSON_HEX_QUOT)'
                                                            title="View donation">
                                                            <i class="fas fa-eye"></i>
                                                            <span class="d-none d-md-inline"> View</span>
                                                        </button>
                                                        
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            {{-- Pagination --}}
                            @if($successWalkIn instanceof \Illuminate\Pagination\LengthAwarePaginator)
                                <div class="d-flex justify-content-center mt-4">
                                    {{ $successWalkIn->links() }}
                                </div>
                            @endif
                        @else
                            <div class="text-center text-muted py-4">
                                <p>No completed walk-in donations yet</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Success Home Collection Tab -->
            <div class="tab-pane fade {{ $tabStatus == 'success_home_collection' ? 'show active' : '' }}" id="success-home"
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
                                            <th class="text-center">Contact</th>
                                            <th class="text-center">Address</th>
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
                                                    <strong>{{ trim(data_get($donation, 'user.first_name', '') . ' ' . data_get($donation, 'user.last_name', '')) }}</strong>
                                                </td>
                                                <td data-label="Contact" class="text-center">
                                                    {{ data_get($donation, 'user.contact_number') ?: (data_get($donation, 'user.phone') ?: '-') }}
                                                </td>
                                                <td data-label="Address" class="text-center">
                                                    <small>{{ data_get($donation, 'user.address', 'Not provided') }}</small>
                                                </td>
                                                {{-- Location button removed from table (available in View modal) --}}
                                                <td data-label="Total volume" class="text-center">
                                                    <strong>{{ $donation->formatted_total_volume }}ml</strong>
                                                </td>
                                                <td data-label="Date" class="text-center">
                                                    <small>{{ $donation->scheduled_pickup_date ? $donation->scheduled_pickup_date->format('M d, Y') : 'N/A' }}</small>
                                                </td>
                                                <td data-label="Time" class="text-center">
                                                    <small>
                                                        {{ isset($donation->scheduled_pickup_time) && $donation->scheduled_pickup_time ? \Carbon\Carbon::parse($donation->scheduled_pickup_time)->format('g:i A') : 'N/A' }}
                                                    </small>
                                                </td>
                                                <td data-label="Action" class="text-center">
                                                    <div class="table-actions d-inline-flex align-items-center gap-2 flex-nowrap"
                                                        style="display:inline-flex;flex-wrap:nowrap;align-items:center;gap:0.5rem;">
                                                        <button class="btn btn-sm btn-primary me-1 view-donation"
                                                            data-id="{{ $donation->breastmilk_donation_id }}"
                                                            data-donor-name="{{ trim(data_get($donation, 'user.first_name', '') . ' ' . data_get($donation, 'user.last_name', '')) }}"
                                                            data-donor-contact="{{ data_get($donation, 'user.contact_number') ?: (data_get($donation, 'user.phone') ?: '') }}"
                                                            data-donor-address="{{ data_get($donation, 'user.address', 'Not provided') }}"
                                                            data-latitude="{{ $donation->latitude ?? (optional($donation->user)->latitude ?? '') }}"
                                                            data-longitude="{{ $donation->longitude ?? (optional($donation->user)->longitude ?? '') }}"
                                                            data-donation-method="{{ $donation->donation_method ?? 'home_collection' }}"
                                                            data-bags="{{ $donation->number_of_bags ?? (is_array($donation->bag_details ?? null) ? count($donation->bag_details) : '') }}"
                                                            data-total="{{ $donation->total_volume ?? $donation->formatted_total_volume ?? '' }}"
                                                            data-bag-details='@json($donation->bag_details ?? [], JSON_HEX_APOS | JSON_HEX_QUOT)'
                                                            title="View donation">
                                                            <i class="fas fa-eye"></i>
                                                            <span class="d-none d-md-inline"> View</span>
                                                        </button>
                                                        
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            {{-- Pagination --}}
                            @if($successHomeCollection instanceof \Illuminate\Pagination\LengthAwarePaginator)
                                <div class="d-flex justify-content-center mt-4">
                                    {{ $successHomeCollection->links() }}
                                </div>
                            @endif
                        @else
                            <div class="text-center text-muted py-4">
                                
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Declined Tab -->
            <div class="tab-pane fade {{ $tabStatus == 'declined' ? 'show active' : '' }}" id="declined-donations" role="tabpanel">
                <div class="card card-standard">
                    <div class="card-header text-white" style="background: linear-gradient(90deg,#ff7eb6,#ff65a3);">
                        <h5 class="mb-0">Declined Donations</h5>
                    </div>
                    @php
                        $declinedOrdered = $declinedDonations instanceof \Illuminate\Pagination\LengthAwarePaginator
                            ? $declinedDonations->getCollection()->sortByDesc('declined_at')
                            : collect($declinedDonations)->sortByDesc('declined_at');
                    @endphp
                    <div class="card-body">
                        @if($declinedOrdered->count())
                            <div class="table-container table-wide">
                                <table class="table table-striped table-hover align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-center">Name</th>
                                            <th class="text-center">Contact</th>
                                            <th class="text-center">Method</th>
                                            <th class="text-center">Bags</th>
                                            <th class="text-center">Declined At</th>
                                            <th class="text-center">Reason</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($declinedOrdered as $donation)
                                            <tr>
                                                <td class="text-center">
                                                    <strong>{{ trim(data_get($donation,'user.first_name','').' '.data_get($donation,'user.last_name','')) }}</strong>
                                                </td>
                                                <td class="text-center">
                                                    <small>{{ data_get($donation,'user.contact_number','N/A') }}</small>
                                                </td>
                                                <td class="text-center">
                                                    @if($donation->donation_method === 'walk_in')
                                                        <span class="badge donation-type-badge bg-info">Walk-in</span>
                                                    @else
                                                        <span class="badge donation-type-badge bg-primary">Home Collection</span>
                                                    @endif
                                                </td>
                                                <td class="text-center"><strong>{{ $donation->number_of_bags ?? '-' }}</strong></td>
                                                <td class="text-center"><small>{{ $donation->declined_at ? $donation->declined_at->format('M d, Y g:i A') : 'N/A' }}</small></td>
                                                <td class="text-center"><small>{{ $donation->decline_reason ?? 'No reason provided' }}</small></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            {{-- Pagination --}}
                            @if($declinedDonations instanceof \Illuminate\Pagination\LengthAwarePaginator)
                                <div class="d-flex justify-content-center mt-4">
                                    {{ $declinedDonations->links() }}
                                </div>
                            @endif
                        @else
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-3x mb-3"></i>
                                <p class="mb-0">No declined donations</p>
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
                            <div class="d-flex flex-wrap gap-3 align-items-center mb-3">
                                <label class="form-label fw-bold mb-0 me-1">Donor:</label>
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
                            <button type="button" class="btn btn-danger" id="walkin-decline-btn"
                                onclick="declineDonation(currentDonationId)">
                                <i class="fas fa-times me-1"></i> Decline
                            </button>
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

        <!-- Schedule Pickup Modal (Tabbed) -->
        <div class="modal fade" id="schedulePickupModal" tabindex="-1" aria-labelledby="schedulePickupModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="schedulePickupModalLabel">Schedule Home Collection Pickup</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="schedulePickupForm" method="POST">
                        @csrf
                        <div class="modal-body">
                            <ul class="nav nav-tabs" id="schedulePickupTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="pickup-donor-tab" data-bs-toggle="tab" data-bs-target="#pickup-donor" type="button" role="tab" aria-controls="pickup-donor" aria-selected="true"><i class="fas fa-user me-1"></i> Donor Info</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="pickup-bags-tab" data-bs-toggle="tab" data-bs-target="#pickup-bags" type="button" role="tab" aria-controls="pickup-bags" aria-selected="false"><i class="fas fa-box-open me-1"></i> Bag Details</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="pickup-screening-tab" data-bs-toggle="tab" data-bs-target="#pickup-screening" type="button" role="tab" aria-controls="pickup-screening" aria-selected="false"><i class="fas fa-clipboard-list me-1"></i> Lifestyle Checklist</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="pickup-schedule-tab" data-bs-toggle="tab" data-bs-target="#pickup-schedule" type="button" role="tab" aria-controls="pickup-schedule" aria-selected="false"><i class="fas fa-calendar-alt me-1"></i> Schedule</button>
                                </li>
                            </ul>
                            <div class="tab-content pt-3" id="schedulePickupTabContent">
                                <!-- Donor Info Tab -->
                                <div class="tab-pane fade show active" id="pickup-donor" role="tabpanel" aria-labelledby="pickup-donor-tab">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="p-3 border rounded bg-light">
                                                <h6 class="mb-2"><i class="fas fa-user me-1"></i> Donor</h6>
                                                <p class="mb-1"><strong>Name:</strong> <span id="schedule-donor-name">&nbsp;</span></p>
                                                <p class="mb-1"><strong>Address:</strong> <span id="schedule-donor-address">&nbsp;</span></p>
                                                <p class="mb-1"><strong>Location:</strong> <span id="schedule-donor-location">-</span></p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="p-3 border rounded bg-light">
                                                <h6 class="mb-2"><i class="fas fa-clock me-1"></i> Expression Dates</h6>
                                                <p class="mb-1"><strong>First Expression:</strong> <span id="schedule-first-expression">&nbsp;</span></p>
                                                <p class="mb-0"><strong>Last Expression:</strong> <span id="schedule-last-expression">&nbsp;</span></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Bag Details Tab -->
                                <div class="tab-pane fade" id="pickup-bags" role="tabpanel" aria-labelledby="pickup-bags-tab">
                                    <div class="mb-2 d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0">Bag Details</h6>
                                        <span class="badge bg-info"><strong>Total Volume:</strong> <span id="schedule-total-volume">0</span> ml</span>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-sm" id="schedule-bag-details-table">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Bag #</th>
                                                    <th>Time</th>
                                                    <th>Date</th>
                                                    <th style="width:150px;">Volume (ml)</th>
                                                    <th>Storage</th>
                                                    <th>Temp (°C)</th>
                                                    <th>Collection Method</th>
                                                </tr>
                                            </thead>
                                            <tbody id="schedule-bag-details-body"></tbody>
                                        </table>
                                    </div>
                                </div>
                                <!-- Lifestyle Checklist Tab -->
                                <div class="tab-pane fade" id="pickup-screening" role="tabpanel" aria-labelledby="pickup-screening-tab">
                                    <div id="schedule-screening-loading" class="text-center text-muted py-3" style="display:none;">
                                        <i class="fas fa-spinner fa-spin me-2"></i> Loading lifestyle checklist...
                                    </div>
                                    <div id="schedule-screening-content" style="max-height:380px; overflow:auto;"></div>
                                </div>
                                <!-- Schedule Form Tab -->
                                <div class="tab-pane fade" id="pickup-schedule" role="tabpanel" aria-labelledby="pickup-schedule-tab">
                                    <h6 class="mb-3">Schedule Pickup</h6>
                                    <div class="mb-3">
                                        <label for="pickup-date" class="form-label">Pickup Date <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="pickup-date" name="scheduled_pickup_date" min="{{ date('Y-m-d') }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="pickup-time" class="form-label">Pickup Time <span class="text-danger">*</span></label>
                                        <input type="time" class="form-control" id="pickup-time" name="scheduled_pickup_time" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-danger" id="schedule-decline-btn" onclick="declineDonation(currentDonationId)"><i class="fas fa-times me-1"></i> Decline</button>
                            <button type="submit" class="btn btn-primary">Save Schedule</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- View Donation Details Modal -->
        <div class="modal fade" id="viewHomeDonationModal" tabindex="-1" aria-labelledby="viewHomeDonationModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header bg-pink text-white"
                        style="background: linear-gradient(90deg,#ff7eb6,#ff65a3);">
                        <h5 class="modal-title" id="viewHomeDonationModalLabel">Donation Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3 p-3" style="background:#f5f5f5;border-radius:6px;">
                            <div class="row">
                                <div class="col-12 mb-2">
                                    <strong class="me-2">Name:</strong>
                                    <span id="view-donor-name" class="text-dark">&nbsp;</span>
                                </div>
                                <div class="col-12 mb-2">
                                    <strong class="me-2">Contact:</strong>
                                    <span id="view-donor-contact" class="text-dark">&nbsp;</span>
                                </div>
                                <div class="col-12 mb-2">
                                    <strong class="me-2">Address:</strong>
                                    <span id="view-donor-address" class="text-dark">&nbsp;</span>
                                </div>
                                <!-- Location removed per request -->
                            </div>
                        </div>

                        <div class="mb-3">
                            <strong>Total Bag:</strong> <span id="view-total-bags">-</span>
                        </div>
                        <div class="mb-3">
                            <strong>Total Vol:</strong> <span id="view-total-vol">-</span>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered" id="view-bag-details-table">
                                <thead>
                                    <tr>
                                        <th style="background: #f8f9fa; font-weight: 600; padding: 12px; text-align: center;">Bag #</th>
                                        <th style="background: #f8f9fa; font-weight: 600; padding: 12px; text-align: center;">Time</th>
                                        <th style="background: #f8f9fa; font-weight: 600; padding: 12px; text-align: center;">Date</th>
                                        <th style="background: #f8f9fa; font-weight: 600; padding: 12px; text-align: center;">Volume (ml)</th>
                                    </tr>
                                </thead>
                                <tbody id="view-bag-details-body">
                                    <!-- rows inserted dynamically -->
                                </tbody>
                            </table>
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
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title" id="validateHomeCollectionModalLabel">Validate Home Collection</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <form id="validateHomeCollectionForm" method="POST" novalidate>
                        @csrf
                        <div class="modal-body">
                            <div id="validate-home-debug"
                                style="display:none; white-space:pre-wrap; font-size:0.85rem; max-height:180px; overflow:auto;"
                                class="mb-2"></div>

                            <div class="row gx-2 gy-2 mb-3 validate-info">
                                <div class="col-6 col-md-3">
                                    <div class="d-flex align-items-center">
                                        <strong class="me-1 mb-0 text-nowrap">Donor:</strong>
                                        <div id="validate-home-donor-name" class="form-control-plaintext mb-0">&nbsp;</div>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="d-flex align-items-center">
                                        <strong class="me-1 mb-0 text-nowrap">Address:</strong>
                                        <div id="validate-home-donor-address" class="form-control-plaintext mb-0">&nbsp;
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="d-flex align-items-center">
                                        <strong class="me-1 mb-0 text-nowrap">First Expression Date:</strong>
                                        <div id="validate-home-first-expression" class="form-control-plaintext mb-0">&nbsp;
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="d-flex align-items-center">
                                        <strong class="me-1 mb-0 text-nowrap">Last Expression Date:</strong>
                                        <div id="validate-home-last-expression" class="form-control-plaintext mb-0">&nbsp;
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <input type="hidden" id="home-donation-id" name="donation_id" value="">
                            <input type="hidden" id="home-bags" name="number_of_bags" value="">

                            <div id="home-form-error" class="alert alert-danger" role="alert" style="display:none;"
                                aria-live="polite"></div>

                            <!-- NOTE: Original read-only bag table removed; editable bag details only -->

                            <div class="mb-3">
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="home-bag-details-table">
                                        <thead>
                                            <tr>
                                                <th style="background: #f8f9fa; font-weight: 600; padding: 12px; text-align: center;">Bag #</th>
                                                <th style="background: #f8f9fa; font-weight: 600; padding: 12px; text-align: center;">Time</th>
                                                <th style="background: #f8f9fa; font-weight: 600; padding: 12px; text-align: center;">Date</th>
                                                <th style="background: #f8f9fa; font-weight: 600; padding: 12px; text-align: center; width: 180px;">Volume (ml)</th>
                                                <th style="background: #f8f9fa; font-weight: 600; padding: 12px; text-align: center;">Storage</th>
                                                <th style="background: #f8f9fa; font-weight: 600; padding: 12px; text-align: center;">Temp (°C)</th>
                                                <th style="background: #f8f9fa; font-weight: 600; padding: 12px; text-align: center;">Method</th>
                                            </tr>
                                        </thead>
                                        <tbody id="home-bag-details-body">
                                            <!-- populated by JS -->
                                        </tbody>
                                    </table>
                                </div>
                                <small class="text-muted">Enter the confirmed volume for each bag before completing
                                    validation.</small>
                            </div>

                            <div class="alert alert-info mb-3">
                                <strong>Total Volume:</strong> <span id="home-total">0 ml</span>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-danger" id="home-decline-btn"
                                onclick="declineDonation(currentDonationId)">
                                <i class="fas fa-times me-1"></i> Decline
                            </button>
                            <button type="submit" class="btn btn-success">
                                <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"
                                    style="display:none;" id="home-validate-spinner"></span>
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
    <!-- Assist Walk-in Donation Modal -->
    <div class="modal fade" id="assistWalkInDonationModal" tabindex="-1" aria-labelledby="assistWalkInDonationLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="assistWalkInDonationLabel"><i class="fas fa-user-plus me-2"></i>Assist
                        Walk-in Donation</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form id="assistWalkInDonationForm" method="POST" action="{{ route('admin.donation.assist-walkin') }}">
                    @csrf
                    <div class="modal-body">
                        <div id="assist-walkin-error" class="alert alert-danger" style="display:none;"></div>
                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-tag me-1"></i> Assist Option <span class="text-danger">*</span></label>
                            <select class="form-select" name="assist_option" required>
                                <option value="">Select option</option>
                                <option value="no_account_direct_record">No account or direct record</option>
                                <option value="record_to_existing_user">Record to existing user</option>
                                <option value="milk_letting_activity">Milk letting activity</option>
                            </select>
                        </div>
                        <div id="assist-existing-user" class="mb-3" style="display:none;">
                            <label class="form-label"><i class="fas fa-search me-1"></i> Find Existing User</label>
                            <input type="text" class="form-control" id="assist_user_search" placeholder="Search by name or contact (min 2 chars)">
                            <div id="assist_user_results" class="list-group mt-2" style="max-height:220px; overflow:auto;"></div>
                            <input type="hidden" name="existing_user_id" id="assist_existing_user_id" value="">
                            <small class="text-muted">When you select a user, their details will auto-fill below.</small>
                        </div>
                        <h6 class="border-bottom pb-2 mb-3"><i class="fas fa-user"></i> Donor Information</h6>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">First Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control auto-capitalize-words" name="donor_first_name" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Last Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control auto-capitalize-words" name="donor_last_name" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Contact Number <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="donor_contact" placeholder="09XXXXXXXXX"
                                    required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Address (optional)</label>
                            <input type="text" class="form-control auto-capitalize-words" name="donor_address">
                        </div>

                        <h6 class="border-bottom pb-2 mb-3"><i class="fas fa-flask"></i> Bags & Volumes</h6>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Number of Bags <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="assist_bags" name="number_of_bags" min="1"
                                    max="50" required>
                            </div>
                        </div>
                        <div id="assist-volumes-container" style="display:none;">
                            <label class="form-label">Volume per bag (ml)</label>
                            <div id="assist-volume-fields"></div>
                        </div>
                        <div class="mt-3" id="assist-total-display" style="display:none;">
                            <div class="alert alert-info mb-0"><strong>Total:</strong> <span id="assist-total">0</span> ml
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"
                                style="display:none;" id="assist-spinner"></span>
                            <span id="assist-submit-text">Record Donation</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- Real-time Search Functionality (improved) --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('searchInput');
            const clearBtn = document.getElementById('clearSearch');
            const searchResults = document.getElementById('searchResults');

            if (!searchInput) return;

            // Helpers to extract searchable text from table rows and donation cards
            function extractRowFields(row) {
                // Include donor Name, Address, and Contact cells for searching
                const nameCell = row.querySelector('[data-label="Name"]');
                const addressCell = row.querySelector('[data-label="Address"]');
                const contactCell = row.querySelector('[data-label="Contact"]');
                const nameText = nameCell ? nameCell.textContent.trim() : '';
                const addressText = addressCell ? addressCell.textContent.trim() : '';
                const contactText = contactCell ? contactCell.textContent.trim() : '';
                return (nameText + ' ' + addressText + ' ' + contactText).toLowerCase();
            }

            function extractCardFields(card) {
                // Mobile card: pull Name, Address, and Contact rows
                const nameEl = card.querySelector('.card-header-row strong') || card.querySelector('strong');
                const findRow = (label) => Array.from(card.querySelectorAll('.card-row')).find(r => (r.querySelector('.card-label')||{}).textContent?.toLowerCase().includes(label));
                const addressValEl = (findRow('address') || {}).querySelector ? findRow('address').querySelector('.card-value') : null;
                const contactValEl = (findRow('contact') || {}).querySelector ? findRow('contact').querySelector('.card-value') : null;
                const nameText = nameEl ? nameEl.textContent.trim() : '';
                const addressText = addressValEl ? addressValEl.textContent.trim() : '';
                const contactText = contactValEl ? contactValEl.textContent.trim() : '';
                return (nameText + ' ' + addressText + ' ' + contactText).toLowerCase();
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

                // With a search term: limit targets to currently visible elements
                const visibleRows = rows.filter(r => isVisible(r));
                const visibleCards = cards.filter(c => isVisible(c));
                totalCount = visibleRows.length + visibleCards.length;

                // Handle table rows (desktop): match substring on Name or Address
                visibleRows.forEach(row => {
                    const hay = extractRowFields(row);
                    if (hay.indexOf(term) !== -1) {
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });

                // Handle donation-card blocks (mobile view) (Name + Address)
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
            clearBtn.addEventListener('click', function () {
                searchInput.value = '';
                performSearch();
                searchInput.focus();
            });

            // Initial run to ensure correct state
            performSearch();
        });
    </script>

    <script>
        // Assist Walk-in dynamic fields and submit
        (function () {
            const optionSelect = document.querySelector('#assistWalkInDonationForm select[name="assist_option"]');
            const existingWrap = document.getElementById('assist-existing-user');
            const searchInput = document.getElementById('assist_user_search');
            const resultsBox = document.getElementById('assist_user_results');
            const userIdInput = document.getElementById('assist_existing_user_id');
            const donorFirst = document.querySelector('input[name="donor_first_name"]');
            const donorLast = document.querySelector('input[name="donor_last_name"]');
            const donorContact = document.querySelector('input[name="donor_contact"]');
            const donorAddress = document.querySelector('input[name="donor_address"]');

            function setDonorFieldsReadonly(readonly) {
                const fields = [donorFirst, donorLast, donorContact, donorAddress];
                fields.forEach(field => {
                    if (field) {
                        if (readonly) {
                            field.setAttribute('readonly', 'readonly');
                            field.style.backgroundColor = '#e9ecef';
                            field.style.cursor = 'not-allowed';
                        } else {
                            field.removeAttribute('readonly');
                            field.style.backgroundColor = '';
                            field.style.cursor = '';
                        }
                    }
                });
            }

            function toggleExistingUser() {
                const val = optionSelect ? optionSelect.value : '';
                if (val === 'record_to_existing_user') {
                    existingWrap.style.display = 'block';
                } else {
                    existingWrap.style.display = 'none';
                    resultsBox.innerHTML = '';
                    userIdInput.value = '';
                    setDonorFieldsReadonly(false); // Enable fields when switching away from existing user
                }
            }
            if (optionSelect) {
                optionSelect.addEventListener('change', toggleExistingUser);
                toggleExistingUser();
            }

            let searchTimer = null;
            function renderResults(items) {
                resultsBox.innerHTML = '';
                if (!items || items.length === 0) return;
                items.forEach(u => {
                    const a = document.createElement('button');
                    a.type = 'button';
                    a.className = 'list-group-item list-group-item-action';
                    const name = `${u.first_name || ''} ${u.last_name || ''}`.trim();
                    a.innerHTML = `<div class="d-flex justify-content-between"><strong>${name || 'Unnamed user'}</strong><span class="badge bg-secondary">${u.user_type || ''}</span></div><div class="small text-muted">${u.contact_number || ''} • ${u.address || ''}</div>`;
                    a.addEventListener('click', () => {
                        userIdInput.value = u.user_id;
                        if (donorFirst) donorFirst.value = u.first_name || '';
                        if (donorLast) donorLast.value = u.last_name || '';
                        if (donorContact) donorContact.value = u.contact_number || '';
                        if (donorAddress) donorAddress.value = u.address || '';
                        resultsBox.innerHTML = '';
                        searchInput.value = name || u.contact_number || '';
                        // Make donor fields readonly when existing user is selected
                        setDonorFieldsReadonly(true);
                    });
                    resultsBox.appendChild(a);
                });
            }
            async function doSearch() {
                const q = (searchInput.value || '').trim();
                if (q.length < 2) { resultsBox.innerHTML = ''; return; }
                try {
                    const resp = await fetch(`{{ route('admin.users.search') }}?q=${encodeURIComponent(q)}`, { headers: { 'Accept': 'application/json' } });
                    if (!resp.ok) return;
                    const data = await resp.json();
                    renderResults((data && data.data) || []);
                } catch (e) { /* ignore */ }
            }
            if (searchInput) {
                searchInput.addEventListener('input', () => {
                    if (searchTimer) clearTimeout(searchTimer);
                    searchTimer = setTimeout(doSearch, 300);
                });
            }

            const bagsEl = document.getElementById('assist_bags');
            const container = document.getElementById('assist-volume-fields');
            const wrap = document.getElementById('assist-volumes-container');
            const totalBox = document.getElementById('assist-total-display');
            const totalEl = document.getElementById('assist-total');
            function renderFields() {
                const n = parseInt(bagsEl.value || '0', 10);
                if (!n || n < 1) { wrap.style.display = 'none'; totalBox.style.display = 'none'; container.innerHTML = ''; return; }
                wrap.style.display = 'block'; totalBox.style.display = 'block';
                let html = '<div class="row">';
                for (let i = 1; i <= n; i++) {
                    html += `
                                                                                                                            <div class="col-md-6 mb-2">
                                                                                                                                <label class="form-label">Bag ${i} (ml)</label>
                                                                                                                                <input type="number" step="0.01" min="0.01" class="form-control assist-bag-volume" name="bag_volumes[]" required>
                                                                                                                            </div>`;
                }
                html += '</div>';
                container.innerHTML = html;
                updateTotal();
            }
            function updateTotal() {
                const inputs = container.querySelectorAll('.assist-bag-volume');
                let t = 0; inputs.forEach(inp => { const v = parseFloat(inp.value || '0'); if (!isNaN(v)) t += v; });
                totalEl.textContent = (t % 1 === 0 ? Math.round(t) : t.toFixed(2).replace(/\.?0+$/, ''));
            }
            if (bagsEl) { bagsEl.addEventListener('input', renderFields); }
            container?.addEventListener('input', function (e) { if (e.target && e.target.classList.contains('assist-bag-volume')) updateTotal(); });

            // Submit via AJAX
            const form = document.getElementById('assistWalkInDonationForm');
            if (form) {
                form.addEventListener('submit', function (e) {
                    e.preventDefault();
                    const spinner = document.getElementById('assist-spinner');
                    const text = document.getElementById('assist-submit-text');
                    const err = document.getElementById('assist-walkin-error');
                    err.style.display = 'none'; err.textContent = '';
                    spinner.style.display = 'inline-block'; text.textContent = 'Recording...';
                    const formData = new FormData(form);
                    fetch(form.action, { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }, body: formData })
                        .then(r => r.json())
                        .then(data => {
                            if (data && data.success) {
                                const modal = bootstrap.Modal.getInstance(document.getElementById('assistWalkInDonationModal'));
                                if (modal) modal.hide();
                                setTimeout(() => {
                                    Swal.fire({ icon: 'success', title: 'Recorded', text: data.message || 'Walk-in donation recorded.', timer: 1400, showConfirmButton: false })
                                        .then(() => {
                                            const url = new URL(window.location.href); url.searchParams.set('status', 'success_walk_in'); window.location.href = url.toString();
                                        });
                                }, 200);
                            } else {
                                err.textContent = (data && data.message) ? data.message : 'Failed to record walk-in donation.';
                                err.style.display = 'block';
                            }
                        })
                        .catch(() => { err.textContent = 'Failed to record walk-in donation.'; err.style.display = 'block'; })
                        .finally(() => { spinner.style.display = 'none'; text.textContent = 'Record Donation'; });
                });
            }

            // Reset donor fields to editable when modal is closed
            const assistModal = document.getElementById('assistWalkInDonationModal');
            if (assistModal) {
                assistModal.addEventListener('hidden.bs.modal', function () {
                    setDonorFieldsReadonly(false);
                    // Also clear the hidden user ID and search results
                    if (userIdInput) userIdInput.value = '';
                    if (resultsBox) resultsBox.innerHTML = '';
                    if (searchInput) searchInput.value = '';
                });
            }
        })();
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
                const lat = $(this).data('latitude');
                const lng = $(this).data('longitude');
                const firstExpression = $(this).data('first-expression') || '--';
                const lastExpression = $(this).data('last-expression') || '--';
                const bagDetailsRaw = $(this).attr('data-bag-details');
                const totalVolume = $(this).data('total') || 0;

                // Populate donor info
                $('#schedule-donor-name').text(donorName);
                $('#schedule-donor-address').text(donorAddress);
                $('#schedule-first-expression').text(firstExpression);
                $('#schedule-last-expression').text(lastExpression);

                // Inject location button into modal if coordinates are present
                (function setScheduleLocation() {
                    const hasLat = (lat !== undefined && lat !== null && String(lat) !== '');
                    const hasLng = (lng !== undefined && lng !== null && String(lng) !== '');
                    if (hasLat && hasLng) {
                        const safeName = donorName || '';
                        const safeAddress = donorAddress || '';
                        $('#schedule-donor-location').html(
                            `<button class="btn btn-info btn-sm view-location" title="View on Map" data-donor-name="${$('<div>').text(safeName).html()}" data-donor-address="${$('<div>').text(safeAddress).html()}" data-latitude="${lat}" data-longitude="${lng}"><i class="fas fa-map-marked-alt"></i></button>`
                        );
                    } else {
                        $('#schedule-donor-location').text('-');
                    }
                })();

                // Robust bag details parser (local copy) to handle HTML-encoded attributes
                function safeParseBagDetails_local(raw) {
                    if (!raw) return [];
                    if (Array.isArray(raw)) return raw;
                    if (typeof raw !== 'string') return [];
                    const s = raw.trim();
                    if (s === '' || s === 'null') return [];
                    try { return JSON.parse(s); } catch (e) { }
                    try {
                        const txt = document.createElement('textarea');
                        txt.innerHTML = s;
                        const decoded = txt.value;
                        if (decoded && decoded !== s) {
                            try { return JSON.parse(decoded); } catch (e) { }
                        }
                    } catch (e) { }
                    try {
                        const replaced = s.replace(/&quot;/g, '"').replace(/&apos;|&#039;/g, "'").replace(/&amp;/g, '&');
                        return JSON.parse(replaced);
                    } catch (e) { }
                    try {
                        const unescaped = s.replace(/\\\"/g, '"').replace(/\\'/g, "'");
                        return JSON.parse(unescaped);
                    } catch (e) { }
                    return [];
                }

                const bagDetails = safeParseBagDetails_local(bagDetailsRaw);

                console.log('Parsed bag details:', bagDetails);

                // Populate bag details table (with editable Volume inputs)
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

                // Map storage keys to readable labels
                function mapStorage(s) {
                    if (!s || s === '--' || s === '-') return s || '--';
                    const key = String(s).toLowerCase().trim();

                    // common short codes used in home collection form
                    if (key === 'ref' || key === 'refr' || key === 'fridge' || key === 'refrigerator' || key.indexOf('refrig') !== -1) return 'Refrigerator';
                    if (key === 'frz' || key === 'fridge_freeze' || key.indexOf('freez') !== -1 || key.indexOf('freeze') !== -1) return 'Freezer';

                    if (key.indexOf('room') !== -1 || key.indexOf('ambient') !== -1 || key.indexOf('room_temp') !== -1 || key === 'roomtemp') return 'Room temperature';

                    // fallback: title case the token and replace underscores
                    return String(s).replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
                }

                let total = 0;
                const numberOfBags = parseInt($(this).data('bags')) || (Array.isArray(bagDetails) ? bagDetails.length : 0) || 0;
                if (bagDetails && bagDetails.length > 0) {
                    bagDetails.forEach((bag, index) => {
                        const bagNum = bag.bag_number || (index + 1);
                        const time = formatTime12(bag.time) || '--';
                        const date = bag.date || '--';
                        const volume = bag.volume || '';
                        const storage = bag.storage_location || '--';
                        const storageLabel = mapStorage(storage);
                        const temp = bag.temperature || '--';
                        const method = bag.collection_method || '--';

                        const row = `
                                                                                                                                <tr>
                                                                                                                                    <td class="text-center fw-bold">Bag ${bagNum}</td>
                                                                                                                                    <td>${time}</td>
                                                                                                                                    <td>${date}</td>
                                                                                                                                    <td>
                                                                                                                                        <div class="input-group input-group-sm">
                                                                                                                                            <span class="form-control form-control-sm schedule-bag-volume-display">${volume ? volume : '--'}</span>
                                                                                                                                            <input type="hidden" name="bag_volumes[]" class="schedule-bag-volume" value="${volume}">
                                                                                                                                            <span class="input-group-text">ml</span>
                                                                                                                                        </div>
                                                                                                                                    </td>
                                                                                                                                    <td>${storageLabel}</td>
                                                                                                                                    <td class="text-end">${temp}</td>
                                                                                                                                    <td><small>${method}</small></td>
                                                                                                                                </tr>
                                                                                                                            `;
                        tbody.append(row);
                        const v = parseFloat(volume); if (!isNaN(v)) total += v;
                    });
                } else if (numberOfBags > 0) {
                    for (let i = 0; i < numberOfBags; i++) {
                        const row = `
                                                                                                                                <tr>
                                                                                                                                    <td class="text-center fw-bold">Bag ${i + 1}</td>
                                                                                                                                    <td>--</td>
                                                                                                                                    <td>--</td>
                                                                                                                                    <td>
                                                                                                                                        <div class="input-group input-group-sm">
                                                                                                                                            <span class="form-control form-control-sm schedule-bag-volume-display">--</span>
                                                                                                                                            <input type="hidden" name="bag_volumes[]" class="schedule-bag-volume" value="">
                                                                                                                                            <span class="input-group-text">ml</span>
                                                                                                                                        </div>
                                                                                                                                    </td>
                                                                                                                                    <td>--</td>
                                                                                                                                    <td class="text-end">--</td>
                                                                                                                                    <td><small>--</small></td>
                                                                                                                                </tr>
                                                                                                                            `;
                        tbody.append(row);
                    }
                } else {
                    tbody.append('<tr><td colspan="7" class="text-center text-muted"><i class="fas fa-info-circle me-2"></i>No bag details available</td></tr>');
                }

                function updateScheduleTotal() {
                    let sum = 0;
                    $('#schedule-bag-details-body .schedule-bag-volume').each(function () {
                        const v = parseFloat($(this).val());
                        if (!isNaN(v)) sum += v;
                    });
                    $('#schedule-total-volume').text(sum.toFixed(2));
                }
                // Initial total
                if (total > 0) {
                    $('#schedule-total-volume').text(total.toFixed(2));
                } else {
                    // fallback to server-provided total
                    const displayTotal = (parseFloat(totalVolume) || 0);
                    $('#schedule-total-volume').text(displayTotal.toFixed(2));
                }
                // Live updating: keep function available but volumes are rendered as hidden inputs
                // so updates won't be triggered by user input here.
                $('#schedule-bag-details-body').off('input.scheduleVol').on('input.scheduleVol', '.schedule-bag-volume', updateScheduleTotal);

                $('#schedulePickupForm').attr('action', `/admin/donations/${currentDonationId}/schedule-pickup`);
                $('#schedulePickupModal').modal('show');

                // Preload lifestyle checklist for this donation (Tab 3)
                try { loadScheduleScreening(currentDonationId); } catch (e) { console.warn('Failed to load lifestyle checklist', e); }
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

            // Home collection validation modal - Use Bootstrap's modal show event
            $('#validateHomeCollectionModal').on('show.bs.modal', function (event) {
                // Button that triggered the modal
                const button = $(event.relatedTarget);

                currentDonationId = button.data('id');
                const donorName = button.data('donor');
                const numberOfBags = button.data('bags');
                const bagDetailsRaw = button.attr('data-bag-details');
                const totalVolume = button.data('total');

                // Optional fields sent via data-attributes
                const donorAddress = button.data('address') || '';
                const scheduledDate = button.data('date') || '';
                const scheduledTimeRaw = button.data('time') || '';
                // first/last expression may be present on some buttons (from earlier flows)
                const firstExpression = button.data('first-expression') || button.data('first_expression') || '';
                const lastExpression = button.data('last-expression') || button.data('last_expression') || '';

                console.log('Button Data:', {
                    id: currentDonationId,
                    donorName: donorName,
                    donorAddress: donorAddress,
                    scheduledDate: scheduledDate,
                    scheduledTimeRaw: scheduledTimeRaw,
                    numberOfBags: numberOfBags,
                    totalVolume: totalVolume
                });

                // Robust bag details parser: handles JSON, HTML-encoded attributes, and hex-escaped JSON
                function safeParseBagDetails(raw) {
                    if (!raw) return [];
                    if (Array.isArray(raw)) return raw;
                    if (typeof raw !== 'string') return [];

                    const s = raw.trim();
                    if (s === '' || s === 'null') return [];

                    // 1) Try direct JSON.parse
                    try {
                        return JSON.parse(s);
                    } catch (e) {
                        // continue to fallbacks
                    }

                    // 2) Try decoding HTML entities (e.g. &quot;) using a textarea
                    try {
                        const txt = document.createElement('textarea');
                        txt.innerHTML = s;
                        const decoded = txt.value;
                        if (decoded && decoded !== s) {
                            try { return JSON.parse(decoded); } catch (e) { }
                        }
                    } catch (e) { }

                    // 3) Replace common HTML entities and attempt parse
                    try {
                        const replaced = s.replace(/&quot;/g, '"').replace(/&apos;|&#039;/g, "'")
                            .replace(/&amp;/g, '&');
                        return JSON.parse(replaced);
                    } catch (e) { }

                    // 4) If the string looks like a PHP-encoded JSON with escaped quotes (\"), unescape and parse
                    try {
                        const unescaped = s.replace(/\\\"/g, '"').replace(/\\'/g, "'");
                        return JSON.parse(unescaped);
                    } catch (e) { }

                    // Give up
                    return [];
                }

                const bagDetails = safeParseBagDetails(bagDetailsRaw);

                console.log('Validate modal - Parsed bag details:', bagDetails);
                // Pre-render parsed bag details (button data) immediately for snappy UI
                try {
                    if (bagDetails && bagDetails.length > 0) {
                        renderBagTables(bagDetails);
                    }
                } catch (e) {
                    console.warn('renderBagTables failed during pre-render:', e);
                }
                // Debug panel suppressed in production. To enable, set window.VALIDATE_HOME_DEBUG = true
                if (window.VALIDATE_HOME_DEBUG) {
                    try {
                        const dbg = $('#validate-home-debug');
                        const dump = {
                            buttonData: {
                                id: currentDonationId,
                                donorName: donorName,
                                donorAddress: donorAddress,
                                scheduledDate: scheduledDate,
                                scheduledTimeRaw: scheduledTimeRaw,
                                numberOfBags: numberOfBags,
                                totalVolume: totalVolume,
                                rawBagDetailsAttr: bagDetailsRaw
                            },
                            parsedBagDetails: bagDetails
                        };
                        dbg.text(JSON.stringify(dump, null, 2));
                        dbg.show();
                    } catch (e) {
                        console.warn('Failed to populate validate debug panel', e);
                    }
                }

                // Set donation id and number of bags
                $('#home-donation-id').val(currentDonationId);
                $('#home-bags').val(numberOfBags || '');

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

                // Populate donor info immediately with data attributes (will be updated by AJAX if available)
                $('#validate-home-donor-name').text(donorName || '');
                $('#validate-home-donor-address').text(donorAddress || 'Not provided');
                $('#validate-home-date').text(scheduledDate || '');
                $('#validate-home-time').text(formatTime12(scheduledTimeRaw) || '');
                // populate first/last expression if available from button data (AJAX will override if server provides)
                function formatDateShort(d) {
                    if (!d) return '';
                    try {
                        const dt = new Date(d);
                        if (isNaN(dt)) return d;
                        return dt.toLocaleDateString(undefined, { month: 'short', day: '2-digit', year: 'numeric' });
                    } catch (e) { return d; }
                }
                $('#validate-home-first-expression').text(formatDateShort(firstExpression) || '--');
                $('#validate-home-last-expression').text(formatDateShort(lastExpression) || '--');

                // Map storage keys to readable labels
                function mapStorage(s) {
                    if (!s || s === '--' || s === '-') return s || '--';
                    const key = String(s).toLowerCase().trim();

                    // common short codes used in home collection form
                    if (key === 'ref' || key === 'refr' || key === 'fridge' || key === 'refrigerator' || key.indexOf('refrig') !== -1) return 'Refrigerator';
                    if (key === 'frz' || key === 'fridge_freeze' || key.indexOf('freez') !== -1 || key.indexOf('freeze') !== -1) return 'Freezer';

                    if (key.indexOf('room') !== -1 || key.indexOf('ambient') !== -1 || key.indexOf('room_temp') !== -1 || key === 'roomtemp') return 'Room temperature';

                    // fallback: title case the token and replace underscores
                    return String(s).replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
                }

                // Set form action
                $('#validateHomeCollectionForm').attr('action', `/admin/donations/${currentDonationId}/validate-pickup`);

                // Fetch full donation details from server (prefer fresh source)
                const tbody = $('#home-bag-details-body');
                tbody.empty();

                let totalVol = 0;
                const volumeFieldsContainer = $('#home-volume-fields'); // legacy container, keep empty
                volumeFieldsContainer.empty();

                // Request donation details from server to populate both original (readonly) and editable tables
                $.ajax({
                    url: `/admin/donations/${currentDonationId}`,
                    method: 'GET',
                    success: function (response) {
                        console.log('AJAX Response:', response);
                        console.log('Number of Bags from button:', numberOfBags);
                        console.log('Bag Details from button:', bagDetails);

                        const donationData = (response && response.donation) ? response.donation : null;
                        console.log('Donation Data:', donationData);

                        // Populate donor info - prioritize server data, fallback to button data
                        if (donationData) {
                            $('#validate-home-donor-name').text(donationData.donor_name || donorName || 'N/A');
                            $('#validate-home-donor-address').text(donationData.address || donorAddress || 'Not provided');
                            $('#validate-home-date').text(donationData.donation_date || scheduledDate || 'N/A');
                            $('#validate-home-time').text(donationData.donation_time || formatTime12(scheduledTimeRaw) || 'N/A');
                            // Set first/last expression if present on the returned donation
                            try {
                                const f = donationData.first_expression_date || donationData.first_expression || null;
                                const l = donationData.last_expression_date || donationData.last_expression || null;
                                const fmt = function (d) {
                                    if (!d) return '';
                                    try { const dt = new Date(d); if (isNaN(dt)) return d; return dt.toLocaleDateString(undefined, { month: 'short', day: '2-digit', year: 'numeric' }); } catch (e) { return d; }
                                };
                                $('#validate-home-first-expression').text(fmt(f) || '--');
                                $('#validate-home-last-expression').text(fmt(l) || '--');
                            } catch (e) { console.warn('Failed to set first/last expression dates', e); }
                        } else {
                            console.warn('No donation data from server, using button data');
                            $('#validate-home-donor-name').text(donorName || 'N/A');
                            $('#validate-home-donor-address').text(donorAddress || 'Not provided');
                            $('#validate-home-date').text(scheduledDate || 'N/A');
                            $('#validate-home-time').text(formatTime12(scheduledTimeRaw) || 'N/A');
                        }

                        const effectiveBags = donationData?.bag_details && donationData.bag_details.length > 0
                            ? donationData.bag_details
                            : (bagDetails && bagDetails.length > 0 ? bagDetails : []);

                        console.log('Effective Bags:', effectiveBags);
                        console.log('Effective Bags Length:', effectiveBags.length);

                        // We no longer show a separate original (read-only) table.
                        // The editable table below will be populated using effectiveBags (server-preferred) or button data fallback.

                        // Build editable table (use effectiveBags or numberOfBags)
                        if (effectiveBags && effectiveBags.length > 0) {
                            currentOriginalVolumes = [];
                            effectiveBags.forEach((bag, index) => {
                                const bagNum = bag.bag_number || (index + 1);
                                const time = formatTime12(bag.time) || '';
                                const date = bag.date || '--';
                                const volume = bag.volume || '';
                                const storage = bag.storage_location || '--';
                                const storageLabel = mapStorage(storage);
                                const temp = bag.temperature || '--';
                                const method = bag.collection_method || '--';

                                currentOriginalVolumes.push(volume || '');
                                totalVol += parseFloat(volume) || 0;

                                const row = `
                                                                                                        <tr>
                                                                                                            <td style="text-align: center; padding: 12px; font-weight: 600;">Bag ${bagNum}</td>
                                                                                                            <td style="padding: 8px;">
                                                                                                                <input type="text" name="bag_time[]" class="form-control" value="${time}" placeholder="e.g. 4:49 PM" style="border: 1px solid #dee2e6; padding: 8px;">
                                                                                                            </td>
                                                                                                            <td style="padding: 8px;">
                                                                                                                <input type="text" name="bag_date[]" class="form-control" value="${bag.date || ''}" placeholder="date" style="border: 1px solid #dee2e6; padding: 8px;">
                                                                                                            </td>
                                                                                                            <td style="padding: 8px;">
                                                                                                                <div class="input-group">
                                                                                                                    <input type="number"
                                                                                                                           id="home_bag_volume_${index + 1}"
                                                                                                                           name="bag_volumes[]"
                                                                                                                           class="form-control home-bag-volume-input"
                                                                                                                           step="0.01"
                                                                                                                           min="0.01"
                                                                                                                           value="${volume}"
                                                                                                                           placeholder="400"
                                                                                                                           style="border: 1px solid #dee2e6; padding: 8px; text-align: right;"
                                                                                                                           required>
                                                                                                                    <span class="input-group-text" style="background: white; border-left: 0; color: #0d6efd; font-weight: 500;">ml</span>
                                                                                                                </div>
                                                                                                            </td>
                                                                                                            <td style="padding: 8px;">
                                                                                                                <select name="bag_storage[]" class="form-select" style="border: 1px solid #dee2e6; padding: 8px;">
                                                                                                                    ${(() => {
                                        const raw = bag.storage_location || '';
                                        const key = String(raw).toLowerCase();
                                        if (key.indexOf('ref') !== -1 || key.indexOf('refrig') !== -1 || key.indexOf('fridge') !== -1) return `
                                                                                                                            <option value="Refrigerator" selected>Refrigerator</option>
                                                                                                                            <option value="Freezer">Freezer</option>
                                                                                                                            <option value="Room temperature">Room temperature</option>
                                                                                                                            <option value="Other">Other</option>
                                                                                                                        `;
                                        if (key.indexOf('freez') !== -1 || key.indexOf('freeze') !== -1 || key.indexOf('frz') !== -1) return `
                                                                                                                            <option value="Refrigerator">Refrigerator</option>
                                                                                                                            <option value="Freezer" selected>Freezer</option>
                                                                                                                            <option value="Room temperature">Room temperature</option>
                                                                                                                            <option value="Other">Other</option>
                                                                                                                        `;
                                        if (key.indexOf('room') !== -1 || key.indexOf('ambient') !== -1) return `
                                                                                                                            <option value="Refrigerator">Refrigerator</option>
                                                                                                                            <option value="Freezer">Freezer</option>
                                                                                                                            <option value="Room temperature" selected>Room temperature</option>
                                                                                                                            <option value="Other">Other</option>
                                                                                                                        `;
                                        if (raw && raw !== '') return `
                                                                                                                            <option value="Refrigerator">Refrigerator</option>
                                                                                                                            <option value="Freezer">Freezer</option>
                                                                                                                            <option value="Room temperature">Room temperature</option>
                                                                                                                            <option value="Other" selected>Other</option>
                                                                                                                        `;
                                        return `
                                                                                                                            <option value="Refrigerator">Refrigerator</option>
                                                                                                                            <option value="Freezer">Freezer</option>
                                                                                                                            <option value="Room temperature">Room temperature</option>
                                                                                                                            <option value="Other">Other</option>
                                                                                                                        `;
                                    })()}
                                                                                                                </select>
                                                                                                            </td>
                                                                                                            <td style="padding: 8px;">
                                                                                                                <input type="text" name="bag_temp[]" class="form-control" value="${bag.temperature || ''}" placeholder="temp" style="border: 1px solid #dee2e6; padding: 8px; text-align: center;">
                                                                                                            </td>
                                                                                                            <td style="padding: 8px;">
                                                                                                                <input type="text" name="bag_method[]" class="form-control" value="${bag.collection_method || ''}" placeholder="method" style="border: 1px solid #dee2e6; padding: 8px;">
                                                                                                            </td>
                                                                                                        </tr>`;
                                tbody.append(row);
                                tbody.closest('.table-responsive').show();
                            });
                        } else {
                            // No bag details array: generate editable rows based on numberOfBags
                            const n = parseInt(numberOfBags) || 0;
                            if (n > 0) {
                                for (let i = 1; i <= n; i++) {
                                    const row = `
                                                                                                                    <tr>
                                                                                                                        <td style="text-align: center; padding: 12px; font-weight: 600;">Bag ${i}</td>
                                                                                                                        <td style="padding: 8px;">
                                                                                                                            <input type="text" name="bag_time[]" class="form-control" value="" placeholder="time" style="border: 1px solid #dee2e6; padding: 8px;">
                                                                                                                        </td>
                                                                                                                        <td style="padding: 8px;">
                                                                                                                            <input type="text" name="bag_date[]" class="form-control" value="" placeholder="date" style="border: 1px solid #dee2e6; padding: 8px;">
                                                                                                                        </td>
                                                                                                                        <td style="padding: 8px;">
                                                                                                                            <div class="input-group">
                                                                                                                                <input type="number"
                                                                                                                                       id="home_bag_volume_${i}"
                                                                                                                                       name="bag_volumes[]"
                                                                                                                                       class="form-control home-bag-volume-input"
                                                                                                                                       step="0.01" min="0.01" placeholder="400" style="border: 1px solid #dee2e6; padding: 8px; text-align: right;" required>
                                                                                                                                <span class="input-group-text" style="background: white; border-left: 0; color: #0d6efd; font-weight: 500;">ml</span>
                                                                                                                            </div>
                                                                                                                        </td>
                                                                                                                        <td style="padding: 8px;">
                                                                                                                            <select name="bag_storage[]" class="form-select" style="border: 1px solid #dee2e6; padding: 8px;">
                                                                                                                                <option value="Refrigerator">Refrigerator</option>
                                                                                                                                <option value="Freezer">Freezer</option>
                                                                                                                                <option value="Room temperature">Room temperature</option>
                                                                                                                                <option value="Other">Other</option>
                                                                                                                            </select>
                                                                                                                        </td>
                                                                                                                        <td style="padding: 8px;">
                                                                                                                            <input type="text" name="bag_temp[]" class="form-control" value="" placeholder="temp" style="border: 1px solid #dee2e6; padding: 8px; text-align: center;">
                                                                                                                        </td>
                                                                                                                        <td style="padding: 8px;">
                                                                                                                            <input type="text" name="bag_method[]" class="form-control" value="" placeholder="method" style="border: 1px solid #dee2e6; padding: 8px;">
                                                                                                                        </td>
                                                                                                                    </tr>`;
                                    tbody.append(row);
                                }
                                totalVol = 0;
                            } else {
                                tbody.append('<tr><td colspan="7" class="text-center text-muted"><i class="fas fa-info-circle me-2"></i>No bag details available</td></tr>');
                                totalVol = parseFloat(totalVolume) || 0;
                            }
                        }

                        // Update total display
                        $('#home-total').text(totalVol.toFixed(2) + ' ml');

                        // Live update total when editing volumes
                        $('#home-bag-details-body').off('input.homeVol').on('input.homeVol', '.home-bag-volume-input', function () {
                            let sum = 0;
                            $('#home-bag-details-body .home-bag-volume-input').each(function () {
                                const v = parseFloat($(this).val());
                                if (!isNaN(v)) sum += v;
                            });
                            $('#home-total').text(sum.toFixed(2) + ' ml');
                        });

                        $('#home-form-error').hide().text('');
                    },
                    error: function (xhr, status, error) {
                        console.error('Failed to fetch donation for validation:', error);

                        // Populate donor info with fallback data attributes
                        $('#validate-home-donor-name').text(donorName || '');
                        $('#validate-home-donor-address').text(donorAddress || 'Not provided');
                        $('#validate-home-date').text(scheduledDate || '');
                        $('#validate-home-time').text(formatTime12(scheduledTimeRaw) || '');
                        // fallback for first/last expression (use pre-read values if available)
                        try {
                            $('#validate-home-first-expression').text(formatDateShort(firstExpression) || '--');
                            $('#validate-home-last-expression').text(formatDateShort(lastExpression) || '--');
                        } catch (e) { console.warn('No first/last expression available'); }

                        // Fallback: use parsed data-bag-details already available
                        if (bagDetails && bagDetails.length > 0) {
                            bagDetails.forEach((bag, index) => {
                                const bagNum = bag.bag_number || (index + 1);
                                const time = formatTime12(bag.time) || '--';
                                const date = bag.date || '--';
                                const volume = bag.volume || 0;
                                const storageLabel = mapStorage(bag.storage_location || '--');
                                const temp = bag.temperature || '--';
                                const method = bag.collection_method || '--';

                                const originalRow = `\n                                    <tr class="text-center">\n                                        <td>Bag ${bagNum}</td>\n                                        <td>${time}</td>\n                                        <td>${date}</td>\n                                        <td>${volume}</td>\n                                        <td>${storageLabel}</td>\n                                        <td class="text-end">${temp}</td>\n                                        <td><small>${method}</small></td>\n                                    </tr>`;
                                originalTbody.append(originalRow);

                                const row = `\n                                    <tr>\n                                        <td class="text-center fw-bold">Bag ${bagNum}</td>\n                                        <td>${time}</td>\n                                        <td>${date}</td>\n                                        <td>\n                                            <div class="input-group input-group-sm">\n                                                <input type="number" id="home_bag_volume_${index + 1}" name="bag_volumes[]" class="form-control home-bag-volume-input" step="0.01" min="0.01" value="${volume}" placeholder="ml" required>\n                                                <span class="input-group-text">ml</span>\n                                            </div>\n                                        </td>\n                                        <td>${storageLabel}</td>\n                                        <td class="text-end">${temp}</td>\n                                        <td><small>${method}</small></td>\n                                    </tr>`;
                                tbody.append(row);
                                tbody.closest('.table-responsive').show();
                                totalVol += parseFloat(volume) || 0;
                            });
                            $('#home-total').text(totalVol.toFixed(2) + ' ml');

                            // Live update total when editing volumes
                            $('#home-bag-details-body').off('input.homeVol').on('input.homeVol', '.home-bag-volume-input', function () {
                                let sum = 0;
                                $('#home-bag-details-body .home-bag-volume-input').each(function () {
                                    const v = parseFloat($(this).val());
                                    if (!isNaN(v)) sum += v;
                                });
                                $('#home-total').text(sum.toFixed(2) + ' ml');
                            });

                            $('#home-form-error').hide().text('');
                        } else {
                            $('#home-bag-details-body').append('<tr><td colspan="7" class="text-center text-muted">No bag details available</td></tr>');
                            $('#home-total').text(parseFloat(totalVolume || 0).toFixed(2) + ' ml');
                            $('#home-form-error').hide().text('');
                        }
                    }
                });
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
                if (bagCount <= 0) {
                    $('#walkin-form-error').text('Please enter the number of bags.').show();
                    return;
                }
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
                                    // Navigate directly to Walk-in Success tab for clarity
                                    const url = new URL(window.location.href);
                                    url.searchParams.set('status', 'success_walk_in');
                                    window.location.href = url.toString();
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

                // Validate bag volumes (hidden inputs are used for read-only display)
                const volumeInputs = $('#schedule-bag-details-body .schedule-bag-volume');
                if (volumeInputs.length > 0) {
                    let allValid = true;
                    volumeInputs.each(function () {
                        const v = parseFloat($(this).val());
                        if (isNaN(v) || v <= 0) { allValid = false; return false; }
                    });
                    if (!allValid) {
                        Swal.fire({ icon: 'error', title: 'Invalid volumes', text: 'Please enter a valid volume (> 0) for each bag.' });
                        return;
                    }
                }

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

        // Reset schedule modal (tabbed) when closed
        $('#schedulePickupModal').on('hidden.bs.modal', function () {
            $('#schedulePickupModalLabel').text('Schedule Home Collection Pickup');
            $('#schedulePickupForm button[type="submit"]').text('Save Schedule');
            $('#schedulePickupForm').attr('action', '');
            $('#pickup-date').val('');
            $('#pickup-time').val('');
            $('#schedule-bag-details-body').empty();
            $('#schedule-total-volume').text('0');
            $('#schedule-donor-name').text('');
            $('#schedule-donor-address').text('');
            $('#schedule-first-expression').text('');
            $('#schedule-last-expression').text('');
            $('#schedule-donor-location').text('-');
            $('#schedule-screening-content').empty();
            $('#schedule-screening-loading').hide();
            // Reset active tab to donor info
            const donorTab = document.querySelector('#pickup-donor-tab');
            if (donorTab) new bootstrap.Tab(donorTab).show();
        });

        // When Lifestyle Checklist tab is shown, always reload to ensure fresh data
        document.getElementById('pickup-screening-tab')?.addEventListener('shown.bs.tab', function () {
            if (currentDonationId) {
                try { loadScheduleScreening(currentDonationId); } catch (e) { /* ignore */ }
            }
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
            $('#validate-home-donor-name').text('');
            $('#validate-home-donor-address').text('');
            $('#validate-home-date').text('');
            $('#validate-home-time').text('');
            // Clear original and editable bag tables
            $('#home-bag-details-body').empty();
            $('#home-bag-original-body').empty();
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
        // View donation modal script - fetch donation details and populate modal
        $(document).on('click', '.view-donation', function (e) {
            e.preventDefault();
            const btn = $(this);
            const id = btn.data('id');
            if (!id) return;

            // Use button-provided data attributes as immediate fallback/display
            const bDonorName = btn.data('donorName') || btn.data('donor') || btn.attr('data-donor-name') || '';
            const bContact = btn.data('donorContact') || btn.data('donorContact') || btn.attr('data-donor-contact') || '';
            const bAddress = btn.data('donorAddress') || btn.attr('data-donor-address') || '';
            // Location removed per request (bLat/bLng omitted)
            const bBags = btn.attr('data-bags') || '';
            const bTotal = btn.attr('data-total') || '';
            const bBagDetailsRaw = btn.attr('data-bag-details') || '';
            const donationMethodAttr = (btn.data('donationMethod') || btn.attr('data-donation-method') || '').toString().toLowerCase();

            const fallbackBagDetails = (function parseBagDetails(raw) {
                if (!raw) return [];
                try {
                    const parsed = JSON.parse(raw);
                    return Array.isArray(parsed) ? parsed : [];
                } catch (err) {
                    console.warn('Unable to parse fallback bag details', err);
                    return [];
                }
            })(bBagDetailsRaw);

            function setModalTitle(method) {
                const normalized = (method || '').toString().toLowerCase();
                const titleEl = $('#viewHomeDonationModalLabel');
                if (!titleEl.length) return;
                if (normalized === 'walk_in') {
                    titleEl.text('Walk-in Success');
                } else if (normalized === 'home_collection') {
                    titleEl.text('Home Collection Success');
                } else {
                    titleEl.text('Donation Details');
                }
            }

            setModalTitle(donationMethodAttr);

            // Clear previous content then set immediate donor info fallbacks only (we'll render bag details from server)
            $('#view-donor-name').text(bDonorName || 'Loading...');
            $('#view-donor-contact').text(bContact || '-');
            $('#view-donor-address').text(bAddress || 'Not provided');
            // Location display removed
            $('#view-total-bags').text('Loading...');
            $('#view-total-vol').text('Loading...');
            // show loading row for bag details until AJAX finishes
            $('#view-bag-details-body').html('<tr><td colspan="4" class="text-center text-muted">Loading details&hellip;</td></tr>');

            // Render helper so we can reuse for server/fallback data
            function renderBagTableRows(bags) {
                const tbody = $('#view-bag-details-body');
                if (!Array.isArray(bags) || bags.length === 0) {
                    tbody.html('<tr><td colspan="4" class="text-center text-muted">No bag details available</td></tr>');
                    return;
                }

                const rows = bags.map((bag, i) => {
                    const bagNum = bag.bag_number ?? bag.bagNumber ?? (i + 1);
                    const volumeRaw = bag.volume ?? bag.vol ?? bag.amount ?? '-';
                    const volume = (volumeRaw || volumeRaw === 0)
                        ? String(volumeRaw)
                        : '-';
                    const date = bag.date ?? bag.collection_date ?? bag.collected_at ?? '-';
                    const rawTime = bag.time ?? bag.collection_time ?? bag.collected_time ?? null;
                    const time = formatTimeDisplay(rawTime);

                    return `
                            <tr>
                                <td style="text-align: center; padding: 12px; font-weight: 600;">${bagNum ?? '-'}</td>
                                <td style="text-align: center; padding: 12px;">${time}</td>
                                <td style="text-align: center; padding: 12px;">${date || '-'}</td>
                                <td style="text-align: center; padding: 12px;"><span style="color: #0d6efd; font-weight: 500;">${volume}</span> ml</td>
                            </tr>
                        `;
                }).join('');

                tbody.html(rows);
            }

            // Show modal immediately for snappy UI, we'll fill content after fetch
            const modalEl = document.getElementById('viewHomeDonationModal');
            const bsModal = new bootstrap.Modal(modalEl, { keyboard: true });
            bsModal.show();

            // Fetch fresh details and overwrite fallbacks when available
            $.ajax({
                url: `/admin/donations/${id}`,
                method: 'GET',
                dataType: 'json',
                success: function (resp) {
                    const donation = resp && resp.donation ? resp.donation : null;
                    if (!donation) {
                        $('#view-donor-name').text(bDonorName || 'N/A');
                        renderBagTableRows(fallbackBagDetails);
                        if (fallbackBagDetails.length) {
                            $('#view-total-bags').text(fallbackBagDetails.length);
                            const fallbackTotal = fallbackBagDetails.reduce((acc, bag) => {
                                const val = parseFloat(bag.volume ?? bag.vol ?? bag.amount ?? 0);
                                return acc + (isNaN(val) ? 0 : val);
                            }, 0);
                            if (fallbackTotal || fallbackTotal === 0) {
                                const fallbackDisplay = fallbackTotal % 1 === 0 ? Math.round(fallbackTotal) : fallbackTotal.toFixed(2);
                                const normalizedFallback = normalizeVolumeValue(fallbackDisplay);
                                $('#view-total-vol').text(normalizedFallback !== '' ? `${normalizedFallback} ml` : '-');
                            } else {
                                $('#view-total-vol').text('-');
                            }
                        }
                        return;
                    }

                    setModalTitle(donation.donation_method || donationMethodAttr);

                    // Donor info
                    const donorName = (donation.user && donation.user.first_name && donation.user.last_name) ? `${donation.user.first_name} ${donation.user.last_name}` : (donation.donor_name || bDonorName || 'N/A');
                    const donorContact = donation.user?.contact_number || donation.user?.phone || donation.contact_number || bContact || '-';
                    const donorAddress = donation.user?.address || donation.address || bAddress || 'Not provided';
                    $('#view-donor-name').text(donorName);
                    $('#view-donor-contact').text(donorContact);
                    $('#view-donor-address').text(donorAddress);

                    // Location logic removed

                    // Totals
                    const bagDetails = Array.isArray(donation.bag_details) ? donation.bag_details : (Array.isArray(donation.bags) ? donation.bags : []);
                    const resolvedBagDetails = bagDetails.length ? bagDetails : fallbackBagDetails;
                    renderBagTableRows(resolvedBagDetails);

                    let bagCount = donation.number_of_bags ?? (bagDetails.length ? bagDetails.length : (fallbackBagDetails.length || bBags || '-'));
                    let totalVol = donation.total_volume ?? donation.formatted_total_volume ?? bTotal ?? '';

                    if ((!totalVol || totalVol === '-') && Array.isArray(resolvedBagDetails) && resolvedBagDetails.length) {
                        const computedTotal = resolvedBagDetails.reduce((acc, bag) => {
                            const val = parseFloat(bag.volume ?? bag.vol ?? bag.amount ?? 0);
                            return acc + (isNaN(val) ? 0 : val);
                        }, 0);
                        if (computedTotal || computedTotal === 0) {
                            totalVol = computedTotal % 1 === 0 ? Math.round(computedTotal) : computedTotal.toFixed(2);
                        }
                    }

                    $('#view-total-bags').text(bagCount || (bagCount === 0 ? 0 : '-'));
                    const normalizedTotal = normalizeVolumeValue(totalVol);
                    $('#view-total-vol').text(normalizedTotal !== '' ? `${normalizedTotal} ml` : '-');
                },
                error: function () {
                    // keep any fallback values already rendered from button data
                    renderBagTableRows(fallbackBagDetails);
                    if (!fallbackBagDetails.length) {
                        $('#view-bag-details-body').empty().append('<tr><td colspan="7" class="text-center text-danger">Failed to load details</td></tr>');
                    }
                    if (fallbackBagDetails.length) {
                        $('#view-total-bags').text(fallbackBagDetails.length);
                        const fallbackTotal = fallbackBagDetails.reduce((acc, bag) => {
                            const val = parseFloat(bag.volume ?? bag.vol ?? bag.amount ?? 0);
                            return acc + (isNaN(val) ? 0 : val);
                        }, 0);
                        if (fallbackTotal || fallbackTotal === 0) {
                            const fallbackDisplay = fallbackTotal % 1 === 0 ? Math.round(fallbackTotal) : fallbackTotal.toFixed(2);
                            const normalizedFallback = normalizeVolumeValue(fallbackDisplay);
                            $('#view-total-vol').text(normalizedFallback !== '' ? `${normalizedFallback} ml` : '-');
                        } else {
                            $('#view-total-vol').text('-');
                        }
                    } else {
                        $('#view-total-bags').text(bBags || '-');
                        const normalizedFallback = normalizeVolumeValue(bTotal);
                        $('#view-total-vol').text(normalizedFallback !== '' ? `${normalizedFallback} ml` : '-');
                    }
                }
            });
        });

        // Helper: format various time strings into a user-friendly 12-hour format
        function formatTimeDisplay(t) {
            if (t === null || t === undefined) return '-';
            const s = String(t).trim();
            if (s === '') return '-';
            // If already contains am/pm, normalize spacing and uppercase
            if (/\b(am|pm)\b/i.test(s)) return s.replace(/\s+/g, ' ').toUpperCase();

            // Match HH:MM or HH:MM:SS (24-hour)
            const m = s.match(/^(\d{1,2}):(\d{2})(?::(\d{2}))?$/);
            if (m) {
                let hh = parseInt(m[1], 10);
                const mm = m[2];
                const ampm = hh >= 12 ? 'PM' : 'AM';
                hh = hh % 12; if (hh === 0) hh = 12;
                return hh + ':' + mm + ' ' + ampm;
            }

            // Try parsing as ISO datetime and extract time portion
            const asDate = new Date(s);
            if (!isNaN(asDate.getTime())) {
                let hh = asDate.getHours();
                const mm = String(asDate.getMinutes()).padStart(2, '0');
                const ampm = hh >= 12 ? 'PM' : 'AM';
                hh = hh % 12; if (hh === 0) hh = 12;
                return hh + ':' + mm + ' ' + ampm;
            }

            // Fallback: return original string
            return s;
        }

        function normalizeVolumeValue(val) {
            if (val === null || val === undefined) return '';
            const str = String(val).trim();
            if (!str) return '';
            return str.replace(/\s*ml$/i, '');
        }
    </script>

    <!-- Archive/restore functionality removed per requirements -->
    <script>
        function declineDonation(id) {
            // Close any open Bootstrap modals first to prevent focus trap blocking textarea typing
            try {
                if (typeof bootstrap !== 'undefined') {
                    document.querySelectorAll('.modal.show').forEach(m => {
                        const inst = bootstrap.Modal.getInstance(m);
                        if (inst) inst.hide(); else m.classList.remove('show');
                    });
                } else if (window.$) {
                    $('.modal.show').modal('hide');
                }
            } catch (e) { /* ignore */ }

            if (typeof Swal !== 'undefined') {
                // Slight delay to allow modal backdrop removal before SweetAlert opens
                setTimeout(() => {
                    Swal.fire({
                        title: 'Decline Donation',
                        input: 'textarea',
                        inputLabel: 'Reason for decline',
                        inputPlaceholder: 'Enter reason/notes...',
                        inputAttributes: {
                            'aria-label': 'Reason for decline',
                            'style': 'min-height:110px;resize:vertical;'
                        },
                        didOpen: (el) => {
                            const ta = el.querySelector('textarea.swal2-textarea');
                            if (ta) { ta.focus(); ta.select(); }
                        },
                        inputValidator: (value) => {
                            if (!value || value.trim() === '') {
                                return 'Please enter a reason.';
                            }
                            if (value.trim().length < 3) {
                                return 'Reason must be at least 3 characters.';
                            }
                            return undefined;
                        },
                        showCancelButton: true,
                        confirmButtonText: 'Decline',
                        confirmButtonColor: '#dc2626'
                    }).then(result => {
                        if (result.isConfirmed) {
                            const reason = (result.value || '').trim();
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
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Declined',
                                            text: data.message || 'Donation declined successfully.'
                                        }).then(() => location.reload());
                                    } else {
                                        Swal.fire('Error', (data && data.message) || 'Failed to decline donation', 'error');
                                    }
                                })
                                .catch(() => Swal.fire('Error', 'Failed to decline donation', 'error'));
                        }
                    });
                }, 50);
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
                    .then(() => location.reload())
                    .catch(() => alert('Failed to decline donation'));
            }
        }
    </script>
    <script>
        // Client-side rounding: snap all ml inputs to nearest 10 on blur/change
        (function () {
            function round10(val) {
                const n = parseFloat(val);
                if (isNaN(n)) return '';
                if (n < 10) return n; // avoid snapping tiny entries to 0
                return Math.round(n / 10) * 10;
            }

            // Walk-in validation individual bag volumes
            $('#walkin-volume-fields')
                .on('change', '.walkin-bag-volume-input', function () {
                    const r = round10(this.value);
                    if (r !== '' && String(r) !== String(this.value)) {
                        this.value = r;
                    }
                    if (typeof window.calculateWalkinTotal === 'function') window.calculateWalkinTotal();
                })
                .on('focusout', '.walkin-bag-volume-input', function () {
                    const r = round10(this.value);
                    if (r !== '' && String(r) !== String(this.value)) {
                        this.value = r;
                        if (typeof window.calculateWalkinTotal === 'function') window.calculateWalkinTotal();
                    }
                });

            // Home collection validation bag volumes (both dynamic containers)
            $('#home-bag-details-body, #home-volume-fields')
                .on('change', '.home-bag-volume-input', function () {
                    const r = round10(this.value);
                    if (r !== '' && String(r) !== String(this.value)) {
                        this.value = r;
                    }
                    if (typeof window.calculateHomeTotal === 'function') window.calculateHomeTotal();
                })
                .on('focusout', '.home-bag-volume-input', function () {
                    const r = round10(this.value);
                    if (r !== '' && String(r) !== String(this.value)) {
                        this.value = r;
                        if (typeof window.calculateHomeTotal === 'function') window.calculateHomeTotal();
                    }
                });

            // Assist Walk-in modal bag volumes
            $('#assist-volume-fields')
                .on('change', '.assist-bag-volume', function () {
                    const r = round10(this.value);
                    if (r !== '' && String(r) !== String(this.value)) {
                        this.value = r;
                    }
                    // Recompute total locally
                    let t = 0; $('#assist-volume-fields .assist-bag-volume').each(function () {
                        const v = parseFloat(this.value || ''); if (!isNaN(v)) t += v;
                    });
                    const disp = (t % 1 === 0) ? Math.round(t) : t.toFixed(2).replace(/\.?0+$/, '');
                    $('#assist-total').text(disp);
                })
                .on('focusout', '.assist-bag-volume', function () {
                    const r = round10(this.value);
                    if (r !== '' && String(r) !== String(this.value)) {
                        this.value = r;
                        let t = 0; $('#assist-volume-fields .assist-bag-volume').each(function () {
                            const v = parseFloat(this.value || ''); if (!isNaN(v)) t += v;
                        });
                        const disp = (t % 1 === 0) ? Math.round(t) : t.toFixed(2).replace(/\.?0+$/, '');
                        $('#assist-total').text(disp);
                    }
                });
        })();
    </script>
    <script>
        // Load and render 10-question Lifestyle Checklist for Schedule modal (Tab 3)
        async function loadScheduleScreening(donationId) {
            const loading = document.getElementById('schedule-screening-loading');
            const box = document.getElementById('schedule-screening-content');
            if (!box || !donationId) return;

            // Clear any existing content first
            box.innerHTML = '';
            if (loading) loading.style.display = 'block';

            try {
                const resp = await fetch(`/admin/donations/${donationId}/screening`, {
                    headers: { 'Accept': 'application/json' }
                });
                if (!resp.ok) {
                    let msg = 'Failed to load lifestyle checklist';
                    if (resp.status === 401) msg = 'Unauthorized. Please log in as admin to view the lifestyle checklist.';
                    if (resp.status === 404) msg = 'Lifestyle checklist not found for this donation.';
                    box.innerHTML = `<div class="alert alert-warning" role="alert">${msg}</div>`;
                    return;
                }
                const data = await resp.json();
                const questions = Array.isArray(data.questions) ? data.questions : [];

                if (!questions.length) {
                    box.innerHTML = '<div class="text-muted">No lifestyle checklist available.</div>';
                    return;
                }

                // Build HTML string instead of DOM manipulation to avoid duplication
                let listHtml = '<div class="list-group">';
                questions.forEach((q, idx) => {
                    const ans = (q && q.answer) ? String(q.answer).toUpperCase() : 'N/A';
                    const color = ans === 'YES' ? 'success' : (ans === 'NO' ? 'danger' : 'secondary');
                    const detailsHtml = q.details ? `<div class="mt-1 small text-muted">Details: ${escapeHtml(String(q.details))}</div>` : '';
                    listHtml += `
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <span>${idx + 1}. ${escapeHtml(q.label || q.key || 'Question')}</span>
                                <span class="badge bg-${color}">${ans}</span>
                            </div>
                            ${detailsHtml}
                        </div>`;
                });
                listHtml += '</div>';
                
                // Set innerHTML once to replace all content
                box.innerHTML = listHtml;
            } catch (e) {
                box.innerHTML = '<div class="alert alert-danger" role="alert">Error loading lifestyle checklist.</div>';
            } finally {
                if (loading) loading.style.display = 'none';
            }
        }

        // Helper function to escape HTML
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
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

        // Edit UI removed per request
    </script>
@endsection