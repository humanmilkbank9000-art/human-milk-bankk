@extends('layouts.user-layout')

@section('title', 'Pending Donations')
@section('pageTitle', 'Pending Donations')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/table-layout-standard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/responsive-tables.css') }}">
    <style>
        /* Ensure bag labels and corresponding volumes stack and align */
        .bags-column,
        .volumes-column {
            display: flex;
            flex-direction: column;
            gap: 6px;
            align-items: center;
            justify-content: center;
        }
        .bag-item,
        .volume-item {
            padding: 2px 0;
            min-width: 56px;
            box-sizing: border-box;
        }
        /* Keep text compact on small screens */
        @media (max-width: 576px) {
            .bag-item, .volume-item { font-size: 0.85rem; }
        }
        /* Calendar styles (reused) */
        .calendar-container { border: 1px solid #dee2e6; border-radius: 12px; overflow: visible; max-width: 480px; width: 100%; margin: 0 auto 24px auto; box-shadow: 0 2px 16px rgba(0,0,0,0.06); background: #fff; display: flex; flex-direction: column; align-items: center; justify-content: center; }
        .calendar-header { display: grid; grid-template-columns: 40px 1fr 40px; align-items: center; background-color: #f8f9fa; padding: 18px 24px; border-bottom: 1px solid #dee2e6; width: 100%; }
        .calendar-nav-btn { background: none; border: none; font-size: 18px; cursor: pointer; padding: 5px 10px; border-radius: 4px; transition: background-color 0.2s; }
        .calendar-nav-btn:hover { background-color: #e9ecef; }
        .calendar-month-year { font-weight: bold; font-size: 1.1rem; text-align: center; white-space: nowrap; }
        .calendar-grid { display: grid; grid-template-columns: repeat(7, 1fr); background-color: white; width: 100%; min-width: 350px; max-width: 480px; margin: 0 auto; }
        .calendar-day-header { background-color: #e9ecef; padding: 10px 0; text-align: center; font-weight: bold; font-size: 0.95rem; border-bottom: 1px solid #dee2e6; }
        @media (max-width: 576px) { .calendar-container { max-width: 100%; } .calendar-grid { min-width: 100%; max-width: 100%; } .calendar-header { padding: 12px 16px; } .calendar-month-year { font-size: 1rem; } }
        .calendar-day { aspect-ratio: 1; display: flex; align-items: center; justify-content: center; border-right: 1px solid #f0f0f0; border-bottom: 1px solid #f0f0f0; cursor: pointer; transition: all 0.2s; position: relative; }
        .calendar-day:nth-child(7n) { border-right: none; }
        .calendar-day.available { background-color: #e8f5e8; color: #155724; font-weight: 500; }
        .calendar-day.available:hover { background-color: #d4edda; transform: scale(1.05); }
        .calendar-day.unavailable { background-color: #f8f9fa; color: #6c757d; cursor: not-allowed; }
        .calendar-day.past { background-color: #f8f9fa; color: #adb5bd; cursor: not-allowed; }
        .calendar-day.selected { background-color: #ff89ceff !important; color: white !important; font-weight: bold; transform: scale(1.1); z-index: 10; }
    </style>
@endsection

@section('content')
    <div id="pending-donations-page" class="container-fluid page-container-standard">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @if($pendingDonations->count() > 0)
            <div class="card card-standard">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Your Pending Donations</h5>
                </div>
                <div class="card-body">
                    <div class="table-container">
                        <table class="table table-standard table-hover">
                            <thead>
                                <tr>
                                    <th class="text-center">Donation Type</th>
                                    <th class="text-center">Bags</th>
                                    <th class="text-center">Volume per Bag</th>
                                    <th class="text-center">Total Volume</th>
                                    <th class="text-center">Date</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Actions</th>
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

                                        <td data-label="Bags" class="text-center align-top">
                                            @php
                                                // Prefer individual_bag_volumes (set when validated). If not present,
                                                // fall back to bag_details volumes saved at submission time.
                                                $bagVolumes = $donation->individual_bag_volumes ?? [];
                                                if (empty($bagVolumes) && !empty($donation->bag_details) && is_array($donation->bag_details)) {
                                                    $bagVolumes = array_map(function($d) { return isset($d['volume']) ? $d['volume'] : null; }, $donation->bag_details);
                                                }
                                                $bagCount = $donation->number_of_bags ?? count(array_filter($bagVolumes));
                                            @endphp
                                            @if($bagCount > 0)
                                                <div class="bags-column">
                                                    @for($i = 1; $i <= $bagCount; $i++)
                                                        <div class="bag-item">Bag {{ $i }}</div>
                                                    @endfor
                                                </div>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td data-label="Volume per Bag" class="text-center align-top">
                                            @if(!empty($bagVolumes) && count($bagVolumes) > 0)
                                                <div class="volumes-column">
                                                    @foreach($bagVolumes as $vol)
                                                        @if(is_null($vol) || $vol === '')
                                                            <div class="volume-item text-muted">-</div>
                                                        @else
                                                            <div class="volume-item">{{ (float)$vol == (int)$vol ? (int)$vol : rtrim(rtrim(number_format((float)$vol, 2, '.', ''), '0'), '.') }} ml</div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            @elseif($bagCount > 0)
                                                <div class="volumes-column">
                                                    @for($i = 1; $i <= $bagCount; $i++)
                                                        <div class="volume-item text-muted">-</div>
                                                    @endfor
                                                </div>
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
                                        {{-- Time column removed for walk-in availability (date-only). For pickup, time may be shown elsewhere if needed. --}}
                                        <td data-label="Status" class="text-center align-middle">
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
                                        </td>
                                        <td data-label="Actions" class="text-center align-middle">
                                            @if($donation->status === 'pending_walk_in')
                                                <button type="button" class="btn btn-sm btn-outline-primary me-1" onclick="openRescheduleModal({{ $donation->breastmilk_donation_id }})">
                                                    Reschedule
                                                </button>
                                                <form action="{{ route('user.donation.cancel', $donation->breastmilk_donation_id) }}" method="POST" class="d-inline" onsubmit="return confirmCancel(event)">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">Cancel</button>
                                                </form>
                                            @elseif(in_array($donation->status, ['pending_home_collection','scheduled_home_collection']))
                                                <form action="{{ route('user.donation.cancel', $donation->breastmilk_donation_id) }}" method="POST" class="d-inline" onsubmit="return confirmCancel(event)">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">Cancel</button>
                                                </form>
                                            @else
                                                <span class="text-muted">—</span>
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
                    <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="#6c757d" class="mb-3" viewBox="0 0 16 16">
                        <path d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71V3.5z"/>
                        <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0z"/>
                    </svg>
                    <h5 class="text-muted">No Pending Donations</h5>
                    <p class="text-muted">You don't have any pending donation requests at the moment.</p>
                    <a href="{{ route('user.donate') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Make a Donation
                    </a>
                </div>
            </div>
        @endif
        {{-- Reschedule Walk-in Modal (single, reused) --}}
        <div class="modal fade" id="rescheduleWalkInModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Reschedule Walk-in</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="filter: invert(1);"></button>
                    </div>
                    <form id="rescheduleForm" method="POST">
                        @csrf
                        <input type="hidden" name="availability_id" id="res_availability_id">
                        <input type="hidden" name="appointment_date" id="res_selected_date">
                        <div class="modal-body">
                            <div class="alert alert-info">
                                Select a new date from the calendar below. Only highlighted dates are available.
                            </div>
                            <div id="reschedule-calendar" class="calendar-container"></div>
                            <small id="res-slot-status" class="mt-2 d-block text-muted">Select a highlighted date to check availability.</small>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" id="reschedule-submit-btn" class="btn btn-primary" disabled>Confirm Reschedule</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>{{-- Close container-fluid --}}
@endsection

@section('scripts')
<script>
    // Availability and slots endpoint reused from donation page
    const RES_SLOTS_URL = "{{ route('admin.availability.slots') }}";
    let resAvailableDates = @json($availableDates ?? []);
    let resSelectedDate = null;
    let resSelectedSlotId = null;
    let resCurrentMonth = new Date().getMonth();
    let resCurrentYear = new Date().getFullYear();
    let resDonationId = null;

    function toLocalYMD(date) {
        const y = date.getFullYear();
        const m = String(date.getMonth() + 1).padStart(2, '0');
        const d = String(date.getDate()).padStart(2, '0');
        return `${y}-${m}-${d}`;
    }

    function renderRescheduleCalendar() {
        const cal = document.getElementById('reschedule-calendar');
        const monthNames = ['January','February','March','April','May','June','July','August','September','October','November','December'];
        const dayNames = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];

        const firstDay = new Date(resCurrentYear, resCurrentMonth, 1);
        let html = `
            <div class="calendar-header">
                <button type="button" class="calendar-nav-btn" onclick="resNav(-1)">&lt;</button>
                <div class="calendar-month-year">${monthNames[resCurrentMonth]} ${resCurrentYear}</div>
                <button type="button" class="calendar-nav-btn" onclick="resNav(1)">&gt;</button>
            </div>
            <div class="calendar-grid">
        `;
        dayNames.forEach(d => html += `<div class="calendar-day-header">${d}</div>`);

        const startDate = new Date(firstDay);
        startDate.setDate(startDate.getDate() - firstDay.getDay());
        const now = new Date();
        const todayStart = new Date(now.getFullYear(), now.getMonth(), now.getDate()).getTime();
        for (let i=0;i<42;i++){
            const date = new Date(startDate.getTime() + i*86400000);
            const dateStr = toLocalYMD(date);
            const isCurrentMonth = date.getMonth() === resCurrentMonth;
            const dayStart = new Date(date.getFullYear(), date.getMonth(), date.getDate()).getTime();
            const isPast = dayStart < todayStart;
            const isAvailable = resAvailableDates.includes(dateStr);
            const isSelected = resSelectedDate === dateStr;
            let cls = 'calendar-day';
            if (!isCurrentMonth) cls += ' other-month';
            else if (isPast) cls += ' past';
            else if (isAvailable) cls += ' available';
            else cls += ' unavailable';
            if (isSelected) cls += ' selected';
            const click = (!isPast && isAvailable && isCurrentMonth) ? `onclick=\"resSelect('${dateStr}')\"` : '';
            html += `<div class="${cls}" ${click}>${date.getDate()}</div>`;
        }
        html += '</div>';
        cal.innerHTML = html;
    }

    function resNav(dir){
        resCurrentMonth += dir;
        if (resCurrentMonth > 11){ resCurrentMonth = 0; resCurrentYear++; }
        else if (resCurrentMonth < 0){ resCurrentMonth = 11; resCurrentYear--; }
        renderRescheduleCalendar();
    }

    function resSelect(dateStr){
        resSelectedDate = dateStr;
        document.getElementById('res_selected_date').value = dateStr;
        renderRescheduleCalendar();
        fetch(`${RES_SLOTS_URL}?date=${encodeURIComponent(dateStr)}`)
            .then(r=>r.json())
            .then(data=>{
                const btn = document.getElementById('reschedule-submit-btn');
                const statusEl = document.getElementById('res-slot-status');
                if (data.available_slots && data.available_slots.length > 0){
                    resSelectedSlotId = data.available_slots[0].id;
                    document.getElementById('res_availability_id').value = resSelectedSlotId;
                    statusEl.textContent = 'Slot available. Click Confirm Reschedule to proceed.';
                    statusEl.className = 'mt-2 d-block text-success';
                    btn.disabled = false;
                } else {
                    resSelectedSlotId = null;
                    document.getElementById('res_availability_id').value = '';
                    statusEl.textContent = 'No availability for the selected date. Please choose another date.';
                    statusEl.className = 'mt-2 d-block text-danger';
                    btn.disabled = true;
                }
            })
            .catch(()=>{
                const btn = document.getElementById('reschedule-submit-btn');
                const statusEl = document.getElementById('res-slot-status');
                resSelectedSlotId = null;
                document.getElementById('res_availability_id').value = '';
                statusEl.textContent = 'Unable to load availability. Please try again.';
                statusEl.className = 'mt-2 d-block text-warning';
                btn.disabled = true;
            });
    }

    function openRescheduleModal(donationId){
        resDonationId = donationId;
        // Set form action dynamically
        const form = document.getElementById('rescheduleForm');
        form.action = "{{ url('/user/donations') }}/" + donationId + "/reschedule-walkin";

        // Reset state
        resSelectedDate = null;
        resSelectedSlotId = null;
        document.getElementById('res_availability_id').value = '';
        document.getElementById('res_selected_date').value = '';
        const statusEl = document.getElementById('res-slot-status');
        statusEl.textContent = 'Select a highlighted date to check availability.';
        statusEl.className = 'mt-2 d-block text-muted';

        // Jump to first available date's month if present
        if (Array.isArray(resAvailableDates) && resAvailableDates.length){
            try { const d = new Date(resAvailableDates[0]); resCurrentMonth = d.getMonth(); resCurrentYear = d.getFullYear(); } catch(e){ /* ignore */ }
        } else {
            const now = new Date(); resCurrentMonth = now.getMonth(); resCurrentYear = now.getFullYear();
        }
        renderRescheduleCalendar();

        const modal = new bootstrap.Modal(document.getElementById('rescheduleWalkInModal'));
        modal.show();
    }

    function confirmCancel(e){
        e.preventDefault();
        const form = e.target;
        if (window.Swal){
            Swal.fire({
                title: 'Cancel donation?',
                text: 'This will cancel your pending donation request.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, cancel',
                cancelButtonText: 'Keep',
                confirmButtonColor: '#d33'
            }).then(res=>{ if (res.isConfirmed) form.submit(); });
            return false;
        }
        // Fallback
        if (confirm('Are you sure you want to cancel this donation?')) form.submit();
        return false;
    }
</script>
@endsection