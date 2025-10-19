@extends('layouts.user-layout')

@section('title', 'Breastmilk Request')
@section('pageTitle', 'Breastmilk Request')

@section('styles')
    <style>
        /* Main content area - no internal scrolling */
        .container-fluid {
            overflow-x: hidden;
        }

        /* Tab content - dynamic height, no internal scroll */
        .tab-content {
            overflow: visible;
            height: auto;
            min-height: auto;
        }

        .tab-pane {
            overflow: visible;
            height: auto;
            min-height: auto;
        }

        .card {
            overflow: visible;
            height: auto;
        }

        .card-body {
            overflow: visible;
            height: auto;
            max-height: none;
            padding: 1.5rem;
        }

        /* Calendar styles - same as donation */
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

        .calendar-nav-btn:hover {
            background-color: #e9ecef;
        }

        .calendar-month-year {
            font-weight: bold;
            font-size: 1.1rem;
            text-align: center;
            white-space: nowrap;
        }

        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            background-color: white;
            width: 100%;
            min-width: 350px;
            max-width: 480px;
            margin: 0 auto;
        }

        .calendar-day-header {
            background-color: #e9ecef;
            padding: 10px 0;
            text-align: center;
            font-weight: bold;
            font-size: 0.95rem;
            border-bottom: 1px solid #dee2e6;
        }

        /* Responsive calendar on smaller screens */
        @media (max-width: 576px) {
            .calendar-container {
                max-width: 100%;
                margin: 0 auto 20px auto;
            }

            .calendar-grid {
                min-width: 100%;
                max-width: 100%;
            }

            .calendar-header {
                padding: 12px 16px;
            }

            .calendar-month-year {
                font-size: 1rem;
            }
        }

        /* Time slots horizontal layout */
        #time-slots-container {
            display: flex !important;
            flex-direction: row;
            flex-wrap: wrap;
            gap: 12px;
            justify-content: center;
            align-items: center;
            margin-top: 12px;
            width: 100%;
            position: relative;
        }

        #available-slots {
            display: flex;
            flex-direction: row;
            gap: 12px;
            flex-wrap: wrap;
            justify-content: center;
            align-items: center;
            width: 100%;
        }

        .time-slot {
            min-width: 120px;
            margin-bottom: 0 !important;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .calendar-day {
            aspect-ratio: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            border-right: 1px solid #f0f0f0;
            border-bottom: 1px solid #f0f0f0;
            cursor: pointer;
            transition: all 0.2s;
            position: relative;
        }

        .calendar-day:nth-child(7n) {
            border-right: none;
        }

        .calendar-day.available {
            background-color: #e8f5e8;
            color: #155724;
            font-weight: 500;
        }

        .calendar-day.available:hover {
            background-color: #d4edda;
            transform: scale(1.05);
        }

        .calendar-day.unavailable {
            background-color: #f8f9fa;
            color: #6c757d;
            cursor: not-allowed;
        }

        .calendar-day.past {
            background-color: #f8f9fa;
            color: #adb5bd;
            cursor: not-allowed;
        }

        .calendar-day.selected {
            background-color: #ff89ceff !important;
            color: white !important;
            font-weight: bold;
            transform: scale(1.1);
            z-index: 10;
        }

        /* Make the card headers pink on this page (flat color, no gradient) */
        .card-header.bg-primary {
            background: #ff93c1 !important; /* solid pink */
            border-bottom: 1px solid rgba(255,111,166,0.2) !important;
            color: #ffffff !important; /* white text for contrast */
        }

        /* Table header should match card header (flat pink, white text) */
        .table-standard thead th {
            background: #ff93c1 !important;
            color: #ffffff !important;
            font-weight: 700;
            border-bottom: 0 !important;
        }

        /* Step indicator styles for multi-step form */
        .step-indicator {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background-color: #6c757d;
            color: white;
            font-size: 0.75rem;
            margin-right: 8px;
        }

        .step-indicator.active {
            background-color: #ff89ceff;
        }

        .step-indicator.completed {
            background-color: #28a745;
        }

        /* Infant card styles */
        .infant-card {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .infant-card:hover {
            border-color: #007bff;
            box-shadow: 0 2px 4px rgba(0, 123, 255, 0.1);
        }

        .infant-card.selected {
            border-color: #ff89ceff;
            background-color: #f8f9ff;
            box-shadow: 0 2px 8px rgba(0, 123, 255, 0.2);
        }

        .infant-card input[type="radio"] {
            display: none;
        }

        /* Time slot styles */
        .time-slot {
            border: 2px solid #e9ecef;
            border-radius: 6px;
            padding: 10px 15px;
            margin-bottom: 8px;
            cursor: pointer;
            transition: all 0.2s;
            text-align: center;
        }

        .time-slot:hover {
            border-color: #ff89ceff;
            background-color: #f8f9ff;
        }

        .time-slot input[type="radio"] {
            display: none;
        }

        .time-slot.selected {
            border-color: #ff89ceff;
            background-color: #ff89ceff;
            color: white;
            font-weight: 500;
        }

        /* File upload styles */
        .file-upload-area {
            border: 2px dashed #dee2e6;
            border-radius: 8px;
            padding: 40px 20px;
            text-align: center;
            transition: all 0.2s;
            cursor: pointer;
        }

        .file-upload-area:hover {
            border-color: #007bff;
            background-color: #f8f9ff;
        }

        .file-upload-area.dragover {
            border-color: #007bff;
            background-color: #e8f4fd;
        }

        .file-preview {
            max-width: 200px;
            max-height: 200px;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            margin-top: 10px;
        }

        /* Ensure proper spacing throughout */
        .alert {
            margin-bottom: 1.5rem;
        }

        .mb-3 {
            margin-bottom: 1rem !important;
        }

        .mt-3 {
            margin-top: 1rem !important;
        }

        /* Table responsive without scroll */
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        @media (max-width: 768px) {
            .card-body {
                padding: 1rem;
            }

            .calendar-container {
                margin-bottom: 1rem;
            }

            #time-slots-container {
                margin-top: 1rem;
            }
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($infants->count() == 0)
            <div class="alert alert-warning" role="alert">
                <h5><i class="fas fa-exclamation-triangle"></i> No Infant Registered</h5>
                <p>You need to register at least one infant before submitting a breastmilk request.</p>
                <a href="{{ route('user.register.infant') }}" class="btn btn-primary">Register Infant</a>
            </div>
        @else
            <form id="breastmilkRequestForm" action="{{ route('user.breastmilk-request.store') }}" method="POST"
                enctype="multipart/form-data">
                @csrf

                <!-- Tab Navigation -->
                <ul class="nav nav-tabs mb-4" id="requestTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="infant-tab" data-bs-toggle="tab" data-bs-target="#infant-section"
                            type="button" role="tab">
                            <span class="step-indicator active" id="step1">1</span>
                            Select Infant
                            <span class="tab-completed-badge">✓</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="appointment-tab" data-bs-toggle="tab" data-bs-target="#appointment-section"
                            type="button" role="tab">
                            <span class="step-indicator" id="step2">2</span>
                            Book Appointment
                            <span class="tab-completed-badge">✓</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="prescription-tab" data-bs-toggle="tab"
                            data-bs-target="#prescription-section" type="button" role="tab">
                            <span class="step-indicator" id="step3">3</span>
                            Upload Prescription
                            <span class="tab-completed-badge">✓</span>
                        </button>
                    </li>
                </ul>

                <!-- Tab Content -->
                <div class="tab-content" id="requestTabContent">
                    <!-- Step 1: Infant Selection -->
                    <div class="tab-pane fade show active" id="infant-section" role="tabpanel">
                        <div class="card">
                            <div class="card-header bg-primary text-white rounded-top">
                                <h5 class="mb-0"><i class="fas fa-baby"></i> Select Infant for Breastmilk Request</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-standard table-bordered align-middle">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Birth Date</th>
                                                <th>Age (months)</th>
                                                <th>Sex</th>
                                                <th>Birth Weight (kg)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($infants as $infant)
                                                <tr>
                                                    <td>{{ $infant->first_name }} {{ $infant->middle_name }}
                                                        {{ $infant->last_name }}{{ $infant->suffix ? ' ' . $infant->suffix : '' }}
                                                    </td>
                                                    <td>{{ Carbon\Carbon::parse($infant->date_of_birth)->format('M d, Y') }}</td>
                                                    <td>{{ $infant->getFormattedAge() }}</td>
                                                    <td>{{ $infant->sex === 'male' ? 'Male' : 'Female' }}</td>
                                                    <td>{{ $infant->birth_weight }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                {{-- Hidden input: keep first infant id for submission (non-editable) --}}
                                <input type="hidden" name="infant_id" id="infant_id" value="{{ $infants->first()->infant_id }}">

                                {{-- Selected infant details removed per request --}}

                                <div class="d-flex justify-content-end mt-3">
                                    <button type="button" class="btn btn-primary" id="nextToAppointment">
                                        Next: Book Appointment <i class="fas fa-arrow-right"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Appointment Booking -->
                    <div class="tab-pane fade" id="appointment-section" role="tabpanel">
                        <div class="card">
                            <div class="card-header bg-primary text-white rounded-top">
                                <h5 class="mb-0"><i class="fas fa-calendar-plus"></i> Book Your Appointment</h5>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-info mb-3 rounded shadow-sm">
                                    <strong>Appointment Process:</strong> Select an available date (highlighted in green) to see
                                    available time slots.
                                </div>

                                <div class="row">
                                    <div class="col-12 d-flex flex-column align-items-center">
                                        <label class="form-label">Select Appointment </label>
                                        <div id="appointment-calendar" class="calendar-container">
                                            <!-- Calendar will be generated here -->
                                        </div>
                                        <input type="hidden" name="availability_id" id="selected_availability_id">
                                        <div id="time-slots-container" style="display: none;">
                                            <label class="form-label">Available Time Slots:</label>
                                            <div id="available-slots">
                                                <!-- Time slots will be loaded here -->
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-3" id="selected-appointment-info" style="display: none;">
                                    <div class="alert alert-success">
                                        <h6><i class="fas fa-check-circle"></i> Appointment Selected</h6>
                                        <div id="appointment-details"></div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between mt-3">
                                    <button type="button" class="btn btn-outline-secondary" id="backToInfant">
                                        <i class="fas fa-arrow-left"></i> Back
                                    </button>
                                    <button type="button" class="btn btn-primary" id="nextToPrescription" disabled>
                                        Next: Upload Prescription <i class="fas fa-arrow-right"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 3: Prescription Upload -->
                    <div class="tab-pane fade" id="prescription-section" role="tabpanel">
                        <div class="card">
                            <div class="card-header bg-primary text-white rounded-top">
                                <h5 class="mb-0"><i class="fas fa-file-medical"></i> Upload Prescription</h5>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-warning">
                                    <strong>Important:</strong> Please upload a clear image or PDF of your prescription.
                                    Accepted formats: JPG, PNG, PDF (Max size: 5MB)
                                </div>

                                <div class="mb-3">
                                    <label for="prescription" class="form-label">Prescription File *</label>
                                    <div class="file-upload-area border rounded p-4 text-center bg-light" id="fileUploadArea"
                                        style="cursor:pointer;">
                                        <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                                        <h6>Drop your prescription file here or click to browse</h6>
                                        <p class="text-muted">Supported formats: JPG, PNG, PDF (Max: 5MB)</p>
                                        <input type="file" name="prescription" id="prescription" accept=".jpg,.jpeg,.png,.pdf"
                                            required style="display: none;">
                                    </div>

                                    <div id="file-preview-container" style="display: none;"
                                        class="mt-3 border rounded p-3 bg-white">
                                        <h6>File Preview:</h6>
                                        <div id="file-preview"></div>
                                        <button type="button" class="btn btn-sm btn-outline-danger mt-2" id="removeFile">
                                            <i class="fas fa-trash"></i> Remove File
                                        </button>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between mt-3">
                                    <button type="button" class="btn btn-outline-secondary px-4 py-2" id="backToAppointment">
                                        <i class="fas fa-arrow-left"></i> Back
                                    </button>
                                    <button type="submit" class="btn btn-success px-4 py-2" id="submitRequest" disabled
                                        style="box-shadow:0 2px 8px rgba(0,0,0,0.08);">
                                        <i class="fas fa-paper-plane"></i> Submit Request
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        @endif
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Available dates from backend
            const availableDates = @json($availableDates);

            // Global variables for calendar and form state
            let currentYear = new Date().getFullYear();
            let currentMonth = new Date().getMonth();
            let selectedDate = null;
            let selectedAvailabilityId = null;
            let selectedInfantId = null;

            // Initialize the form
            initializeForm();
            generateCalendar();

            function initializeForm() {
                // Use first infant as the selected infant (non-editable table)
                const firstInfantId = @json($infants->first()->infant_id);
                selectedInfantId = firstInfantId;
                document.getElementById('infant_id').value = firstInfantId;

                // Mark infant tab as completed immediately since infant is auto-selected
                const infantTab = document.getElementById('infant-tab');
                if (infantTab && selectedInfantId) {
                    infantTab.classList.add('tab-completed');
                }

                // Tab navigation
                document.getElementById('nextToAppointment').addEventListener('click', function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    markTabAsCompleted(); // Mark infant tab as completed
                    showTab('appointment-tab');
                    updateStepIndicators(2);
                });

                document.getElementById('backToInfant').addEventListener('click', function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    showTab('infant-tab');
                    updateStepIndicators(1);
                });

                document.getElementById('nextToPrescription').addEventListener('click', function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    markTabAsCompleted(); // Mark appointment tab as completed
                    showTab('prescription-tab');
                    updateStepIndicators(3);
                });

                document.getElementById('backToAppointment').addEventListener('click', function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    showTab('appointment-tab');
                    updateStepIndicators(2);
                });

                // File upload handlers
                setupFileUpload();
            }

            // Infant details and selection removed: using first infant by default (hidden input)

            // Confirmation on final submit with SweetAlert
            const requestForm = document.getElementById('breastmilkRequestForm');
            const submitBtn = document.getElementById('submitRequest');
            if (requestForm && submitBtn) {
                requestForm.addEventListener('submit', function (e) {
                    // Prevent immediate submit and show confirmation
                    e.preventDefault();
                    Swal.fire({
                        title: 'Submit Request? ',
                        text: 'Are you sure you want to submit this breastmilk request?',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, submit',
                        cancelButtonText: 'Cancel'
                    }).then(result => {
                        if (result.isConfirmed) {
                            // show a small success toast and submit
                            Swal.fire({
                                icon: 'success',
                                title: 'Submitting...',
                                timer: 900,
                                showConfirmButton: false
                            }).then(() => requestForm.submit());
                        }
                    });
                });
            }

            function showTab(tabId) {
                const tabElement = document.querySelector(`#${tabId}`);
                if (tabElement) {
                    const tab = new bootstrap.Tab(tabElement);
                    tab.show();
                }
            }

            function updateStepIndicators(activeStep) {
                // Reset all indicators
                document.querySelectorAll('.step-indicator').forEach((indicator, index) => {
                    indicator.classList.remove('active', 'completed');
                    if (index + 1 < activeStep) {
                        indicator.classList.add('completed');
                    } else if (index + 1 === activeStep) {
                        indicator.classList.add('active');
                    }
                });

                // Enable/disable tabs
                document.getElementById('appointment-tab').disabled = activeStep < 2;
                document.getElementById('prescription-tab').disabled = activeStep < 3;

                // Mark completed tabs with check badge
                markTabAsCompleted();
            }

            // Mark the currently active tab as completed when moving forward
            function markTabAsCompleted() {
                const activeTab = document.querySelector('.nav-link.active');
                if (activeTab && isTabCompleted(activeTab.id)) {
                    activeTab.classList.add('tab-completed');
                }
            }

            // Check if a tab is completed based on its content
            function isTabCompleted(tabId) {
                switch (tabId) {
                    case 'infant-tab':
                        // Infant tab is always completed since we auto-select first infant
                        return selectedInfantId !== null;
                    case 'appointment-tab':
                        // Check if date and time slot are selected
                        return selectedDate !== null && selectedAvailabilityId !== null;
                    case 'prescription-tab':
                        // Check if prescription file is uploaded
                        const fileInput = document.getElementById('prescription_image');
                        return fileInput && fileInput.files.length > 0;
                    default:
                        return false;
                }
            }

            // Calendar functionality (same as donation)
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
                                                <button type="button" class="calendar-nav-btn" onclick="navigateMonth(-1)">&lt;</button>
                                                <div class="calendar-month-year">${monthNames[currentMonth]} ${currentYear}</div>
                                                <button type="button" class="calendar-nav-btn" onclick="navigateMonth(1)">&gt;</button>
                                            </div>
                                            <div class="calendar-grid">
                                        `;

                // Day headers
                dayNames.forEach(day => {
                    calendarHTML += `<div class="calendar-day-header">${day}</div>`;
                });

                // Calculate days to show
                const startDate = new Date(firstDay);
                startDate.setDate(startDate.getDate() - firstDay.getDay());

                for (let i = 0; i < 42; i++) {
                    const date = new Date(startDate);
                    date.setDate(startDate.getDate() + i);
                    const day = date.getDate();
                    const dateString = date.toISOString().split('T')[0];
                    const isPast = date < today.setHours(0, 0, 0, 0);
                    const isCurrentMonth = date.getMonth() === currentMonth;
                    const isAvailable = availableDates.includes(dateString);
                    const isSelected = selectedDate === dateString;

                    let dayClass = 'calendar-day';
                    if (!isCurrentMonth) {
                        dayClass += ' other-month';
                    } else if (isPast) {
                        dayClass += ' past';
                    } else if (isAvailable) {
                        dayClass += ' available';
                    } else {
                        dayClass += ' unavailable';
                    }

                    if (isSelected) {
                        dayClass += ' selected';
                    }

                    const clickHandler = (!isPast && isAvailable && isCurrentMonth) ? `onclick="selectDate('${dateString}')"` : '';
                    calendarHTML += `<div class="${dayClass}" ${clickHandler}>${day}</div>`;
                }

                calendarHTML += '</div>';
                calendarContainer.innerHTML = calendarHTML;
            }

            window.navigateMonth = function (direction) {
                currentMonth += direction;
                if (currentMonth > 11) {
                    currentMonth = 0;
                    currentYear++;
                } else if (currentMonth < 0) {
                    currentMonth = 11;
                    currentYear--;
                }
                generateCalendar();
            };

            window.selectDate = function (dateString) {
                selectedDate = dateString;
                generateCalendar();
                loadAvailableSlots(dateString);
            };

            function loadAvailableSlots(date) {
                const slotsContainer = document.getElementById('available-slots');
                const timeContainer = document.getElementById('time-slots-container');

                slotsContainer.innerHTML = '<div class="text-center"><div class="spinner-border spinner-border-sm" role="status"></div> Loading...</div>';
                timeContainer.style.display = 'block';

                fetch(`/admin/availability/slots?date=${date}`)
                    .then(response => response.json())
                    .then(data => {
                        slotsContainer.innerHTML = '';
                        if (data.available_slots && data.available_slots.length > 0) {
                            data.available_slots.forEach(slot => {
                                const slotDiv = document.createElement('div');
                                slotDiv.className = 'time-slot';
                                slotDiv.innerHTML = `
                                                                                <input type="radio" name="time_slot" value="${slot.id}" id="slot_${slot.id}">
                                                                                <strong>${slot.formatted_time}</strong>
                                                                            `;
                                slotDiv.addEventListener('click', () => selectTimeSlot(slot.id, slot.formatted_time, date));
                                slotsContainer.appendChild(slotDiv);
                            });
                        } else {
                            slotsContainer.innerHTML = '<div class="alert alert-warning">No available slots for this date.</div>';
                        }
                    })
                    .catch(error => {
                        console.error('Error loading slots:', error);
                        slotsContainer.innerHTML = '<div class="alert alert-danger">Error loading slots. Please try again.</div>';
                    });
            }

            function selectTimeSlot(slotId, formattedTime, date) {
                selectedAvailabilityId = slotId;
                document.getElementById('selected_availability_id').value = slotId;

                // Update UI
                document.querySelectorAll('.time-slot').forEach(slot => {
                    slot.classList.remove('selected');
                });
                document.querySelector(`#slot_${slotId}`).closest('.time-slot').classList.add('selected');

                // Mark appointment tab as completed when slot is selected
                markTabAsCompleted();

                // Show appointment details
                const appointmentDetails = document.getElementById('appointment-details');
                appointmentDetails.innerHTML = `
                                                                <strong>Date:</strong> ${new Date(date).toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })}<br>
                                                                <strong>Time:</strong> ${formattedTime}
                                                            `;
                document.getElementById('selected-appointment-info').style.display = 'block';

                // Enable next button
                document.getElementById('nextToPrescription').disabled = false;
            }

            function setupFileUpload() {
                const fileInput = document.getElementById('prescription');
                const uploadArea = document.getElementById('fileUploadArea');
                const previewContainer = document.getElementById('file-preview-container');
                const previewDiv = document.getElementById('file-preview');
                const removeBtn = document.getElementById('removeFile');
                const submitBtn = document.getElementById('submitRequest');

                uploadArea.addEventListener('click', () => fileInput.click());

                uploadArea.addEventListener('dragover', (e) => {
                    e.preventDefault();
                    uploadArea.classList.add('dragover');
                });

                uploadArea.addEventListener('dragleave', () => {
                    uploadArea.classList.remove('dragover');
                });

                uploadArea.addEventListener('drop', (e) => {
                    e.preventDefault();
                    uploadArea.classList.remove('dragover');
                    const files = e.dataTransfer.files;
                    if (files.length > 0) {
                        handleFileSelection(files[0]);
                    }
                });

                fileInput.addEventListener('change', (e) => {
                    if (e.target.files.length > 0) {
                        handleFileSelection(e.target.files[0]);
                        // Mark prescription tab as completed when file is uploaded
                        markTabAsCompleted();
                    }
                });

                removeBtn.addEventListener('click', () => {
                    fileInput.value = '';
                    previewContainer.style.display = 'none';
                    submitBtn.disabled = true;
                    // Remove completed badge when file is removed
                    const prescriptionTab = document.getElementById('prescription-tab');
                    if (prescriptionTab) {
                        prescriptionTab.classList.remove('tab-completed');
                    }
                });

                function handleFileSelection(file) {
                    if (file.size > 5 * 1024 * 1024) { // 5MB
                        alert('File size must be less than 5MB');
                        return;
                    }

                    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'];
                    if (!allowedTypes.includes(file.type)) {
                        alert('Only JPG, PNG, and PDF files are allowed');
                        return;
                    }

                    // Show preview
                    if (file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = (e) => {
                            previewDiv.innerHTML = `<img src="${e.target.result}" class="file-preview" alt="Prescription preview">`;
                        };
                        reader.readAsDataURL(file);
                    } else {
                        previewDiv.innerHTML = `<div class="alert alert-info"><i class="fas fa-file-pdf fa-2x"></i><br>PDF File: ${file.name}</div>`;
                    }

                    previewContainer.style.display = 'block';
                    submitBtn.disabled = false;
                }
            }
        });
    </script>
@endsection