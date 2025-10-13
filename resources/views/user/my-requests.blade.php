@extends('layouts.user-layout')

@section('title', 'My Requests')
@section('pageTitle', 'My Submitted Requests')

@section('content')
    <div class="container-fluid">
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
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Your Submitted Requests</h5>
            </div>
            <div class="card-body">
                @if($requests->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-standard table-striped">
                            <thead>
                                <tr>
                                    <th class="column-id">Request ID</th>
                                    <th>Infant</th>
                                    <th>Volume Requested</th>
                                    <th>Submitted</th>
                                    <th>Validated</th>
                                    <th>Status</th>
                                    <th>Dispensed From</th>
                                    <th>Prescription</th>
                                    <th>Admin Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($requests as $request)
                                    <tr>
                                        <td class="column-id">
                                            <strong>#{{ $request->breastmilk_request_id }}</strong>
                                        </td>
                                        <td>
                                            @if($request->infant)
                                                <strong>{{ $request->infant->first_name }}
                                                    {{ $request->infant->last_name }}{{ $request->infant->suffix ? ' ' . $request->infant->suffix : '' }}</strong><br>
                                                <small class="text-muted">{{ $request->infant->getFormattedAge() }}</small>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $request->volume_requested ? $request->formatted_volume_requested . ' ml' : '-' }}
                                        </td>
                                        <td>
                                            {{ $request->created_at ? \Carbon\Carbon::parse($request->created_at)->format('M d, Y h:i A') : '-' }}
                                        </td>
                                        <td>
                                            @php
                                                $validated = $request->approved_at ?? $request->declined_at;
                                            @endphp
                                            {{ $validated ? \Carbon\Carbon::parse($validated)->format('M d, Y h:i A') : '-' }}
                                        </td>
                                        <td>
                                            @php
                                                $status = $request->status === 'dispensed' ? 'completed' : $request->status;
                                            @endphp
                                            <span class="badge-status {{ $status }}">
                                                {{ ucfirst($status === 'completed' ? 'Dispensed' : $status) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($request->isDispensed() && $request->dispensedMilk)
                                                @if($request->dispensedMilk->isFromUnpasteurized())
                                                    Unpasteurized Milk<br>
                                                    <small>{{ $request->dispensedMilk->getSourceDisplayAttribute() }}</small>
                                                @elseif($request->dispensedMilk->isFromPasteurized())
                                                    Pasteurized Milk<br>
                                                    <small>{{ $request->dispensedMilk->getSourceDisplayAttribute() }}</small>
                                                @else
                                                    Unknown
                                                @endif
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($request->hasPrescription())
                                                <button class="btn btn-sm btn-outline-info" data-bs-toggle="modal"
                                                    data-bs-target="#requestModal{{ $request->breastmilk_request_id }}"
                                                    onclick="fetchUserPrescription({{ $request->breastmilk_request_id }})">
                                                    <i class="fas fa-eye"></i> View
                                                </button>
                                            @else
                                                <span class="text-muted">No</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($request->isDispensed() && $request->dispensedMilk && $request->dispensedMilk->dispensing_notes)
                                                {{ $request->dispensedMilk->dispensing_notes }}
                                            @elseif($request->admin_notes)
                                                {{ $request->admin_notes }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center text-muted py-5">
                        <i class="fas fa-inbox fa-3x mb-3"></i>
                        <h4 class="mb-2">No Requests Submitted</h4>
                        <p class="mb-0">You have not submitted any breastmilk requests yet.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Request Detail Modals -->
        @foreach($requests as $request)
            @include('partials.shared-modal', [
                'id' => 'requestModal' . $request->breastmilk_request_id,
                'title' => '<i class="fas fa-file-medical"></i> Prescription Image',
                'secondary' => 'Close',
                'slot' => ($request->hasPrescription() ? '<div id="prescription-content-' . $request->breastmilk_request_id . '" class="text-center"><div class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading prescription...</div></div>' : '<div class="alert alert-secondary">No prescription image attached.</div>'),
            ])
        @endforeach
        </div>
@endsection

@section('scripts')
    <script>
        // Fetch prescription JSON and populate the modal placeholder immediately (called from View button)
        function fetchUserPrescription(id) {
            try {
                const placeholder = document.getElementById('prescription-content-' + id);
                if (!placeholder) return;

                // Show immediate spinner
                placeholder.innerHTML = '<div class="d-flex align-items-center justify-content-center"><div class="spinner-border text-primary me-2" role="status"><span class="visually-hidden">Loading prescription...</span></div><div>Loading prescription...</div></div>';

                const controller = new AbortController();
                const timeout = setTimeout(() => controller.abort(), 10000);

                fetch(`{{ url('/user/breastmilk-request') }}/${id}/prescription-json`, { credentials: 'same-origin', signal: controller.signal })
                    .then(response => {
                        clearTimeout(timeout);
                        if (!response.ok) {
                            return response.json().then(err => { throw err; }).catch(() => { throw { error: 'Failed to load prescription (status ' + response.status + ')' }; });
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (!data || data.error) {
                            placeholder.innerHTML = '<div class="alert alert-danger">' + (data?.error || 'No prescription available') + '</div>';
                            return;
                        }

                        placeholder.innerHTML = `
                            <div class="d-flex flex-column align-items-center justify-content-center">
                                <h6 class="mb-3">File: ${data.filename}</h6>
                                <div class="d-flex justify-content-center align-items-center" style="min-height: 400px;">
                                    <img src="${data.image}" alt="Prescription" class="img-fluid rounded border" style="max-width:100%; max-height:70vh; object-fit:contain;" />
                                </div>
                                <div class="mt-3">
                                    <a href="${data.image}" download="${data.filename}" class="btn btn-sm btn-outline-primary mt-2"><i class="fas fa-download"></i> Download</a>
                                </div>
                            </div>
                        `;
                    })
                    .catch(err => {
                        console.error('Prescription fetch error:', err);
                        const message = (err && err.error) ? err.error : (err.name === 'AbortError' ? 'Request timed out' : 'Failed to load prescription.');
                        placeholder.innerHTML = '<div class="alert alert-danger">' + message + '</div>';
                    });
            } catch (e) {
                console.error('fetchUserPrescription fatal error', e);
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            // Attach event listener for all request modals
            document.querySelectorAll('[id^="requestModal"]').forEach(modalEl => {
                modalEl.addEventListener('shown.bs.modal', function (event) {
                    const id = this.id.replace('requestModal', '');
                    const placeholder = document.getElementById('prescription-content-' + id);
                    if (!placeholder) return;

                    // Fetch base64 JSON via authenticated request with timeout and robust handling
                    const controller = new AbortController();
                    const timeout = setTimeout(() => controller.abort(), 10000); // 10s timeout

                    fetch(`{{ url('/user/breastmilk-request') }}/${id}/prescription-json`, { credentials: 'same-origin', signal: controller.signal })
                        .then(response => {
                            clearTimeout(timeout);
                            if (!response.ok) {
                                // Try to parse JSON error body
                                return response.json().then(err => { throw err; }).catch(() => { throw { error: 'Failed to load prescription (status ' + response.status + ')' }; });
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (!data || data.error) {
                                placeholder.innerHTML = '<div class="alert alert-danger">' + (data?.error || 'No prescription available') + '</div>';
                                return;
                            }

                            placeholder.innerHTML = `
                                <div class="d-flex flex-column align-items-center justify-content-center">
                                    <h6 class="mb-3">File: ${data.filename}</h6>
                                    <div class="d-flex justify-content-center align-items-center" style="min-height: 400px;">
                                        <img src="${data.image}" alt="Prescription" class="img-fluid rounded border" style="max-width:100%; max-height:70vh; object-fit:contain;" />
                                    </div>
                                    <div class="mt-3">
                                        <a href="${data.image}" download="${data.filename}" class="btn btn-sm btn-outline-primary mt-2"><i class="fas fa-download"></i> Download</a>
                                    </div>
                                </div>
                            `;
                        })
                        .catch(err => {
                            console.error('Prescription fetch error:', err);
                            const message = (err && err.error) ? err.error : (err.name === 'AbortError' ? 'Request timed out' : 'Failed to load prescription.');
                            placeholder.innerHTML = '<div class="alert alert-danger">' + message + '</div>';
                        });
                });
            });
        });
    </script>
@endsection