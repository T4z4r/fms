@extends('layouts.app')

@section('content')
<style>
    .auth-wrapper {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #f8f9fa;
    }
    .auth-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
        overflow: hidden;
        max-width: 420px;
        width: 100%;
    }
    .auth-header {
        background-color: #495057;
        padding: 1.5rem;
        text-align: center;
        color: white;
    }
    .auth-header h1 {
        font-size: 1.5rem;
        font-weight: 600;
        margin: 0;
    }
    .auth-header i {
        font-size: 2rem;
        margin-bottom: 0.5rem;
    }
    .auth-body {
        padding: 2rem;
    }
    .form-floating > label {
        color: #6b757e;
    }
    .form-control:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.15rem rgba(13, 110, 253, 0.25);
    }
    .btn-primary {
        background-color: #0d6efd;
        border: none;
        padding: 0.75rem 1.5rem;
        font-weight: 500;
    }
    .btn-primary:hover {
        background-color: #0b5ed7;
    }
    .auth-link {
        color: #0d6efd;
        text-decoration: none;
        font-weight: 500;
    }
    .auth-link:hover {
        color: #0b5ed7;
    }
    .divider {
        display: flex;
        align-items: center;
        text-align: center;
        margin: 1.5rem 0;
    }
    .divider::before,
    .divider::after {
        content: '';
        flex: 1;
        border-bottom: 1px solid #dee2e6;
    }
    .divider span {
        padding: 0 1rem;
        color: #6c757d;
        font-size: 0.8rem;
    }
</style>

<div class="auth-wrapper">
    <div class="auth-card">
        <div class="auth-header">
            <i class="bi bi-speedometer2 d-block"></i>
            <h1>{{ __('FMS') }}</h1>
            <p class="mb-0 small opacity-75">{{ __('Create Your Account') }}</p>
        </div>
        <div class="auth-body">
            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div class="form-floating mb-3">
                    <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus placeholder="John Doe">
                    <label for="name"><i class="bi bi-person"></i> {{ __('Full Name') }}</label>
                    @error('name')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-floating mb-3">
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="name@example.com">
                    <label for="email"><i class="bi bi-envelope"></i> {{ __('Email Address') }}</label>
                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-floating mb-3">
                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password" placeholder="Password">
                    <label for="password"><i class="bi bi-lock"></i> {{ __('Password') }}</label>
                    @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-floating mb-4">
                    <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password" placeholder="Confirm Password">
                    <label for="password-confirm"><i class="bi bi-lock-fill"></i> {{ __('Confirm Password') }}</label>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-2">
                    <i class="bi bi-person-plus"></i> {{ __('Register') }}
                </button>
            </form>

            @if (Route::has('login'))
            <div class="divider">
                <span>ALREADY HAVE AN ACCOUNT</span>
            </div>

            <div class="text-center">
                <a class="auth-link" href="{{ route('login') }}">
                    <i class="bi bi-box-arrow-in-right"></i> {{ __('Login to Your Account') }}
                </a>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection