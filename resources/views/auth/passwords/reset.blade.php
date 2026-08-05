@extends('layouts.auth-modal')

@section('title', 'Set New Password')

@section('modal-content')
<h2 style="text-align: center; margin-bottom: 18px; color: var(--primary-green); font-family: 'Reem Kufi Fun', sans-serif; font-size: 2rem;">Set New Password</h2>

<p style="text-align: center; margin-bottom: 20px; color: #555; font-family: 'El Messiri', sans-serif;">Create a new password for your TajTrainer account.</p>

<form method="POST" action="{{ route('password.update') }}">
    @csrf

    <input type="hidden" name="token" value="{{ $token }}">

    <div class="form-group">
        <label for="email">Email Address</label>
        <input id="email" type="email" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus>
        @error('email')
            <div class="error-message">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="password">New Password</label>
        <input id="password" type="password" name="password" required autocomplete="new-password">
        @error('password')
            <div class="error-message">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="password-confirm">Confirm New Password</label>
        <input id="password-confirm" type="password" name="password_confirmation" required autocomplete="new-password">
    </div>

    <button type="submit" class="submit-btn">Reset Password</button>
</form>

<div class="form-footer">
    <p>Need to request another link? <a href="{{ route('password.request') }}">Back to Forgot Password</a></p>
</div>
@endsection
