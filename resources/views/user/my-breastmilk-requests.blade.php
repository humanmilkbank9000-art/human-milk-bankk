@extends('layouts.user-layout')

@section('title', 'My Breastmilk Requests')
@section('pageTitle', 'My Breastmilk Requests')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/table-layout-standard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/responsive-tables.css') }}">
@endsection

@section('content')
    <div class="container-fluid page-container-standard">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <p class="text-muted mb-0">Track the status of your breastmilk requests</p>
            </div>
            <a href="{{ route('user.breastmilk-request') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> New Request
            </a>
        </div>

        @if($requests->count() > 0)
            <div class="card card-standard">
                <div class="card-header">
                    <h5 class="mb-0">Your Breastmilk Requests</h5>
                </div>
                <div class="card-body">
                    <div class="table-container">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th class="text-center">Request ID</th>
                                    <th class="text-center">Infant</th>
                                    <th class="text-center">Appointment</th>
                                    <th class="text-center">Volume Requested</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Submitted</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($requests as $request)
                                    <tr>
                                        <td data-label="Request ID" class="text-center">
                                            <strong>#{{ $request->breastmilk_request_id }}</strong>
                                        </td>
                                        <td data-label="Infant" class="text-center">
                                            <strong>{{ $request->infant->first_name }}
                                                {{ $request->infant->last_name }}{{ $request->infant->suffix ? ' ' . $request->infant->suffix : '' }}</strong><br>
                                            <small class="text-muted">
                                                {{ $request->infant->getFormattedAge() }}
                                            </small>
                                        </td>
                                        <td data-label="Appointment" class="text-center">
                                            @if($request->availability)
                                                <strong>{{ $request->availability->formatted_date }}</strong><br>
                                                <small class="text-muted">{{ $request->availability->formatted_time }}</small>
                                            @else
                                                <span class="text-muted">To be scheduled</span>
                                            @endif
                                        </td>
                                        <td data-label="Volume Requested" class="text-center">
                                            @if($request->volume_requested)
                                                <strong>{{ $request->formatted_volume_requested }} ml</strong>
                                            @else
                                                <span class="text-muted">To be determined</span>
                                            @endif
                                        </td>
                                        <td data-label="Status" class="text-center">
                                            <span class="badge bg-{{ $request->getStatusBadgeColor() }}">
                                                {{ ucfirst($request->status) }}
                                            </span>
                                            @if($request->status === 'approved')
                                                <br><small class="text-success">Ready for pickup</small>
                                            @elseif($request->status === 'declined')
                                                <br><small class="text-danger">Request denied</small>
                                            @else
                                                <br><small class="text-muted">Under review</small>
                                            @endif
                                        </td>
                                        <td data-label="Submitted" class="text-center">
                                            {{ $request->created_at->format('M d, Y') }}<br>
                                            <small class="text-muted">{{ $request->created_at->format('g:i A') }}</small>
                                        </td>
                                        <td data-label="Actions" class="text-center">
                                            <button class="btn btn-sm btn-outline-info" data-bs-toggle="modal"
                                                data-bs-target="#requestModal{{ $request->breastmilk_request_id }}">
                                                <i class="fas fa-eye"></i> View
                                            </button>

                                            @if($request->status === 'approved')
                                                <span class="badge bg-success mt-1">
                                                    <i class="fas fa-check"></i> Approved
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Request Detail Modals -->
            @foreach($requests as $request)
                <div class="modal fade" id="requestModal{{ $request->breastmilk_request_id }}" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">
                                    <i class="fas fa-file-medical"></i>
                                    Request #{{ $request->breastmilk_request_id }} Details
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6><i class="fas fa-baby"></i> Infant Information</h6>
                                        <table class="table table-standard table-sm">
                                            <tr>
                                                <td><strong>Name:</strong></td>
                                                <td>{{ $request->infant->first_name }} {{ $request->infant->middle_name }}
                                                    {{ $request->infant->last_name }}{{ $request->infant->suffix ? ' ' . $request->infant->suffix : '' }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>Age:</strong></td>
                                                <td>{{ $request->infant->getFormattedAge() }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Sex:</strong></td>
                                                <td>{{ ucfirst($request->infant->sex) }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Birth Weight:</strong></td>
                                                <td>{{ $request->infant->birth_weight }} kg</td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="col-md-6">
                                        <h6><i class="fas fa-calendar"></i> Appointment Details</h6>
                                        <table class="table table-standard table-sm">
                                            <tr>
                                                <td><strong>Date:</strong></td>
                                                <td>
                                                    @if($request->availability)
                                                        {{ $request->availability->formatted_date }}
                                                    @else
                                                        {{ Carbon\Carbon::parse($request->request_date)->format('M d, Y') }}
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>Time:</strong></td>
                                                <td>
                                                    @if($request->availability)
                                                        {{ $request->availability->formatted_time }}
                                                    @else
                                                        {{ Carbon\Carbon::parse($request->request_time)->format('g:i A') }}
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>Status:</strong></td>
                                                <td>
                                                    <span class="badge bg-{{ $request->getStatusBadgeColor() }}">
                                                        {{ ucfirst($request->status) }}
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>Volume:</strong></td>
                                                <td>
                                                    @if($request->volume_requested)
                                                        {{ $request->formatted_volume_requested }} ml
                                                    @else
                                                        <span class="text-muted">To be determined</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>

                                @if($request->admin_notes)
                                    <div class="mt-3">
                                        <h6><i class="fas fa-sticky-note"></i> Admin Notes</h6>
                                        <div class="alert alert-info">
                                            {{ $request->admin_notes }}
                                        </div>
                                    </div>
                                @endif

                                @if($request->hasPrescription())
                                    <div class="mt-3">
                                        <h6><i class="fas fa-file-medical"></i> Prescription</h6>
                                        <div class="alert alert-success">
                                            <i class="fas fa-check-circle"></i> Prescription file
                                            "{{ $request->prescription_filename }}" has been uploaded and is being reviewed.
                                            <div class="mt-2">
                                                <button class="btn btn-sm btn-primary"
                                                    onclick="fetchUserPrescription({{ $request->breastmilk_request_id }})">
                                                    <i class="fas fa-eye"></i> View Prescription
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <div class="mt-3">
                                    <h6><i class="fas fa-clock"></i> Timeline</h6>
                                    <div class="timeline">
                                        <div class="timeline-item">
                                            <span class="badge bg-primary">{{ $request->created_at->format('M d, Y g:i A') }}</span>
                                            Request submitted
                                        </div>
                                        @if($request->approved_at)
                                            <div class="timeline-item">
                                                <span
                                                    class="badge bg-success">{{ $request->approved_at->format('M d, Y g:i A') }}</span>
                                                Request approved
                                            </div>
                                        @endif
                                        @if($request->declined_at)
                                            <div class="timeline-item">
                                                <span class="badge bg-danger">{{ $request->declined_at->format('M d, Y g:i A') }}</span>
                                                Request declined
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-heart fa-3x text-muted mb-3"></i>
                    <h5>No Breastmilk Requests Yet</h5>
                    <p class="text-muted">You haven't submitted any breastmilk requests yet.</p>
                    <a href="{{ route('user.breastmilk-request') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Submit Your First Request
                    </a>
                </div>
            </div>
        @endif
    </div>{{-- Close container-fluid --}}

    <style>
        .timeline {
            position: relative;
            padding-left: 20px;
        }

        .timeline-item {
            position: relative;
            padding-bottom: 10px;
            border-left: 2px solid #e9ecef;
            padding-left: 20px;
            margin-bottom: 10px;
        }

        .timeline-item:before {
            content: '';
            position: absolute;
            left: -5px;
            top: 5px;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background-color: #007bff;
        }

        .timeline-item:last-child {
            border-left: none;
        }
    </style>

    <script>
        function fetchUserPrescription(requestId) {
            const modal = document.getElementById('requestModal' + requestId);
            const imgContainer = modal.querySelector('.modal-body .text-center');

            // Show a spinner in the modal body while loading
            imgContainer.innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading prescription...</div>';

            fetch(`{{ url('/user/breastmilk-request') }}/${requestId}/prescription-json`, {
                credentials: 'same-origin'
            })
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        imgContainer.innerHTML = '<div class="alert alert-danger">' + data.error + '</div>';
                        return;
                    }

                    imgContainer.innerHTML = `
                                                        <div class="d-flex flex-column align-items-center justify-content-center">
                                                            <h6 class="mb-3">Prescription: ${data.filename}</h6>
                                                            <div class="d-flex justify-content-center align-items-center" style="min-height: 400px;">
                                                                <img src="${data.image}" alt="Prescription" class="img-fluid rounded border" style="max-width:100%; max-height:70vh; object-fit:contain;" />
                                                            </div>
                                                        </div>
                                                    `;
                })
                .catch(err => {
                    imgContainer.innerHTML = '<div class="alert alert-danger">Failed to load prescription.</div>';
                });
        }
    </script>
@endsection