@extends('layouts.user-layout')

@section('title', 'Pending Donations')
@section('pageTitle', 'Pending Donations')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/table-layout-standard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/responsive-tables.css') }}">
@endsection

@section('content')
    <div class="container-fluid page-container-standard">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @if($pendingDonations->count() > 0)
            <div class="card card-standard">
                <div class="card-header">
                    <h5 class="mb-0">Your Pending Donations</h5>
                </div>
                <div class="card-body">
                    <div class="table-container">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th class="text-center">Donation Type</th>
                                    <th class="text-center">Number of Bags</th>
                                    <th class="text-center">Volume per Bag</th>
                                    <th class="text-center">Total Volume</th>
                                    <th class="text-center">Date</th>
                                    <th class="text-center">Time</th>
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pendingDonations as $donation)
                                    <tr>
                                        <td data-label="Donation Type" class="text-center">
                                            <span
                                                class="badge {{ $donation->donation_method === 'walk_in' ? 'bg-primary' : 'bg-info' }}">
                                                {{ $donation->donation_method === 'walk_in' ? 'Walk-in' : 'Home Collection' }}
                                            </span>
                                        </td>
                                        <td data-label="Number of Bags" class="text-center">
                                            @if($donation->number_of_bags)
                                                {{ $donation->number_of_bags }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td data-label="Volume per Bag" class="text-center">
                                            @if($donation->individual_bag_volumes && count($donation->individual_bag_volumes) > 0)
                                                <small>{{ $donation->formatted_bag_volumes }}</small>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td data-label="Total Volume" class="text-center">
                                            @if($donation->total_volume)
                                                {{ $donation->formatted_total_volume }} ml
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td data-label="Date" class="text-center">
                                            @if($donation->donation_date)
                                                {{ $donation->donation_date->format('M d, Y') }}
                                            @elseif($donation->scheduled_pickup_date)
                                                {{ $donation->scheduled_pickup_date->format('M d, Y') }}
                                            @else
                                                <span class="text-muted">To be scheduled</span>
                                            @endif
                                        </td>
                                        <td data-label="Time" class="text-center">
                                            @if($donation->donation_time)
                                                {{ $donation->availability ? $donation->availability->formatted_time : \Carbon\Carbon::parse($donation->donation_time)->format('g:i A') }}
                                            @elseif($donation->scheduled_pickup_time)
                                                {{ \Carbon\Carbon::parse($donation->scheduled_pickup_time)->format('g:i A') }}
                                            @else
                                                <span class="text-muted">To be scheduled</span>
                                            @endif
                                        </td>
                                        <td data-label="Status" class="text-center">
                                            @php
                                                $statusColors = [
                                                    'pending_walk_in' => 'warning',
                                                    'pending_home_collection' => 'info',
                                                    'scheduled_home_collection' => 'primary'
                                                ];
                                                $statusLabels = [
                                                    'pending_walk_in' => 'Appointment Scheduled',
                                                    'pending_home_collection' => 'Awaiting Pickup Schedule',
                                                    'scheduled_home_collection' => 'Pickup Scheduled'
                                                ];
                                            @endphp
                                            <span class="badge bg-{{ $statusColors[$donation->status] ?? 'secondary' }}">
                                                {{ $statusLabels[$donation->status] ?? ucfirst(str_replace('_', ' ', $donation->status)) }}
                                            </span>
                                            @if($donation->status === 'pending_walk_in')
                                            @elseif($donation->status === 'pending_home_collection')
                                            @elseif($donation->status === 'scheduled_home_collection')
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        <small class="text-muted">
                            <strong>Note:</strong>
                            <ul class="mb-0">
                                <li><strong>For Walk-in:</strong> Your Donation details will be confirmed after your scheduled
                                    donation.</li>
                                <li><strong>For Home Collection:</strong> Human milk bank staff will schedule a date and time on
                                    when they collect your stored breastmilk at home.</li>
                            </ul>
                        </small>
                    </div>
                </div>
            </div>
        @else
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-calendar-check fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No Pending Donations</h5>
                    <p class="text-muted">You don't have any pending donation requests at the moment.</p>
                    <a href="{{ route('user.donate') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Make a Donation
                    </a>
                </div>
            </div>
        @endif
    </div>{{-- Close container-fluid --}}
@endsection