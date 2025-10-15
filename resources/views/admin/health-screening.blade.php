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
        }

        .table-responsive {
            border-radius: 8px;
            overflow: hidden;
        }

        .btn {
            font-size: 0.95rem;
            border-radius: 6px;
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

    {{-- Real-time Search Functionality --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('searchInput');
            const clearBtn = document.getElementById('clearSearch');
            const searchResults = document.getElementById('searchResults');
            const tableBody = document.querySelector('.table tbody');
            const noDataAlert = document.querySelector('.alert-info');
            
            if (!searchInput || !tableBody) return;

            const allRows = Array.from(tableBody.querySelectorAll('tr'));
            const totalCount = allRows.length;

            // Real-time search function
            function performSearch() {
                const searchTerm = searchInput.value.toLowerCase().trim();
                let visibleCount = 0;

                if (searchTerm === '') {
                    // Show all rows
                    allRows.forEach(row => row.style.display = '');
                    clearBtn.style.display = 'none';
                    searchResults.textContent = '';
                    return;
                }

                // Filter rows
                allRows.forEach(row => {
                    const name = row.cells[0]?.textContent.toLowerCase() || '';
                    const contact = row.cells[1]?.textContent.toLowerCase() || '';
                    const dateTime = row.cells[2]?.textContent.toLowerCase() || '';
                    
                    const matches = name.includes(searchTerm) || 
                                  contact.includes(searchTerm) || 
                                  dateTime.includes(searchTerm);

                    if (matches) {
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
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

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Handle form submissions with SweetAlert
            document.querySelectorAll('.modal-footer form').forEach(function (form) {
                form.addEventListener('submit', function (e) {
                    e.preventDefault();
                    let isAccept = form.querySelector('button').classList.contains('btn-success');
                    Swal.fire({
                        title: isAccept ? 'Accepted!' : 'Rejected!',
                        text: isAccept ? 'Health screening has been accepted.' : 'Health screening has been rejected.',
                        icon: isAccept ? 'success' : 'error',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        form.submit();
                    });
                });
            });

            // Ensure modals are properly initialized and stable
            const modals = document.querySelectorAll('.modal');
            modals.forEach(modal => {
                modal.addEventListener('show.bs.modal', function (e) {
                    // Prevent any background scrolling
                    document.body.style.overflow = 'hidden';
                });

                modal.addEventListener('hidden.bs.modal', function (e) {
                    // Restore scrolling
                    document.body.style.overflow = 'auto';
                    // Clean up any remaining backdrops
                    const backdrops = document.querySelectorAll('.modal-backdrop');
                    backdrops.forEach(backdrop => backdrop.remove());
                });
            });

            // Prevent modal flickering by ensuring proper pointer events
            document.querySelectorAll('.modal-dialog').forEach(dialog => {
                dialog.addEventListener('mouseenter', function (e) {
                    e.stopPropagation();
                });
                dialog.addEventListener('mouseleave', function (e) {
                    e.stopPropagation();
                });
            });
        });

        // Accept health screening with SweetAlert confirmation
        function acceptScreening(screeningId) {
            const comments = document.getElementById('adminComments' + screeningId).value;

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
        <ul class="nav nav-tabs mb-3" role="tablist">
            <li class="nav-item">
                <a class="nav-link {{ $status == 'pending' ? 'active bg-warning text-dark' : 'text-dark' }}"
                    href="{{ route('admin.health-screening', ['status' => 'pending']) }}">
                    Pending
                    <span class="badge bg-warning text-dark ms-1">{{ $pendingCount }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $status == 'accepted' ? 'active bg-success text-white' : 'text-success' }}"
                    href="{{ route('admin.health-screening', ['status' => 'accepted']) }}">
                    Accepted
                    <span
                        class="badge {{ $status == 'accepted' ? 'bg-light text-success' : 'bg-success text-white' }} ms-1">{{ $acceptedCount }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $status == 'declined' ? 'active bg-danger text-white' : 'text-danger' }}"
                    href="{{ route('admin.health-screening', ['status' => 'declined']) }}">
                    Rejected
                    <span
                        class="badge {{ $status == 'declined' ? 'bg-light text-danger' : 'bg-danger text-white' }} ms-1">{{ $declinedCount }}</span>
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
                       placeholder="Search by name, contact number..."
                       aria-label="Search health screenings">
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
                                @foreach($healthScreenings as $index => $screening)
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
                                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#detailsModal{{ $screening->health_screening_id }}">
                                                <i class="bi bi-eye"></i> View
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Modals for each health screening --}}
            @foreach($healthScreenings as $screening)
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
                                            <div class="col-md-6 mb-2">
                                                <strong>Date of Birth:</strong> {{ $screening->user->date_of_birth }}
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
                                            <div class="col-md-6 mb-2">
                                                <strong>Date of Birth:</strong> {{ $screening->infant->date_of_birth }}
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
                                        <div class="col-md-6 mb-2">
                                            <strong>Status:</strong>
                                            <span
                                                class="badge bg-{{ $screening->status == 'accepted' ? 'success' : ($screening->status == 'declined' ? 'danger' : 'warning text-dark') }} ms-2">
                                                {{ ucfirst($screening->status) }}
                                            </span>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <strong>Submitted:</strong>
                                            {{ optional($screening->created_at)->setTimezone('Asia/Manila')->format('M d, Y h:i A') }}
                                        </div>
                                        @if($screening->status == 'accepted' && $screening->date_accepted)
                                            <div class="col-md-6 mb-2">
                                                <strong>Accepted At:</strong>
                                                {{ optional($screening->date_accepted)->setTimezone('Asia/Manila')->format('M d, Y h:i A') }}
                                            </div>
                                        @endif
                                        @if($screening->status == 'declined' && $screening->date_declined)
                                            <div class="col-md-6 mb-2">
                                                <strong>Declined At:</strong>
                                                {{ optional($screening->date_declined)->setTimezone('Asia/Manila')->format('M d, Y h:i A') }}
                                            </div>
                                        @endif
                                        @if(!empty($screening->admin_notes))
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
                                        <h6 class="text-primary border-bottom pb-2 mb-3"><i
                                                class="bi bi-chat-left-text-fill me-2"></i>Admin Action</h6>
                                        <div class="row">
                                            <div class="col-12 mb-2">
                                                <textarea class="form-control admin-comments-textarea rounded mt-2"
                                                    id="adminComments{{ $screening->health_screening_id }}" 
                                                    name="comments" 
                                                    rows="3"
                                                    placeholder="Enter comments or notes (required for declining, optional for accepting)"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="modal-footer">
                                @if($screening->status == 'pending')
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="button" class="btn btn-danger"
                                        onclick="declineScreening({{ $screening->health_screening_id }})">
                                        <i class="bi bi-x-circle me-1"></i> Decline
                                    </button>
                                    <button type="button" class="btn btn-success"
                                        onclick="acceptScreening({{ $screening->health_screening_id }})">
                                        <i class="bi bi-check-circle me-1"></i> Accept
                                    </button>
                                @elseif($screening->status == 'declined')
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="button" class="btn btn-success"
                                        onclick="undoDeclineScreening({{ $screening->health_screening_id }})">
                                        <i class="bi bi-arrow-counterclockwise me-1"></i> Undo & Accept
                                    </button>
                                @else
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
@endsection