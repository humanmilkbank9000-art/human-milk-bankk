<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Create Account - Human Milk Bank</title>
    
    <!-- Preload critical images to prevent FOUC -->
    <link rel="preload" as="image" href="{{ asset('hmblsc-logo.jpg') }}" fetchpriority="high">
    
    <!-- Load Quicksand (body) from Google Fonts -->
    <link
        href="https://fonts.googleapis.com/css2?family=Quicksand:wght@600;700&display=swap"
        rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --heading-font: 'Quicksand', system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            --body-font: 'Segoe UI', 'Helvetica Neue', Arial, sans-serif;
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
            animation: fadeInRegisterLogo 0.4s ease-in forwards;
        }
        
        @keyframes fadeInRegisterLogo {
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

        .form-input,
        .form-textarea {
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

        .form-input:focus,
        .form-textarea:focus {
            border-color: var(--pink-primary);
            box-shadow: 0 0 0 3px rgba(236, 72, 153, 0.1);
            background: white;
        }

        .form-input::placeholder {
            color: #9ca3af;
        }

        .form-textarea {
            min-height: 50px;
            resize: vertical;
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

        .input-wrapper {
            position: relative;
        }

        .password-toggle {
            position: absolute;
            right: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #6b7280;
            cursor: pointer;
            padding: 0.2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: color 0.3s ease;
        }

        .password-toggle:hover {
            color: var(--pink-primary);
        }

        .password-toggle svg {
            width: 18px;
            height: 18px;
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

        .btn-next {
            background: linear-gradient(135deg, var(--pink-primary) 0%, #f472b6 100%);
            color: white;
        }

        .btn-next:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(236, 72, 153, 0.3);
        }

        .btn:active {
            transform: translateY(0);
        }

        /* Section title for address block */
        .section-title {
            font-family: var(--heading-font);
            font-size: 1rem;
            font-weight: 700;
            color: var(--gray-dark);
            margin: 0.5rem 0 0.25rem 0;
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

            .form-input,
            .form-textarea {
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

            .form-input,
            .form-textarea {
                padding: 0.5rem 0.65rem; /* Compact */
                font-size: 16px;
            }

            .radio-group {
                gap: 0.875rem;
            }

            .button-group {
                margin-top: 0.75rem;
                gap: 0.5rem;
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
            <h1 class="page-title">Create Account</h1>
        </div>

        @if ($errors->any())
            <div class="error-list">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="/user-register/store" method="POST">
            @csrf

            <div class="form-row">
                <div class="form-group">
                    <label for="first_name" class="form-label">First Name</label>
                    <input type="text" id="first_name" name="first_name" class="form-input"
                        value="{{ old('first_name', $userData['first_name'] ?? '') }}" style="text-transform: capitalize;">
                    <div id="first-name-error" style="display:none; color:#ff5a7a; font-size:0.8em; margin-top:2px; font-weight:500;">
                        Please enter your first name.
                    </div>
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
                    <input type="text" id="last_name" name="last_name" class="form-input" value="{{ old('last_name', $userData['last_name'] ?? '') }}"
                        style="text-transform: capitalize;">
                    <div id="last-name-error" style="display:none; color:#ff5a7a; font-size:0.8em; margin-top:2px; font-weight:500;">
                        Please enter your last name.
                    </div>
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
                        value="{{ old('middle_name', $userData['middle_name'] ?? '') }}" style="text-transform: capitalize;">
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
                    <label class="form-label">Gender</label>
                    <div class="radio-group">
                        <div class="radio-option">
                            <input type="radio" id="female" name="sex" value="female" 
                                {{ old('sex', $userData['sex'] ?? '') == 'female' ? 'checked' : '' }}>
                            <label for="female">Female</label>
                        </div>
                        <div class="radio-option">
                            <input type="radio" id="male" name="sex" value="male" 
                                {{ old('sex', $userData['sex'] ?? '') == 'male' ? 'checked' : '' }}>
                            <label for="male">Male</label>
                        </div>
                    </div>
                    <div id="sex-error" style="display:none; color:#ff5a7a; font-size:0.8em; margin-top:2px; font-weight:500;">
                        Please select your gender.
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="date_of_birth" class="form-label">Birthday</label>
                    <input type="date" id="date_of_birth" name="date_of_birth" class="form-input"
                        value="{{ old('date_of_birth', $userData['date_of_birth'] ?? '') }}">
                    <div id="dob-error" style="display:none; color:#ff5a7a; font-size:0.8em; margin-top:2px; font-weight:500;">
                        Please enter your date of birth.
                    </div>
                </div>

                <div class="form-group">
                    <label for="age" class="form-label">Age</label>
                    <input type="number" id="age" name="age" class="form-input" readonly>
                </div>
            </div>

            <div class="form-group">
                <label for="contact_number" class="form-label">Contact Number</label>
                <input type="text" id="contact_number" name="contact_number" class="form-input"
                    value="{{ old('contact_number', $userData['contact_number'] ?? '') }}" placeholder="09XXXXXXXXX" pattern="[0-9]{11}">
                <div id="contact-error" style="display:none; color:#ff5a7a; font-size:0.8em; margin-top:2px; font-weight:500;">
                    Please enter your contact number.
                </div>
                <div id="contact-req" style="display:none; color:#ff5a7a; font-size:0.8em; margin-top:2px;">
                    Contact number must be exactly 11 digits and start with 09.
                </div>
                <script>
                document.addEventListener('DOMContentLoaded', function() {
                    var contactInput = document.getElementById('contact_number');
                    var reqMsg = document.getElementById('contact-req');
                    contactInput.addEventListener('input', function(e) {
                        // Remove non-digit characters
                        contactInput.value = contactInput.value.replace(/\D/g, '');
                        // Show/hide requirements
                        if (contactInput.value.length === 0) {
                            reqMsg.style.display = 'none';
                        } else if (!/^\d{11}$/.test(contactInput.value)) {
                            reqMsg.style.display = 'block';
                        } else {
                            reqMsg.style.display = 'none';
                        }
                    });
                });
                </script>
            </div>

            <div class="form-group full-width">
                <div class="section-title">Please select current address</div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="region" class="form-label">Region</label>
                    <select id="region" name="region" class="form-input"></select>
                    <div id="region-error" style="display:none; color:#ff5a7a; font-size:0.8em; margin-top:2px; font-weight:500;">
                        Please select your region.
                    </div>
                </div>
                <div class="form-group">
                    <label for="province" class="form-label">Province</label>
                    <select id="province" name="province" class="form-input" disabled></select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="city" class="form-label">City/Municipality</label>
                    <select id="city" name="city" class="form-input" disabled></select>
                    <div id="city-error" style="display:none; color:#ff5a7a; font-size:0.8em; margin-top:2px; font-weight:500;">
                        Please select your city/municipality.
                    </div>
                </div>
                <div class="form-group">
                    <label for="barangay" class="form-label">Barangay</label>
                    <select id="barangay" name="barangay" class="form-input" disabled></select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group full-width">
                    <label for="street" class="form-label">House/Street, Subdivision (optional)</label>
                    <input type="text" id="street" name="street" class="form-input" value="{{ old('street') }}" placeholder="e.g., 123 Sampaguita St, Unit 5B">
                </div>
            </div>

            <!-- Hidden composed address to keep backend unchanged -->
            <input type="hidden" id="address" name="address" value="{{ old('address', $userData['address'] ?? '') }}">

            <div class="form-row">
                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-wrapper">
                        <input type="password" id="password" name="password" class="form-input" 
                            value="{{ old('password', $userData['password'] ?? '') }}">
                        <button type="button" class="password-toggle"
                            onclick="togglePassword('password', 'eye-icon-1')">
                            <svg id="eye-icon-1" width="20" height="20" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                        </button>
                    </div>
                    <div id="password-error" style="display:none; color:#ff5a7a; font-size:0.8em; margin-top:2px; font-weight:500;">
                        Please enter a password.
                    </div>
                    <div id="password-req" style="display:none; color:#ff5a7a; font-size:0.8em; margin-top:2px;">
                        Password must be 8-64 chars and include upper, lower, number, and special character.
                    </div>
                    <script>
                    function checkPasswordStrength(pw) {
                        return pw.length >= 8 && pw.length <= 64 &&
                            /[A-Z]/.test(pw) && /[a-z]/.test(pw) && /[0-9]/.test(pw) && /[^A-Za-z0-9]/.test(pw);
                    }
                    document.addEventListener('DOMContentLoaded', function() {
                        var pwInput = document.getElementById('password');
                        var reqMsg = document.getElementById('password-req');
                        pwInput.addEventListener('input', function() {
                            if (pwInput.value === '') {
                                reqMsg.style.display = 'none';
                            } else if (!checkPasswordStrength(pwInput.value)) {
                                reqMsg.style.display = 'block';
                            } else {
                                reqMsg.style.display = 'none';
                            }
                        });
                    });
                    </script>
                </div>

                <div class="form-group">
                    <label for="password_confirmation" class="form-label">Confirm Password</label>
                    <div class="input-wrapper">
                        <input type="password" id="password_confirmation" name="password_confirmation"
                            class="form-input" value="{{ old('password_confirmation', $userData['password'] ?? '') }}">
                        <button type="button" class="password-toggle"
                            onclick="togglePassword('password_confirmation', 'eye-icon-2')">
                            <svg id="eye-icon-2" width="20" height="20" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                        </button>
                    </div>
                    <div id="password-confirm-error" style="display:none; color:#ff5a7a; font-size:0.8em; margin-top:2px; font-weight:500;">
                        Please confirm your password.
                    </div>
                    <div id="password-match-error" style="display:none; color:#ff5a7a; font-size:0.8em; margin-top:2px; font-weight:500;">
                        Passwords do not match.
                    </div>
                </div>
            </div>

            <div class="button-group">
                <a href="{{ route('login') }}" class="btn btn-back">Back</a>
                <button type="submit" class="btn btn-next">Next</button>
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

        // ==================== ADDRESS CAPITALIZATION ====================

        // ==================== CASCADING ADDRESS SELECTS (PH) ====================
        const REGION_API = 'https://psgc.gitlab.io/api/regions/';
        const PROVINCES_API = (regionCode) => `https://psgc.gitlab.io/api/regions/${regionCode}/provinces/`;
        const CITIES_MUN_API = (parentCode, parentType) => parentType === 'province'
            ? `https://psgc.gitlab.io/api/provinces/${parentCode}/cities-municipalities/`
            : `https://psgc.gitlab.io/api/regions/${parentCode}/cities-municipalities/`;
        const BARANGAYS_API = (cityMunCode) => `https://psgc.gitlab.io/api/cities-municipalities/${cityMunCode}/barangays/`;

        const selRegion = document.getElementById('region');
        const selProvince = document.getElementById('province');
        const selCity = document.getElementById('city');
        const selBarangay = document.getElementById('barangay');
        const streetInput = document.getElementById('street');
        const hiddenAddress = document.getElementById('address');

        // Helpers
        function setOptions(select, items, placeholder) {
            select.innerHTML = '';
            const ph = document.createElement('option');
            ph.value = '';
            ph.textContent = placeholder;
            ph.disabled = true;
            ph.selected = true;
            select.appendChild(ph);
            for (const it of items) {
                const opt = document.createElement('option');
                opt.value = it.code || it.psgcCode || it.id || it.name;
                opt.textContent = it.name || it.provinceName || it.municipalityName || it.cityName || it.barangayName || '';
                // Some PSGC payloads use 'name'
                if (!opt.textContent && it.fullName) opt.textContent = it.fullName;
                select.appendChild(opt);
            }
        }

        function toggle(select, enabled) {
            select.disabled = !enabled;
        }

        function composeAddress() {
            const parts = [];
            const street = streetInput.value.trim();
            if (street) parts.push(street);
            const bgyText = selBarangay.options[selBarangay.selectedIndex]?.textContent;
            if (selBarangay.value) parts.push(bgyText);
            const cityText = selCity.options[selCity.selectedIndex]?.textContent;
            if (selCity.value) parts.push(cityText);
            const provText = selProvince.options[selProvince.selectedIndex]?.textContent;
            // Some regions (e.g., NCR) have no province
            if (selProvince.value) parts.push(provText);
            const regionText = selRegion.options[selRegion.selectedIndex]?.textContent;
            if (selRegion.value) parts.push(regionText);
            hiddenAddress.value = parts.join(', ');
        }

        async function loadRegions() {
            try {
                const res = await fetch(REGION_API, { cache: 'force-cache' });
                const data = await res.json();
                // Sort by name
                data.sort((a, b) => a.regionName.localeCompare(b.regionName));
                // PSGC regions use 'regionName' field; map to common shape
                const items = data.map(r => ({ code: r.code || r.psgcCode, name: r.regionName || r.name }));
                setOptions(selRegion, items, 'Select Region');
            } catch (e) {
                setOptions(selRegion, [], 'Failed to load regions');
            }
        }

        async function loadProvinces(regionCode) {
            try {
                const res = await fetch(PROVINCES_API(regionCode), { cache: 'force-cache' });
                const data = await res.json();
                if (Array.isArray(data) && data.length > 0) {
                    const items = data.map(p => ({ code: p.code || p.psgcCode, name: p.name || p.provinceName }));
                    setOptions(selProvince, items, 'Select Province');
                    toggle(selProvince, true);
                } else {
                    // No provinces (e.g., NCR). Disable and clear province; we'll load cities by region
                    setOptions(selProvince, [], 'No Province');
                    toggle(selProvince, false);
                }
            } catch (e) {
                setOptions(selProvince, [], 'Failed to load provinces');
                toggle(selProvince, false);
            }
        }

        async function loadCities(parentCode, parentType) {
            try {
                const res = await fetch(CITIES_MUN_API(parentCode, parentType), { cache: 'force-cache' });
                const data = await res.json();
                const items = data.map(c => ({ code: c.code || c.psgcCode, name: c.name || c.municipalityName || c.cityName }));
                setOptions(selCity, items, 'Select City/Municipality');
                toggle(selCity, true);
            } catch (e) {
                setOptions(selCity, [], 'Failed to load cities');
                toggle(selCity, false);
            }
        }

        async function loadBarangays(cityCode) {
            try {
                const res = await fetch(BARANGAYS_API(cityCode), { cache: 'force-cache' });
                const data = await res.json();
                const items = data.map(b => ({ code: b.code || b.psgcCode, name: b.name || b.barangayName }));
                setOptions(selBarangay, items, 'Select Barangay');
                toggle(selBarangay, true);
            } catch (e) {
                setOptions(selBarangay, [], 'Failed to load barangays');
                toggle(selBarangay, false);
            }
        }

        // Event wiring
        selRegion.addEventListener('change', async () => {
            // Reset lower levels
            setOptions(selProvince, [], 'Loading...');
            setOptions(selCity, [], 'Select City/Municipality');
            setOptions(selBarangay, [], 'Select Barangay');
            toggle(selCity, false); toggle(selBarangay, false);

            const regionCode = selRegion.value;
            await loadProvinces(regionCode);

            if (selProvince.disabled) {
                // No provinces; load cities by region
                await loadCities(regionCode, 'region');
            } else {
                // Wait for province selection
                toggle(selCity, false);
            }
            composeAddress();
        });

        selProvince.addEventListener('change', async () => {
            setOptions(selCity, [], 'Loading...');
            setOptions(selBarangay, [], 'Select Barangay');
            toggle(selBarangay, false);
            const provCode = selProvince.value;
            await loadCities(provCode, 'province');
            composeAddress();
        });

        selCity.addEventListener('change', async () => {
            setOptions(selBarangay, [], 'Loading...');
            const cityCode = selCity.value;
            await loadBarangays(cityCode);
            composeAddress();
        });

        selBarangay.addEventListener('change', composeAddress);
        streetInput.addEventListener('input', composeAddress);
        streetInput.addEventListener('blur', composeAddress);

        // Initialize
        loadRegions().then(async () => {
            // Restore previous selections if any
            const oldRegion = @json(old('region')) || null;
            const oldProvince = @json(old('province')) || null;
            const oldCity = @json(old('city')) || null;
            const oldBarangay = @json(old('barangay')) || null;

            try {
                if (oldRegion) {
                    selRegion.value = oldRegion;
                    await loadProvinces(oldRegion);
                    // If province list is disabled for this region (e.g., NCR)
                    if (selProvince.disabled) {
                        await loadCities(oldRegion, 'region');
                    } else if (oldProvince) {
                        selProvince.value = oldProvince;
                        await loadCities(oldProvince, 'province');
                    }

                    if (oldCity) {
                        selCity.value = oldCity;
                        await loadBarangays(oldCity);
                    }

                    if (oldBarangay) {
                        selBarangay.value = oldBarangay;
                    }
                }
            } catch (e) {
                // ignore restore errors
            } finally {
                composeAddress();
            }
        });

        // ==================== CONTACT NUMBER VALIDATION ====================

        const contactInput = document.getElementById('contact_number');

        // Only allow numbers
        contactInput.addEventListener('input', function (e) {
            // Remove any non-digit characters
            e.target.value = e.target.value.replace(/\D/g, '');

            // Limit to 11 digits
            if (e.target.value.length > 11) {
                e.target.value = e.target.value.slice(0, 11);
            }
        });

        // Validate on form submit
        contactInput.addEventListener('blur', function (e) {
            const value = e.target.value;
            if (value && value.length !== 11) {
                e.target.setCustomValidity('Contact number must be exactly 11 digits');
            } else if (value && !value.startsWith('09')) {
                e.target.setCustomValidity('Contact number must start with 09');
            } else {
                e.target.setCustomValidity('');
            }
        });

        // Clear custom validity on input
        contactInput.addEventListener('input', function (e) {
            e.target.setCustomValidity('');
        });

        // ==================== AGE CALCULATION ====================

        const dobInput = document.getElementById('date_of_birth');
        const ageInput = document.getElementById('age');

        dobInput.addEventListener('change', () => {
            const dob = new Date(dobInput.value);
            const today = new Date();
            let age = today.getFullYear() - dob.getFullYear();
            const m = today.getMonth() - dob.getMonth();
            if (m < 0 || (m === 0 && today.getDate() < dob.getDate())) {
                age--;
            }
            ageInput.value = age >= 0 ? age : 0;
        });

        // Calculate age on page load if DOB is already filled (when coming back from infant registration)
        if (dobInput.value) {
            const dob = new Date(dobInput.value);
            const today = new Date();
            let age = today.getFullYear() - dob.getFullYear();
            const m = today.getMonth() - dob.getMonth();
            if (m < 0 || (m === 0 && today.getDate() < dob.getDate())) {
                age--;
            }
            ageInput.value = age >= 0 ? age : 0;
        }

        // ==================== PASSWORD TOGGLE ====================

        function togglePassword(inputId, iconId) {
            const passwordInput = document.getElementById(inputId);
            const eyeIcon = document.getElementById(iconId);

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>';
            } else {
                passwordInput.type = 'password';
                eyeIcon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>';
            }
        }

        // ==================== FORM VALIDATION ====================

        const form = document.querySelector('form');
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            // Hide all error messages first
            document.querySelectorAll('[id$="-error"]').forEach(el => el.style.display = 'none');

            // Trim all text inputs before validation
            const textInputs = form.querySelectorAll('input[type="text"]');
            textInputs.forEach(input => {
                input.value = input.value.trim();
            });

            let isValid = true;
            let firstErrorField = null;

            // Validate First Name
            const firstName = document.getElementById('first_name');
            const firstNameError = document.getElementById('first-name-error');
            const firstNameReq = document.getElementById('first-name-req');
            if (!firstName.value.trim()) {
                firstNameError.style.display = 'block';
                firstNameReq.style.display = 'none';
                isValid = false;
                if (!firstErrorField) firstErrorField = firstName;
            } else if (/[<>=\'\"]/.test(firstName.value)) {
                firstNameError.style.display = 'none';
                firstNameReq.style.display = 'block';
                isValid = false;
                if (!firstErrorField) firstErrorField = firstName;
            }

            // Validate Last Name
            const lastName = document.getElementById('last_name');
            const lastNameError = document.getElementById('last-name-error');
            const lastNameReq = document.getElementById('last-name-req');
            if (!lastName.value.trim()) {
                lastNameError.style.display = 'block';
                lastNameReq.style.display = 'none';
                isValid = false;
                if (!firstErrorField) firstErrorField = lastName;
            } else if (/[<>=\'\"]/.test(lastName.value)) {
                lastNameError.style.display = 'none';
                lastNameReq.style.display = 'block';
                isValid = false;
                if (!firstErrorField) firstErrorField = lastName;
            }

            // Validate Middle Name (optional but check for invalid chars if filled)
            const middleName = document.getElementById('middle_name');
            const middleNameReq = document.getElementById('middle-name-req');
            if (middleName.value && /[<>=\'\"]/.test(middleName.value)) {
                middleNameReq.style.display = 'block';
                isValid = false;
                if (!firstErrorField) firstErrorField = middleName;
            }

            // Validate Gender
            const sex = document.querySelector('input[name=\"sex\"]:checked');
            const sexError = document.getElementById('sex-error');
            if (!sex) {
                sexError.style.display = 'block';
                isValid = false;
            }

            // Validate Date of Birth
            const dob = document.getElementById('date_of_birth');
            const dobError = document.getElementById('dob-error');
            if (!dob.value) {
                dobError.style.display = 'block';
                isValid = false;
                if (!firstErrorField) firstErrorField = dob;
            } else {
                const dobDate = new Date(dob.value);
                const today = new Date();
                if (dobDate > today) {
                    dobError.textContent = 'Date of birth cannot be in the future.';
                    dobError.style.display = 'block';
                    isValid = false;
                    if (!firstErrorField) firstErrorField = dob;
                }
            }

            // Validate Contact Number
            const contactInput = document.getElementById('contact_number');
            const contactError = document.getElementById('contact-error');
            const contactReq = document.getElementById('contact-req');
            const contactValue = contactInput.value.trim();
            if (!contactValue) {
                contactError.style.display = 'block';
                contactReq.style.display = 'none';
                isValid = false;
                if (!firstErrorField) firstErrorField = contactInput;
            } else if (contactValue.length !== 11 || !contactValue.startsWith('09')) {
                contactError.style.display = 'none';
                contactReq.style.display = 'block';
                isValid = false;
                if (!firstErrorField) firstErrorField = contactInput;
            }

            // Validate Region
            const selRegion = document.getElementById('region');
            const regionError = document.getElementById('region-error');
            if (!selRegion.value) {
                regionError.style.display = 'block';
                isValid = false;
                if (!firstErrorField) firstErrorField = selRegion;
            }

            // Validate City
            const selCity = document.getElementById('city');
            const cityError = document.getElementById('city-error');
            if (!selCity.value) {
                cityError.style.display = 'block';
                isValid = false;
                if (!firstErrorField) firstErrorField = selCity;
            }

            // Validate Password
            const password = document.getElementById('password');
            const passwordError = document.getElementById('password-error');
            const passwordReq = document.getElementById('password-req');
            if (!password.value) {
                passwordError.style.display = 'block';
                passwordReq.style.display = 'none';
                isValid = false;
                if (!firstErrorField) firstErrorField = password;
            } else if (!checkPasswordStrength(password.value)) {
                passwordError.style.display = 'none';
                passwordReq.style.display = 'block';
                isValid = false;
                if (!firstErrorField) firstErrorField = password;
            }

            // Validate Password Confirmation
            const passwordConfirm = document.getElementById('password_confirmation');
            const passwordConfirmError = document.getElementById('password-confirm-error');
            const passwordMatchError = document.getElementById('password-match-error');
            if (!passwordConfirm.value) {
                passwordConfirmError.style.display = 'block';
                passwordMatchError.style.display = 'none';
                isValid = false;
                if (!firstErrorField) firstErrorField = passwordConfirm;
            } else if (password.value !== passwordConfirm.value) {
                passwordConfirmError.style.display = 'none';
                passwordMatchError.style.display = 'block';
                isValid = false;
                if (!firstErrorField) firstErrorField = passwordConfirm;
            }

            // Ensure composed address exists
            composeAddress();
            const hiddenAddress = document.getElementById('address');
            if (!hiddenAddress.value) {
                if (!selRegion.value) regionError.style.display = 'block';
                if (!selCity.value) cityError.style.display = 'block';
                isValid = false;
            }

            // If validation failed, focus on first error field and scroll to it
            if (!isValid) {
                if (firstErrorField) {
                    firstErrorField.focus();
                    firstErrorField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
                return false;
            }

            // All validations passed, submit the form
            form.submit();
        });
    </script>
</body>

</html>