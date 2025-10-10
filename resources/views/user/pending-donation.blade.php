@extends('layouts.user-layout')

@section('title', 'Pending Donations')
@section('pageTitle', 'Pending Donations')

@section('content')
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if($pendingDonations->count() > 0)
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Your Pending Donations</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-standard table-striped">
                        <thead>
                            <tr>
                                <th>Donation Type</th>
                                <th>Number of Bags</th>
                                <th>Volume per Bag</th>
                                <th>Total Volume</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pendingDonations as $donation)
                                <tr>
                                    <td>
                                        <span
                                            class="badge {{ $donation->donation_method === 'walk_in' ? 'bg-primary' : 'bg-info' }}">
                                            {{ $donation->donation_method === 'walk_in' ? 'Walk-in' : 'Home Collection' }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($donation->number_of_bags)
                                            {{ $donation->number_of_bags }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($donation->individual_bag_volumes && count($donation->individual_bag_volumes) > 0)
                                            <small>{{ $donation->formatted_bag_volumes }}</small>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($donation->total_volume)
                                            {{ $donation->total_volume }} ml
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($donation->donation_date)
                                            {{ $donation->donation_date->format('M d, Y') }}
                                        @elseif($donation->scheduled_pickup_date)
                                            {{ $donation->scheduled_pickup_date->format('M d, Y') }}
                                        @else
                                            <span class="text-muted">To be scheduled</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($donation->donation_time)
                                            {{ $donation->availability ? $donation->availability->formatted_time : $donation->donation_time }}
                                        @elseif($donation->scheduled_pickup_time)
                                            {{ $donation->scheduled_pickup_time }}
                                        @else
                                            <span class="text-muted">To be scheduled</span>
                                        @endif
                                    </td>
                                    <td>
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
                                            <br><small class="text-muted">Please visit on scheduled date</small>
                                        @elseif($donation->status === 'pending_home_collection')
                                            <br><small class="text-muted">Admin will contact you soon</small>
                                        @elseif($donation->status === 'scheduled_home_collection')
                                            <br><small class="text-muted">Pickup confirmed</small>
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
                            <li><strong>Walk-in:</strong> Volume details will be confirmed when you visit the center</li>
                            <li><strong>Home Collection:</strong> Date/time will be scheduled by admin after your request</li>
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
@endsection