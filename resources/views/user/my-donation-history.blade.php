@extends('layouts.user-layout')

@section('title', 'My Donation History')
@section('pageTitle', 'My Donation History')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/table-layout-standard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/responsive-tables.css') }}">
@endsection

@section('content')
    <div class="container-fluid page-container-standard">
        @if($completedDonations->count() > 0)
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>

                </div>
                <a href="{{ route('user.donate') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Donate Again
                </a>
            </div>
        @endif

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="card card-standard mb-4" style="background: #fff;">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Completed Donations</h5>
            </div>
            <div class="card-body">
                @if($completedDonations->count() > 0)
                    <div class="table-container">
                        <table class="table table-hover table-striped">
                            <thead>
                                <tr>
                                    <th class="text-center">Donation Method</th>
                                    <th class="text-center">Number of Bags</th>
                                    <th class="text-center">Volume per Bag</th>
                                    <th class="text-center">Total Volume</th>
                                    <th class="text-center">Date</th>
                                    <th class="text-center">Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($completedDonations as $donation)
                                    <tr>
                                        <td data-label="Donation Method" class="text-center">
                                            @if($donation->donation_method === 'walk_in')
                                                <span class="badge bg-info">Walk-in</span>
                                            @else
                                                <span class="badge bg-success">Home Collection</span>
                                            @endif
                                        </td>
                                        <td data-label="Number of Bags" class="text-center">
                                            <strong>{{ $donation->number_of_bags }}</strong>
                                        </td>
                                        <td data-label="Volume per Bag" class="text-center">
                                            @if($donation->individual_bag_volumes && count($donation->individual_bag_volumes) > 0)
                                                <small>{{ $donation->formatted_bag_volumes }}</small>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td data-label="Total Volume" class="text-center">
                                            <strong>{{ $donation->formatted_total_volume }} ml</strong>
                                        </td>
                                        <td data-label="Date" class="text-center">
                                            @if($donation->donation_method === 'walk_in' && $donation->donation_date)
                                                {{ $donation->donation_date->format('M d, Y') }}
                                            @elseif($donation->donation_method === 'home_collection' && $donation->scheduled_pickup_date)
                                                {{ $donation->scheduled_pickup_date->format('M d, Y') }}
                                            @else
                                                <span class="text-muted">{{ $donation->updated_at->format('M d, Y') }}</span>
                                            @endif
                                        </td>
                                        <td data-label="Time" class="text-center">
                                            @if($donation->donation_method === 'walk_in')
                                                {{ $donation->availability ? $donation->availability->formatted_time : ($donation->donation_time ? \Carbon\Carbon::parse($donation->donation_time)->format('g:i A') : 'N/A') }}
                                            @elseif($donation->donation_method === 'home_collection' && $donation->scheduled_pickup_time)
                                                {{ \Carbon\Carbon::parse($donation->scheduled_pickup_time)->format('g:i A') }}
                                            @else
                                                <span class="text-muted">{{ $donation->updated_at->format('g:i A') }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        <div class="d-flex flex-wrap justify-content-center gap-3">
                            <div class="flex-fill" style="min-width: 250px; max-width: 350px;">
                                <div class="card shadow-sm rounded-lg border-0 bg-light h-100">
                                    <div class="card-body text-center py-4">
                                        <h6 class="card-title text-uppercase text-secondary mb-2">Total Donations</h6>
                                        <h2 class="text-primary fw-bold mb-0">{{ $completedDonations->count() }}</h2>
                                    </div>
                                </div>
                            </div>
                            <div class="flex-fill" style="min-width: 250px; max-width: 350px;">
                                <div class="card shadow-sm rounded-lg border-0 bg-light h-100">
                                    <div class="card-body text-center py-4">
                                        <h6 class="card-title text-uppercase text-secondary mb-2">Total Volume</h6>
                                        <h2 class="text-success fw-bold mb-0">{{ $completedDonations->sum('total_volume') }} ml
                                        </h2>
                                    </div>
                                </div>
                            </div>
                            <div class="flex-fill" style="min-width: 250px; max-width: 350px;">
                                <div class="card shadow-sm rounded-lg border-0 bg-light h-100">
                                    <div class="card-body text-center py-4">
                                        <h6 class="card-title text-uppercase text-secondary mb-2">Total Bags</h6>
                                        <h2 class="text-info fw-bold mb-0">{{ $completedDonations->sum('number_of_bags') }}</h2>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center text-muted py-5">
                        <i class="fas fa-history fa-3x mb-3"></i>
                        <h4 class="mb-2">No Donation History</h4>
                        <p class="mb-3">You haven't completed any donations yet.</p>
                        <a href="{{ route('user.donate') }}" class="btn btn-primary px-4 py-2">
                            <i class="fas fa-plus"></i> Make Your First Donation
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>{{-- Close container-fluid --}}
@endsection