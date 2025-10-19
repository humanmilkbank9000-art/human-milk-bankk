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
        }

        .modal-content {
            position: relative;
            z-index: 10003;
            /* Higher than modal-dialog's z-index */
        }
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

        .calendar-container {
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            padding: 15px;
            background: #fff;
        }

        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .calendar-nav-btn {
            background: #007bff;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
        }

        .calendar-nav-btn:hover {
            background: #0056b3;
        }

        .calendar-month-year {
            font-weight: bold;
            font-size: 1.1em;
        }

        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 2px;
        }

        .calendar-day-header {
            text-align: center;
            font-weight: bold;
            padding: 8px;
            background: #f8f9fa;
            font-size: 0.9em;
        }

        .calendar-day {
            text-align: center;
            padding: 10px 5px;
            cursor: pointer;
            border-radius: 4px;
            transition: all 0.2s;
            min-height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .calendar-day.other-month {
            color: #ccc;
            cursor: not-allowed;
        }

        .calendar-day.past {
            color: #999;
            cursor: not-allowed;
        }

        .calendar-day.available {
            background: #28a745;
            color: white;
            font-weight: bold;
        }

        .calendar-day.available:hover {
            background: #218838;
            transform: scale(1.05);
        }

        .calendar-day.selected {
            background: #007bff;
            color: white;
            font-weight: bold;
            border: 2px solid #0056b3;
        }

        .calendar-day.unavailable {
            background: #f8f9fa;
            color: #6c757d;
        }

        .calendar-day.unavailable:hover {
            background: #e9ecef;
        }
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
    <script>
        let selectedSlotId = null;
        let currentMonth = new Date().getMonth();
        let currentYear = new Date().getFullYear();
        let availableDates = @json($availableDates ?? []);
        let selectedDate = null;

        // Initialize calendar when walk-in modal opens
        document.getElementById('walkInModal').addEventListener('shown.bs.modal', function () {
            generateCalendar();
        });

        // Reset forms when modals are closed
        document.getElementById('walkInModal').addEventListener('hidden.bs.modal', function () {
            selectedSlotId = null;
            selectedDate = null;
            document.getElementById('time-slots-container').style.display = 'none';
            document.getElementById('walk-in-submit-btn').disabled = true;
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

        // Add SweetAlert confirmation for Home Collection Form
        document.getElementById('homeCollectionForm').addEventListener('submit', function (e) {
            e.preventDefault();
            const form = this;
            const bagCount = document.getElementById('bags_home').value;
            const totalVolume = document.getElementById('total_home').textContent;

            Swal.fire({
                title: 'Confirm Home Collection Donation?',
                html: `Are you sure you want to schedule a home collection with:<br>
                                   <strong>${bagCount} bag(s)</strong> totaling <strong>${totalVolume} ml</strong>?<br><br>
                                   <small class="text-muted">Admin will contact you to schedule a pickup time.</small>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#6f42c1',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="bi bi-check-circle me-1"></i> Yes, Schedule',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });

        function generateCalendar() {
            const calendarContainer = document.getElementById('appointment-calendar');
            const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
                'July', 'August', 'September', 'October', 'November', 'December'];
            const dayNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

            const firstDay = new Date(currentYear, currentMonth, 1);
            const lastDay = new Date(currentYear, currentMonth + 1, 0);
            const today = new Date();

            let calendarHTML = `
                                                    <div class="calendar-header">
                                                        <button type="button" class="calendar-nav-btn" onclick="navigateMonth(-1)">‹</button>
                                                        <div class="calendar-month-year">${monthNames[currentMonth]} ${currentYear}</div>
                                                        <button type="button" class="calendar-nav-btn" onclick="navigateMonth(1)">›</button>
                                                    </div>
                                                    <div class="calendar-grid">
                                                `;

            // Day headers
            dayNames.forEach(day => {
                calendarHTML += `<div class="calendar-day-header">${day}</div>`;
            });

            // Empty cells for days before the first day of the month
            const startingDayOfWeek = firstDay.getDay();
            for (let i = 0; i < startingDayOfWeek; i++) {
                const prevMonthDay = new Date(currentYear, currentMonth, 0 - (startingDayOfWeek - 1 - i));
                calendarHTML += `<div class="calendar-day other-month">${prevMonthDay.getDate()}</div>`;
            }

            // Days of the current month
            for (let day = 1; day <= lastDay.getDate(); day++) {
                const currentDate = new Date(currentYear, currentMonth, day);
                const dateString = currentDate.toISOString().split('T')[0];
                const isPast = currentDate < today.setHours(0, 0, 0, 0);
                const isAvailable = availableDates.includes(dateString);
                const isSelected = selectedDate === dateString;

                let dayClass = 'calendar-day';
                if (isPast) {
                    dayClass += ' past';
                } else if (isAvailable) {
                    dayClass += ' available';
                } else {
                    dayClass += ' unavailable';
                }

                if (isSelected) {
                    dayClass += ' selected';
                }

                const clickHandler = (!isPast && isAvailable) ? `onclick="selectDate('${dateString}')"` : '';
                calendarHTML += `<div class="${dayClass}" ${clickHandler}>${day}</div>`;
            }

            calendarHTML += '</div>';
            calendarContainer.innerHTML = calendarHTML;
        }

        function navigateMonth(direction) {
            currentMonth += direction;
            if (currentMonth > 11) {
                currentMonth = 0;
                currentYear++;
            } else if (currentMonth < 0) {
                currentMonth = 11;
                currentYear--;
            }
            generateCalendar();
        }

        function selectDate(dateString) {
            selectedDate = dateString;
            document.getElementById('selected_date').value = dateString;

            // Update calendar display
            generateCalendar();

            // Load available slots for this date
            loadAvailableSlots(dateString);
        }

        function loadAvailableSlots(date) {
            const slotsContainer = document.getElementById('available-slots');
            const timeContainer = document.getElementById('time-slots-container');

            slotsContainer.innerHTML = '<div class="text-center"><div class="spinner-border spinner-border-sm" role="status"></div> Loading available times...</div>';
            timeContainer.style.display = 'block';

            fetch(`/admin/availability/slots?date=${date}`)
                .then(response => response.json())
                .then(data => {
                    slotsContainer.innerHTML = '';

                    if (data.available_slots && data.available_slots.length > 0) {
                        data.available_slots.forEach(slot => {
                            const slotDiv = document.createElement('div');
                            slotDiv.className = 'form-check mb-2';
                            slotDiv.innerHTML = `
                                                                    <input class="form-check-input" type="radio" name="availability_id"
                                                                        value="${slot.id}" id="slot_${slot.id}" onchange="selectTimeSlot(${slot.id})">
                                                                    <label class="form-check-label" for="slot_${slot.id}">
                                                                        <strong>${slot.formatted_time}</strong>
                                                                    </label>
                                                                `;
                            slotsContainer.appendChild(slotDiv);
                        });
                    } else {
                        slotsContainer.innerHTML = '<div class="alert alert-warning">No available time slots for this date.</div>';
                    }

                    updateSubmitButton();
                })
                .catch(error => {
                    console.error('Error loading slots:', error);
                    slotsContainer.innerHTML = '<div class="alert alert-danger">Error loading available times. Please try again.</div>';
                });
        }

        function selectTimeSlot(slotId) {
            selectedSlotId = slotId;
            updateSubmitButton();
        }

        function updateSubmitButton() {
            // Walk-in form
            const walkInBtn = document.getElementById('walk-in-submit-btn');
            if (walkInBtn) {
                walkInBtn.disabled = !selectedSlotId || !selectedDate;
            }

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

        // Generate individual bag volume input fields
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

        // Calculate total from individual bag volumes
        function calculateIndividualTotal() {
            const bagCount = parseInt(document.getElementById('bags_home').value) || 0;
            let total = 0;

            for (let i = 1; i <= bagCount; i++) {
                const volumeInput = document.getElementById(`bag_volume_${i}`);
                if (volumeInput && volumeInput.value) {
                    total += parseFloat(volumeInput.value) || 0;
                }
            }

            // Remove .00 from whole numbers
            const displayTotal = total % 1 === 0 ? Math.round(total) : total.toFixed(2).replace(/\.?0+$/, '');
            document.getElementById('total_home').textContent = displayTotal;
            updateSubmitButton();
        }

        // Add event listeners
        document.addEventListener('DOMContentLoaded', function () {
            const bagsHome = document.getElementById('bags_home');

            if (bagsHome) {
                bagsHome.addEventListener('input', generateBagVolumeFields);
            }
        });
    </script>
@endsection