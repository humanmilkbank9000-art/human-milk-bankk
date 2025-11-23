@extends('layouts.admin-layout')

@section('title', 'Admin Health Screening')
@section('pageTitle', 'Health Screening')

@section('styles')
    <style>
        .nav-tabs .nav-link {
            margin-right: 2px;
            font-weight: 500;
            transition: background 0.2s, color 0.2s;
        }

        /* Compact pill-style tabs for Admin Health Screening */
        .nav-tabs.hs-tabs {
            justify-content: flex-start;
            gap: 4px;
            border-bottom: 1px solid #dee2e6;
            flex-wrap: nowrap;
            padding-bottom: 0;
            margin-bottom: 0;
        }

        .nav-tabs.hs-tabs .nav-item {
            flex: 0 0 auto;
        }

        .nav-tabs.hs-tabs .nav-link {
            padding: 0.25rem 0.5rem;
            font-size: 0.85rem;
            border-radius: 0;
            border: none;
            border-bottom: 2px solid transparent;
            line-height: 1.2;
            background: transparent;
            transition: all 0.2s ease;
            font-weight: 500;
        }

        .nav-tabs.hs-tabs .nav-link:hover {
            background: rgba(0, 0, 0, 0.02);
        }

        .nav-tabs.hs-tabs .count-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 16px;
            height: 16px;
            padding: 0 0.3rem;
            border-radius: 9999px;
            font-size: 0.6rem;
            font-weight: 700;
            line-height: 1;
            margin-left: 0.3rem;
        }

        /* inactive colors */
        .nav-tabs.hs-tabs .nav-link.hs-pending {
            color: #6c757d;
        }

        .nav-tabs.hs-tabs .nav-link.hs-accepted {
            color: #6c757d;
        }

        .nav-tabs.hs-tabs .nav-link.hs-declined {
            color: #6c757d;
        }

        /* active fills - remove background, use border-bottom */
        .nav-tabs.hs-tabs .nav-link.active.hs-pending {
            background-color: transparent;
            color: #ffc107;
            border-bottom-color: #ffc107;
        }

        .nav-tabs.hs-tabs .nav-link.active.hs-accepted {
            background-color: transparent;
            color: #198754;
            border-bottom-color: #198754;
        }

        .nav-tabs.hs-tabs .nav-link.active.hs-declined {
            background-color: transparent;
            color: #dc3545;
            border-bottom-color: #dc3545;
        }

        /* count bubble colors (both active/inactive) */
        .nav-tabs.hs-tabs .nav-link.hs-pending .count-badge {
            background: #ffc107;
            color: #111827;
        }

        .nav-tabs.hs-tabs .nav-link.hs-accepted .count-badge {
            background: #198754;
            color: #fff;
        }

        .nav-tabs.hs-tabs .nav-link.hs-declined .count-badge {
            background: #dc3545;
            color: #fff;
        }

        .nav-tabs .nav-link.active {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .nav-tabs .nav-link .badge {
            font-size: 0.75rem;
            padding: 0.25em 0.5em;
            font-weight: 600;
            vertical-align: middle;
        }

        .table thead th {
            background: #f8fafc;
            font-weight: 600;
            font-size: 1rem;
            border-bottom: 2px solid #eaeaea;
            padding: 1rem 1.5rem;
        }

        .table tbody tr {
            transition: box-shadow 0.2s, background 0.2s;
            /* Archive button styles removed */
            /* .hs-archive-btn {
                        background: #f8f9fa;
                        border: 1px solid #dee2e6;
                        color: #6c757d;
                        padding: 0.375rem 0.75rem;
                        border-radius: 6px;
                    }

                    .hs-archive-btn:hover {
                        background: #f1f3f5;
                        color: #495057;
                    }

                    .hs-archive-btn i {
                        margin-right: 0.25rem;
                    } */
        }

        .card-header {
            border-radius: 12px 12px 0 0;
            font-size: 1.1rem;
        }

        .table-responsive {
            border-radius: 8px;
        }

        .btn {
            font-size: 0.95rem;
            border-radius: 6px;
        }

        /* Card-based responsive layout for smaller screens */
        @media (max-width: 1400px) {
            .table-responsive table {
                display: none !important;
            }

            .responsive-card {
                display: block !important;
                border: 1px solid #dee2e6;
                border-radius: 8px;
                padding: 1rem;
                margin-bottom: 1rem;
                background: white;
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            }

            .responsive-card .card-row {
                display: flex;
                justify-content: space-between;
                padding: 0.5rem 0;
                border-bottom: 1px solid #f0f0f0;
            }

            .responsive-card .card-row:last-child {
                border-bottom: none;
            }

            .responsive-card .card-label {
                font-weight: 600;
                color: #495057;
                font-size: 0.9rem;
            }

            .responsive-card .card-value {
                text-align: right;
                color: #212529;
                font-size: 0.9rem;
            }

            .responsive-card .card-actions {
                margin-top: 0.75rem;
                padding-top: 0.75rem;
                border-top: 2px solid #e9ecef;
                text-align: center;
            }
        }

        @media (min-width: 1401px) {
            .responsive-card {
                display: none !important;
            }
        }

        .tab-content>.tab-pane {
            padding-top: 0.5rem;
        }

        /* Modal Stability Fixes */
        .modal {
            z-index: 1050 !important;
        }

        .modal-backdrop {
            z-index: 1040 !important;
        }

        .modal-dialog {
            pointer-events: all;
        }

        .modal-content {
            position: relative;
            display: flex;
            flex-direction: column;
            pointer-events: auto;
            background-color: #fff;
            background-clip: padding-box;
            border: 1px solid rgba(0, 0, 0, .2);
            border-radius: 12px !important;
            outline: 0;
            overflow: hidden;
        }

        .modal-header {
            border-top-left-radius: 12px !important;
            border-top-right-radius: 12px !important;
        }

        .modal-body {
            position: relative;
            flex: 1 1 auto;
            padding: 1rem;
        }

        .modal.fade .modal-dialog {
            transition: transform .3s ease-out;
        }

        .modal.show .modal-dialog {
            transform: none;
        }

        /* Prevent modal flickering on hover */
        .modal *,
        .modal *:before,
        .modal *:after {
            pointer-events: auto;
        }

        .translation {
            font-style: italic;
            color: #666;
            font-size: 0.9rem;
            margin-top: 2px;
        }

        .modal-body h6 {
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .modal-body .bg-light {
            background-color: #f8f9fa !important;
        }

        .modal-body .border-bottom {
            border-bottom: 2px solid #dee2e6 !important;
        }

        .admin-comments-textarea {
            border-radius: 8px !important;
            border: 1px solid #d1d5db;
            padding: 12px;
            background: #f9fafb;
            font-family: inherit;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            outline: none;
            line-height: 1.5;
        }

        .admin-comments-textarea:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.1);
            background: white;
        }

        .admin-comments-textarea::placeholder {
            color: #9ca3af;
        }

        @media (max-width: 768px) {
            .nav-tabs {
                flex-wrap: nowrap;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
                scrollbar-width: thin;
            }

            .nav-tabs .nav-item {
                flex-shrink: 0;
            }

            .nav-tabs .nav-link {
                font-size: 0.85rem;
                padding: 0.5rem 0.75rem;
                white-space: nowrap;
            }

            .nav-tabs .nav-link .badge {
                font-size: 0.65rem;
                padding: 0.2em 0.4em;
                margin-left: 0.25rem;
            }

            .table-responsive {
                font-size: 0.95rem;
            }

            .card-header {
                font-size: 1rem;
            }

            .modal-dialog {
                margin: 0.5rem;
            }
        }

        /* Extra small devices */
        @media (max-width: 576px) {
            .nav-tabs .nav-link {
                font-size: 0.8rem;
                padding: 0.4rem 0.6rem;
            }

            .nav-tabs .nav-link .badge {
                font-size: 0.6rem;
                padding: 0.15em 0.35em;
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

        /* Health Screening Action Buttons - match provided screenshot */
        .admin-review-btn {
            background: var(--primary-color);
            color: #fff;
            border: none;
            display: inline-flex;
            align-items: center;
            padding: 0.45rem 0.7rem;
            border-radius: 8px;
            box-shadow: 0 1px 0 rgba(0, 0, 0, 0.06);
        }

        .admin-review-btn:hover {
            filter: brightness(0.95);
            color: #fff;
        }

        .admin-review-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
            background: #fff;
            /* white circle */
            color: #000;
            /* black icon */
            border-radius: 50%;
            margin-right: 0.55rem;
            font-size: 0.95rem;
        }

        .hs-archive-btn {
            background: #d73b4b;
            /* red */
            color: #fff;
            border: none;
            padding: 0.45rem 0.8rem;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .hs-archive-btn i {
            margin-right: 0.45rem;
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
    </style>
    <link rel="stylesheet" href="{{ asset('css/responsive-tables.css') }}">
@endsection

@section('content')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <!-- Restore functionality removed per requirements -->

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Accept health screening with SweetAlert confirmation
        function acceptScreening(screeningId) {
            const commentsEl = document.getElementById('adminComments' + screeningId);
            const comments = commentsEl ? commentsEl.value : '';

            Swal.fire({
                title: 'Accept Health Screening?',
                text: "Are you sure you want to accept this health screening? The user will be notified.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="bi bi-check-circle me-1"></i> Yes, Accept',
                cancelButtonText: 'Cancel',
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    // Create form data
                    const formData = new FormData();
                    formData.append('_token', '{{ csrf_token() }}');
                    formData.append('_method', 'POST');
                    if (comments) {
                        formData.append('comments', comments);
                    }

                    // Submit via fetch
                    return fetch(`/admin/health-screening/${screeningId}/accept`, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: formData
                    })
                        .then(response => {
                            if (!response.ok) {
                                return response.text().then(text => {
                                    throw new Error(text || 'Network response was not ok');
                                });
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
                    Swal.fire({
                        title: 'Accepted!',
                        text: 'The health screening has been accepted. The user will be notified.',
                        icon: 'success',
                        confirmButtonColor: '#28a745',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        window.location.reload();
                    });
                }
            });
        }

        // Decline health screening with SweetAlert confirmation
        function declineScreening(screeningId) {
            const commentsEl = document.getElementById('adminComments' + screeningId);
            const comments = commentsEl ? commentsEl.value : '';

            // Client-side enforcement: require comments before declining
            if (!comments || !comments.trim()) {
                Swal.fire({
                    title: 'Comments required',
                    text: 'Please enter comments or notes explaining why you are declining this health screening.',
                    icon: 'warning',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#dc3545'
                }).then(() => {
                    // focus the comments input for convenience
                    if (commentsEl) {
                        commentsEl.focus();
                        commentsEl.classList.add('border', 'border-danger');
                    }
                });

                return; // stop here
            }

            Swal.fire({
                title: 'Decline Health Screening?',
                text: "Are you sure you want to decline this health screening? The user will be notified.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="bi bi-x-circle me-1"></i> Yes, Decline',
                cancelButtonText: 'Cancel',
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    // Create form data
                    const formData = new FormData();
                    formData.append('_token', '{{ csrf_token() }}');
                    formData.append('_method', 'POST');
                    if (comments) {
                        formData.append('comments', comments);
                    }

                    // Submit via fetch
                    return fetch(`/admin/health-screening/${screeningId}/reject`, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: formData
                    })
                        .then(response => {
                            if (!response.ok) {
                                return response.text().then(text => {
                                    throw new Error(text || 'Network response was not ok');
                                });
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
                    Swal.fire({
                        title: 'Declined!',
                        text: 'The health screening has been declined. The user will be notified.',
                        icon: 'success',
                        confirmButtonColor: '#28a745',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        window.location.reload();
                    });
                }
            });
        }

        // Undo decline health screening with SweetAlert confirmation
        function undoDeclineScreening(screeningId) {
            Swal.fire({
                title: 'Undo Decline & Accept?',
                text: "This will reverse the declined status and mark this health screening as accepted. The user will be notified that their screening is now accepted.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="bi bi-arrow-counterclockwise me-1"></i> Yes, Accept Now',
                cancelButtonText: 'Cancel',
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    // Create form data
                    const formData = new FormData();
                    formData.append('_token', '{{ csrf_token() }}');
                    formData.append('_method', 'POST');

                    // Submit via fetch
                    return fetch(`/admin/health-screening/${screeningId}/undo-decline`, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: formData
                    })
                        .then(response => {
                            if (!response.ok) {
                                return response.json().then(data => {
                                    throw new Error(data.message || 'Failed to undo decline');
                                });
                            }
                            return response.json();
                        })
                        .catch(error => {
                            Swal.showValidationMessage(`Request failed: ${error.message || error}`);
                        });
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Accepted!',
                        text: 'The health screening has been accepted. The user will be notified.',
                        icon: 'success',
                        confirmButtonColor: '#28a745',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        window.location.reload();
                    });
                }
            });
        }
    </script>

    @php
        $sections = [
            'medical_history' => [
                ['Have you donated breastmilk before?', 'Nakahatag/naka-donar ka na ba sa imung gatas kaniadto?', false],
                ['Have you for any reason been deferred as a breastmilk donor? If yes, specify reason', 'Naballbaran na ba ka nga mag-donar sa imung gatas kaniadto? Kung oo, unsay hinungdan?', true],
                ['Did you have a normal pregnancy and delivery for your most recent pregnancy?', 'Wala ka bay naaging mnga kalisod og komplikasyon sa pinakaulahi nimung pagburos og pagpanganak?', false],
                ['Do you have any acute or chronic infection such as tuberculosis, hepatitis, systemic disorders? If yes, specify', 'Aduna ka bay gibating mga sakit sama sa Tuberculosis, sakit sa atay or sakit sa dugo? Kung naa, unsa man kini?', true],
                ['Have you been diagnosed with a chronic non-infectious illness such as diabetes, hypertension, heart disease? If yes, specify', 'Nadayagnos ka ba nga adunay laygay nga dili makatakod nga sakit sama sa diabetes, altapresyon, sakit sa kasingkasing? Kung naa, unsa man kini?', true],
                ['Have you received any blood transfusion or blood products within the last 12 months?', 'Naabunohan ka ba ug dugo sulod sa niaging 12 ka buwan?', false],
                ['Have you received any organ or tissue transplant within the last 12 months?', 'Niagi ka ba ug operasyon din nidawat ka ug bahin/parte sa lawas sulod sa nlilabay nga 12 ka bulan?', false],
                ['Have you had any intake of alcohol within the last 24 hours? If yes, how much', 'Sulod sa 24 oras, naka inum ka ba og bisan unsang ilimnong makahubog? Kung oo, unsa ka daghan?', true],
                ['Do you use megadose vitamins or pharmacologically active herbal preparations?', 'Gainum ka ba og sobra sa gitakda na mga bitamina og mga produktong adunay sagol na herbal?', false],
                ['Do you regularly use medications such as hormones, antidiabetics, blood thinners? If yes, specify', 'Kanunay ba ka gagamit o gainum sa mga tambal kung lain ang paminaw sa lawas? Og gainum ka ba sa mga tambal pampugong sa pagburos? Kung oo, unsa ngalan sa tambal?', true],
                ['Are you a total vegetarian/vegan? If yes, do you supplement with vitamins', 'Ikaw ba dili gakaon sa lain pagkaon kundi utan lang? Kung oo, gainum ka ba mga bitamina?', true],
                ['Do you use illicit drugs?', 'Gagamit ka ba sa ginadilina mga droga?', false],
                ['Do you smoke? If yes, how many per day', 'Gapanigarilyo ka ba? Kung oo, pila ka "stick" o pack se ise ka adlaw?', true],
                ['Are you around people who smoke (passive smoking)?', 'Doul ba ka permi sa mga tao nga gapanigarilyo?', false],
                ['Have you had breast augmentation surgery using silicone implants?', 'Kaw ba niagi ug operasyon sa imung suso din nagpabutang ug "silicone" O artipisyal na suso?', false],
            ],
            'sexual_history' => [
                ['Have you ever had Syphilis, HIV, herpes or any STD?', 'Niagi ka ba og bisan unsang sakit sa kinatawo? Sakit na makuha pinaagi sa pakighilawas?', false],
                ['Do you have multiple sexual partners?', 'Aduna ka bay lain pares sa pakighilawas gawas sa imu bana/kapikas?', false],
                ['Have you had a sexual partner with risk factors (bisexual, promiscuous, STD, blood transfusion, IV drug use)? If yes, specify', 'Niagi ka ba og pakighilawas ning mga mosunod? Kung oo, specify', true],
                ['Have you had a tattoo, accidental needlestick or contact with someone else\'s blood? If yes, specify', 'Niagi ka ba og papatik sukad? Niagi ka ba og katusok sa bisan unsang dagom? Kung oo, specify', true],
            ],
            'donor_infant' => [
                ['Is your child healthy?', 'Himsog ba ang imung anak?', false],
                ['Was your child delivered full term?', 'Gipanganak ba siya sa saktong buwan?', false],
                ['Are you exclusively breastfeeding your child?', 'Kaugalingong gatas lang ba nimu ang gipalnum sa bata?', false],
                ['Is/was your youngest child jaundiced? If yes, specify age and duration', 'Imung kinamanghuran na bata ba niagi og pagdalag sa pamanit? Kung oo, pilay edad sa bata ato nga higayon? Ug unsa ang kadugayon sa pagdalag?', true],
                ['Have you ever received breastmilk from another mother? If yes, specify when', 'Nakadawat ba ang imung anak og gatas sa laing inahan? Kung oo, kanus.a kini nahtabo?', true],
            ]
        ];
    @endphp
    <div class="container-fluid px-2 px-md-4">

        {{-- Tabs for statuses; clicking reloads page with status query param so controller provides matching rows --}}
        <ul class="nav nav-tabs hs-tabs mb-3" role="tablist">
            <li class="nav-item">
                <a class="nav-link hs-pending {{ $status == 'pending' ? 'active' : '' }}"
                    href="{{ route('admin.health-screening', ['status' => 'pending']) }}">
                    Pending
                    <span class="count-badge badge bg-warning text-dark ms-1">{{ $pendingCount }}</span>
                </a>
            </li>
            <!-- Archived tab removed per requirements -->
            <li class="nav-item">
                <a class="nav-link hs-accepted {{ $status == 'accepted' ? 'active' : '' }}"
                    href="{{ route('admin.health-screening', ['status' => 'accepted']) }}">
                    Accepted
                    <span class="count-badge">{{ $acceptedCount }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link hs-declined {{ $status == 'declined' ? 'active' : '' }}"
                    href="{{ route('admin.health-screening', ['status' => 'declined']) }}">
                    Rejected
                    <span class="count-badge">{{ $declinedCount }}</span>
                </a>
            </li>

        </ul>

        {{-- Search Input Below Tabs --}}
        <div class="mb-3">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0">
                    <i class="bi bi-search"></i>
                </span>
                <input type="text" class="form-control border-start-0 ps-0" id="searchInput"
                    placeholder="Search name or contact number" aria-label="Search health screenings">
                <button class="btn btn-outline-secondary" type="button" id="clearSearch" style="display: none;">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <small class="text-muted d-block mt-1">
                <span id="searchResults"></span>
            </small>
        </div>

        @if($healthScreenings->isEmpty())
            <div class="alert alert-info">No health screenings found for status <strong>{{ ucfirst($status) }}</strong>.</div>
        @else
                <div class="card mt-3 shadow-sm rounded-lg border-0">
                    <div class="card-header bg-primary text-white rounded-top">
                        <h5 class="mb-0">{{ ucfirst($status) }} Health Screenings</h5>
                    </div>
                    <div class="card-body py-3">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped align-middle shadow-sm rounded"
                                style="min-width: 900px;">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-center px-4 py-3" style="width: 20%;">Name</th>
                                        <th class="text-center px-4 py-3" style="width: 18%">Contact Number</th>
                                        <th class="text-center px-4 py-3" style="width: 18%">Date and Time Submitted</th>
                                        @if($status == 'accepted')
                                            <th class="text-center px-4 py-3" style="width: 18%">Date and Time Accepted</th>
                                        @elseif($status == 'declined')
                                            <th class="text-center px-4 py-3" style="width: 18%">Date and Time Declined</th>
                                        @endif
                                        <th class="text-center px-4 py-3" style="width: 12%">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        // For pending tab, show oldest-first (first-to-submit on top). Keep newest-first for other statuses.
                                        if ($healthScreenings instanceof \Illuminate\Pagination\LengthAwarePaginator) {
                                            $screeningsOrdered = ($status === 'pending')
                                                ? $healthScreenings->getCollection()->sortBy('created_at')
                                                : $healthScreenings->getCollection()->sortByDesc('created_at');
                                        } else {
                                            $screeningsOrdered = ($status === 'pending')
                                                ? collect($healthScreenings)->sortBy('created_at')
                                                : collect($healthScreenings)->sortByDesc('created_at');
                                        }
                                    @endphp
                                    @foreach($screeningsOrdered as $index => $screening)
                                        <tr>
                                            <td>{{ $screening->user->first_name ?? '-' }} {{ $screening->user->last_name ?? '' }}</td>
                                            <td>{{ $screening->user->contact_number ?? '-' }}</td>
                                            <td>
                                                <span
                                                    title="{{ optional($screening->created_at)->setTimezone('Asia/Manila')->format('M d, Y h:i A') }}">
                                                    <strong>{{ optional($screening->created_at)->setTimezone('Asia/Manila')->format('M d, Y') }}</strong>
                                                    <span class="text-muted"
                                                        style="font-size:0.97em;">&nbsp;{{ optional($screening->created_at)->setTimezone('Asia/Manila')->format('h:i A') }}</span>
                                                </span>
                                            </td>
                                            @if($status == 'accepted')
                                                <td>
                                                    @if(!empty($screening->date_accepted))
                                                        <span
                                                            title="{{ optional($screening->date_accepted)->setTimezone('Asia/Manila')->format('M d, Y h:i A') }}">
                                                            <strong>{{ optional($screening->date_accepted)->setTimezone('Asia/Manila')->format('M d, Y') }}</strong>
                                                            <span class="text-muted"
                                                                style="font-size:0.97em;">&nbsp;{{ optional($screening->date_accepted)->setTimezone('Asia/Manila')->format('h:i A') }}</span>
                                                        </span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                            @elseif($status == 'declined')
                                                <td>
                                                    @if(!empty($screening->date_declined))
                                                        <span
                                                            title="{{ optional($screening->date_declined)->setTimezone('Asia/Manila')->format('M d, Y h:i A') }}">
                                                            <strong>{{ optional($screening->date_declined)->setTimezone('Asia/Manila')->format('M d, Y') }}</strong>
                                                            <span class="text-muted"
                                                                style="font-size:0.97em;">&nbsp;{{ optional($screening->date_declined)->setTimezone('Asia/Manila')->format('h:i A') }}</span>
                                                        </span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                            @endif
                                            <td class="text-center align-middle">
                                                <div class="d-inline-flex align-items-center" style="gap:0.5rem;">
                                                    <button class="admin-review-btn" data-bs-toggle="modal"
                                                        data-bs-target="#detailsModal{{ $screening->health_screening_id }}">
                                                        Review
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Pagination --}}
                        <div class="d-flex justify-content-center mt-4">
                            {{ $healthScreenings->links() }}
                        </div>

                        {{-- Card Layout for Smaller Screens --}}
                        @foreach($screeningsOrdered as $index => $screening)
                            <div class="responsive-card" style="display: none;">
                                <div class="card-row">
                                    <span class="card-label">Name:</span>
                                    <span class="card-value"><strong>{{ $screening->user->first_name ?? '-' }}
                                            {{ $screening->user->last_name ?? '' }}</strong></span>
                                </div>
                                <div class="card-row">
                                    <span class="card-label">Contact Number:</span>
                                    <span class="card-value">{{ $screening->user->contact_number ?? '-' }}</span>
                                </div>
                                <div class="card-row">
                                    <span class="card-label">Date Submitted:</span>
                                    <span class="card-value">
                                        <strong>{{ optional($screening->created_at)->setTimezone('Asia/Manila')->format('M d, Y') }}</strong><br>
                                        <small
                                            class="text-muted">{{ optional($screening->created_at)->setTimezone('Asia/Manila')->format('h:i A') }}</small>
                                    </span>
                                </div>
                                @if($status == 'accepted' && !empty($screening->date_accepted))
                                    <div class="card-row">
                                        <span class="card-label">Date Accepted:</span>
                                        <span class="card-value">
                                            <strong>{{ optional($screening->date_accepted)->setTimezone('Asia/Manila')->format('M d, Y') }}</strong><br>
                                            <small
                                                class="text-muted">{{ optional($screening->date_accepted)->setTimezone('Asia/Manila')->format('h:i A') }}</small>
                                        </span>
                                    </div>
                                @elseif($status == 'declined' && !empty($screening->date_declined))
                                    <div class="card-row">
                                        <span class="card-label">Date Declined:</span>
                                        <span class="card-value">
                                            <strong>{{ optional($screening->date_declined)->setTimezone('Asia/Manila')->format('M d, Y') }}</strong><br>
                                            <small
                                                class="text-muted">{{ optional($screening->date_declined)->setTimezone('Asia/Manila')->format('h:i A') }}</small>
                                        </span>
                                    </div>
                                @endif
                                <div class="card-actions">
                                    <div class="d-inline-flex align-items-center" style="gap:0.5rem;">
                                        <button class="admin-review-btn" data-bs-toggle="modal"
                                            data-bs-target="#detailsModal{{ $screening->health_screening_id }}">
                                            Review
                                        </button>

                                    </div>
                                </div>
                            </div>
                        @endforeach
                        </table>
                    </div>
                </div>
            </div>

            {{-- Modals for each health screening --}}
            @foreach($screeningsOrdered as $screening)
                <div class="modal fade" id="detailsModal{{ $screening->health_screening_id }}" tabindex="-1"
                    aria-labelledby="detailsModalLabel{{ $screening->health_screening_id }}" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                        <div class="modal-content">
                            <div class="modal-header bg-primary text-white">
                                <h5 class="modal-title" id="detailsModalLabel{{ $screening->health_screening_id }}">Health Screening
                                    Details</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                {{-- User Information --}}
                                <div class="mb-4">
                                    <h6 class="text-primary border-bottom pb-2 mb-3"><i class="bi bi-person-fill me-2"></i>User
                                        Information</h6>
                                    @if($screening->user)
                                        <div class="row">
                                            <div class="col-md-6 mb-2">
                                                <strong>Name:</strong> {{ $screening->user->first_name }}
                                                {{ $screening->user->last_name }}
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <strong>Contact Number:</strong> {{ $screening->user->contact_number }}
                                            </div>
                                            @php
                                                $userDob = $screening->user->date_of_birth ?? null;
                                            @endphp
                                            <div class="col-md-6 mb-2">
                                                <strong>Date of Birth:</strong>
                                                <span>
                                                    {{ $userDob ? \Carbon\Carbon::parse($userDob)->format('M d, Y') : '-' }}
                                                </span>
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <strong>Age:</strong> {{ $screening->user->age }}
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <strong>Sex:</strong> {{ ucfirst($screening->user->sex) }}
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <strong>Civil Status:</strong> {{ ucfirst($screening->civil_status ?? 'N/A') }}
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <strong>Occupation:</strong> {{ $screening->occupation ?? 'N/A' }}
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <strong>Type of Donor:</strong>
                                                {{ ucwords(str_replace('_', ' ', $screening->type_of_donor ?? 'N/A')) }}
                                            </div>
                                            <div class="col-12 mb-2">
                                                <strong>Address:</strong> {{ $screening->user->address }}
                                            </div>
                                        </div>
                                    @else
                                        <p class="text-muted">No user data found.</p>
                                    @endif
                                </div>

                                {{-- Infant Information --}}
                                <div class="mb-4">
                                    <h6 class="text-primary border-bottom pb-2 mb-3"><i class="bi bi-heart-fill me-2"></i>Infant
                                        Information</h6>
                                    @if($screening->infant)
                                        <div class="row">
                                            <div class="col-md-6 mb-2">
                                                <strong>Name:</strong> {{ $screening->infant->first_name }}
                                                {{ $screening->infant->last_name }}{{ $screening->infant->suffix ? ' ' . $screening->infant->suffix : '' }}
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <strong>Sex:</strong> {{ ucfirst($screening->infant->sex) }}
                                            </div>
                                            @php
                                                $infantDob = $screening->infant->date_of_birth ?? null;
                                            @endphp
                                            <div class="col-md-6 mb-2">
                                                <strong>Date of Birth:</strong>
                                                <span>
                                                    {{ $infantDob ? \Carbon\Carbon::parse($infantDob)->format('M d, Y') : '-' }}
                                                </span>
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <strong>Age:</strong> {{ $screening->infant->getFormattedAge() }}
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <strong>Birth Weight:</strong> {{ $screening->infant->birth_weight }} kg
                                            </div>
                                        </div>
                                    @else
                                        <p class="text-muted">No infant data found.</p>
                                    @endif
                                </div>

                                {{-- Screening Answers --}}
                                <div class="mb-3">
                                    <h6 class="text-primary border-bottom pb-2 mb-3"><i
                                            class="bi bi-clipboard-check-fill me-2"></i>Health Screening Answers</h6>

                                    @foreach($sections as $sectionKey => $questions)
                                        <div class="mb-4">
                                            <h6 class="text-secondary mb-3">{{ ucwords(str_replace('_', ' ', $sectionKey)) }}</h6>
                                            @php $qNum = 1; @endphp
                                            @foreach($questions as $q)
                                                @php
                                                    $field = $sectionKey . '_' . str_pad($qNum, 2, '0', STR_PAD_LEFT);
                                                    $value = $screening->{$field} ?? '';
                                                    $details = $screening->{$field . '_details'} ?? '';
                                                @endphp
                                                <div class="mb-3 p-3 bg-light rounded">
                                                    <div class="mb-1">
                                                        <strong>{{ $qNum }}.</strong> {{ $q[0] }}
                                                    </div>
                                                    <div class="translation text-muted mb-2" style="font-size: 0.9rem;">
                                                        <em>{{ $q[1] }}</em>
                                                    </div>
                                                    <div>
                                                        <span
                                                            class="badge bg-{{ $value == 'yes' ? 'success' : ($value == 'no' ? 'secondary' : 'light text-dark') }} px-3 py-2">
                                                            {{ $value ? ucfirst($value) : 'N/A' }}
                                                        </span>
                                                        @if($details)
                                                            <div class="mt-2 p-2 bg-white rounded border">
                                                                <strong>Details:</strong> {{ $details }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                                @php $qNum++; @endphp
                                            @endforeach
                                        </div>
                                    @endforeach
                                </div>

                                {{-- Submission Status --}}
                                <div class="mb-3">
                                    <h6 class="text-primary border-bottom pb-2 mb-3"><i
                                            class="bi bi-info-circle-fill me-2"></i>Submission Status</h6>
                                    <div class="row">
                                        <div class="col-md-6 mb-2 d-flex align-items-center">
                                            <strong class="me-2 mb-0">Status:</strong>
                                            <span
                                                class="badge bg-{{ $screening->status == 'accepted' ? 'success' : ($screening->status == 'declined' ? 'danger' : 'warning text-dark') }}">
                                                {{ ucfirst($screening->status) }}
                                            </span>
                                        </div>
                                        <div class="col-md-6 mb-2 d-flex align-items-center">
                                            <strong class="me-2 mb-0">Submitted:</strong>
                                            <span>{{ optional($screening->created_at)->setTimezone('Asia/Manila')->format('M d, Y h:i A') }}</span>
                                        </div> @if($screening->status == 'accepted' && $screening->date_accepted)
                                            <div class="col-md-6 mb-2 d-flex align-items-center">
                                                <strong class="me-2 mb-0">Accepted At:</strong>
                                                <span>{{ optional($screening->date_accepted)->setTimezone('Asia/Manila')->format('M d, Y h:i A') }}</span>
                                        </div> @endif @if($screening->status == 'declined' && $screening->date_declined)
                                            <div class="col-md-6 mb-2 d-flex align-items-center">
                                                <strong class="me-2 mb-0">Declined At:</strong>
                                                <span>{{ optional($screening->date_declined)->setTimezone('Asia/Manila')->format('M d, Y h:i A') }}</span>
                                        </div> @endif @if(!empty($screening->admin_notes))
                                            <div class="col-12 mb-2 mt-2">
                                                <strong>Admin Comments:</strong>
                                                <div class="mt-2 p-3 bg-light border rounded">
                                                    {{ $screening->admin_notes }}
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                {{-- Admin Comments/Notes Section --}}
                                @if($screening->status == 'pending')
                                    <div class="mb-4">
                                        <h6 class="text-primary border-bottom pb-2 mb-3"><i class="bi bi-chat-left-text-fill me-2"></i>Admin
                                            Action</h6>
                                        <div class="row">
                                            <div class="col-12 mb-2">
                                                <textarea class="form-control admin-comments-textarea rounded mt-2"
                                                    id="adminComments{{ $screening->health_screening_id }}" name="comments" rows="3"
                                                    placeholder="Enter comments or notes (required for declining, optional for accepting)"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                @if($screening->status == 'pending')
                                    {{-- Keep accept/decline actions for pending items only; archive/restore handled
                                    in archived tab --}}
                                    <button type="button" class="btn btn-danger"
                                        onclick="declineScreening({{ $screening->health_screening_id }})">
                                        <i class="bi bi-x-circle me-1"></i> Decline
                                    </button>
                                    <button type="button" class="btn btn-success"
                                        onclick="acceptScreening({{ $screening->health_screening_id }})">
                                        <i class="bi bi-check-circle me-1"></i> Accept
                                    </button>
                                @elseif($screening->status == 'declined')
                                    {{-- Allow undo/accept when declined --}}
                                    <button type="button" class="btn btn-success"
                                        onclick="undoDeclineScreening({{ $screening->health_screening_id }})">
                                        <i class="bi bi-arrow-counterclockwise me-1"></i> Undo & Accept
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>

    {{-- Real-time Search Functionality for Health Screening --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('searchInput');
            const clearBtn = document.getElementById('clearSearch');
            const searchResults = document.getElementById('searchResults');

            if (!searchInput) return;

            // Helpers to extract searchable text from table rows and responsive cards
            function extractRowFields(row) {
                // Include Name and Contact cells for searching
                const cells = row.querySelectorAll('td');
                if (cells.length === 0) return '';

                // First cell is Name, second is Contact Number
                const nameText = cells[0] ? cells[0].textContent.trim() : '';
                const contactText = cells[1] ? cells[1].textContent.trim() : '';
                return (nameText + ' ' + contactText).toLowerCase();
            }

            function extractCardFields(card) {
                // Mobile card: pull Name and Contact rows
                const rows = card.querySelectorAll('.card-row');
                let nameText = '';
                let contactText = '';

                rows.forEach(row => {
                    const label = row.querySelector('.card-label');
                    const value = row.querySelector('.card-value');
                    if (label && value) {
                        const labelTxt = label.textContent.toLowerCase();
                        if (labelTxt.includes('name')) {
                            nameText = value.textContent.trim();
                        } else if (labelTxt.includes('contact')) {
                            contactText = value.textContent.trim();
                        }
                    }
                });
                return (nameText + ' ' + contactText).toLowerCase();
            }

            function isVisible(el) {
                if (!el) return false;
                return !!(el.offsetWidth || el.offsetHeight || el.getClientRects().length);
            }

            function getAllTableRows() {
                const rows = [];
                document.querySelectorAll('.table tbody tr').forEach(r => rows.push(r));
                return rows;
            }

            function getAllCards() {
                return Array.from(document.querySelectorAll('.responsive-card'));
            }

            function performSearch() {
                const term = searchInput.value.trim().toLowerCase();
                let totalCount = 0;
                let visibleCount = 0;

                const rows = getAllTableRows();
                const cards = getAllCards();

                // Get total count from all rows/cards
                totalCount = rows.length + cards.length;

                // If no term, restore all rows/cards
                if (!term) {
                    rows.forEach(row => { row.style.display = ''; });
                    cards.forEach(card => { card.style.display = ''; });

                    clearBtn.style.display = 'none';
                    searchResults.textContent = '';
                    searchResults.classList.remove('text-danger');
                    return;
                }

                // With a search term: hide all rows/cards by default, show only matches
                rows.forEach(row => {
                    const hay = extractRowFields(row);
                    if (hay.indexOf(term) !== -1) {
                        row.style.removeProperty('display');
                        visibleCount++;
                    } else {
                        row.style.setProperty('display', 'none', 'important');
                    }
                });

                // Handle responsive-card blocks (mobile view)
                cards.forEach(card => {
                    const hay = extractCardFields(card);
                    if (hay.indexOf(term) !== -1) {
                        card.style.removeProperty('display');
                        visibleCount++;
                    } else {
                        card.style.setProperty('display', 'none', 'important');
                    }
                });

                // Update UI
                clearBtn.style.display = 'inline-block';
                searchResults.textContent = `Showing ${visibleCount} of ${totalCount} results`;
                if (visibleCount === 0) {
                    searchResults.textContent = 'No results found';
                    searchResults.classList.add('text-danger');
                } else {
                    searchResults.classList.remove('text-danger');
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
@endsection