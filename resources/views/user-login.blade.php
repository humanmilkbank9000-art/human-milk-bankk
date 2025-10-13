<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Human Milk Bank</title>

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
            --pink-light: #ffc0cb;
            --pink-lighter: #ffd4e3;
            --green-primary: #10b981;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, #ffc0cb 0%, #ffb6c1 50%, #ffc0cb 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: var(--body-font);
            line-height: var(--line-height);
            padding: 1rem;
            overflow: hidden;
            /* Prevent scrolling */
        }

        .login-container {
            display: flex;
            width: 100%;
            max-width: 850px;
            max-height: 90vh;
            /* Ensure it fits in viewport */
            background: white;
            border-radius: 1.5rem;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
            overflow: hidden;
        }

        /* Left Panel - Welcome Section */
        .welcome-panel {
            flex: 1;
            background: linear-gradient(135deg, var(--pink-lighter) 0%, var(--pink-light) 100%);
            padding: 2rem 1.5rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        .logo-container {
            margin-bottom: 1rem;
        }

        .logo {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            overflow: hidden;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .logo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            /* Prevent FOUC - smooth fade in */
            opacity: 0;
            animation: fadeInLoginLogo 0.4s ease-in forwards;
        }

        @keyframes fadeInLoginLogo {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .welcome-text {
            font-family: var(--heading-font);
            font-size: 1rem;
            font-weight: 600;
            color: #6b7280;
            margin-bottom: 1rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .app-title {
            font-family: var(--heading-font);
            font-size: 1.65rem;
            font-weight: 700;
            color: var(--pink-primary);
            line-height: 1.3;
            margin-bottom: 1.25rem;
        }

        .app-subtitle {
            font-family: var(--body-font);
            font-size: 0.95rem;
            color: #6b7280;
            font-weight: 400;
            margin-bottom: 0;
        }

        /* Right Panel - Login Form */
        .form-panel {
            flex: 1;
            padding: 2rem 1.5rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .form-header {
            margin-bottom: 1.25rem;
        }

        .form-title {
            font-family: var(--heading-font);
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--pink-primary);
            margin-bottom: 0.5rem;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-label {
            display: block;
            font-family: var(--body-font);
            font-size: 0.9rem;
            font-weight: 500;
            color: #374151;
            margin-bottom: 0.5rem;
        }

        .input-wrapper {
            position: relative;
        }

        .form-input {
            width: 100%;
            padding: 0.75rem 0.9rem;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            font-family: var(--body-font);
            font-size: 0.9rem;
            transition: all 0.3s ease;
            outline: none;
            background: #f9fafb;
        }

        .form-input:focus {
            border-color: var(--pink-primary);
            box-shadow: 0 0 0 3px rgba(236, 72, 153, 0.1);
            background: white;
        }

        .form-input::placeholder {
            color: #9ca3af;
        }

        .password-toggle {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #6b7280;
            cursor: pointer;
            padding: 0.25rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .password-toggle:hover {
            color: var(--pink-primary);
        }

        .btn-login {
            width: 100%;
            padding: 0.75rem;
            background: linear-gradient(135deg, var(--pink-primary) 0%, #f472b6 100%);
            color: white;
            border: none;
            border-radius: 0.5rem;
            font-family: var(--heading-font);
            font-size: 0.95rem;
            font-weight: 600;
            text-transform: uppercase;
            cursor: pointer;
            transition: all 0.3s ease;
            letter-spacing: 0.5px;
            margin-top: 0.35rem;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(236, 72, 153, 0.3);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .forgot-password {
            text-align: center;
            margin: 1rem 0;
        }

        .forgot-password a {
            font-family: var(--body-font);
            color: var(--pink-primary);
            text-decoration: none;
            font-size: 0.9rem;
            transition: color 0.3s ease;
        }

        .forgot-password a:hover {
            color: #db2777;
            text-decoration: underline;
        }

        .btn-create-account {
            width: 100%;
            padding: 0.75rem;
            background: linear-gradient(135deg, var(--green-primary) 0%, #34d399 100%);
            color: white;
            border: none;
            border-radius: 0.5rem;
            font-family: var(--heading-font);
            font-size: 0.95rem;
            font-weight: 600;
            text-transform: uppercase;
            cursor: pointer;
            transition: all 0.3s ease;
            letter-spacing: 0.5px;
            text-decoration: none;
            display: block;
            text-align: center;
        }

        .btn-create-account:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(16, 185, 129, 0.3);
        }

        .btn-create-account:active {
            transform: translateY(0);
        }

        .error-message {
            background-color: #fee2e2;
            border: 1px solid #fecaca;
            color: #dc2626;
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
            font-family: var(--body-font);
            font-size: 0.9rem;
        }

        .form-input.error {
            border-color: #dc2626;
        }

        .form-input.limit-reached {
            color: #dc2626;
            border-color: #dc2626;
        }

        .error-text {
            color: #dc2626;
            font-size: 0.8rem;
            margin-top: 0.25rem;
            display: block;
            font-family: var(--body-font);
        }

        /* Responsive Design */

        /* Large Tablet & Small Desktop */
        @media (max-width: 1024px) {
            .login-container {
                max-width: 750px;
            }
        }

        /* Tablet */
        @media (max-width: 968px) {
            .login-container {
                max-width: 680px;
            }

            .welcome-panel {
                padding: 1.5rem 1.25rem;
                /* More compact */
            }

            .form-panel {
                padding: 1.5rem 1.25rem;
                /* More compact */
            }

            .logo {
                width: 80px;
                /* Smaller */
                height: 80px;
            }

            .logo-container {
                margin-bottom: 0.75rem;
                /* Reduced */
            }

            .welcome-text {
                font-size: 0.85rem;
                margin-bottom: 0.5rem;
            }

            .app-title {
                font-size: 1.4rem;
                /* Reduced */
                margin-bottom: 0.875rem;
            }

            .app-subtitle {
                font-size: 0.875rem;
            }

            .form-title {
                font-size: 1.5rem;
                /* Reduced */
            }

            .form-header {
                margin-bottom: 1rem;
                /* Reduced */
            }

            .form-group {
                margin-bottom: 0.875rem;
                /* Tighter */
            }
        }

        /* Mobile - Stack vertically */
        @media (max-width: 768px) {
            body {
                padding: 0.5rem;
                align-items: center;
                overflow: hidden;
                /* No scrolling on mobile */
                height: 100vh;
            }

            .login-container {
                flex-direction: column;
                max-width: 100%;
                max-height: 95vh;
                /* Fit in viewport */
                margin: 0 auto;
                overflow-y: auto;
                /* Internal scroll if needed */
            }

            .welcome-panel {
                padding: 1.5rem 1.25rem;
                /* More compact */
                border-radius: 1.5rem 1.5rem 0 0;
            }

            .logo {
                width: 70px;
                /* Smaller */
                height: 70px;
            }

            .logo-container {
                margin-bottom: 0.75rem;
                /* Reduced */
            }

            .welcome-text {
                font-size: 0.8rem;
                margin-bottom: 0.5rem;
                /* Reduced */
            }

            .app-title {
                font-size: 1.3rem;
                /* Smaller */
                margin-bottom: 0.75rem;
                /* Reduced */
            }

            .app-subtitle {
                font-size: 0.85rem;
                margin-bottom: 0;
            }

            .form-panel {
                padding: 1.5rem 1.25rem;
                /* More compact */
                border-radius: 0 0 1.5rem 1.5rem;
            }

            .form-title {
                font-size: 1.35rem;
                /* Smaller */
            }

            .form-header {
                margin-bottom: 1rem;
                /* Reduced */
            }

            .form-group {
                margin-bottom: 0.875rem;
                /* Reduced */
            }

            .form-input {
                padding: 0.7rem 0.8rem;
                /* More compact */
                font-size: 16px;
                /* Prevent iOS zoom */
            }

            .btn-login,
            .btn-create-account {
                padding: 0.7rem;
                /* More compact */
                font-size: 0.875rem;
                min-height: 44px;
            }

            .password-toggle {
                padding: 0.375rem;
            }

            .forgot-password {
                margin: 0.75rem 0;
                /* Reduced */
            }

            .forgot-password a {
                font-size: 0.85rem;
            }
        }

        /* Small Mobile */
        @media (max-width: 480px) {
            body {
                padding: 0.375rem;
            }

            .login-container {
                border-radius: 1.25rem;
                max-height: 96vh;
            }

            .welcome-panel {
                padding: 1.25rem 1rem;
            }

            .logo {
                width: 65px;
                height: 65px;
            }

            .logo-container {
                margin-bottom: 0.625rem;
            }

            .welcome-text {
                font-size: 0.75rem;
                margin-bottom: 0.375rem;
            }

            .app-title {
                font-size: 1.2rem;
                line-height: 1.3;
                margin-bottom: 0.625rem;
            }

            .app-subtitle {
                font-size: 0.8rem;
            }

            .form-panel {
                padding: 1.25rem 1rem;
            }

            .form-title {
                font-size: 1.25rem;
            }

            .form-header {
                margin-bottom: 0.875rem;
            }

            .form-group {
                margin-bottom: 0.75rem;
            }

            .form-label {
                font-size: 0.85rem;
                margin-bottom: 0.375rem;
            }

            .form-input {
                padding: 0.65rem 0.75rem;
                font-size: 16px;
            }

            .btn-login,
            .btn-create-account {
                padding: 0.65rem;
                font-size: 0.85rem;
                min-height: 44px;
            }

            .forgot-password {
                margin: 0.625rem 0;
            }

            .forgot-password a {
                font-size: 0.825rem;
            }

            .password-toggle {
                right: 0.75rem;
            }
        }

        /* Extra Small Mobile (iPhone SE, etc.) */
        @media (max-width: 375px) {
            body {
                padding: 0.25rem;
            }

            .login-container {
                border-radius: 1rem;
                max-height: 97vh;
            }

            .welcome-panel {
                padding: 1rem 0.875rem;
            }

            .logo {
                width: 60px;
                height: 60px;
            }

            .logo-container {
                margin-bottom: 0.5rem;
            }

            .welcome-text {
                font-size: 0.7rem;
                margin-bottom: 0.375rem;
            }

            .app-title {
                font-size: 1.1rem;
                line-height: 1.25;
                margin-bottom: 0.5rem;
            }

            .app-subtitle {
                font-size: 0.75rem;
            }

            .form-panel {
                padding: 1rem 0.875rem;
            }

            .form-title {
                font-size: 1.15rem;
            }

            .form-header {
                margin-bottom: 0.75rem;
            }

            .form-group {
                margin-bottom: 0.625rem;
            }

            .form-label {
                font-size: 0.8rem;
                margin-bottom: 0.3rem;
            }

            .form-input {
                padding: 0.6rem 0.7rem;
                font-size: 16px;
            }

            .btn-login,
            .btn-create-account {
                padding: 0.6rem;
                font-size: 0.8rem;
                min-height: 44px;
            }

            .forgot-password {
                margin: 0.5rem 0;
            }

            .forgot-password a {
                font-size: 0.8rem;
            }

            .error-message {
                padding: 0.6rem 0.75rem;
                font-size: 0.8rem;
                margin-bottom: 0.75rem;
            }
        }
    </style>
</head>

<body>
    <div class="login-container">
        <!-- Left Panel - Welcome Section -->
        <div class="welcome-panel">
            <div class="logo-container">
                <div class="logo">
                    <img src="{{ asset('hmblsc-logo.jpg') }}" alt="Human Milk Bank Logo" width="120" height="120"
                        loading="eager">
                </div>
            </div>

            <p class="welcome-text">Welcome To</p>

            <h1 class="app-title">
                Cagayan de Oro City -<br>
                Human Milk Bank &<br>
                Lactation Support<br>
                Center
            </h1>

            <p class="app-subtitle">
            </p>
        </div>

        <!-- Right Panel - Login Form -->
        <div class="form-panel">
            <div class="form-header">
                <h2 class="form-title">LOGIN</h2>
            </div>

            <!-- Error Message -->
            @if($errors->has('phone'))
                <div class="error-message">
                    {{ $errors->first('phone') }}
                </div>
            @elseif($errors->has('password'))
                <div class="error-message">
                    {{ $errors->first('password') }}
                </div>
            @elseif(session('error'))
                <div class="error-message">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Login Form -->
            <form action="{{ route('login.submit') }}" method="POST">
                @csrf

                <!-- Contact Number Field -->
                <div class="form-group">
                    <label for="phone" class="form-label">Contact Number</label>
                    <input type="text" id="phone" name="phone" class="form-input @error('phone') error @enderror"
                        placeholder="Enter your phone number " required value="{{ old('phone') }}" maxlength="20">
                    @error('phone')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Password Field -->
                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-wrapper">
                        <input type="password" id="password" name="password"
                            class="form-input @error('password') error @enderror" placeholder="Enter your password"
                            required>
                        <button type="button" class="password-toggle" onclick="togglePassword()">
                            <svg id="eye-icon" width="20" height="20" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Login Button -->
                <button type="submit" class="btn-login">
                    Login
                </button>
            </form>

            <!-- Forgot Password Link -->
            <div class="forgot-password">
                <a href="{{ route('password.forgot') }}">Forgot password?</a>
            </div>

            <!-- Create Account Button -->
            <a href="{{ route('user.register') }}" class="btn-create-account">
                Create Account
            </a>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eye-icon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>';
            } else {
                passwordInput.type = 'password';
                eyeIcon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>';
            }
        }

        // Input sanitization for phone/username field
        document.addEventListener('DOMContentLoaded', function () {
            const phoneInput = document.getElementById('phone');

            phoneInput.addEventListener('input', function (e) {
                let value = e.target.value;

                // If the value is purely numeric, restrict to numbers only
                // This allows admin usernames (with letters) but restricts regular phone numbers
                if (/^[0-9]*$/.test(value)) {
                    // Only numbers - limit to 11 digits
                    let sanitizedValue = value.replace(/[^0-9]/g, '').substring(0, 11);
                    e.target.value = sanitizedValue;
                    // Remove red styling for numeric input
                    e.target.classList.remove('limit-reached');
                } else {
                    // Contains letters - allow alphanumeric for admin username
                    // Remove special characters but allow letters, numbers, and underscore
                    let sanitizedValue = value.replace(/[^a-zA-Z0-9_]/g, '');
                    e.target.value = sanitizedValue;

                    // Show red text while typing (indicates potential invalid username)
                    e.target.classList.add('limit-reached');

                    // Check if username exists in database (debounced)
                    clearTimeout(window.usernameDebounce);
                    window.usernameDebounce = setTimeout(function () {
                        checkUsernameExists(sanitizedValue, e.target);
                    }, 300); // Wait 300ms after user stops typing
                }
            });

            // Function to check if username exists via AJAX
            function checkUsernameExists(username, inputElement) {
                if (!username) {
                    inputElement.classList.add('limit-reached');
                    return;
                }

                fetch('{{ route("check.username") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ username: username })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.exists) {
                            // Username exists - remove red styling
                            inputElement.classList.remove('limit-reached');
                        } else {
                            // Username doesn't exist - keep red styling
                            inputElement.classList.add('limit-reached');
                        }
                    })
                    .catch(error => {
                        console.error('Error checking username:', error);
                    });
            }


            // Prevent typing when 11 digits reached (numbers only)
            phoneInput.addEventListener('keydown', function (e) {
                const value = e.target.value;

                // Only apply limit to numeric input
                if (/^[0-9]*$/.test(value) && value.length >= 11) {
                    // Allow: backspace, delete, tab, escape, enter, arrow keys
                    const allowedKeys = ['Backspace', 'Delete', 'Tab', 'Escape', 'Enter', 'ArrowLeft', 'ArrowRight', 'ArrowUp', 'ArrowDown', 'Home', 'End'];

                    // Allow Ctrl/Cmd + A, C, V, X (select all, copy, paste, cut)
                    if (e.ctrlKey || e.metaKey) {
                        if (['a', 'c', 'v', 'x'].includes(e.key.toLowerCase())) {
                            return;
                        }
                    }

                    // Block any other key
                    if (!allowedKeys.includes(e.key)) {
                        e.preventDefault();
                    }
                }
            });
        });
    </script>
</body>

</html>