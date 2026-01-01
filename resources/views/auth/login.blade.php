@extends('layouts.app')

@section('title', 'Login - SVP Tech')

@section('content')
<div class="login-container">
    <div class="login-card glass">
        <div class="login-header">
            <img src="{{ asset('images/logo.png') }}" alt="SVP Tech" style="height: 60px; margin-bottom: 1rem;">
            <h2>Welcome Back</h2>
            <p>SVP Tech Management System</p>
        </div>

        <form method="POST" action="{{ route('login') }}" class="login-form">
            @csrf

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus placeholder="technician@svp.tech">
                @error('email')
                    <span class="error-msg">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required placeholder="••••••••">
                @error('password')
                    <span class="error-msg">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="btn-primary glow">
                Sign In
            </button>
        </form>
    </div>
</div>
@endsection
