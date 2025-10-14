<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Infant Registration - Human Milk Bank</title>
    
    <!-- Preload critical images to prevent FOUC -->
    <link rel="preload" as="image" href="{{ asset('hmblsc-logo.jpg') }}" fetchpriority="high">
    
    <!-- Load Quicksand (headings) and Merriweather (body) from Google Fonts as per design system -->
    <link
        href="https://fonts.googleapis.com/css2?family=Quicksand:wght@600;700&family=Merriweather:wght@400;500&display=swap"
        rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --heading-font: 'Quicksand', system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            --body-font: 'Merriweather', Georgia, 'Times New Roman', serif;
            --line-height: 1.6;
            --pink-primary: #ec4899;
            --gray-primary: #6b7280;
            --gray-dark: #374151;
            --green-success: #10b981;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, #fef3f8 0%, #fce7f3 100%);
            min-height: 100vh;
            font-family: var(--body-font);
            line-height: var(--line-height);
            padding: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .container {
            max-width: 780px;
            margin: 0 auto;
            background: white;
            border-radius: 1rem;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.08);
            padding: 1.25rem 1.5rem;
        }

        .header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .logo {
            width: 55px;
            height: 55px;
            border-radius: 50%;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            flex-shrink: 0;
        }

        .logo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            /* Prevent FOUC - smooth fade in */
            opacity: 0;
            animation: fadeInRegisterInfantLogo 0.4s ease-in forwards;
        }
        
        @keyframes fadeInRegisterInfantLogo {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        .page-title {
            font-family: var(--heading-font);
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--pink-primary);
            margin: 0;
        }

        .success-message {
            background-color: #d1fae5;
            border: 1px solid #a7f3d0;
            border-radius: 0.4rem;
            padding: 0.5rem 0.75rem;
            margin-bottom: 0.875rem;
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .success-message-text {
            color: #065f46;
            font-family: var(--body-font);
            font-size: 0.8rem;
            margin: 0;
        }

        .close-btn {
            margin-left: auto;
            background: none;
            border: none;
            color: #065f46;
            cursor: pointer;
            font-size: 1.15rem;
            padding: 0;
            line-height: 1;
        }

        .error-list {
            background-color: #fee2e2;
            border: 1px solid #fecaca;
            border-radius: 0.4rem;
            padding: 0.5rem 0.75rem;
            margin-bottom: 0.875rem;
        }

        .error-list ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .error-list li {
            color: #dc2626;
            font-family: var(--body-font);
            font-size: 0.8rem;
            padding: 0.15rem 0;
        }

        .error-list li:before {
            content: "• ";
            font-weight: bold;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-bottom: 0.75rem;
        }

        .form-group {
            margin-bottom: 0.75rem;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        .form-label {
            display: block;
            font-family: var(--body-font);
            font-size: 0.8rem;
            font-weight: 500;
            color: var(--gray-dark);
            margin-bottom: 0.3rem;
        }

        .form-input {
            width: 100%;
            padding: 0.55rem 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 0.4rem;
            font-family: var(--body-font);
            font-size: 0.8rem;
            transition: all 0.3s ease;
            outline: none;
            background: #f9fafb;
        }

        .form-input:focus {
            border-color: var(--pink-primary);
            box-shadow: 0 0 0 3px rgba(236, 72, 153, 0.1);
            background: white;
        }

        .form-input[readonly] {
            background: #e5e7eb;
            cursor: not-allowed;
        }

        .radio-group {
            display: flex;
            gap: 1.25rem;
            margin-top: 0.3rem;
        }

        .radio-option {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .radio-option input[type="radio"] {
            width: 16px;
            height: 16px;
            cursor: pointer;
            accent-color: var(--pink-primary);
        }

        .radio-option label {
            font-family: var(--body-font);
            font-size: 0.8rem;
            color: var(--gray-dark);
            cursor: pointer;
        }

        .button-group {
            display: flex;
            justify-content: flex-end;
            gap: 0.875rem;
            margin-top: 1rem;
        }

        .btn {
            padding: 0.55rem 1.5rem;
            border: none;
            border-radius: 0.4rem;
            font-family: var(--heading-font);
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            cursor: pointer;
            transition: all 0.3s ease;
            letter-spacing: 0.5px;
            min-width: 100px;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }

        .btn-back {
            background: #6b7280;
            color: white;
        }

        .btn-back:hover {
            background: #4b5563;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(107, 114, 128, 0.3);
        }

        .btn-submit {
            background: linear-gradient(135deg, var(--pink-primary) 0%, #f472b6 100%);
            color: white;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(236, 72, 153, 0.3);
        }

        .btn:active {
            transform: translateY(0);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            body {
                padding: 0.5rem 0.375rem; /* Reduced padding */
            }

            .container {
                padding: 1.25rem 1rem; /* More compact */
                border-radius: 1rem;
            }

            .header {
                gap: 0.75rem; /* Tighter gap */
                margin-bottom: 1rem; /* Reduced from 2rem */
            }

            .logo {
                width: 55px; /* Smaller logo */
                height: 55px;
            }

            .page-title {
                font-size: 1.35rem; /* Smaller title */
            }

            .form-row {
                grid-template-columns: 1fr;
                gap: 0.75rem; /* Reduced gap */
            }

            .form-group {
                margin-bottom: 0.625rem; /* Tighter spacing */
            }

            .form-label {
                font-size: 0.85rem;
                margin-bottom: 0.25rem; /* Tighter */
            }

            .form-input {
                padding: 0.55rem 0.7rem; /* More compact */
                font-size: 16px; /* Prevent iOS zoom */
            }

            .radio-group {
                gap: 1rem; /* Tighter */
                margin-top: 0.25rem;
            }

            .button-group {
                flex-direction: column-reverse;
                margin-top: 0.875rem; /* Reduced */
                gap: 0.625rem;
            }

            .btn {
                width: 100%;
                min-height: 44px;
                padding: 0.55rem 1rem;
            }

            .success-message {
                padding: 0.5rem 0.625rem; /* Compact */
                margin-bottom: 0.75rem;
            }

            .success-message-text {
                font-size: 0.8rem;
            }
        }

        @media (max-width: 480px) {
            body {
                padding: 0.375rem 0.25rem; /* Even tighter */
            }

            .container {
                padding: 1rem 0.875rem; /* Very compact */
                border-radius: 0.875rem;
            }

            .header {
                gap: 0.625rem;
                margin-bottom: 0.875rem; /* Reduced */
            }

            .logo {
                width: 50px; /* Smaller */
                height: 50px;
            }

            .page-title {
                font-size: 1.25rem; /* Smaller */
            }

            .form-group {
                margin-bottom: 0.5rem; /* Tighter */
            }

            .form-label {
                font-size: 0.825rem;
                margin-bottom: 0.2rem;
            }

            .form-input {
                padding: 0.5rem 0.65rem; /* Compact */
                font-size: 16px;
            }

            .radio-group {
                gap: 0.875rem; /* Tighter */
            }

            .button-group {
                margin-top: 0.75rem; /* Reduced */
                gap: 0.5rem;
            }

            .success-message {
                flex-direction: column;
                align-items: flex-start;
                padding: 0.5rem 0.625rem;
            }

            .success-message-text {
                font-size: 0.775rem;
            }

            .close-btn {
                align-self: flex-end;
                margin-left: 0;
            }
        }

        /* ==================== MODAL STYLES ==================== */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            z-index: 9999;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            animation: fadeIn 0.3s ease;
        }

        .modal-overlay.active {
            display: flex;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .modal-content {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 700px;
            max-height: 90vh;
            overflow-y: auto;
            animation: slideUp 0.3s ease;
        }

        @keyframes slideUp {
            from {
                transform: translateY(50px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .modal-header {
            padding: 1.5rem;
            border-bottom: 2px solid #f3f4f6;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: linear-gradient(135deg, #fef3f8 0%, #fce7f3 100%);
        }

        .modal-title {
            font-family: var(--heading-font);
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--pink-primary);
            margin: 0;
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 2rem;
            color: var(--gray-primary);
            cursor: pointer;
            line-height: 1;
            padding: 0;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: all 0.2s ease;
        }

        .modal-close:hover {
            background: rgba(236, 72, 153, 0.1);
            color: var(--pink-primary);
        }

        .modal-body {
            padding: 1.5rem;
        }

        .info-section {
            background: #f9fafb;
            border-radius: 0.5rem;
            padding: 1.25rem;
            margin-bottom: 1.25rem;
        }

        .info-section:last-child {
            margin-bottom: 0;
        }

        .section-title {
            font-family: var(--heading-font);
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--gray-dark);
            margin: 0 0 1rem 0;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--pink-primary);
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .info-item {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        .info-label {
            font-family: var(--body-font);
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--gray-primary);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .info-value {
            font-family: var(--body-font);
            font-size: 0.95rem;
            color: var(--gray-dark);
            font-weight: 500;
        }

        .modal-note {
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 1rem;
            border-radius: 0.5rem;
            margin-top: 1.25rem;
        }

        .modal-note p {
            font-family: var(--body-font);
            font-size: 0.85rem;
            color: #92400e;
            margin: 0;
            line-height: 1.5;
        }

        .modal-footer {
            padding: 1.25rem 1.5rem;
            border-top: 2px solid #f3f4f6;
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
            background: #f9fafb;
        }

        /* Modal Responsive */
        @media (max-width: 768px) {
            .modal-content {
                max-width: 95%;
            }

            .modal-title {
                font-size: 1.25rem;
            }

            .info-grid {
                grid-template-columns: 1fr;
            }

            .modal-footer {
                flex-direction: column-reverse;
            }

            .modal-footer .btn {
                width: 100%;
            }
        }

        @media (max-width: 480px) {
            .modal-header {
                padding: 1rem;
            }

            .modal-body {
                padding: 1rem;
            }

            .modal-title {
                font-size: 1.1rem;
            }

            .info-section {
                padding: 1rem;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <div class="logo">
                <img src="{{ asset('hmblsc-logo.jpg') }}" alt="Human Milk Bank Logo" width="55" height="55" loading="eager">
            </div>
            <h1 class="page-title">Infant's Information</h1>
        </div>

        @if(session('success'))
            <div class="success-message" id="success-message">
                <p class="success-message-text">{{ session('success') }}</p>
                <button class="close-btn" onclick="document.getElementById('success-message').remove()">×</button>
            </div>
        @endif

        @if(session('error'))
            <div class="error-list">
                <ul>
                    <li>{{ session('error') }}</li>
                </ul>
            </div>
        @endif

        @if ($errors->any())
            <div class="error-list">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="/user-register-infant/store" method="POST" id="infantForm">
            @csrf
            <input type="hidden" name="user_id" value="{{ $userData['user_id'] ?? old('user_id') }}">

            <div class="form-row">
                <div class="form-group">
                    <label for="first_name" class="form-label">First Name</label>
                    <input type="text" id="first_name" name="first_name" class="form-input"
                        value="{{ old('first_name', $infantData['first_name'] ?? '') }}" required style="text-transform: capitalize;">
                    <div id="first-name-req" style="display:none; color:#ff5a7a; font-size:0.8em; margin-top:2px;">
                        Special characters like <, >, =, ', " are not allowed in names.
                    </div>
                    <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        var firstNameInput = document.getElementById('first_name');
                        var firstNameReq = document.getElementById('first-name-req');
                        firstNameInput.addEventListener('input', function() {
                            if (firstNameInput.value === '') {
                                firstNameReq.style.display = 'none';
                            } else if (/[<>=\'\"]/g.test(firstNameInput.value)) {
                                firstNameReq.style.display = 'block';
                            } else {
                                firstNameReq.style.display = 'none';
                            }
                        });
                    });
                    </script>
                </div>

                <div class="form-group">
                    <label for="last_name" class="form-label">Last Name</label>
                    <input type="text" id="last_name" name="last_name" class="form-input" value="{{ old('last_name', $infantData['last_name'] ?? '') }}"
                        required style="text-transform: capitalize;">
                    <div id="last-name-req" style="display:none; color:#ff5a7a; font-size:0.8em; margin-top:2px;">
                        Special characters like <, >, =, ', " are not allowed in names.
                    </div>
                    <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        var lastNameInput = document.getElementById('last_name');
                        var lastNameReq = document.getElementById('last-name-req');
                        lastNameInput.addEventListener('input', function() {
                            if (lastNameInput.value === '') {
                                lastNameReq.style.display = 'none';
                            } else if (/[<>=\'\"]/g.test(lastNameInput.value)) {
                                lastNameReq.style.display = 'block';
                            } else {
                                lastNameReq.style.display = 'none';
                            }
                        });
                    });
                    </script>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="middle_name" class="form-label">Middle Name (optional)</label>
                    <input type="text" id="middle_name" name="middle_name" class="form-input"
                        value="{{ old('middle_name', $infantData['middle_name'] ?? '') }}" style="text-transform: capitalize;">
                    <div id="middle-name-req" style="display:none; color:#ff5a7a; font-size:0.8em; margin-top:2px;">
                        Special characters like <, >, =, ', " are not allowed in names.
                    </div>
                    <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        var middleNameInput = document.getElementById('middle_name');
                        var middleNameReq = document.getElementById('middle-name-req');
                        middleNameInput.addEventListener('input', function() {
                            if (middleNameInput.value === '') {
                                middleNameReq.style.display = 'none';
                            } else if (/[<>=\'\"]/g.test(middleNameInput.value)) {
                                middleNameReq.style.display = 'block';
                            } else {
                                middleNameReq.style.display = 'none';
                            }
                        });
                    });
                    </script>
                </div>

                <div class="form-group">
                    <label for="suffix" class="form-label">Suffix (optional)</label>
                    <input type="text" id="suffix" name="suffix" class="form-input"
                        value="{{ old('suffix', $infantData['suffix'] ?? '') }}" placeholder="e.g., Jr., Sr., III">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Gender</label>
                    <div class="radio-group">
                        <div class="radio-option">
                            <input type="radio" id="female" name="infant_sex" value="female" 
                                {{ old('infant_sex', $infantData['infant_sex'] ?? '') == 'female' ? 'checked' : '' }} required>
                            <label for="female">Female</label>
                        </div>
                        <div class="radio-option">
                            <input type="radio" id="male" name="infant_sex" value="male" 
                                {{ old('infant_sex', $infantData['infant_sex'] ?? '') == 'male' ? 'checked' : '' }}>
                            <label for="male">Male</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="infant_date_of_birth" class="form-label">Birthday</label>
                    <input type="date" id="infant_date_of_birth" name="infant_date_of_birth" class="form-input"
                        value="{{ old('infant_date_of_birth', $infantData['infant_date_of_birth'] ?? '') }}" required>
                </div>

                <div class="form-group">
                    <label for="infant_age_display" class="form-label">Age</label>
                    <input type="text" id="infant_age_display" class="form-input" readonly>
                    <input type="hidden" id="infant_age" name="infant_age">
                </div>
            </div>

            <div class="form-group">
                <label for="birth_weight" class="form-label">Birth weight (kg)</label>
                <input type="number" step="0.01" id="birth_weight" name="birth_weight" class="form-input"
                    value="{{ old('birth_weight', $infantData['birth_weight'] ?? '') }}" min="0" placeholder="e.g., 3.2" required>
            </div>

            <div class="button-group">
                <button type="button" class="btn btn-back" onclick="saveAndGoBack()">Back</button>
                <button type="submit" class="btn btn-submit">Submit</button>
            </div>
        </form>
    </div>

    <script>
        // ==================== AUTO-CAPITALIZATION ====================

        // Capitalize first letter of each word in name fields
        function capitalizeNames(input) {
            input.addEventListener('input', function (e) {
                let value = e.target.value;
                // Capitalize first letter of each word
                e.target.value = value.replace(/\b\w/g, char => char.toUpperCase());
            });
        }

        // Apply to name fields
        capitalizeNames(document.getElementById('first_name'));
        capitalizeNames(document.getElementById('middle_name'));
        capitalizeNames(document.getElementById('last_name'));

        // ==================== AGE CALCULATION ====================

        const dobInput = document.getElementById('infant_date_of_birth');
        const ageInput = document.getElementById('infant_age');
        const ageDisplayInput = document.getElementById('infant_age_display');

        function calculateAge() {
            const dob = new Date(dobInput.value);
            const today = new Date();
            
            // Calculate total age in months
            let totalMonths = (today.getFullYear() - dob.getFullYear()) * 12;
            totalMonths += today.getMonth() - dob.getMonth();
            
            // Adjust if the day hasn't occurred yet in the current month
            if (today.getDate() < dob.getDate()) {
                totalMonths--;
            }
            
            // Store total months in hidden field (for backend)
            ageInput.value = totalMonths >= 0 ? totalMonths : 0;
            
            // Format display based on age
            if (totalMonths < 0) {
                ageDisplayInput.value = '0 months';
            } else if (totalMonths < 12) {
                // Less than 1 year - show only months
                ageDisplayInput.value = totalMonths + (totalMonths === 1 ? ' month' : ' months');
            } else {
                // 1 year or more - show years and months
                const years = Math.floor(totalMonths / 12);
                const months = totalMonths % 12;
                
                let displayText = years + (years === 1 ? ' year' : ' years');
                if (months > 0) {
                    displayText += ' ' + months + (months === 1 ? ' month' : ' months');
                }
                ageDisplayInput.value = displayText;
            }
        }

        dobInput.addEventListener('change', calculateAge);

        // Pre-calculate age if date is already filled (e.g. after validation error)
        if (dobInput.value) {
            calculateAge();
        }

        // ==================== FORM VALIDATION ====================

        const form = document.querySelector('form');
        form.addEventListener('submit', function (e) {
            e.preventDefault(); // Prevent immediate submission

            // Trim all text inputs before validation
            const textInputs = form.querySelectorAll('input[type="text"]');
            textInputs.forEach(input => {
                input.value = input.value.trim();
            });

            // Validate form before showing modal
            if (form.checkValidity()) {
                showConfirmationModal();
            } else {
                // If form is invalid, show validation errors
                form.reportValidity();
            }
        });

        // ==================== SAVE AND GO BACK ====================
        
        function saveAndGoBack() {
            // Create a form to send infant data to session
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/user-register-infant/save-temp';
            
            // Add CSRF token
            const csrfToken = document.querySelector('input[name="_token"]').value;
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = csrfToken;
            form.appendChild(csrfInput);
            
            // Get all form data
            const firstName = document.getElementById('first_name').value.trim();
            const middleName = document.getElementById('middle_name').value.trim();
            const lastName = document.getElementById('last_name').value.trim();
            const infantSex = document.querySelector('input[name="infant_sex"]:checked')?.value || '';
            const infantDob = document.getElementById('infant_date_of_birth').value;
            const birthWeight = document.getElementById('birth_weight').value;
            
            // Add form data as hidden inputs
            const fields = {
                'first_name': firstName,
                'middle_name': middleName,
                'last_name': lastName,
                'infant_sex': infantSex,
                'infant_date_of_birth': infantDob,
                'birth_weight': birthWeight
            };
            
            Object.keys(fields).forEach(key => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = key;
                input.value = fields[key];
                form.appendChild(input);
            });
            
            // Append form to body and submit
            document.body.appendChild(form);
            form.submit();
        }

        // ==================== MODAL FUNCTIONS ====================

        function showConfirmationModal() {
            const modal = document.getElementById('confirmationModal');

            // Populate infant information
            const firstName = document.getElementById('first_name').value;
            const middleName = document.getElementById('middle_name').value;
            const lastName = document.getElementById('last_name').value;
            const fullName = `${firstName} ${middleName} ${lastName}`.replace(/\s+/g, ' ').trim();

            document.getElementById('modal-infant-name').textContent = fullName;

            const infantSex = document.querySelector('input[name="infant_sex"]:checked')?.value || '';
            document.getElementById('modal-infant-sex').textContent = infantSex;

            const infantDob = document.getElementById('infant_date_of_birth').value;
            if (infantDob) {
                const dobDate = new Date(infantDob);
                const options = { year: 'numeric', month: 'long', day: 'numeric' };
                document.getElementById('modal-infant-dob').textContent = dobDate.toLocaleDateString('en-US', options);
            }

            const infantAge = document.getElementById('infant_age').value;
            const ageText = infantAge == 1 ? `${infantAge} year old` : `${infantAge} years old`;
            document.getElementById('modal-infant-age').textContent = ageText;

            const birthWeight = document.getElementById('birth_weight').value;
            document.getElementById('modal-infant-weight').textContent = birthWeight ? `${birthWeight} kg` : '';

            // Show modal
            modal.classList.add('active');
            document.body.style.overflow = 'hidden'; // Prevent background scrolling
        }

        function closeModal() {
            const modal = document.getElementById('confirmationModal');
            modal.classList.remove('active');
            document.body.style.overflow = ''; // Restore scrolling
        }

        function confirmSubmit() {
            // Close modal
            closeModal();

            // Submit the form
            const form = document.querySelector('form');

            // Remove the submit event listener to allow actual submission
            const newForm = form.cloneNode(true);
            form.parentNode.replaceChild(newForm, form);

            // Submit the new form
            newForm.submit();
        }

        // Close modal when clicking outside
        document.getElementById('confirmationModal')?.addEventListener('click', function (e) {
            if (e.target === this) {
                closeModal();
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                closeModal();
            }
        });
    </script>

    <!-- Confirmation Modal -->
    <div id="confirmationModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Confirm Registration Details</h2>
                <button type="button" class="modal-close" onclick="closeModal()">&times;</button>
            </div>

            <div class="modal-body">
                <!-- User Information Section -->
                <div class="info-section">
                    <h3 class="section-title">Parent/Guardian Information</h3>
                    <div class="info-grid">
                        <div class="info-item">
                            <span class="info-label">Full Name:</span>
                            <span class="info-value" id="modal-user-name">{{ $userData['first_name'] ?? '' }}
                                {{ $userData['middle_name'] ?? '' }} {{ $userData['last_name'] ?? '' }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Contact Number:</span>
                            <span class="info-value">{{ $userData['contact_number'] ?? '' }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Address:</span>
                            <span class="info-value">{{ $userData['address'] ?? '' }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Date of Birth:</span>
                            <span class="info-value">
                                @if(isset($userData['date_of_birth']))
                                    {{ \Carbon\Carbon::parse($userData['date_of_birth'])->format('F d, Y') }}
                                @endif
                            </span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Age:</span>
                            <span class="info-value">{{ $userData['age'] ?? '' }} years old</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Sex:</span>
                            <span class="info-value" style="text-transform: capitalize;">{{ $userData['sex'] ?? '' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Infant Information Section -->
                <div class="info-section">
                    <h3 class="section-title">Infant Information</h3>
                    <div class="info-grid">
                        <div class="info-item">
                            <span class="info-label">Full Name:</span>
                            <span class="info-value" id="modal-infant-name"></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Gender:</span>
                            <span class="info-value" id="modal-infant-sex" style="text-transform: capitalize;"></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Date of Birth:</span>
                            <span class="info-value" id="modal-infant-dob"></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Age:</span>
                            <span class="info-value" id="modal-infant-age"></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Birth Weight:</span>
                            <span class="info-value" id="modal-infant-weight"></span>
                        </div>
                    </div>
                </div>

                <div class="modal-note">
                    <p><strong>Note:</strong> Please review the information carefully. Once confirmed, you will be
                        redirected to your dashboard.</p>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-back" onclick="closeModal()">Go Back & Edit</button>
                <button type="button" class="btn btn-submit" onclick="confirmSubmit()">Confirm & Submit</button>
            </div>
        </div>
    </div>
</body>

</html>