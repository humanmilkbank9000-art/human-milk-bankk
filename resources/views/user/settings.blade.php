@extends('layouts.user-layout')
@section('title', 'Settings')
@section('pageTitle', 'User Settings')

@section('content')
    <div class="container-fluid">
        @section('styles')
            <style>
                /* Add spacing between card header and table and ensure table sits on white rounded surface */
                .table-card {
                    padding: 12px;
                }

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
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $tab == 'infant' ? 'active bg-info text-white' : '' }}"
                            href="?tab=infant">Infant Info</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $tab == 'password' ? 'active bg-secondary text-white' : '' }}"
                            href="?tab=password">Change Password</a>
                    </li>
                </ul>
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
                            </div>
                        </div>
                    </div>
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
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
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
            // Show SweetAlert on successful password update
            @if(session('status'))
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: {!! json_encode(session('status')) !!},
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
                });
    </script>
@endsection