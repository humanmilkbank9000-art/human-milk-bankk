@extends('layouts.user-layout')

@section('title', 'Health Screening Form')

@section('styles')
<style>
    .conditional-input {
        display: none;
        margin-top: 5px;
    }

    .info-card {
        border: 1px solid #ddd;
        border-radius: 5px;
        padding: 20px;
        background: #f9f9f9;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .info-card h6 {
        margin-bottom: 15px;
        font-size: 1.1rem;
        font-weight: 600;
        padding-bottom: 10px;
        border-bottom: 2px solid #0d6efd;
    }

    .info-card p {
        margin-bottom: 12px;
        line-height: 1.6;
    }

    .info-card p:last-child {
        margin-bottom: 0;
    }

    .info-card .form-label {
        font-weight: 500;
        margin-bottom: 6px;
        color: #333;
    }

    .info-card .form-control,
    .info-card .form-select {
        border-radius: 4px;
        border: 1px solid #ced4da;
    }

    /* Ensure columns stay side by side */
    .user-info-row {
        display: flex;
        flex-wrap: wrap;
        margin-left: -15px;
        margin-right: -15px;
        gap: 0;
    }

    .user-info-row > .col-md-6 {
        padding-left: 15px;
        padding-right: 15px;
        flex: 0 0 50%;
        max-width: 50%;
    }

    @media (max-width: 768px) {
        .user-info-row > .col-md-6 {
            flex: 0 0 100%;
            max-width: 100%;
            margin-bottom: 20px;
        }
    }

    .translation {
        font-style: italic;
        color: #555;
        font-size: 0.9rem;
        margin-bottom: 5px;
    }

    .section-title {
        margin-top: 30px;
        margin-bottom: 15px;
        font-weight: bold;
        font-size: 1.2rem;
    }

    /* Ensure SweetAlert appears above Bootstrap modals */
    .swal2-container {
        z-index: 9999 !important;
    }

    /* Tab content specific styles */
    .tab-content {
        border: 1px solid #dee2e6;
        border-top: none;
        padding: 20px;
        background-color: #fff;
        min-height: 400px;
    }

    .tab-navigation-buttons {
        margin-top: 20px;
        display: flex;
        justify-content: space-between;
    }

    .review-section {
        margin-bottom: 20px;
        padding: 15px;
        background-color: #f8f9fa;
        border-radius: 5px;
    }

    .review-section h6 {
        color: #0d6efd;
        margin-bottom: 10px;
        border-bottom: 2px solid #0d6efd;
        padding-bottom: 5px;
    }

    .review-item {
        margin-bottom: 10px;
        padding: 8px;
        background-color: white;
        border-radius: 3px;
    }

    .review-item strong {
        color: #333;
    }

    .review-answer {
        display: inline-block;
        margin-left: 10px;
        padding: 2px 8px;
        border-radius: 3px;
    }

    .review-answer.yes {
        background-color: #d4edda;
        color: #155724;
    }

    .review-answer.no {
        background-color: #d1ecf1;
        color: #0c5460;
    }

    /* Mobile-friendly radio buttons */
    .radio-group {
        display: flex;
        gap: 10px;
        margin-top: 8px;
        max-width: 300px;
    }

    .radio-option {
        position: relative;
        flex: 1;
    }

    .radio-option input[type="radio"] {
        position: absolute;
        opacity: 0;
        width: 0;
        height: 0;
    }

    .radio-option label {
        display: block;
        padding: 10px 16px;
        background-color: #f8f9fa;
        border: 2px solid #dee2e6;
        border-radius: 8px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
        font-weight: 500;
        user-select: none;
        min-height: 44px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.95rem;
    }

    .radio-option input[type="radio"]:checked + label {
        background-color: #0d6efd;
        border-color: #0d6efd;
        color: white;
        box-shadow: 0 2px 8px rgba(13, 110, 253, 0.3);
    }

    .radio-option label:hover {
        border-color: #0d6efd;
        background-color: #e7f1ff;
    }

    .radio-option input[type="radio"]:checked + label:hover {
        background-color: #0b5ed7;
    }

    .radio-option label::before {
        content: '';
        display: inline-block;
        width: 18px;
        height: 18px;
        border: 2px solid #dee2e6;
        border-radius: 50%;
        margin-right: 6px;
        transition: all 0.3s ease;
        background-color: white;
    }

    .radio-option input[type="radio"]:checked + label::before {
        background-color: white;
        border-color: white;
        box-shadow: inset 0 0 0 4px #0d6efd;
    }

    /* Yes/No specific colors */
    .radio-option.yes input[type="radio"]:checked + label {
        background-color: #28a745;
        border-color: #28a745;
    }

    .radio-option.yes input[type="radio"]:checked + label::before {
        box-shadow: inset 0 0 0 4px #28a745;
    }

    .radio-option.no input[type="radio"]:checked + label {
        background-color: #6c757d;
        border-color: #6c757d;
    }

    .radio-option.no input[type="radio"]:checked + label::before {
        box-shadow: inset 0 0 0 4px #6c757d;
    }

    /* Question container */
    .question-item {
        padding: 15px;
        background-color: #ffffff;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        margin-bottom: 15px;
        transition: box-shadow 0.2s ease;
    }

    .question-item:hover {
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .question-label {
        font-weight: 500;
        color: #212529;
        margin-bottom: 5px;
        font-size: 1rem;
    }

    /* Mobile optimization */
    @media (max-width: 576px) {
        .radio-group {
            max-width: 100%;
        }
        
        .radio-option label {
            padding: 12px 16px;
            font-size: 1rem;
            min-height: 48px;
        }
    }

    /* Pink table theme for health screening (colors only) */
    .hs-table-card {
        background: #fff0f6; /* pale pink card background */
        border-radius: 10px;
        padding: 10px;
        margin-top: 10px;
    }

    /* Keep the table itself white for contrast inside the pink card */
    .hs-table-card table {
        background: #ffffff;
        border-collapse: separate;
        border-spacing: 0;
        width: 100%;
        border-radius: 8px;
        overflow: hidden;
    }

    /* Pink header row */
    .hs-table-card thead th {
        background: linear-gradient(180deg, #ffd9e8 0%, #ff93c1 100%);
        color: #222222; /* dark text for readability */
        font-weight: 700;
        padding: 0.75rem 0.9rem;
        border-bottom: none;
    }

    /* Subtle pink stripe for even rows */
    .hs-table-card tbody tr:nth-child(even) {
        background: rgba(255, 223, 234, 0.55);
    }

    .hs-table-card tbody tr:hover {
        background: rgba(255, 207, 224, 0.95);
    }

    .hs-table-card td, .hs-table-card th {
        vertical-align: middle;
        border-top: 1px solid rgba(0,0,0,0.03);
    }

    /* Page-wide pink theme overrides (colors only) */
    .tab-content {
        background-color: #fff0f6 !important;
        border: 1px solid rgba(255,111,166,0.14) !important;
    }

    .info-card {
        border-color: rgba(255,111,166,0.18) !important;
        background: #fff6fb !important;
    }

    .info-card h6 {
        border-bottom-color: #ff93c1 !important;
        color: #ff3478 !important;
    }

    .review-section {
        background-color: #fff0f6 !important;
        border: 1px solid rgba(255,111,166,0.12) !important;
    }

    .review-section h6 {
        color: #ff3478 !important;
        border-bottom-color: rgba(255,147,193,0.8) !important;
    }

    .radio-option label {
        background-color: #fff6fb !important;
        border-color: rgba(255,111,166,0.2) !important;
        color: #222 !important;
    }

    .radio-option input[type="radio"]:checked + label {
        background-color: #ff93c1 !important;
        border-color: #ff93c1 !important;
        color: white !important;
        box-shadow: 0 2px 8px rgba(255,83,140,0.18) !important;
    }

    .radio-option label:hover {
        border-color: #ff93c1 !important;
        background-color: #fff0f6 !important;
    }

    .radio-option input[type="radio"]:checked + label::before {
        box-shadow: inset 0 0 0 4px #ff93c1 !important;
    }

    .question-item {
        background-color: #fff9fb !important;
        border-color: rgba(255,111,166,0.12) !important;
    }

    .question-item:hover {
        box-shadow: 0 4px 20px rgba(255,111,166,0.08) !important;
    }

    .nav-tabs .nav-link.active {
        background: linear-gradient(180deg, #ffd9e8 0%, #ff93c1 100%) !important;
        color: #222 !important;
        border-color: rgba(255,147,193,0.6) !important;
    }

    /* Make primary buttons pink inside this page */
    .tab-navigation-buttons .btn-primary,
    #healthScreeningForm .btn-primary {
        background-color: #ff93c1 !important;
        border-color: #ff93c1 !important;
        color: #fff !important;
    }

    /* Review modal header should use pink instead of Bootstrap primary */
    .modal-header.bg-primary {
        background-color: #ff93c1 !important;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
        <h2 class="mb-4">Health Screening Form</h2>

        @php
            $isSubmitted = isset($existing) && $existing;
            $status = $existing->status ?? null;

            $sections = [
                'medical_history' => [
                    ['Have you donated breastmilk before?', 'Nakahatag/naka-donar ka na ba sa imung gatas kaniadto?', false],
                    ['Have you for any reason been deferred as a breastmilk donor? If yes, specify reason', 'Naballbaran na ba ka nga mag-donar sa imung gatas kaniadto? Kung oo, unsay hinungdan?', true],
                    ['Did you have a normal pregnancy and delivery for your most recent pregnancy?', 'Wala ka bay naaging mnga kalisod og komplikasyon sa pinakaulahi nimung pagburos og pagpanganak?', false],
                    ['Do you have any acute or chronic infection such as tuberculosis, hepatitis, systemic disorders? If yes, specify', 'Aduna ka bay gibating mga sakit sama sa Tuberculosis, sakit sa atay or sakit sa dugo? Kung naa, unsa man kini?', true],
                    ['Have you been diagnosed with a chronic non-infectious illness such as diabetes, hypertension, heart disease? If yes, specify', 'Nadayagnos ka ba nga adunay laygay nga dili makatakod nga sakit sama sa diabetes, altapresyon, sakit sa kasingkasing? Kung naa, unsa man kini?', true],
                    ['Have you received any blood transfusion or blood products within the last 12 months?', 'Naabunohan ka ba ug dugo sulod sa niaging 12 ka buwan?', false],
                    ['Have you received any organ or tissue transplant within the last 12 months?', 'Niagi ka ba ug operasyon din nidawat ka ug bahin/parte sa lawas sulod sa nlilabay nga 12 ka bulan?', false],
                    ['Have you had any intake of alcohol within the last 24 hours? If yes, how much', 'Sulod sa 24 oras, naka inum ka ba og bisan unsang ilimnong makahubog? Kung oo, unsa ka daghan?', true],
                    ['Do you use megadose vitamins or pharmacologically active herbal preparations?', 'Gainum ka ba og sobra sa gitakda na mga bitamina og mga produktong adunay sagol na herbal?', false],
                    ['Do you regularly use medications such as hormones, antidiabetics, blood thinners? If yes, specify', 'Kanunay ba ka gagamit o gainum sa mga tambal kung lain ang paminaw sa lawas? Og gainum ka ba sa mga tambal pampugong sa pagburos? Kung oo, unsa ngalan sa tambal?', true],
                    ['Are you a total vegetarian/vegan? If yes, do you supplement with vitamins', 'Ikaw ba dili gakaon sa lain pagkaon kundi utan lang? Kung oo, gainum ka ba mga bitamina?', true],
                    ['Do you use illicit drugs?', 'Gagamit ka ba sa ginadilina mga droga?', false],
                    ['Do you smoke? If yes, how many per day', 'Gapanigarilyo ka ba? Kung oo, pila ka "stick" o pack se ise ka adlaw?', true],
                    ['Are you around people who smoke (passive smoking)?', 'Doul ba ka permi sa mga tao nga gapanigarilyo?', false],
                    ['Have you had breast augmentation surgery using silicone implants?', 'Kaw ba niagi ug operasyon sa imung suso din nagpabutang ug "silicone" O artipisyal na suso?', false],
                ],
                'sexual_history' => [
                    ['Have you ever had Syphilis, HIV, herpes or any STD?', 'Niagi ka ba og bisan unsang sakit sa kinatawo? Sakit na makuha pinaagi sa pakighilawas?', false],
                    ['Do you have multiple sexual partners?', 'Aduna ka bay lain pares sa pakighilawas gawas sa imu bana/kapikas?', false],
                    ['Have you had a sexual partner with risk factors (bisexual, promiscuous, STD, blood transfusion, IV drug use)? If yes, specify', 'Niagi ka ba og pakighilawas ning mga mosunod? Kung oo, specify', true],
                    ['Have you had a tattoo, accidental needlestick or contact with someone else\'s blood? If yes, specify', 'Niagi ka ba og papatik sukad? Niagi ka ba og katusok sa bisan unsang dagom? Kung oo, specify', true],
                ],
                'donor_infant' => [
                    ['Is your child healthy?', 'Himsog ba ang imung anak?', false],
                    ['Was your child delivered full term?', 'Gipanganak ba siya sa saktong buwan?', false],
                    ['Are you exclusively breastfeeding your child?', 'Kaugalingong gatas lang ba nimu ang gipalnum sa bata?', false],
                    ['Is/was your youngest child jaundiced? If yes, specify age and duration', 'Imung kinamanghuran na bata ba niagi og pagdalag sa pamanit? Kung oo, pilay edad sa bata ato nga higayon? Ug unsa ang kadugayon sa pagdalag?', true],
                    ['Have you ever received breastmilk from another mother? If yes, specify when', 'Nakadawat ba ang imung anak og gatas sa laing inahan? Kung oo, kanus.a kini nahtabo?', true],
                ]
            ];
        @endphp

        <!-- Show Status Message for all submissions -->
        @if($isSubmitted)
            <div class="alert alert-{{ $status === 'accepted' ? 'success' : ($status === 'declined' ? 'danger' : 'info') }} mt-3">
                <div class="d-flex align-items-center">
                    <i class="bi bi-{{ $status === 'accepted' ? 'check-circle-fill' : ($status === 'declined' ? 'x-circle-fill' : 'clock-fill') }} me-3" style="font-size: 2rem;"></i>
                    <div>
                        <h5 class="mb-1">
                            @if($status === 'accepted')
                                Health Screening Accepted
                            @elseif($status === 'declined')
                                Health Screening Declined
                            @else
                                Health Screening Under Review
                            @endif
                        </h5>
                        <p class="mb-0">
                            @if($status === 'accepted')
                                Your health screening has been <strong>Accepted</strong>. You are now eligible to donate breastmilk.
                            @elseif($status === 'declined')
                                Your health screening has been <strong>Declined</strong>. Please contact the administrator for more information.
                            @else
                                Your health screening is currently being reviewed by the administrator. You will be notified once a decision has been made.
                            @endif
                        </p>
                        @if(!empty($existing->notes))
                            <div class="mt-2 p-2 bg-light rounded"><strong>Admin Notes:</strong> {{ $existing->notes }}</div>
                        @endif
                        <button class="btn btn-outline-{{ $status === 'accepted' ? 'success' : ($status === 'declined' ? 'danger' : 'primary') }} btn-sm mt-3" data-bs-toggle="modal" data-bs-target="#detailsModal">
                            <i class="bi bi-eye me-1"></i> View Submitted Details
                        </button>
                    </div>
                </div>
            </div>
        @endif

        <!-- Modal for Submitted Details -->
        @if($isSubmitted)
        <div class="modal fade" id="detailsModal" tabindex="-1" aria-labelledby="detailsModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="detailsModalLabel">Submitted Health Screening Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <h6>User Information</h6>
                        @if($user)
                            <p><strong>Name:</strong> {{ $user->first_name }} {{ $user->last_name }}</p>
                            <p><strong>Contact Number:</strong> {{ $user->contact_number }}</p>
                            <p><strong>Address:</strong> {{ $user->address }}</p>
                            <p><strong>Date of Birth:</strong> {{ $user->date_of_birth ? \Carbon\Carbon::parse($user->date_of_birth)->format('M d, Y') : '-' }}</p>
                            <p><strong>Age:</strong> {{ $user->age }}</p>
                            <p><strong>Sex:</strong> {{ ucfirst($user->sex) }}</p>
                        @else
                            <p>No user data found.</p>
                        @endif
                        <hr>
                        <h6>Infant Information</h6>
                        @if($infant)
                            <p><strong>Name:</strong> {{ $infant->first_name }} {{ $infant->last_name }}{{ $infant?->suffix ? ' ' . $infant->suffix : '' }}</p>
                            <p><strong>Sex:</strong> {{ ucfirst($infant->sex) }}</p>
                            <p><strong>Date of Birth:</strong> {{ $infant->date_of_birth ? \Carbon\Carbon::parse($infant->date_of_birth)->format('M d, Y') : '-' }}</p>
                            <p><strong>Age:</strong> {{ $infant->getFormattedAge() }}</p>
                            <p><strong>Birth Weight:</strong> {{ rtrim(rtrim(number_format($infant->birth_weight, 2, '.', ''), '0'), '.') }} kg</p>
                        @else
                            <p>No infant data found.</p>
                        @endif
                        <hr>
                        <h6>Screening Answers</h6>
                        @foreach($sections as $section => $questions)
                            <div class="mb-2"><strong>{{ ucwords(str_replace('_', ' ', $section)) }}</strong></div>
                            @php $qNum = 1; @endphp
                            @foreach($questions as $q)
                                @php
                                    $field = $section . '_' . str_pad($qNum, 2, '0', STR_PAD_LEFT);
                                    $value = $existing->{$field} ?? '';
                                    $details = $existing->{$field.'_details'} ?? '';
                                @endphp
                                <div class="mb-1">
                                    <span>{{ $qNum }}. {{ $q[0] }}</span>
                                    <div class="translation">{{ $q[1] }}</div>
                                    <span class="badge bg-{{ $value == 'yes' ? 'success' : ($value == 'no' ? 'secondary' : 'light') }}">{{ ucfirst($value) ?: 'N/A' }}</span>
                                    @if($details)
                                        <div class="text-muted">Details: {{ $details }}</div>
                                    @endif
                                </div>
                                @php $qNum++; @endphp
                            @endforeach
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Health Screening Form -->
        @if(!$isSubmitted)
            <form id="healthScreeningForm" action="{{ route('health_screening.store') }}" method="POST">
                @csrf
                <input type="hidden" name="infant_id" value="{{ $infant->infant_id ?? '' }}">
                <fieldset @if($status == 'accepted' || $status == 'pending') disabled @endif>
                    
                    <!-- Tabs Navigation -->
                    <ul class="nav nav-tabs" id="healthScreeningTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="user-info-tab" data-bs-toggle="tab" data-bs-target="#user-info" type="button" role="tab">
                                1. User Information
                                <span class="tab-completed-badge">✓</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="infant-tab" data-bs-toggle="tab" data-bs-target="#infant" type="button" role="tab">
                                2. Infant Information
                                <span class="tab-completed-badge">✓</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="medical-history-tab" data-bs-toggle="tab" data-bs-target="#medical-history" type="button" role="tab">
                                3. Medical History
                                <span class="tab-completed-badge">✓</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="sexual-history-tab" data-bs-toggle="tab" data-bs-target="#sexual-history" type="button" role="tab">
                                4. Sexual History
                                <span class="tab-completed-badge">✓</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="donor-infant-tab" data-bs-toggle="tab" data-bs-target="#donor-infant" type="button" role="tab">
                                5. Donor's Infant Questions
                                <span class="tab-completed-badge">✓</span>
                            </button>
                        </li>
                    </ul>

                    <!-- Tabs Content -->
                    <div class="tab-content" id="healthScreeningTabContent">
                        
                        <!-- Tab 1: User Information -->
                        <div class="tab-pane fade show active" id="user-info" role="tabpanel">
                            <h5 class="mb-4">User Information & Basic Details</h5>
                            
                            <div class="row user-info-row">
                                <!-- Left Column: Personal Information -->
                                <div class="col-md-6">
                                    <div class="info-card">
                                        <h6 class="text-primary">Personal Information</h6>
                                        @if($user)
                                            <p><strong>Name:</strong> {{ $user->first_name }} {{ $user->last_name }}</p>
                                            <p><strong>Contact Number:</strong> {{ $user->contact_number }}</p>
                                            <p><strong>Address:</strong> {{ $user->address }}</p>
                                            <p><strong>Date of Birth:</strong> {{ $user->date_of_birth ? \Carbon\Carbon::parse($user->date_of_birth)->format('M d, Y') : '-' }}</p>
                                            <p><strong>Age:</strong> {{ $user->age }}</p>
                                            <p><strong>Sex:</strong> {{ ucfirst($user->sex) }}</p>
                                        @else
                                            <p>No user data found.</p>
                                        @endif
                                    </div>
                                </div>

                                <!-- Right Column: Basic Information Form -->
                                <div class="col-md-6">
                                    <div class="info-card">
                                        <h6 class="text-primary">Basic Information</h6>
                                        <div class="mb-3">
                                            <label for="civil_status" class="form-label">Civil Status <span class="text-danger">*</span></label>
                                            <select name="civil_status" id="civil_status" class="form-control form-select" required>
                                                <option value="">-- Select Status --</option>
                                                <option value="single" {{ old('civil_status', $existing->civil_status ?? '') == 'single' ? 'selected' : '' }}>Single</option>
                                                <option value="married" {{ old('civil_status', $existing->civil_status ?? '') == 'married' ? 'selected' : '' }}>Married</option>
                                                <option value="divorced" {{ old('civil_status', $existing->civil_status ?? '') == 'divorced' ? 'selected' : '' }}>Divorced</option>
                                                <option value="widowed" {{ old('civil_status', $existing->civil_status ?? '') == 'widowed' ? 'selected' : '' }}>Widowed</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="occupation" class="form-label">Occupation <span class="text-danger">*</span></label>
                                            <input type="text" name="occupation" id="occupation" class="form-control"
                                                value="{{ old('occupation', $existing->occupation ?? '') }}" 
                                                placeholder="Enter your occupation" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="type_of_donor" class="form-label">Type of Donor <span class="text-danger">*</span></label>
                                            <select name="type_of_donor" id="type_of_donor" class="form-control form-select" required>
                                                <option value="">-- Select Type --</option>
                                                @php $types = ['community','private','employee','network_office_agency']; @endphp
                                                @foreach($types as $type)
                                                    <option value="{{ $type }}" {{ old('type_of_donor', $existing->type_of_donor ?? '') == $type ? 'selected' : '' }}>
                                                        {{ ucfirst(str_replace('_',' ',$type)) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-navigation-buttons">
                                <div></div>
                                <button type="button" class="btn btn-primary" onclick="nextTab('infant-tab')">Next →</button>
                            </div>
                        </div>

                        <!-- Tab 2: Infant Information -->
                        <div class="tab-pane fade" id="infant" role="tabpanel">
                            <h5 class="mb-4">Infant Information</h5>
                            <div class="info-card">
                                @if($infant)
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Name:</strong> {{ $infant->first_name }} {{ $infant->last_name }}{{ $infant?->suffix ? ' ' . $infant->suffix : '' }}</p>
                                            <p><strong>Sex:</strong> {{ ucfirst($infant->sex) }}</p>
                                            <p><strong>Date of Birth:</strong> {{ $infant->date_of_birth ? \Carbon\Carbon::parse($infant->date_of_birth)->format('M d, Y') : '-' }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Age:</strong> {{ $infant->getFormattedAge() }}</p>
                                            <p><strong>Birth Weight:</strong> {{ rtrim(rtrim(number_format($infant->birth_weight, 2, '.', ''), '0'), '.') }} kg</p>
                                        </div>
                                    </div>
                                @else
                                    <p>No infant data found.</p>
                                @endif
                            </div>

                            <div class="tab-navigation-buttons">
                                <button type="button" class="btn btn-secondary" onclick="previousTab('user-info-tab')">← Previous</button>
                                <button type="button" class="btn btn-primary" onclick="nextTab('medical-history-tab')">Next →</button>
                            </div>
                        </div>

                        <!-- Tab 3: Medical History -->
                        <div class="tab-pane fade" id="medical-history" role="tabpanel">
                            <h5 class="mb-4">Medical History</h5>
                            @php 
                                $section = 'medical_history';
                                $questions = $sections[$section];
                                $qNum = 1;
                            @endphp
                            @foreach($questions as $q)
                                @php
                                    $field = $section . '_' . str_pad($qNum, 2, '0', STR_PAD_LEFT);
                                    $value = old($field, $existing->{$field} ?? '');
                                    $hasConditional = $q[2];
                                @endphp
                                <div class="question-item">
                                    <div class="question-label"><strong>{{ $qNum }}.</strong> {{ $q[0] }}</div>
                                    <div class="translation">{{ $q[1] }}</div>
                                    <div class="radio-group">
                                        <div class="radio-option yes">
                                            <input type="radio" name="{{ $field }}" value="yes" id="{{ $field }}_yes"
                                                {{ $value == 'yes' ? 'checked' : '' }} onclick="toggleConditional('{{ $field }}', {{ $hasConditional ? 'true' : 'false' }})">
                                            <label for="{{ $field }}_yes">Yes</label>
                                        </div>
                                        <div class="radio-option no">
                                            <input type="radio" name="{{ $field }}" value="no" id="{{ $field }}_no"
                                                {{ $value == 'no' ? 'checked' : '' }} onclick="toggleConditional('{{ $field }}', false)">
                                            <label for="{{ $field }}_no">No</label>
                                        </div>
                                    </div>
                                    @if($hasConditional)
                                        <input type="text" name="{{ $field }}_details" class="form-control conditional-input"
                                            id="{{ $field }}_details" placeholder="Please specify..."
                                            value="{{ old($field.'_details', $existing->{$field.'_details'} ?? '') }}">
                                    @endif
                                </div>
                                @php $qNum++; @endphp
                            @endforeach

                            <div class="tab-navigation-buttons">
                                <button type="button" class="btn btn-secondary" onclick="previousTab('infant-tab')">← Previous</button>
                                <button type="button" class="btn btn-primary" onclick="nextTab('sexual-history-tab')">Next →</button>
                            </div>
                        </div>

                        <!-- Tab 4: Sexual History -->
                        <div class="tab-pane fade" id="sexual-history" role="tabpanel">
                            <h5 class="mb-4">Sexual History</h5>
                            @php 
                                $section = 'sexual_history';
                                $questions = $sections[$section];
                                $qNum = 1;
                            @endphp
                            @foreach($questions as $q)
                                @php
                                    $field = $section . '_' . str_pad($qNum, 2, '0', STR_PAD_LEFT);
                                    $value = old($field, $existing->{$field} ?? '');
                                    $hasConditional = $q[2];
                                @endphp
                                <div class="question-item">
                                    <div class="question-label"><strong>{{ $qNum }}.</strong> {{ $q[0] }}</div>
                                    <div class="translation">{{ $q[1] }}</div>
                                    <div class="radio-group">
                                        <div class="radio-option yes">
                                            <input type="radio" name="{{ $field }}" value="yes" id="{{ $field }}_yes"
                                                {{ $value == 'yes' ? 'checked' : '' }} onclick="toggleConditional('{{ $field }}', {{ $hasConditional ? 'true' : 'false' }})">
                                            <label for="{{ $field }}_yes">Yes</label>
                                        </div>
                                        <div class="radio-option no">
                                            <input type="radio" name="{{ $field }}" value="no" id="{{ $field }}_no"
                                                {{ $value == 'no' ? 'checked' : '' }} onclick="toggleConditional('{{ $field }}', false)">
                                            <label for="{{ $field }}_no">No</label>
                                        </div>
                                    </div>
                                    @if($hasConditional)
                                        <input type="text" name="{{ $field }}_details" class="form-control conditional-input"
                                            id="{{ $field }}_details" placeholder="Please specify..."
                                            value="{{ old($field.'_details', $existing->{$field.'_details'} ?? '') }}">
                                    @endif
                                </div>
                                @php $qNum++; @endphp
                            @endforeach

                            <div class="tab-navigation-buttons">
                                <button type="button" class="btn btn-secondary" onclick="previousTab('medical-history-tab')">← Previous</button>
                                <button type="button" class="btn btn-primary" onclick="nextTab('donor-infant-tab')">Next →</button>
                            </div>
                        </div>

                        <!-- Tab 5: Donor's Infant Questions -->
                        <div class="tab-pane fade" id="donor-infant" role="tabpanel">
                            <h5 class="mb-4">Donor's Infant Questions</h5>
                            @php 
                                $section = 'donor_infant';
                                $questions = $sections[$section];
                                $qNum = 1;
                            @endphp
                            @foreach($questions as $q)
                                @php
                                    $field = $section . '_' . str_pad($qNum, 2, '0', STR_PAD_LEFT);
                                    $value = old($field, $existing->{$field} ?? '');
                                    $hasConditional = $q[2];
                                @endphp
                                <div class="question-item">
                                    <div class="question-label"><strong>{{ $qNum }}.</strong> {{ $q[0] }}</div>
                                    <div class="translation">{{ $q[1] }}</div>
                                    <div class="radio-group">
                                        <div class="radio-option yes">
                                            <input type="radio" name="{{ $field }}" value="yes" id="{{ $field }}_yes"
                                                {{ $value == 'yes' ? 'checked' : '' }} onclick="toggleConditional('{{ $field }}', {{ $hasConditional ? 'true' : 'false' }})">
                                            <label for="{{ $field }}_yes">Yes</label>
                                        </div>
                                        <div class="radio-option no">
                                            <input type="radio" name="{{ $field }}" value="no" id="{{ $field }}_no"
                                                {{ $value == 'no' ? 'checked' : '' }} onclick="toggleConditional('{{ $field }}', false)">
                                            <label for="{{ $field }}_no">No</label>
                                        </div>
                                    </div>
                                    @if($hasConditional)
                                        <input type="text" name="{{ $field }}_details" class="form-control conditional-input"
                                            id="{{ $field }}_details" placeholder="Please specify..."
                                            value="{{ old($field.'_details', $existing->{$field.'_details'} ?? '') }}">
                                    @endif
                                </div>
                                @php $qNum++; @endphp
                            @endforeach

                            <div class="tab-navigation-buttons">
                                <button type="button" class="btn btn-secondary" onclick="previousTab('sexual-history-tab')">← Previous</button>
                                <button type="button" class="btn btn-success" onclick="showReviewModal()">Review</button>
                            </div>
                        </div>

                    </div>
                </fieldset>
            </form>
        @endif

        <!-- Review Modal -->
        <div class="modal fade" id="reviewModal" tabindex="-1" aria-labelledby="reviewModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="reviewModalLabel">Review Your Health Screening Answers</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="reviewContent">
                        <!-- Review content will be populated by JavaScript -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Go Back to Edit</button>
                        <button type="button" class="btn btn-success" id="finalSubmitBtn">Submit Health Screening</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Donor Consent Modal -->
        <div class="modal fade" id="consentModal" tabindex="-1" aria-labelledby="consentModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="consentModalLabel">Donor Consent</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>
                            I certify that I am the person being referred as a prospective milk donor to the CDOC Human Milk
                            Bank and Lactation Support Center (HMB-LSC). I have read and understood the information
                            relayed to me and/or the learning materials given to me by the HMB-LSC staff.
                        </p>
                        <p><em>
                            Nagpamatuod nga ako usa ka tao na posibleng maka donor sa gatas para sa human milk bank,
                            Akong nabasa og nasabtan tanan ang mga nakasulat nini pinaagi sa pag splekar og pagpasabot
                            sa ako pinaagi sa bisaya nga pagstorya. Og ako nanumpa nga akong natubag ang mga
                            pangutana nga matuod og sigun sa akong kaalam.
                        </em></p>

                        <p>
                            I confirm that I will answer the Donor's Screening Questionnaire truthfully and to the best of my
                            knowledge.
                        </p>
                        <p><em>
                            Gikumpirma nako nga akong tubagon ang mga pangutana sa Donor's Screening Questionnaire
                            nga matinud-anon og sa labing maayo sa akong kahibalo.
                        </em></p>

                        <p>
                            I consent to an orientation on guidelines for milk donation to be able to ensure proper and clean
                            collection of milk prior to its pasteurization.
                        </p>
                        <p><em>
                            Nisugot ko sa mga gabayan sa pagdonar sa gatas sa inahan para masiguro ang ensakto og
                            limpyo nga pagkuha niini bag o paman kini i proseso.
                        </em></p>
                        <div class="form-check mt-3">
                            <input class="form-check-input" type="checkbox" value="1" id="consentCheckbox">
                            <label class="form-check-label" for="consentCheckbox">
                                I have read and understood the above. <strong>Accept and continue</strong>
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Go Back to Review</button>
                        <button type="button" class="btn btn-primary" id="consentContinueBtn" disabled>Accept and Continue</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="messageModal" tabindex="-1" aria-labelledby="messageModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="messageModalLabel">Notification</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="modalMessage"></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" id="modalOkBtn" data-bs-dismiss="modal">OK</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
        // Tab navigation functions
        function nextTab(tabId) {
            // Mark current tab as completed before moving to next
            markTabAsCompleted();
            
            const tab = new bootstrap.Tab(document.getElementById(tabId));
            tab.show();
            
            // Scroll to top of the tab content
            scrollToTop();
        }

        function previousTab(tabId) {
            const tab = new bootstrap.Tab(document.getElementById(tabId));
            tab.show();
            
            // Scroll to top of the tab content
            scrollToTop();
        }

        // Scroll to top of the page/tab content
        function scrollToTop() {
            // Scroll to the tabs navigation
            const tabsElement = document.getElementById('healthScreeningTabs');
            if (tabsElement) {
                tabsElement.scrollIntoView({ behavior: 'smooth', block: 'start' });
            } else {
                // Fallback: scroll to top of page
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        }

        // Mark the currently active tab as completed
        function markTabAsCompleted() {
            const activeTab = document.querySelector('.nav-tabs .nav-link.active');
            if (activeTab && isTabCompleted(activeTab.id)) {
                activeTab.classList.add('tab-completed');
            }
        }

        // Check if a tab is completed based on its content
        function isTabCompleted(tabId) {
            switch(tabId) {
                case 'user-info-tab':
                    // Check if basic info fields are filled
                    const civilStatus = document.getElementById('civil_status').value;
                    const occupation = document.getElementById('occupation').value;
                    const typeOfDonor = document.getElementById('type_of_donor').value;
                    return civilStatus && occupation && typeOfDonor;
                
                case 'infant-tab':
                    // Infant tab is view-only, mark as completed when visited
                    return true;
                
                case 'medical-history-tab':
                    // Check if all medical history questions are answered
                    return checkSectionCompleted('medical_history', {{ count($sections['medical_history']) }});
                
                case 'sexual-history-tab':
                    // Check if all sexual history questions are answered
                    return checkSectionCompleted('sexual_history', {{ count($sections['sexual_history']) }});
                
                case 'donor-infant-tab':
                    // Check if all donor infant questions are answered
                    return checkSectionCompleted('donor_infant', {{ count($sections['donor_infant']) }});
                
                default:
                    return false;
            }
        }

        // Check if all questions in a section are answered
        function checkSectionCompleted(section, questionCount) {
            for (let i = 1; i <= questionCount; i++) {
                const fieldName = section + '_' + String(i).padStart(2, '0');
                const yesRadio = document.getElementById(fieldName + '_yes');
                const noRadio = document.getElementById(fieldName + '_no');
                
                if (!yesRadio?.checked && !noRadio?.checked) {
                    return false;
                }
            }
            return true;
        }

        // Check and mark tabs on page load
        function checkAllTabsCompletion() {
            const tabs = ['user-info-tab', 'infant-tab', 'medical-history-tab', 'sexual-history-tab', 'donor-infant-tab'];
            tabs.forEach(tabId => {
                const tabElement = document.getElementById(tabId);
                if (tabElement && isTabCompleted(tabId)) {
                    tabElement.classList.add('tab-completed');
                }
            });
        }

        // Add event listeners to form fields to check completion
        function addCompletionListeners() {
            // User info tab fields
            ['civil_status', 'occupation', 'type_of_donor'].forEach(id => {
                document.getElementById(id)?.addEventListener('change', () => {
                    if (isTabCompleted('user-info-tab')) {
                        document.getElementById('user-info-tab').classList.add('tab-completed');
                    }
                });
            });

            // Radio button changes for other sections
            document.querySelectorAll('input[type="radio"]').forEach(radio => {
                radio.addEventListener('change', () => {
                    checkAllTabsCompletion();
                });
            });

            // Capitalize first letter of occupation field
            const occupationField = document.getElementById('occupation');
            if (occupationField) {
                occupationField.addEventListener('input', function(e) {
                    let value = e.target.value;
                    if (value.length > 0) {
                        e.target.value = value.charAt(0).toUpperCase() + value.slice(1);
                    }
                });
            }
        }

        function toggleConditional(field, show) {
            const input = document.getElementById(field + '_details');
            if (input) input.style.display = show ? 'block' : 'none';
            
            // Auto-scroll to next question after answering
            scrollToNextQuestion(field);
        }
        
        // Scroll to the next question after answering current one
        function scrollToNextQuestion(currentField) {
            setTimeout(() => {
                // Find the current question item
                const currentRadio = document.getElementById(currentField + '_yes') || document.getElementById(currentField + '_no');
                if (!currentRadio) return;
                
                const currentQuestionItem = currentRadio.closest('.question-item');
                if (!currentQuestionItem) return;
                
                // Find the next question item
                const nextQuestionItem = currentQuestionItem.nextElementSibling;
                
                if (nextQuestionItem && nextQuestionItem.classList.contains('question-item')) {
                    // Scroll to the next question
                    nextQuestionItem.scrollIntoView({ 
                        behavior: 'smooth', 
                        block: 'center'
                    });
                } else {
                    // If no next question, scroll to navigation buttons
                    const navButtons = currentQuestionItem.parentElement.querySelector('.tab-navigation-buttons');
                    if (navButtons) {
                        navButtons.scrollIntoView({ 
                            behavior: 'smooth', 
                            block: 'center'
                        });
                    }
                }
            }, 300); // Small delay to allow conditional input to show first
        }

        // Show review modal
        function showReviewModal() {
            const form = document.getElementById('healthScreeningForm');
            const formData = new FormData(form);
            
            let reviewHTML = '';

            // User Information & Basic Info
            reviewHTML += '<div class="review-section">';
            reviewHTML += '<h6>User Information & Basic Details</h6>';
            reviewHTML += '<div class="review-item"><strong>Name:</strong> {{ $user->first_name ?? '' }} {{ $user->last_name ?? '' }}</div>';
            reviewHTML += '<div class="review-item"><strong>Contact Number:</strong> {{ $user->contact_number ?? '' }}</div>';
            reviewHTML += '<div class="review-item"><strong>Address:</strong> {{ $user->address ?? '' }}</div>';
            reviewHTML += '<div class="review-item"><strong>Date of Birth:</strong> {{ $user->date_of_birth ? \Carbon\Carbon::parse($user->date_of_birth)->format('M d, Y') : '' }}</div>';
            reviewHTML += '<div class="review-item"><strong>Age:</strong> {{ $user->age ?? '' }}</div>';
            reviewHTML += '<div class="review-item"><strong>Sex:</strong> {{ ucfirst($user->sex ?? '') }}</div>';
            reviewHTML += '<div class="review-item"><strong>Civil Status:</strong> ' + (formData.get('civil_status') || 'N/A') + '</div>';
            reviewHTML += '<div class="review-item"><strong>Occupation:</strong> ' + (formData.get('occupation') || 'N/A') + '</div>';
            reviewHTML += '<div class="review-item"><strong>Type of Donor:</strong> ' + (formData.get('type_of_donor') || 'N/A').replace(/_/g, ' ').toUpperCase() + '</div>';
            reviewHTML += '</div>';

            // Infant Information
            reviewHTML += '<div class="review-section">';
            reviewHTML += '<h6>Infant Information</h6>';
            reviewHTML += '<div class="review-item"><strong>Name:</strong> {{ $infant->first_name ?? '' }} {{ $infant->last_name ?? '' }}{{ $infant?->suffix ? ' ' . $infant->suffix : '' }}</div>';
            reviewHTML += '<div class="review-item"><strong>Sex:</strong> {{ ucfirst($infant->sex ?? '') }}</div>';
            reviewHTML += '<div class="review-item"><strong>Date of Birth:</strong> {{ $infant->date_of_birth ? \Carbon\Carbon::parse($infant->date_of_birth)->format('M d, Y') : '' }}</div>';
            reviewHTML += '<div class="review-item"><strong>Age:</strong> {{ $infant->getFormattedAge() }}</div>';
            reviewHTML += '<div class="review-item"><strong>Birth Weight:</strong> {{ $infant->birth_weight ? rtrim(rtrim(number_format($infant->birth_weight, 2, '.', ''), '0'), '.') : '' }} kg</div>';
            reviewHTML += '</div>';

            // Medical History
            const medicalQuestions = [
                @php $qNum = 1; @endphp
                @foreach($sections['medical_history'] as $q)
                    {
                        field: 'medical_history_{{ str_pad($qNum, 2, "0", STR_PAD_LEFT) }}',
                        question: '{{ addslashes($q[0]) }}',
                        hasDetails: {{ $q[2] ? 'true' : 'false' }}
                    },
                    @php $qNum++; @endphp
                @endforeach
            ];

            reviewHTML += '<div class="review-section">';
            reviewHTML += '<h6>Medical History</h6>';
            medicalQuestions.forEach((q, idx) => {
                const answer = formData.get(q.field);
                const details = formData.get(q.field + '_details');
                reviewHTML += '<div class="review-item">';
                reviewHTML += '<strong>' + (idx + 1) + '.</strong> ' + q.question;
                reviewHTML += ' <span class="review-answer ' + (answer || 'no') + '">' + (answer ? answer.toUpperCase() : 'NO') + '</span>';
                if (q.hasDetails && details) {
                    reviewHTML += '<div class="text-muted mt-1"><em>Details: ' + details + '</em></div>';
                }
                reviewHTML += '</div>';
            });
            reviewHTML += '</div>';

            // Sexual History
            const sexualQuestions = [
                @php $qNum = 1; @endphp
                @foreach($sections['sexual_history'] as $q)
                    {
                        field: 'sexual_history_{{ str_pad($qNum, 2, "0", STR_PAD_LEFT) }}',
                        question: '{{ addslashes($q[0]) }}',
                        hasDetails: {{ $q[2] ? 'true' : 'false' }}
                    },
                    @php $qNum++; @endphp
                @endforeach
            ];

            reviewHTML += '<div class="review-section">';
            reviewHTML += '<h6>Sexual History</h6>';
            sexualQuestions.forEach((q, idx) => {
                const answer = formData.get(q.field);
                const details = formData.get(q.field + '_details');
                reviewHTML += '<div class="review-item">';
                reviewHTML += '<strong>' + (idx + 1) + '.</strong> ' + q.question;
                reviewHTML += ' <span class="review-answer ' + (answer || 'no') + '">' + (answer ? answer.toUpperCase() : 'NO') + '</span>';
                if (q.hasDetails && details) {
                    reviewHTML += '<div class="text-muted mt-1"><em>Details: ' + details + '</em></div>';
                }
                reviewHTML += '</div>';
            });
            reviewHTML += '</div>';

            // Donor's Infant Questions
            const donorInfantQuestions = [
                @php $qNum = 1; @endphp
                @foreach($sections['donor_infant'] as $q)
                    {
                        field: 'donor_infant_{{ str_pad($qNum, 2, "0", STR_PAD_LEFT) }}',
                        question: '{{ addslashes($q[0]) }}',
                        hasDetails: {{ $q[2] ? 'true' : 'false' }}
                    },
                    @php $qNum++; @endphp
                @endforeach
            ];

            reviewHTML += '<div class="review-section">';
            reviewHTML += '<h6>Donor\'s Infant Questions</h6>';
            donorInfantQuestions.forEach((q, idx) => {
                const answer = formData.get(q.field);
                const details = formData.get(q.field + '_details');
                reviewHTML += '<div class="review-item">';
                reviewHTML += '<strong>' + (idx + 1) + '.</strong> ' + q.question;
                reviewHTML += ' <span class="review-answer ' + (answer || 'no') + '">' + (answer ? answer.toUpperCase() : 'NO') + '</span>';
                if (q.hasDetails && details) {
                    reviewHTML += '<div class="text-muted mt-1"><em>Details: ' + details + '</em></div>';
                }
                reviewHTML += '</div>';
            });
            reviewHTML += '</div>';

            // Populate the review modal
            document.getElementById('reviewContent').innerHTML = reviewHTML;
            
            // Show the modal
            const reviewModal = new bootstrap.Modal(document.getElementById('reviewModal'));
            reviewModal.show();
        }

        // Final submit: open consent modal first
        document.getElementById('finalSubmitBtn').addEventListener('click', function() {
            // Hide review modal and show consent modal
            const reviewModal = bootstrap.Modal.getInstance(document.getElementById('reviewModal'));
            if (reviewModal) reviewModal.hide();

            // Reset consent checkbox and button
            const consentCheckbox = document.getElementById('consentCheckbox');
            const consentContinueBtn = document.getElementById('consentContinueBtn');
            if (consentCheckbox) { consentCheckbox.checked = false; }
            if (consentContinueBtn) { consentContinueBtn.disabled = true; }

            const consentModalEl = document.getElementById('consentModal');
            const consentModal = new bootstrap.Modal(consentModalEl);
            consentModal.show();
        });

        // Enable the Accept button only when checkbox is checked
        document.addEventListener('change', function(e) {
            if (e.target && e.target.id === 'consentCheckbox') {
                const consentContinueBtn = document.getElementById('consentContinueBtn');
                consentContinueBtn.disabled = !e.target.checked;
            }
        });

        // When user accepts consent, show confirmation SweetAlert then submit
        document.getElementById('consentContinueBtn').addEventListener('click', function() {
            // Hide consent modal
            const consentModalEl = document.getElementById('consentModal');
            const consentModalInstance = bootstrap.Modal.getInstance(consentModalEl);
            if (consentModalInstance) consentModalInstance.hide();

            // Wait a bit then show confirmation
            setTimeout(() => {
                Swal.fire({
                    title: 'Confirm Submission',
                    text: "Are you sure you want to submit your health screening form?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, Submit!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Submit form via AJAX
                        submitForm();
                    } else if (result.isDismissed) {
                        // If user cancels, show the review modal again
                        const reviewModalElement = document.getElementById('reviewModal');
                        const reviewModalInstance = new bootstrap.Modal(reviewModalElement);
                        reviewModalInstance.show();
                    }
                });
            }, 250);
        });

        function submitForm() {
            let form = $('#healthScreeningForm');

            // Show loading state
            Swal.fire({
                title: 'Submitting...',
                text: 'Please wait while we process your health screening.',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: form.attr('action'),
                method: form.attr('method'),
                data: form.serialize(),
                success: function(res) {
                    Swal.fire({
                        title: 'Success!',
                        text: res.message,
                        icon: 'success',
                        confirmButtonColor: '#28a745',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if(res.redirect) {
                            window.location.href = res.redirect;
                        }
                    });
                },
                error: function(xhr) {
                    console.error('Submission error:', xhr);
                    
                    let msg = 'An error occurred while submitting your health screening. Please try again.';
                    
                    // If there are validation errors, display them
                    if (xhr.status === 422 && xhr.responseJSON?.errors) {
                        let errorMessages = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                        msg = '<strong>Please fix the following errors:</strong><br><br>' + errorMessages;
                    } else if (xhr.responseJSON?.error) {
                        msg = xhr.responseJSON.error;
                    } else if (xhr.responseJSON?.message) {
                        msg = xhr.responseJSON.message;
                    }
                    
                    Swal.fire({
                        title: 'Error!',
                        html: msg,
                        icon: 'error',
                        confirmButtonColor: '#dc3545',
                        confirmButtonText: 'OK'
                    });
                }
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            // Initialize conditional fields visibility
            @foreach($sections as $section => $questions)
                @php $qNum = 1; @endphp
                @foreach($questions as $q)
                    @php
                        $field = $section . '_' . str_pad($qNum, 2, '0', STR_PAD_LEFT);
                        $hasConditional = $q[2];
                    @endphp
                    toggleConditional('{{ $field }}', document.getElementById('{{ $field }}_yes')?.checked && {{ $hasConditional ? 'true' : 'false' }});
                    @php $qNum++; @endphp
                @endforeach
            @endforeach

            // Check tab completion on load
            checkAllTabsCompletion();
            
            // Add listeners for real-time completion checking
            addCompletionListeners();
        });
    </script>
@endsection
