@extends('layouts.user-layout')
@section('title', 'Settings')
@section('pageTitle', 'User Settings')

@section('content')
    <div class="container-fluid">
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
                @elseif($tab == 'infant')
                    <div class="card mb-4">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0">Infant Registration Information</h5>
                        </div>
                        <div class="card-body p-0">
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
                                    <input type="password" class="form-control" id="current_password" name="current_password"
                                        required>
                                </div>
                                <div class="mb-3">
                                    <label for="new_password" class="form-label">New Password</label>
                                    <input type="password" class="form-control" id="new_password" name="new_password" required>
                                    <div style="color: #ff5a7a; font-size: 0.95em; margin-top: 4px;">
                                        Password must be 8-64 chars and include upper, lower, number, and special character.
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="new_password_confirmation" class="form-label">Confirm New Password</label>
                                    <input type="password" class="form-control" id="new_password_confirmation"
                                        name="new_password_confirmation" required>
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