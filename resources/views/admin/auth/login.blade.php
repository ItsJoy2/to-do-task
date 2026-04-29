<!DOCTYPE html>
<html lang="en">
<head>
        @php
            use App\Models\GeneralSetting;
            $generalSettings = GeneralSetting::first();
        @endphp
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $generalSettings->app_name ?? 'Laravel Admin' }}</title>

    @if($generalSettings && $generalSettings->favicon)
        <link rel="icon" type="image/png" href="{{ asset('storage/' . $generalSettings->favicon) }}">
        <link rel="apple-touch-icon" href="{{ asset('storage/' . $generalSettings->favicon) }}">
    @else
        <link rel="icon" type="image/png" href="{{ asset('default-favicon.png') }}">
        <link rel="apple-touch-icon" href="{{ asset('default-favicon.png') }}">
    @endif

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">

    <!-- Font Awesome (for icons) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    * {
        box-sizing: border-box;
        font-family: 'Inter', sans-serif;
    }

    body {
        margin: 0;
        padding: 0;
        background: linear-gradient(135deg, #0f172a, #1e293b, #020617);
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        color: #fff;
    }

    .login-wrapper {
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(15px);
        -webkit-backdrop-filter: blur(15px);
        padding: 40px 30px;
        border-radius: 16px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.6);
        width: 100%;
        max-width: 400px;
        text-align: center;
        border: 1px solid rgba(255,255,255,0.1);
    }

    .login-wrapper .logo img {
        max-height: 60px;
        margin-bottom: 15px;
        filter: brightness(0.9);
    }

    .login-wrapper h2 {
        margin: 0 0 25px;
        font-weight: 600;
        color: #e2e8f0;
        letter-spacing: 0.5px;
    }

    .form-field {
        display: flex;
        align-items: center;
        margin-bottom: 18px;
        background: rgba(255, 255, 255, 0.08);
        border-radius: 10px;
        padding: 12px 15px;
        transition: 0.3s;
        border: 1px solid transparent;
    }

    .form-field:focus-within {
        border: 1px solid #3b82f6;
        background: rgba(255,255,255,0.12);
    }

    .form-field i {
        margin-right: 10px;
        color: #94a3b8;
    }

    .form-field input {
        border: none;
        outline: none;
        background: transparent;
        width: 100%;
        font-size: 15px;
        color: #fff;
    }

    .form-field input::placeholder {
        color: #94a3b8;
    }

    .btn {
        width: 100%;
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: #fff;
        border: none;
        padding: 12px;
        font-size: 16px;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.3s ease;
        font-weight: 600;
        letter-spacing: 0.3px;
    }

    .btn:hover {
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
        transform: translateY(-1px);
        box-shadow: 0 8px 20px rgba(37, 99, 235, 0.4);
    }

    .alert {
        margin-bottom: 20px;
        color: #fff;
        padding: 10px;
        background: rgba(220, 53, 69, 0.85);
        border-radius: 8px;
        text-align: left;
        font-size: 14px;
    }
</style>
</head>
<body>
<div class="login-wrapper">
    <div class="logo">
        @if($generalSettings && $generalSettings->logo)
            <img src="{{ asset('storage/' . $generalSettings->logo) }}" alt="{{ $generalSettings->app_name ?? 'App Name' }}" class="navbar-brand" height="50">
        @endif
    </div>
    <h2>Admin Login</h2>

    {{-- Session error (for non-admin users) --}}
    @if(session('error'))
        <div class="alert">{{ session('error') }}</div>
    @endif

    {{-- Validation Errors --}}
    @if($errors->any())
        <div class="alert">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('signin') }}">
        @csrf
        <div class="form-field">
            <i class="fa fa-envelope"></i>
            <input type="email" name="email" placeholder="Email" value="{{ old('email') }}" required>
        </div>
        <div class="form-field">
            <i class="fa fa-lock"></i>
            <input type="password" name="password" placeholder="Password" required>
        </div>
        <button type="submit" class="btn">Login</button>
    </form>
</div>
</body>
</html>
