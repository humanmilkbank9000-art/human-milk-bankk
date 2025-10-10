@extends('layouts.user-layout')
@section('title', 'Settings')
@section('pageTitle', 'User Settings')

@section('styles')
    <style>
        /* Tab navigation fixes */
        .nav-tabs {
            display: flex;
            flex-wrap: wrap;
            border-bottom: 2px solid #dee2e6;
            margin-bottom: 1.5rem;
            overflow: visible;
        }

        .nav-tabs .nav-item {
            flex: 0 0 auto;
            margin-bottom: 0;
        }

        .nav-tabs .nav-link {
            padding: 0.75rem 1.5rem;
            white-space: nowrap;
            overflow: visible;
            text-overflow: clip;
            border: 1px solid transparent;
            border-radius: 0.25rem 0.25rem 0 0;
            display: inline-block;
            width: auto;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .nav-tabs .nav-link {
                padding: 0.6rem 1.2rem;
                font-size: 0.9rem;
            }
        }

        @media (max-width: 576px) {
            .nav-tabs {
                flex-wrap: wrap;
                /* Allow wrapping if needed */
                gap: 0.25rem;
            }

            .nav-tabs .nav-item {
                flex: 0 1 auto;
                /* Flexible but don't force equal width */
                min-width: fit-content;
                /* Ensure text fits */
            }

            .nav-tabs .nav-link {
                padding: 0.5rem 0.75rem;
                font-size: 0.8rem;
                line-height: 1.3;
                white-space: normal;
                /* Allow wrapping within tab */
                text-align: center;
                min-width: fit-content;
            }
        }

        @media (max-width: 400px) {
            .nav-tabs .nav-link {
                padding: 0.4rem 0.5rem;
                font-size: 0.75rem;
            }
        }
    </style>
@endsection

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
                                        <th>Role</th>
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
                                        <td>{{ $user->user_type ?? 'User' }}</td>
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
                                            <td>{{ $infant->sex ?? '-' }}</td>
                                            <td>{{ $infant->date_of_birth ?? '-' }}</td>
                                            <td>{{ $infant->age ?? '-' }}</td>
                                            <td>{{ $infant->birth_weight ?? '-' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted">No infant registered.</td>
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