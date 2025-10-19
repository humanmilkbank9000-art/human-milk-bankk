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
                        value="{{ old('first_name', $userData['first_name'] ?? '') }}" required style="text-transform: capitalize;">
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
                                {{ old('sex', $userData['sex'] ?? '') == 'female' ? 'checked' : '' }} required>
                            <label for="female">Female</label>
                        </div>
                        <div class="radio-option">
                            <input type="radio" id="male" name="sex" value="male" 
                                {{ old('sex', $userData['sex'] ?? '') == 'male' ? 'checked' : '' }}>
                            <label for="male">Male</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="date_of_birth" class="form-label">Birthday</label>
                    <input type="date" id="date_of_birth" name="date_of_birth" class="form-input"
                        value="{{ old('date_of_birth', $userData['date_of_birth'] ?? '') }}" required>
                </div>

                <div class="form-group">
                    <label for="age" class="form-label">Age</label>
                    <input type="number" id="age" name="age" class="form-input" readonly>
                </div>
            </div>

            <div class="form-group">
                <label for="contact_number" class="form-label">Contact Number</label>
                <input type="text" id="contact_number" name="contact_number" class="form-input"
                    value="{{ old('contact_number', $userData['contact_number'] ?? '') }}" required placeholder="09XXXXXXXXX" pattern="[0-9]{11}">
                <div id="contact-req" style="display:none; color:#ff5a7a; font-size:0.8em; margin-top:2px;">
                    Contact number must be exactly 11 digits.
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

            <div class="form-row">
                <div class="form-group full-width">
                    <label for="address" class="form-label">Address</label>
                    <input type="text" id="address" name="address" class="form-input" value="{{ old('address', $userData['address'] ?? '') }}"
                        required placeholder="Enter complete address">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-wrapper">
                        <input type="password" id="password" name="password" class="form-input" 
                            value="{{ old('password', $userData['password'] ?? '') }}" required>
                        <button type="button" class="password-toggle"
                            onclick="togglePassword('password', 'eye-icon-1')">
                            <svg id="eye-icon-1" width="20" height="20" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                        </button>
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
                            class="form-input" value="{{ old('password_confirmation', $userData['password'] ?? '') }}" required>
                        <button type="button" class="password-toggle"
                            onclick="togglePassword('password_confirmation', 'eye-icon-2')">
                            <svg id="eye-icon-2" width="20" height="20" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                        </button>
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

        const addressInput = document.getElementById('address');

        // Capitalize first letter of address in real-time as user types
        addressInput.addEventListener('input', function (e) {
            const cursorPosition = e.target.selectionStart;
            let value = e.target.value;

            if (value.length > 0) {
                // Always capitalize the very first letter
                let capitalizedValue = value.charAt(0).toUpperCase() + value.slice(1);

                // Only update if changed to avoid cursor jumping
                if (capitalizedValue !== value) {
                    e.target.value = capitalizedValue;
                    e.target.setSelectionRange(cursorPosition, cursorPosition);
                }
            }
        });

        // Additional cleanup on blur (when user clicks away)
        addressInput.addEventListener('blur', function (e) {
            let value = e.target.value.trim();
            if (value) {
                // Ensure first letter is capitalized
                value = value.charAt(0).toUpperCase() + value.slice(1);
                e.target.value = value;
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
            // Trim all text inputs before submission
            const textInputs = form.querySelectorAll('input[type="text"]');
            textInputs.forEach(input => {
                input.value = input.value.trim();
            });

            // Final contact number validation
            const contactValue = contactInput.value;
            if (contactValue.length !== 11) {
                e.preventDefault();
                contactInput.focus();
                alert('Please enter a valid 11-digit contact number.');
                return false;
            }

            if (!contactValue.startsWith('09')) {
                e.preventDefault();
                contactInput.focus();
                alert('Contact number must start with 09.');
                return false;
            }
        });
    </script>
</body>

</html>