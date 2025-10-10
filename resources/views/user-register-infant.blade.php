<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Infant Registration - Human Milk Bank</title>
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
                padding: 1rem 0.5rem;
            }

            .container {
                padding: 2rem 1.5rem;
                border-radius: 1.25rem;
            }

            .header {
                gap: 1rem;
                margin-bottom: 2rem;
            }

            .logo {
                width: 70px;
                height: 70px;
            }

            .page-title {
                font-size: 1.65rem;
            }

            .form-row {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }

            .button-group {
                flex-direction: column-reverse;
            }

            .btn {
                width: 100%;
                min-height: 44px;
            }
        }

        @media (max-width: 480px) {
            body {
                padding: 0.5rem 0.25rem;
            }

            .container {
                padding: 1.75rem 1.25rem;
                border-radius: 1rem;
            }

            .header {
                flex-direction: column;
                text-align: center;
                gap: 1rem;
            }

            .logo {
                width: 65px;
                height: 65px;
            }

            .page-title {
                font-size: 1.5rem;
            }

            .form-group {
                margin-bottom: 1.25rem;
            }

            .form-label {
                font-size: 0.9rem;
            }

            .form-input {
                padding: 0.7rem 0.875rem;
                font-size: 16px;
                /* Prevent iOS zoom */
            }

            .radio-group {
                gap: 1.5rem;
            }

            .button-group {
                margin-top: 1.5rem;
            }

            .success-message {
                flex-direction: column;
                align-items: flex-start;
            }

            .close-btn {
                align-self: flex-end;
                margin-left: 0;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <div class="logo">
                <img src="{{ asset('hmblsc-logo.jpg') }}" alt="Human Milk Bank Logo">
            </div>
            <h1 class="page-title">Infant's Information</h1>
        </div>

        @if(session('success'))
            <div class="success-message" id="success-message">
                <p class="success-message-text">{{ session('success') }}</p>
                <button class="close-btn" onclick="document.getElementById('success-message').remove()">×</button>
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

        <form action="/user-register-infant/store" method="POST">
            @csrf
            <input type="hidden" name="user_id" value="{{ $user->user_id ?? old('user_id') }}">

            <div class="form-row">
                <div class="form-group">
                    <label for="first_name" class="form-label">First Name</label>
                    <input type="text" id="first_name" name="first_name" class="form-input"
                        value="{{ old('first_name') }}" required style="text-transform: capitalize;">
                </div>

                <div class="form-group">
                    <label for="last_name" class="form-label">Last Name</label>
                    <input type="text" id="last_name" name="last_name" class="form-input" value="{{ old('last_name') }}"
                        required style="text-transform: capitalize;">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="middle_name" class="form-label">Middle Name (optional)</label>
                    <input type="text" id="middle_name" name="middle_name" class="form-input"
                        value="{{ old('middle_name') }}" style="text-transform: capitalize;">
                </div>

                <div class="form-group">
                    <label class="form-label">Gender</label>
                    <div class="radio-group">
                        <div class="radio-option">
                            <input type="radio" id="female" name="infant_sex" value="female" {{ old('infant_sex') == 'female' ? 'checked' : '' }} required>
                            <label for="female">Female</label>
                        </div>
                        <div class="radio-option">
                            <input type="radio" id="male" name="infant_sex" value="male" {{ old('infant_sex') == 'male' ? 'checked' : '' }}>
                            <label for="male">Male</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="infant_date_of_birth" class="form-label">Birthday</label>
                    <input type="date" id="infant_date_of_birth" name="infant_date_of_birth" class="form-input"
                        value="{{ old('infant_date_of_birth') }}" required>
                </div>

                <div class="form-group">
                    <label for="infant_age" class="form-label">Age</label>
                    <input type="number" id="infant_age" name="infant_age" class="form-input" readonly>
                </div>
            </div>

            <div class="form-group">
                <label for="birth_weight" class="form-label">Birth weight (kg)</label>
                <input type="number" step="0.01" id="birth_weight" name="birth_weight" class="form-input"
                    value="{{ old('birth_weight') }}" min="0" placeholder="e.g., 3.2" required>
            </div>

            <div class="button-group">
                <a href="/user-register" class="btn btn-back">Back</a>
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

        function calculateAge() {
            const dob = new Date(dobInput.value);
            const today = new Date();
            let age = today.getFullYear() - dob.getFullYear();
            const monthDiff = today.getMonth() - dob.getMonth();
            if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < dob.getDate())) {
                age--;
            }
            ageInput.value = age >= 0 ? age : 0;
        }

        dobInput.addEventListener('change', calculateAge);

        // Pre-calculate age if date is already filled (e.g. after validation error)
        if (dobInput.value) {
            calculateAge();
        }

        // ==================== FORM VALIDATION ====================

        const form = document.querySelector('form');
        form.addEventListener('submit', function (e) {
            // Trim all text inputs before submission
            const textInputs = form.querySelectorAll('input[type="text"]');
            textInputs.forEach(input => {
                input.value = input.value.trim();
            });
        });
    </script>
</body>

</html>