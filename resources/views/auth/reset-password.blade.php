<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Human Milk Bank</title>
    @if (file_exists(public_path('hmblsc-logo.png')))
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('hmblsc-logo.png') }}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('hmblsc-logo.png') }}">
        <link rel="apple-touch-icon" href="{{ asset('hmblsc-logo.png') }}">
        <link rel="shortcut icon" href="{{ asset('hmblsc-logo.png') }}">
    @else
        <link rel="icon" type="image/jpeg" sizes="32x32" href="{{ asset('hmblsc-logo.jpg') }}">
        <link rel="icon" type="image/jpeg" sizes="16x16" href="{{ asset('hmblsc-logo.jpg') }}">
        <link rel="apple-touch-icon" href="{{ asset('hmblsc-logo.jpg') }}">
        <link rel="shortcut icon" href="{{ asset('hmblsc-logo.jpg') }}">
    @endif
    <link
    href="https://fonts.googleapis.com/css2?family=Quicksand:wght@600;700&display=swap"
        rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --heading-font: 'Quicksand', system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            --body-font: 'Segoe UI', 'Helvetica Neue', Arial, sans-serif;
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
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: var(--body-font);
            padding: 1rem;
        }

        .card {
            background: #fff;
            width: 100%;
            max-width: 440px;
            border-radius: 1.5rem;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
            padding: 2.5rem 2rem;
        }

        .card-header {
            text-align: center;
            margin-bottom: 1.8rem;
        }

        .card-title {
            font-family: var(--heading-font);
            font-size: 1.9rem;
            color: var(--pink-primary);
            margin-bottom: 0.5rem;
        }

        .card-subtitle {
            font-size: 0.95rem;
            color: #6b7280;
            line-height: 1.5;
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        label {
            display: block;
            font-size: 0.9rem;
            color: #374151;
            margin-bottom: 0.6rem;
            font-weight: 600;
        }

        input[type="password"] {
            width: 100%;
            padding: 0.85rem 1rem;
            border-radius: 0.75rem;
            border: 1px solid #d1d5db;
            font-size: 0.95rem;
            background: #f9fafb;
            transition: all 0.3s ease;
        }

        input[type="password"]:focus {
            border-color: var(--pink-primary);
            box-shadow: 0 0 0 3px rgba(236, 72, 153, 0.1);
            outline: none;
            background: #fff;
        }

        .btn-primary {
            width: 100%;
            padding: 0.9rem;
            border-radius: 0.75rem;
            border: none;
            font-family: var(--heading-font);
            font-weight: 700;
            font-size: 1rem;
            color: #fff;
            background: linear-gradient(135deg, var(--pink-primary) 0%, #f472b6 100%);
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 20px rgba(236, 72, 153, 0.25);
        }

        .btn-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            margin-top: 1.5rem;
            color: var(--green-primary);
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .btn-link:hover {
            text-decoration: underline;
        }

        .status-message,
        .error-message {
            padding: 0.9rem 1rem;
            border-radius: 0.75rem;
            margin-bottom: 1.25rem;
            font-size: 0.9rem;
            line-height: 1.4;
        }

        .status-message {
            background: #ecfdf5;
            color: #047857;
            border: 1px solid #a7f3d0;
        }

        .error-message {
            background: #fef2f2;
            color: #b91c1c;
            border: 1px solid #fecaca;
        }

        @media (max-width: 480px) {
            .card {
                padding: 2rem 1.5rem;
            }

            .card-title {
                font-size: 1.6rem;
            }
        }
    </style>
</head>

<body>
    <div class="card">
        <div class="card-header">
            <h1 class="card-title">Reset Password</h1>
            <p class="card-subtitle">Create a new password to secure your Human Milk Bank account.</p>
        </div>

        @if (session('status'))
            <div class="status-message">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="error-message">
                <ul style="list-style: none;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            <div class="form-group">
                <label for="password">New Password</label>
                <input type="password" id="password" name="password" autocomplete="new-password" required minlength="8">
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
                <label for="password_confirmation">Confirm New Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation"
                    autocomplete="new-password" required minlength="8">
            </div>
            <button type="submit" class="btn-primary">Update Password</button>
        </form>

        <a class="btn-link" href="{{ route('login') }}">&#8592; Back to Login</a>
    </div>
</body>

</html>