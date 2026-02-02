@extends('layouts.auth-modal')

@section('title', 'Register')

@section('modal-content')
<h2 style="text-align: center; margin-bottom: 30px; color: var(--primary-green); font-family: 'Reem Kufi Fun', sans-serif; font-size: 2rem;">Join TajTrainer</h2>

<form method="POST" action="{{ route('register') }}">
    @csrf

    <div class="form-group">
        <label for="name">Full Name</label>
        <input id="name" type="text" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>
        @error('name')
            <div class="error-message">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="email">Email Address</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email">
        @error('email')
            <div class="error-message">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="password">Password</label>
        <input id="password" type="password" name="password" required autocomplete="new-password">
        @error('password')
            <div class="error-message">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="password-confirm">Confirm Password</label>
        <input id="password-confirm" type="password" name="password_confirmation" required autocomplete="new-password">
    </div>

    <div class="form-group">
        <label for="user_type">I am a</label>
        <select id="user_type" name="role_id" required>
            <option value="">Select your role</option>
            <option value="2" {{ old('role_id') == '2' ? 'selected' : '' }}>Student</option>
            <option value="3" {{ old('role_id') == '3' ? 'selected' : '' }}>Teacher</option>
        </select>
        @error('role_id')
            <div class="error-message">{{ $message }}</div>
        @enderror
    </div>

    <button type="submit" class="submit-btn">Create Account</button>
</form>

<div class="form-footer">
    <p>Already have an account? <a href="{{ route('login') }}">Login here</a></p>
</div>
@endsection
