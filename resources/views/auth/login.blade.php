@extends('layouts.app')

@section('title', 'Login - Cloud Tech')

@section('content')
<style>
    /* Full screen centering for standard login layout */
    .main-content {
        margin-left: 0 !important;
        padding: 0 !important;
        height: 100vh;
        width: 100vw;
        max-width: 100% !important;
        background: radial-gradient(circle at top center, #111827 0%, #030712 100%) !important;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
    }

    /* Ambient soft background glows */
    .login-glow-1 {
        position: absolute;
        width: 500px;
        height: 500px;
        background: radial-gradient(circle, rgba(59, 130, 246, 0.08) 0%, transparent 70%);
        top: -10%;
        left: 50%;
        transform: translateX(-50%);
        pointer-events: none;
        filter: blur(40px);
        z-index: 1;
    }

    .login-glow-2 {
        position: absolute;
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, rgba(147, 51, 234, 0.05) 0%, transparent 70%);
        bottom: 5%;
        right: 15%;
        pointer-events: none;
        filter: blur(30px);
        z-index: 1;
    }

    .login-wrapper {
        position: relative;
        z-index: 10;
        width: 100%;
        max-width: 440px;
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
        align-items: stretch;
        animation: fadeInUp 0.5s cubic-bezier(0.16, 1, 0.3, 1);
    }

    /* Premium Centered Glassmorphic Card */
    .login-card {
        background: rgba(17, 24, 39, 0.7);
        backdrop-filter: blur(24px);
        -webkit-backdrop-filter: blur(24px);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 24px;
        padding: 3rem 2.25rem;
        box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.6);
    }

    /* Header styling */
    .brand-header {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        margin-bottom: 2.5rem;
    }

    .brand-logo {
        height: 84px;
        width: auto;
        margin-bottom: 1.25rem;
        filter: drop-shadow(0 0 15px rgba(59, 130, 246, 0.3));
    }

    .brand-title {
        font-family: 'Inter', sans-serif;
        font-weight: 800;
        font-size: 1.75rem;
        color: #ffffff;
        letter-spacing: -0.5px;
        text-transform: uppercase;
        background: linear-gradient(to right, #ffffff, #93c5fd);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .brand-subtitle {
        color: #64748b;
        font-size: 0.9rem;
        margin-top: 0.5rem;
    }

    /* Input styling */
    .input-label {
        font-size: 0.75rem;
        font-weight: 700;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        display: block;
        margin-bottom: 0.5rem;
    }

    .input-container {
        position: relative;
        margin-bottom: 1.5rem;
    }

    .input-icon {
        position: absolute;
        left: 1.1rem;
        top: 50%;
        transform: translateY(-50%);
        color: #475569;
        transition: color 0.3s;
        pointer-events: none;
        font-size: 0.95rem;
    }

    .form-field {
        width: 100%;
        padding: 0.85rem 1rem 0.85rem 2.8rem !important;
        background: #0b0f19;
        border: 1px solid #1f2937;
        border-radius: 12px;
        color: #ffffff;
        font-size: 0.95rem;
        transition: all 0.25s ease;
        box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .form-field:focus {
        background: #111726;
        border-color: #3b82f6;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.15);
        outline: none;
    }

    .form-field:focus + .input-icon {
        color: #3b82f6;
    }

    .field-error {
        border-color: #ef4444 !important;
        box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.1) !important;
    }

    .validation-msg {
        color: #f87171;
        font-size: 0.8rem;
        font-weight: 600;
        margin-top: 0.4rem;
        display: block;
    }

    /* Checkbox & Options */
    .remember-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
    }

    .checkbox-label {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: #94a3b8;
        font-size: 0.85rem;
        cursor: pointer;
        user-select: none;
    }

    .custom-checkbox {
        width: 16px;
        height: 16px;
        border: 1.5px solid #374151;
        border-radius: 4px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
        background-color: #0b0f19;
    }

    input[type="checkbox"]:checked + .custom-checkbox {
        background-color: #3b82f6;
        border-color: #3b82f6;
        box-shadow: 0 0 8px rgba(59, 130, 246, 0.4);
    }

    input[type="checkbox"]:checked + .custom-checkbox::after {
        content: "\f00c";
        font-family: "Font Awesome 6 Free";
        font-weight: 900;
        color: white;
        font-size: 9px;
    }

    /* Submit Button */
    .submit-button {
        width: 100%;
        padding: 0.95rem;
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: #ffffff;
        border: none;
        border-radius: 12px;
        font-weight: 700;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.25s ease;
        box-shadow: 0 10px 20px -5px rgba(59, 130, 246, 0.3);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    .submit-button:hover {
        transform: translateY(-1px);
        box-shadow: 0 15px 25px -5px rgba(59, 130, 246, 0.45);
        filter: brightness(1.05);
    }

    .submit-button:active {
        transform: translateY(1px);
    }

    /* Footer styling */
    .login-footer {
        text-align: center;
        margin-top: 2rem;
        color: #4b5563;
        font-size: 0.8rem;
        line-height: 1.6;
    }

    .login-footer a {
        color: #60a5fa;
        text-decoration: none;
        font-weight: 600;
        transition: color 0.2s;
    }

    .login-footer a:hover {
        color: #93c5fd;
        text-decoration: underline;
    }

    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(15px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<div class="login-glow-1"></div>
<div class="login-glow-2"></div>

<div class="login-wrapper">
    <div class="login-card">
        <div class="brand-header">
            <img src="{{ asset('images/logo.png') }}" alt="Cloud Tech Logo" class="brand-logo">
            <h1 class="brand-title">Cloud Tech</h1>
            <p class="brand-subtitle">Sign in to your administration panel</p>
        </div>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Email Input -->
            <div>
                <label for="email" class="input-label">Email Address</label>
                <div class="input-container">
                    <input type="email" id="email" name="email" 
                           class="form-field @error('email') field-error @enderror" 
                           value="{{ old('email') }}" required autofocus placeholder="name@cloudtech.com">
                    <i class="fas fa-envelope input-icon"></i>
                    @error('email')
                        <span class="validation-msg">
                            <i class="fas fa-circle-exclamation mr-1"></i> {{ $message }}
                        </span>
                    @enderror
                </div>
            </div>

            <!-- Password Input -->
            <div x-data="{ show: false }">
                <label for="password" class="input-label">Password</label>
                <div class="input-container">
                    <input :type="show ? 'text' : 'password'" id="password" name="password" 
                           class="form-field @error('password') field-error @enderror" 
                           required placeholder="••••••••">
                    <i class="fas fa-lock input-icon"></i>
                    
                    <!-- Password Visibility toggle -->
                    <button type="button" @click="show = !show" 
                            style="position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); background: none; border: none; color: #475569; cursor: pointer; padding: 0.5rem; transition: color 0.2s;" 
                            onmouseover="this.style.color='#3b82f6'" onmouseout="this.style.color='#475569'">
                        <i :class="show ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                    </button>

                    @error('password')
                        <span class="validation-msg">
                            <i class="fas fa-circle-exclamation mr-1"></i> {{ $message }}
                        </span>
                    @enderror
                </div>
            </div>

            <!-- Remember Me checkbox -->
            <div class="remember-row">
                <label class="checkbox-label">
                    <input type="checkbox" name="remember" id="remember" style="display: none;">
                    <span class="custom-checkbox"></span>
                    <span>Remember this device</span>
                </label>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="submit-button">
                <span>Sign In</span>
                <i class="fas fa-arrow-right-to-bracket"></i>
            </button>
        </form>
    </div>

    <!-- Copyright & Attributions Footer -->
    <div class="login-footer">
        &copy; {{ date('Y') }} Cloud Tech. All rights reserved. <br>
        Designed & Developed by <a href="https://dishanmindika.online" target="_blank">DILA</a>
    </div>
</div>
@endsection
