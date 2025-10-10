@extends('layouts.admin-layout')

@section('title', 'Settings')

@section('content')
    <div class="container">

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="card shadow-sm rounded">
            <div class="card-header bg-primary text-white">
                <i class="fas fa-cog me-2"></i>Account Settings
            </div>
            <div class="card-body">
                <ul class="nav nav-tabs mb-3" id="settingsTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile"
                            type="button" role="tab" aria-controls="profile" aria-selected="true">Profile</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="password-tab" data-bs-toggle="tab" data-bs-target="#password"
                            type="button" role="tab" aria-controls="password" aria-selected="false">Change Password</button>
                    </li>
                </ul>
                <div class="tab-content" id="settingsTabContent">
                    <div class="tab-pane fade show active" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                        <form method="POST" action="{{ route('admin.settings.update') }}">
                            @csrf
                            <h5 class="mb-3">Profile</h5>
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    <input type="text" name="username" id="username" class="form-control"
                                        value="{{ old('username', $admin->username ?? '') }}" required>
                                </div>
                                @error('username')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <button class="btn btn-primary w-100" type="submit"><i class="fas fa-save me-2"></i>Update
                                Username</button>
                        </form>
                    </div>
                    <div class="tab-pane fade" id="password" role="tabpanel" aria-labelledby="password-tab">
                        <form method="POST" action="{{ route('admin.settings.update') }}">
                            @csrf
                            <h5 class="mb-3">Change Password</h5>
                            <div class="mb-3">
                                <label for="current_password" class="form-label">Current Password</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    <input type="password" name="current_password" id="current_password"
                                        class="form-control" required>
                                </div>
                                <small class="form-text text-muted">Enter your current password to confirm changes.</small>
                                @error('current_password')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">New Password</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-key"></i></span>
                                    <input type="password" name="password" id="password" class="form-control">
                                </div>
                                <small class="form-text text-muted">Password must be at least 8 characters.</small>
                                @error('password')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">Confirm Password</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-check"></i></span>
                                    <input type="password" name="password_confirmation" id="password_confirmation"
                                        class="form-control">
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