@extends('layouts.user-layout')

@section('title', 'User Donation')
@section('pageTitle', 'Make a Donation')

@section('styles')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        /* Note: Typography is inherited from global typography.css via user-layout.blade.php */

        /* Fix modal z-index and positioning */
        .modal {
            z-index: 10001;
            /* Higher than header's z-index */
        }

        .modal.show {
            /* pointer-events: auto; No need, Bootstrap handles this */
        }

        .modal-dialog {
            margin: 2rem auto;
            position: relative;
            z-index: 10002;
            /* Higher than modal's z-index */
        }

        .modal-content {
            position: relative;
            z-index: 10003;
            /* Higher than modal-dialog's z-index */
        }

        .modal-backdrop {
            z-index: 10000;
        }

        /* SweetAlert z-index fix - must be higher than modal */
        .swal2-container {
            z-index: 20000 !important;
        }

        .swal2-popup {
            z-index: 20001 !important;
        }

        /* Donation Options - Clean Horizontal Layout */
        .donation-options {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            margin: 2rem auto;
            max-width: 1200px;
            padding: 0 1rem;
        }

        /* Medium screens and up: horizontal layout */
        @media (min-width: 768px) {
            .donation-options {
                flex-direction: row;
                gap: 1.5rem;
            }
        }

        /* Donation Button Base Styles */
        .donation-button {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2.5rem 1.5rem;
            border-radius: 0.75rem;
            background: white;
            border: 2px solid transparent;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            text-decoration: none;
            min-height: 280px;
        }

        /* Touch-friendly padding on mobile */
        @media (max-width: 767px) {
            .donation-button {
                padding: 2rem 1.5rem;
                min-height: 240px;
            }
        }

        /* Walk-in Button - Pink Theme */
        .donation-button.walk-in {
            background: linear-gradient(135deg, #ec4899 0%, #db2777 100%);
            border-color: #ec4899;
            color: white;
        }

        .donation-button.walk-in:hover {
            background: linear-gradient(135deg, #db2777 0%, #be185d 100%);
            transform: translateY(-4px);
            box-shadow: 0 8px 28px rgba(236, 72, 153, 0.3);
        }

        /* Home Collection Button - Purple Theme */
        .donation-button.home-collection {
            background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
            border-color: #8b5cf6;
            color: white;
        }

        .donation-button.home-collection:hover {
            background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);
            transform: translateY(-4px);
            box-shadow: 0 8px 28px rgba(139, 92, 246, 0.3);
        }

        /* Icon Styling */
        .donation-button i {
            font-size: 3.5rem;
            margin-bottom: 1rem;
            color: white;
            filter: drop-shadow(0 2px 8px rgba(0, 0, 0, 0.1));
            transition: transform 0.3s ease;
        }

        .donation-button:hover i {
            transform: scale(1.1);
        }

        /* Make specific donation modals use pink header */
        #walkInModal .modal-header,
        #homeCollectionModal .modal-header {
            background: linear-gradient(180deg, #ff93c1 0%, #ff7fb3 100%) !important;
            color: #ffffff !important;
            border-bottom: 1px solid rgba(0, 0, 0, 0.06) !important;
        }

        #walkInModal .modal-header .modal-title,
        #walkInModal .modal-header h5,
        #homeCollectionModal .modal-header .modal-title,
        #homeCollectionModal .modal-header h5 {
            color: #ffffff !important;
        }

        /* Close icon: enforce white SVG with transparent background to avoid black box */
        #homeCollectionModal .modal-header .btn-close,
        #walkInModal .modal-header .btn-close {
            background-color: transparent !important;
            border: none !important;
            box-shadow: none !important;
            opacity: 1 !important;
            width: 1.5rem; height: 1.5rem; padding: 0.25rem;
            text-indent: -9999px; /* hide any stray text */
            background-repeat: no-repeat !important;
            background-position: center !important;
            background-size: 1rem 1rem !important;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 16 16'%3E%3Cpath fill='%23ffffff' d='M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 1 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z'/%3E%3C/svg%3E") !important;
        }
        #homeCollectionModal .modal-header .btn-close:focus,
        #walkInModal .modal-header .btn-close:focus { outline: none !important; box-shadow: none !important; }

    /* Button Title - Segoe UI for emphasis */
        .donation-button h3 {
            margin: 0 0 0.5rem 0;
            font-size: clamp(1.25rem, 3vw, 1.5rem);
            font-weight: 700;
            text-align: center;
            color: white;
            font-family: var(--heading-font);
            line-height: var(--line-height-normal);
            letter-spacing: -0.01em;
        }

        /* Button Description - Quicksand for readability */
        .donation-button p {
            margin: 0;
            text-align: center;
            font-size: clamp(0.9rem, 2vw, 1rem);
            font-weight: 400;
            color: rgba(255, 255, 255, 0.95);
            line-height: var(--line-height-relaxed);
            font-family: var(--body-font);
            letter-spacing: 0.01em;
        }

        /* Responsive text sizing */
        @media (max-width: 767px) {
            .donation-button i {
                font-size: 3rem;
            }

            .donation-button h3 {
                font-size: 1.3rem;
            }

            .donation-button p {
                font-size: 0.95rem;
            }
        }

        /* Calendar styles - mirrored from user breastmilk request */
        .calendar-container {
            border: 1px solid #dee2e6;
            border-radius: 12px;
            overflow: visible;
            max-width: 480px;
            width: 100%;
            margin: 0 auto 24px auto;
            box-shadow: 0 2px 16px rgba(0, 0, 0, 0.06);
            background: #fff;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        .calendar-header {
            display: grid;
            grid-template-columns: 40px 1fr 40px;
            align-items: center;
            background-color: #f8f9fa;
            padding: 18px 24px;
            border-bottom: 1px solid #dee2e6;
            width: 100%;
        }
        .calendar-nav-btn {
            background: none;
            border: none;
            font-size: 18px;
            cursor: pointer;
            padding: 5px 10px;
            border-radius: 4px;
            transition: background-color 0.2s;
        }
        .calendar-nav-btn:hover { background-color: #e9ecef; }
        .calendar-month-year { font-weight: bold; font-size: 1.1rem; text-align: center; white-space: nowrap; }
        .calendar-grid { display: grid; grid-template-columns: repeat(7, 1fr); background-color: white; width: 100%; min-width: 350px; max-width: 480px; margin: 0 auto; }
        .calendar-day-header { background-color: #e9ecef; padding: 10px 0; text-align: center; font-weight: bold; font-size: 0.95rem; border-bottom: 1px solid #dee2e6; }

        /* Responsive calendar on smaller screens */
        @media (max-width: 576px) {
            .calendar-container { max-width: 100%; margin: 0 auto 20px auto; }
            .calendar-grid { min-width: 100%; max-width: 100%; }
            .calendar-header { padding: 12px 16px; }
            .calendar-month-year { font-size: 1rem; }
        }

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
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @php
        $canDonate = false;
        $statusMessage = '';
        $statusClass = '';

        if (!$healthScreening) {
            $statusMessage = 'You need to complete your health screening before you can donate.';
            $statusClass = 'alert-warning';
        } elseif ($healthScreening->status === 'pending') {
            $statusMessage = 'Your health screening is currently pending approval. Please wait for admin approval before donating.';
            $statusClass = 'alert-warning';
        } elseif ($healthScreening->status === 'declined') {
            $statusMessage = 'Your health screening has been declined. You cannot donate at this time.';
            $statusClass = 'alert-danger';
        } elseif ($healthScreening->status === 'accepted') {
            $statusMessage = 'Your health screening has been approved. You can now make donations.';
            $statusClass = 'alert-success';
            $canDonate = true;
        }
    @endphp

    <!-- Health Screening Status Card -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Health Screening Status</h5>
        </div>
        <div class="card-body">
            <div class="alert {{ $statusClass }} mb-3">
                {{ $statusMessage }}
            </div>

            @if (!$healthScreening)
                <a href="{{ route('user.health-screening') }}" class="btn btn-primary">
                    Start Health Screening
                </a>
            @elseif ($healthScreening->status === 'declined')
                <small class="text-muted">
                    Please contact the administrator if you believe this decision was made in error.
                </small>
            @elseif ($healthScreening->status === 'pending')
                <small class="text-muted">
                    Your health screening was submitted on {{ $healthScreening->created_at->format('M d, Y') }}.
                    We will notify you once it has been reviewed.
                </small>
            @endif
        </div>
    </div>

    <!-- Donation Options -->
    @if ($canDonate)
        <div class="donation-options">
            <button type="button" class="donation-button walk-in" data-bs-toggle="modal" data-bs-target="#walkInModal">
                <i class="fas fa-hospital"></i>
                <h3>Walk-in Donation</h3>
                <p>Visit our unit to donate breastmilk</p>
            </button>

            <button type="button" class="donation-button home-collection" data-bs-toggle="modal"
                data-bs-target="#homeCollectionModal">
                <i class="fas fa-home"></i>
                <h3>Home Collection</h3>
                <p>We'll arrange pickup from your home</p>
            </button>
        </div>

        <!-- Walk-in Modal -->


        @component('partials.shared-modal', ['id' => 'walkInModal', 'title' => 'Walk-in Donation', 'secondary' => 'Close', 'primary' => 'Confirm', 'hideFooterButtons' => true])
        @slot('slot')
        @include('partials.donation-walkin-form')
        @endslot
        @endcomponent

        <!-- Home Collection Modal -->



        @component('partials.shared-modal', ['id' => 'homeCollectionModal', 'title' => 'Home Collection', 'secondary' => 'Close', 'primary' => 'Schedule', 'hideFooterButtons' => true])
        @slot('slot')
        @include('partials.donation-home-collection-form')
        @endslot
        @endcomponent
    @endif
@endsection

@section('scripts')
    {{-- PHP Debug: Show first 5 available dates --}}
    <!-- DEBUG: Available dates from PHP: {{ implode(', ', array_slice($availableDates ?? [], 0, 5)) }} -->
    
    <script>
        // Backend URL for fetching availability slots
        const SLOTS_URL = "{{ route('admin.availability.slots') }}";
        // Use let so we can override in-browser for debugging (use ?force_test=1 to enable)
        let availableDates = @json($availableDates ?? []);
        try {
            const params = new URLSearchParams(window.location.search);
            if (params.get('force_test') === '1') {
                // Known-good test array matching admin expected dates for Nov 2025
                availableDates = [
                    '2025-11-01','2025-11-02','2025-11-03','2025-11-04','2025-11-05',
                    '2025-11-06','2025-11-07','2025-11-08','2025-11-09','2025-11-10',
                    '2025-11-11','2025-11-12','2025-11-13','2025-11-23','2025-11-30'
                ];
                console.log('⚙️ Debug override: forcing availableDates for testing (force_test=1)');
            }
        } catch (e) {
            // ignore in older browsers
        }
        let selectedDate = null;
        let selectedSlotId = null;
        let currentMonth = new Date().getMonth();
        let currentYear = new Date().getFullYear();
        
        // Force browser to see this as new code (cache buster v2.1)
        console.log('User Walk-in Calendar v2.1 - Date verification');
        console.log('Raw availableDates from backend:', availableDates);
        console.log('First date:', availableDates[0]);
        console.log('Type check:', typeof availableDates[0], availableDates[0] === '2025-11-01');

            // parseYMD is provided globally by the layout to avoid duplication

        // Initialize vanilla calendar when walk-in modal opens
        document.getElementById('walkInModal').addEventListener('shown.bs.modal', function () {
            // reset state
            selectedDate = null;
            selectedSlotId = null;
            document.getElementById('availability_id').value = '';
            document.getElementById('selected_date').value = '';
            const statusEl = document.getElementById('slot-status');
            const walkInBtn = document.getElementById('walk-in-submit-btn');
            if (statusEl) { statusEl.textContent = 'Select a highlighted date to check availability.'; statusEl.className = 'mt-2 d-block text-muted'; }
            if (walkInBtn) walkInBtn.disabled = true;

            // If admin has opened future dates, show the calendar month that contains the first available date
            if (Array.isArray(availableDates) && availableDates.length > 0) {
                try {
                        const firstDate = parseYMD(availableDates[0]);
                    if (!isNaN(firstDate)) {
                        currentMonth = firstDate.getMonth();
                        currentYear = firstDate.getFullYear();
                    }
                } catch (e) {
                    // fallback to today
                    currentMonth = new Date().getMonth();
                    currentYear = new Date().getFullYear();
                }
            } else {
                currentMonth = new Date().getMonth();
                currentYear = new Date().getFullYear();
            }

            generateCalendar();
        });

        // Reset when modal closes
        document.getElementById('walkInModal').addEventListener('hidden.bs.modal', function () {
            selectedSlotId = null;
            selectedDate = null;
            const walkInBtn = document.getElementById('walk-in-submit-btn');
            if (walkInBtn) walkInBtn.disabled = true;
        });

        document.getElementById('homeCollectionModal').addEventListener('hidden.bs.modal', function () {
            document.getElementById('bags_home').value = '';
            document.getElementById('bag-volumes-container').style.display = 'none';
            document.getElementById('total-volume-display').style.display = 'none';
            document.getElementById('home-submit-btn').disabled = true;
        });

        // Add SweetAlert confirmation for Walk-in Form
        document.getElementById('walkInForm').addEventListener('submit', function (e) {
            e.preventDefault();
            const form = this;

            Swal.fire({
                title: 'Confirm Walk-in Donation?',
                html: `Are you sure you want to schedule this walk-in donation for <strong>${selectedDate}</strong>?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="bi bi-check-circle me-1"></i> Yes, Confirm',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });

        // Hook for the Confirm button in the walk-in modal
        window.submitWalkInDonation = function() {
            const form = document.getElementById('walkInForm');
            const walkInBtn = document.getElementById('walk-in-submit-btn');
            const availInput = document.getElementById('availability_id');
            const dateInput = document.getElementById('selected_date');

            if (!selectedDate || !selectedSlotId) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Select a date',
                    text: 'Please select an available date before confirming.',
                });
                return;
            }

            if (availInput) availInput.value = selectedSlotId;
            if (dateInput) dateInput.value = selectedDate;

            if (form) {
                if (walkInBtn) walkInBtn.disabled = true;
                form.requestSubmit();
            }
        };

        // Calendar functions
        function toLocalYMD(date) {
            const y = date.getFullYear();
            const m = String(date.getMonth() + 1).padStart(2, '0');
            const d = String(date.getDate()).padStart(2, '0');
            return `${y}-${m}-${d}`;
        }

        function generateCalendar() {
            const calendarContainer = document.getElementById('appointment-calendar');
            const monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
            const dayNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

            const firstDay = new Date(currentYear, currentMonth, 1);
            const lastDay = new Date(currentYear, currentMonth + 1, 0);
            const now = new Date();
            const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());

            console.log('=== USER Walk-in Calendar Rendering ===');
            console.log('Current Month/Year:', currentMonth, currentYear);
            console.log('Available Dates Array:', availableDates);
            console.log('Available Dates Type:', typeof availableDates, 'Is Array:', Array.isArray(availableDates));
            console.log('First Available Date:', availableDates[0]);

            let calendarHTML = `
                <div class="calendar-header">
                    <button type="button" class="calendar-nav-btn" onclick="navigateMonth(-1)">&lt;</button>
                    <div class="calendar-month-year">${monthNames[currentMonth]} ${currentYear}</div>
                    <button type="button" class="calendar-nav-btn" onclick="navigateMonth(1)">&gt;</button>
                </div>
                <div class="calendar-grid">
            `;

            dayNames.forEach(day => {
                calendarHTML += `<div class="calendar-day-header">${day}</div>`;
            });

            // Show 6 weeks grid starting from Sunday before firstDay
            const startDate = new Date(firstDay);
            startDate.setDate(startDate.getDate() - firstDay.getDay());
            for (let i = 0; i < 42; i++) {
                // Create date by adding i days to startDate (86400000 ms = 1 day)
                const date = new Date(startDate.getTime() + (i * 86400000));
                const day = date.getDate();
                const dateString = toLocalYMD(date);
                
                // Compare at local start-of-day to prevent timezone-related off-by-one (match admin logic)
                const dayStart = new Date(date.getFullYear(), date.getMonth(), date.getDate()).getTime();
                const todayStart = new Date(now.getFullYear(), now.getMonth(), now.getDate()).getTime();
                const isPast = dayStart < todayStart;
                
                const isCurrentMonth = date.getMonth() === currentMonth;
                const isAvailable = availableDates.includes(dateString);
                const isSelected = selectedDate === dateString;

                // Debug log for November 1-5 specifically
                if (dateString >= '2025-11-01' && dateString <= '2025-11-05') {
                    console.log(`🔍 ${dateString}:`);
                    console.log(`  - Date object: ${date.toString()}`);
                    console.log(`  - isAvailable: ${isAvailable}`);
                    console.log(`  - Check in array: ${availableDates.includes(dateString)}`);
                    console.log(`  - isPast: ${isPast}`);
                    console.log(`  - isCurrentMonth: ${isCurrentMonth}`);
                    
                    // Try to find why it might not match
                    if (!isAvailable) {
                        console.log(`  ⚠️ NOT AVAILABLE - Checking array for similar dates:`);
                        availableDates.forEach((d, idx) => {
                            if (d.indexOf('2025-11') === 0) {
                                console.log(`    [${idx}]: "${d}" (length: ${d.length})`);
                            }
                        });
                    }
                }

                let dayClass = 'calendar-day';
                if (!isCurrentMonth) {
                    dayClass += ' other-month';
                } else if (isPast) {
                    dayClass += ' past';
                } else if (isAvailable) {
                    dayClass += ' available';
                    console.log('✅ HIGHLIGHTED:', dateString);
                } else {
                    dayClass += ' unavailable';
                }

                if (isSelected) dayClass += ' selected';

                const clickHandler = (!isPast && isAvailable && isCurrentMonth) ? `onclick="selectDate('${dateString}')"` : '';
                calendarHTML += `<div class="${dayClass}" ${clickHandler}>${day}</div>`;
            }

            calendarHTML += '</div>';
            calendarContainer.innerHTML = calendarHTML;
            console.log('=== USER Calendar Render Complete ===');
        }

        window.navigateMonth = function(direction) {
            currentMonth += direction;
            if (currentMonth > 11) { currentMonth = 0; currentYear++; }
            else if (currentMonth < 0) { currentMonth = 11; currentYear--; }
            generateCalendar();
        }

        window.selectDate = function(dateString) {
            selectedDate = dateString;
            document.getElementById('selected_date').value = dateString;
            generateCalendar();

            fetch(`${SLOTS_URL}?date=${encodeURIComponent(dateString)}`)
                .then(r => r.json())
                .then(data => {
                    const walkInBtn = document.getElementById('walk-in-submit-btn');
                    const statusEl = document.getElementById('slot-status');
                    if (data.available_slots && data.available_slots.length > 0) {
                        selectedSlotId = data.available_slots[0].id;
                        document.getElementById('availability_id').value = selectedSlotId;
                        if (statusEl) { statusEl.textContent = 'Slot available. Click Confirm to schedule your walk-in.'; statusEl.className = 'mt-2 d-block text-success'; }
                        if (walkInBtn) walkInBtn.disabled = false;
                    } else {
                        selectedSlotId = null;
                        document.getElementById('availability_id').value = '';
                        if (statusEl) { statusEl.textContent = 'No availability for the selected date. Please choose another date.'; statusEl.className = 'mt-2 d-block text-danger'; }
                        if (walkInBtn) walkInBtn.disabled = true;
                    }
                })
                .catch(() => {
                    const walkInBtn = document.getElementById('walk-in-submit-btn');
                    const statusEl = document.getElementById('slot-status');
                    selectedSlotId = null;
                    document.getElementById('availability_id').value = '';
                    if (statusEl) { statusEl.textContent = 'Unable to load availability. Please try again.'; statusEl.className = 'mt-2 d-block text-warning'; }
                    if (walkInBtn) walkInBtn.disabled = true;
                });
        }

        // Home collection helpers (unchanged)
        function updateSubmitButton() {
            // Walk-in button state is managed by flatpickr handlers

            // Home collection form
            const homeBtn = document.getElementById('home-submit-btn');
            if (homeBtn) {
                const bags = document.getElementById('bags_home').value;
                const bagCount = parseInt(bags) || 0;

                // Check if all bag volume inputs are filled
                let allVolumesFilled = bagCount > 0;
                for (let i = 1; i <= bagCount; i++) {
                    const volumeInput = document.getElementById(`bag_volume_${i}`);
                    if (!volumeInput || !volumeInput.value || parseFloat(volumeInput.value) <= 0) {
                        allVolumesFilled = false;
                        break;
                    }
                }

                homeBtn.disabled = !allVolumesFilled;
            }
        }

        function generateBagVolumeFields() {
            const bagCount = parseInt(document.getElementById('bags_home').value) || 0;
            const container = document.getElementById('bag-volume-fields');
            const bagVolumesContainer = document.getElementById('bag-volumes-container');
            const totalDisplay = document.getElementById('total-volume-display');

            if (bagCount <= 0) {
                bagVolumesContainer.style.display = 'none';
                totalDisplay.style.display = 'none';
                container.innerHTML = '';
                updateSubmitButton();
                return;
            }

            bagVolumesContainer.style.display = 'block';
            totalDisplay.style.display = 'block';

            let fieldsHTML = '<div class="row">';
            for (let i = 1; i <= bagCount; i++) {
                fieldsHTML += `
                    <div class="col-md-6 mb-2">
                        <label for="bag_volume_${i}" class="form-label">Bag ${i} Volume (ml):</label>
                        <input type="number"
                            id="bag_volume_${i}"
                            name="bag_volumes[]"
                            class="form-control bag-volume-input"
                            step="0.01"
                            min="0.01"
                            required
                            oninput="calculateIndividualTotal()"
                            onchange="calculateIndividualTotal()">
                    </div>
                `;
            }
            fieldsHTML += '</div>';

            container.innerHTML = fieldsHTML;
            calculateIndividualTotal();
        }

        function calculateIndividualTotal() {
            const bagCount = parseInt(document.getElementById('bags_home').value) || 0;
            let total = 0;

            for (let i = 1; i <= bagCount; i++) {
                const volumeInput = document.getElementById(`bag_volume_${i}`);
                if (volumeInput && volumeInput.value) {
                    total += parseFloat(volumeInput.value) || 0;
                }
            }

            const displayTotal = total % 1 === 0 ? Math.round(total) : total.toFixed(2).replace(/\.?0+$/, '');
            document.getElementById('total_home').textContent = displayTotal;
            updateSubmitButton();
        }

        document.addEventListener('DOMContentLoaded', function () {
            const bagsHome = document.getElementById('bags_home');
            if (bagsHome) {
                bagsHome.addEventListener('input', generateBagVolumeFields);
            }
        });
    </script>
@endsection