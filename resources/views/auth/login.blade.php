@extends('layouts.auth-modal')

@section('title', 'Login')

@section('modal-content')
<h2 style="text-align: center; margin-bottom: 30px; color: var(--primary-green); font-family: 'Reem Kufi Fun', sans-serif; font-size: 2rem;">Login to TajTrainer</h2>

<form method="POST" action="{{ route('login') }}">
    @csrf

    <div class="form-group">
        <label for="email">Email Address</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
        @error('email')
            <div class="error-message">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="password">Password</label>
        <input id="password" type="password" name="password" required autocomplete="current-password">
        @error('password')
            <div class="error-message">{{ $message }}</div>
        @enderror
    </div>

    <div class="remember-group" style="display: flex; align-items: center; gap: 10px; margin-bottom: 25px;">
        <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }} style="width: 20px; height: 20px; cursor: pointer; accent-color: var(--primary-green);">
        <label for="remember" style="margin-bottom: 0; cursor: pointer;">Remember Me</label>
    </div>

    <button type="submit" class="submit-btn">Login</button>
</form>

<div class="form-footer">
    @if (Route::has('password.request'))
        <p style="margin-bottom: 10px;"><a href="{{ route('password.request') }}">Forgot Your Password?</a></p>
    @endif
    <p>Don't have an account? <a href="{{ route('register') }}">Register here</a></p>
</div>
@endsection
