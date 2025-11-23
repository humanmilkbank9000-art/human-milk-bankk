@extends('layouts.user-layout')
@section('title', 'Settings')
@section('pageTitle', 'User Settings')

@section('content')
    <div class="container">
        {{-- Flash messages are handled via SweetAlert for consistency --}}

<<<<<<< HEAD
        <div class="card shadow-sm rounded">
            <div class="card-header bg-primary text-white">
                <i class="fas fa-cog me-2"></i>Account Settings
            </div>
            <div class="card-body">
                <ul class="nav nav-tabs mb-3" id="settingsTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="user-tab" data-bs-toggle="tab" data-bs-target="#user"
                            type="button" role="tab" aria-controls="user" aria-selected="true">User Info</button>
=======
                .table-card .table {
                    margin-top: 8px;
                    background: #ffffff;
                    border-radius: 8px;
                    overflow: hidden;
                }

                .table-card .table thead th:first-child {
                    border-top-left-radius: 8px;
                }

                .table-card .table thead th:last-child {
                    border-top-right-radius: 8px;
                }

                /* Page-specific lighter pink overrides for settings page */
                .container-fluid .card-header.bg-primary,
                .container-fluid .card-header.bg-info,
                .container-fluid .card-header.bg-secondary {
                    background: #ffdfe8 !important;
                    /* lighter pastel pink */
                    border-bottom: 1px solid rgba(255, 111, 166, 0.08) !important;
                    color: #222 !important;
                    /* darker text for readability */
                }

                .table-card .table thead th {
                    background: #ffdfe8 !important;
                    /* match lighter pink */
                    color: #222 !important;
                    font-weight: 700;
                    border-bottom: 0 !important;
                }

                .input-wrapper {
                    position: relative;
                }

                .password-toggle {
                    position: absolute;
                    right: 0.75rem;
                    top: 50%;
                    transform: translateY(-50%);
                    background: none;
                    border: none;
                    color: #6b7280;
                    cursor: pointer;
                    padding: 0.2rem;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    transition: color 0.3s ease;
                }

                .password-toggle:hover {
                    color: #ec4899;
                }

                .password-toggle svg {
                    width: 18px;
                    height: 18px;
                }

                .input-wrapper .form-control {
                    padding-right: 2.5rem;
                }

                .password-toggle {
                    z-index: 3;
                }
            </style>
        @endsection
        <div class="row">
            <div class="col-lg-8 mx-auto">
                @php
                    $tab = request('tab', 'user');
                @endphp
                <ul class="nav nav-tabs mb-3" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link {{ $tab == 'user' ? 'active bg-primary text-white' : '' }}" href="?tab=user">User
                            Info</a>
>>>>>>> df483efdd0e909e747e8711337a4182065ebfce6
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="infant-tab" data-bs-toggle="tab" data-bs-target="#infant"
                            type="button" role="tab" aria-controls="infant" aria-selected="false">Infant Info</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="password-tab" data-bs-toggle="tab" data-bs-target="#password"
                            type="button" role="tab" aria-controls="password" aria-selected="false">Change Password</button>
                    </li>
                </ul>
<<<<<<< HEAD
                <div class="tab-content" id="settingsTabContent">
                    <div class="tab-pane fade show active" id="user" role="tabpanel" aria-labelledby="user-tab">
                        <h5 class="mb-3"><i class="fas fa-user me-2"></i>User Registration Information</h5>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>First Name</th>
                                        <th>Middle Name</th>
                                        <th>Last Name</th>
                                        <th>Sex</th>
                                        <th>Date of Birth</th>
                                        <th>Age</th>
                                        <th>Contact Number</th>
                                        <th>Address</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>{{ $user->first_name ?? '-' }}</td>
                                        <td>{{ $user->middle_name ?? '-' }}</td>
                                        <td>{{ $user->last_name ?? '-' }}</td>
                                        <td>{{ $user->sex ?? '-' }}</td>
                                        <td>{{ $user->date_of_birth ?? '-' }}</td>
                                        <td>{{ $user->age ?? '-' }}</td>
                                        <td>{{ $user->contact_number ?? '-' }}</td>
                                        <td>{{ $user->address ?? '-' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="infant" role="tabpanel" aria-labelledby="infant-tab">
                        <h5 class="mb-3"><i class="fas fa-baby me-2"></i>Infant Registration Information</h5>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>First Name</th>
                                        <th>Middle Name</th>
                                        <th>Last Name</th>
                                        <th>Suffix</th>
                                        <th>Sex</th>
                                        <th>Date of Birth</th>
                                        <th>Age</th>
                                        <th>Birth Weight</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($infants as $infant)
=======
                @if($tab == 'user')
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">User Registration Information</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-card">
                                <table class="table table-standard table-striped table-bordered mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>First Name</th>
                                            <th>Middle Name</th>
                                            <th>Last Name</th>
                                            <th>Sex</th>
                                            <th>Date of Birth</th>
                                            <th>Age</th>
                                            <th>Contact Number</th>
                                            <th>Address</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>{{ $user->first_name ?? '-' }}</td>
                                            <td>{{ $user->middle_name ?? '-' }}</td>
                                            <td>{{ $user->last_name ?? '-' }}</td>
                                            <td>{{ $user->sex ?? '-' }}</td>
                                            <td>{{ $user->date_of_birth ?? '-' }}</td>
                                            <td>{{ $user->age ?? '-' }}</td>
                                            <td>{{ $user->contact_number ?? '-' }}</td>
                                            <td>{{ $user->address ?? '-' }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @elseif($tab == 'infant')
                    <div class="card mb-4">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0">Infant Registration Information</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-card">
                                <table class="table table-standard table-striped table-bordered mb-0">
                                    <thead class="table-light">
>>>>>>> df483efdd0e909e747e8711337a4182065ebfce6
                                        <tr>
                                            <th>First Name</th>
                                            <th>Middle Name</th>
                                            <th>Last Name</th>
                                            <th>Suffix</th>
                                            <th>Sex</th>
                                            <th>Date of Birth</th>
                                            <th>Age</th>
                                            <th>Birth Weight</th>
                                        </tr>
<<<<<<< HEAD
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center text-muted">No infant registered.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="password" role="tabpanel" aria-labelledby="password-tab">
                        <form method="POST" action="{{ route('user.update-password') }}">
                            @csrf
                            <h5 class="mb-3">Change Password</h5>
                            <div class="mb-3">
                                <label for="current_password" class="form-label">Current Password</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    <input type="password" name="current_password" id="current_password"
                                        class="form-control" required>
                                </div>
                                @error('current_password')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
=======
                                    </thead>
                                    <tbody>
                                        @forelse($infants as $infant)
                                            <tr>
                                                <td>{{ $infant->first_name ?? '-' }}</td>
                                                <td>{{ $infant->middle_name ?? '-' }}</td>
                                                <td>{{ $infant->last_name ?? '-' }}</td>
                                                <td>{{ $infant->suffix ?? '-' }}</td>
                                                <td>{{ $infant->sex ?? '-' }}</td>
                                                <td>{{ $infant->date_of_birth ?? '-' }}</td>
                                                <td>{{ $infant->getFormattedAge() }}</td>
                                                <td>{{ $infant->birth_weight ?? '-' }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center text-muted">No infant registered.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
>>>>>>> df483efdd0e909e747e8711337a4182065ebfce6
                            </div>
                            <div class="mb-3">
                                <label for="new_password" class="form-label">New Password</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-key"></i></span>
                                    <input type="password" name="new_password" id="new_password" class="form-control" required>
                                </div>
                                <div id="password-req" style="display:none; color:#ff5a7a; font-size:0.8em; margin-top:2px;">
                                    Password must be 8-64 chars and include upper, lower, number, and special character.
                                </div>
                                @error('new_password')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="new_password_confirmation" class="form-label">Confirm New Password</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-check"></i></span>
                                    <input type="password" name="new_password_confirmation" id="new_password_confirmation"
                                        class="form-control" required>
                                </div>
                            </div>
                            <button class="btn btn-primary w-100" type="submit"><i class="fas fa-save me-2"></i>Update
                                Password</button>
                        </form>
                    </div>
<<<<<<< HEAD
                </div>
=======
                @elseif($tab == 'password')
                    <div class="card">
                        <div class="card-header bg-secondary text-white">
                            <h5 class="mb-0">Change Password</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('user.update-password') }}">
                                @csrf
                                <div class="mb-3">
                                    <label for="current_password" class="form-label">Current Password</label>
                                    <div class="input-wrapper">
                                        <input type="password" class="form-control" id="current_password"
                                            name="current_password" required>
                                        <button type="button" class="password-toggle"
                                            onclick="togglePassword('current_password','eye-icon-current')">
                                            <svg id="eye-icon-current" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2">
                                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                <circle cx="12" cy="12" r="3"></circle>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="new_password" class="form-label">New Password</label>
                                    <div class="input-wrapper">
                                        <input type="password" class="form-control" id="new_password" name="new_password"
                                            required>
                                        <button type="button" class="password-toggle"
                                            onclick="togglePassword('new_password','eye-icon-new')">
                                            <svg id="eye-icon-new" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2">
                                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                <circle cx="12" cy="12" r="3"></circle>
                                            </svg>
                                        </button>
                                    </div>
                                    <div id="password-req"
                                        style="display:none; color:#ff5a7a; font-size:0.8em; margin-top:2px;">
                                        Password must be 8-64 chars and include upper, lower, number, and special character.
                                    </div>
                                    <script>
                                        function checkPasswordStrength(pw) {
                                            return pw.length >= 8 && pw.length <= 64 &&
                                                /[A-Z]/.test(pw) && /[a-z]/.test(pw) && /[0-9]/.test(pw) && /[^A-Za-z0-9]/.test(pw);
                                        }
                                        document.addEventListener('DOMContentLoaded', function () {
                                            var pwInput = document.getElementById('new_password');
                                            var reqMsg = document.getElementById('password-req');
                                            pwInput.addEventListener('input', function () {
                                                if (pwInput.value === '') {
                                                    reqMsg.style.display = 'none';
                                                } else if (!checkPasswordStrength(pwInput.value)) {
                                                    reqMsg.style.display = 'block';
                                                } else {
                                                    reqMsg.style.display = 'none';
                                                }
                                            });
                                        });
                                    </script>
                                </div>
                                <div class="mb-3">
                                    <label for="new_password_confirmation" class="form-label">Confirm New Password</label>
                                    <div class="input-wrapper">
                                        <input type="password" class="form-control" id="new_password_confirmation"
                                            name="new_password_confirmation" required>
                                        <button type="button" class="password-toggle"
                                            onclick="togglePassword('new_password_confirmation','eye-icon-confirm')">
                                            <svg id="eye-icon-confirm" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2">
                                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                <circle cx="12" cy="12" r="3"></circle>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary">Update Password</button>
                            </form>
                        </div>
                    </div>
                @endif
>>>>>>> df483efdd0e909e747e8711337a4182065ebfce6
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
<<<<<<< HEAD
        function checkPasswordStrength(pw) {
            return pw.length >= 8 && pw.length <= 64 &&
                /[A-Z]/.test(pw) && /[a-z]/.test(pw) && /[0-9]/.test(pw) && /[^A-Za-z0-9]/.test(pw);
        }

        document.addEventListener('DOMContentLoaded', function() {
            var pwInput = document.getElementById('new_password');
            var reqMsg = document.getElementById('password-req');
            
            if (pwInput && reqMsg) {
                pwInput.addEventListener('input', function() {
                    if (pwInput.value === '') {
                        reqMsg.style.display = 'none';
                    } else if (!checkPasswordStrength(pwInput.value)) {
                        reqMsg.style.display = 'block';
                    } else {
                        reqMsg.style.display = 'none';
                    }
                });
            }

=======
        function togglePassword(inputId, iconId) {
            var passwordInput = document.getElementById(inputId);
            var eyeIcon = document.getElementById(iconId);

            if (!passwordInput) return;

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                if (eyeIcon) {
                    eyeIcon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>';
                }
            } else {
                passwordInput.type = 'password';
                if (eyeIcon) {
                    eyeIcon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>';
                }
            }
        }
        document.addEventListener('DOMContentLoaded', function () {
>>>>>>> df483efdd0e909e747e8711337a4182065ebfce6
            // Show SweetAlert on successful password update
            @if(session('status'))
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: {!! json_encode(session('status')) !!},
                    confirmButtonText: 'OK'
                });
            @endif

            // Show SweetAlert on error messages
            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: {!! json_encode(session('error')) !!},
                    confirmButtonText: 'OK'
                });
            @endif

            // If there are validation errors related to current_password, show an error alert
            @if($errors->has('current_password'))
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: {!! json_encode($errors->first('current_password')) !!},
                    confirmButtonText: 'OK'
                });
            @endif
<<<<<<< HEAD

            // If there are validation errors related to new_password, show an error alert
            @if($errors->has('new_password'))
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: {!! json_encode($errors->first('new_password')) !!},
                    confirmButtonText: 'OK'
                });
            @endif
        });
=======
                });
>>>>>>> df483efdd0e909e747e8711337a4182065ebfce6
    </script>
@endsection