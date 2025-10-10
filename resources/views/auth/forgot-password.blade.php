<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Human Milk Bank</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Quicksand:wght@600;700&family=Merriweather:wght@400;500&display=swap"
        rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --heading-font: 'Quicksand', system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            --body-font: 'Merriweather', Georgia, 'Times New Roman', serif;
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

        input[type="text"] {
            width: 100%;
            padding: 0.85rem 1rem;
            border-radius: 0.75rem;
            border: 1px solid #d1d5db;
            font-size: 0.95rem;
            background: #f9fafb;
            transition: all 0.3s ease;
        }

        input[type="text"]:focus {
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
            <h1 class="card-title">Forgot Password</h1>
            <p class="card-subtitle">Enter your registered mobile number and we'll send a recovery code via SMS.</p>
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

        <form method="POST" action="{{ route('password.forgot.send') }}">
            @csrf
            <div class="form-group">
                <label for="contact_number">Mobile Number</label>
                <input type="text" id="contact_number" name="contact_number" inputmode="numeric"
                    placeholder="09XXXXXXXXX" value="{{ old('contact_number') }}" required autofocus>
            </div>
            <button type="submit" class="btn-primary">Send Recovery Code</button>
        </form>

        <a class="btn-link" href="{{ route('login') }}">&#8592; Back to Login</a>
    </div>
</body>

</html>