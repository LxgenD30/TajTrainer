@extends('layouts.auth-modal')

@section('title', 'Forgot Password')

@section('modal-content')
<h2 style="text-align: center; margin-bottom: 18px; color: var(--primary-green); font-family: 'Reem Kufi Fun', sans-serif; font-size: 2rem;">Reset Password</h2>

<p style="text-align: center; margin-bottom: 20px; color: #555; font-family: 'El Messiri', sans-serif;">Enter your registered email address and we will send you a password reset link.</p>

@if (session('status'))
    <div style="background: #e8f5e9; color: #1b5e20; border: 1px solid #c8e6c9; border-radius: 10px; padding: 10px 12px; margin-bottom: 18px; font-family: 'El Messiri', sans-serif;">
        {{ session('status') }}
    </div>
@endif

<form method="POST" action="{{ route('password.email') }}">
    @csrf

    <div class="form-group">
        <label for="email">Email Address</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
        @error('email')
            <div class="error-message">{{ $message }}</div>
        @enderror
    </div>

    <button type="submit" class="submit-btn">Send Password Reset Link</button>
</form>

<div class="form-footer">
    <p>Remembered your password? <a href="{{ route('login') }}">Back to Login</a></p>
</div>
@endsection
