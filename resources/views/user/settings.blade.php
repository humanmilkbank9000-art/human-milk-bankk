@extends('layouts.user-layout')
@section('title', 'Settings')
@section('pageTitle', 'User Settings')

@section('content')
    <div class="container">
        {{-- Flash messages are handled via SweetAlert for consistency --}}

        <div class="card shadow-sm rounded">
            <div class="card-header bg-primary text-white">
                <i class="fas fa-cog me-2"></i>Account Settings
            </div>
            <div class="card-body">
                <ul class="nav nav-tabs mb-3" id="settingsTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="user-tab" data-bs-toggle="tab" data-bs-target="#user"
                            type="button" role="tab" aria-controls="user" aria-selected="true">User Info</button>
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
                                    <button class="btn btn-outline-secondary" type="button" id="toggleCurrentPassword">
                                        <i class="fas fa-eye" id="currentPasswordIcon"></i>
                                    </button>
                                </div>
                                @error('current_password')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="new_password" class="form-label">New Password</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-key"></i></span>
                                    <input type="password" name="new_password" id="new_password" class="form-control" required>
                                    <button class="btn btn-outline-secondary" type="button" id="toggleNewPassword">
                                        <i class="fas fa-eye" id="newPasswordIcon"></i>
                                    </button>
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
                                    <button class="btn btn-outline-secondary" type="button" id="toggleConfirmPassword">
                                        <i class="fas fa-eye" id="confirmPasswordIcon"></i>
                                    </button>
                                </div>
                            </div>
                            <button class="btn btn-primary w-100" type="submit"><i class="fas fa-save me-2"></i>Update
                                Password</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
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

            // Toggle password visibility for current password
            document.getElementById('toggleCurrentPassword')?.addEventListener('click', function() {
                const input = document.getElementById('current_password');
                const icon = document.getElementById('currentPasswordIcon');
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    input.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            });

            // Toggle password visibility for new password
            document.getElementById('toggleNewPassword')?.addEventListener('click', function() {
                const input = document.getElementById('new_password');
                const icon = document.getElementById('newPasswordIcon');
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    input.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            });

            // Toggle password visibility for confirm password
            document.getElementById('toggleConfirmPassword')?.addEventListener('click', function() {
                const input = document.getElementById('new_password_confirmation');
                const icon = document.getElementById('confirmPasswordIcon');
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    input.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            });

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
    </script>
@endsection