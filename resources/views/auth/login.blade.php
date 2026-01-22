@extends('layouts.app')

@section('title', 'Login - SVP Tech')

@section('content')
<style>
    /* Override Main Content for Login Page */
    .main-content {
        margin-left: 0 !important;
        padding: 0 !important;
        height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        background: radial-gradient(circle at top center, #1e293b 0%, #0f172a 100%);
        overflow: hidden;
    }

    /* Ambient Background Effects */
    .ambient-glow {
        position: absolute;
        width: 600px;
        height: 600px;
        background: radial-gradient(circle, rgba(59, 130, 246, 0.15) 0%, transparent 70%);
        top: -10%;
        left: 50%;
        transform: translateX(-50%);
        z-index: 1;
        pointer-events: none;
    }

    .login-wrapper {
        position: relative;
        z-index: 10;
        width: 100%;
        max-width: 420px;
        padding: 1rem;
        animation: fadeInUp 0.5s ease-out;
    }

    .login-card {
        background: rgba(30, 41, 59, 0.7);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 20px;
        padding: 3rem 2.5rem;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
    }

    .brand-header {
        text-align: center;
        margin-bottom: 2.5rem;
    }

    .brand-logo {
        height: 150px;
        width: auto;
        margin-bottom: 1rem;
        filter: drop-shadow(0 0 15px rgba(59, 130, 246, 0.3));
    }

    .brand-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #fff;
        margin-bottom: 0.5rem;
        letter-spacing: -0.5px;
    }

    .brand-subtitle {
        color: #94a3b8;
        font-size: 0.95rem;
    }

    .input-group {
        position: relative;
        margin-bottom: 1.5rem;
    }

    .input-icon {
        position: absolute;
        left: 1.2rem;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        transition: color 0.3s;
    }

    .modern-input {
        width: 100%;
        padding: 0.9rem 1rem 0.9rem 3rem !important;
        background: rgba(15, 23, 42, 0.6);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 12px;
        color: #fff;
        font-size: 1rem;
        transition: all 0.3s;
    }

    .modern-input:focus {
        background: rgba(15, 23, 42, 0.8);
        border-color: var(--primary);
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        outline: none;
    }

    .modern-input:focus + .input-icon {
        color: var(--primary);
    }

    .login-btn {
        width: 100%;
        padding: 1rem;
        background: linear-gradient(135deg, var(--primary) 0%, #2563eb 100%);
        color: white;
        border: none;
        border-radius: 12px;
        font-weight: 600;
        font-size: 1.05rem;
        cursor: pointer;
        transition: all 0.3s;
        box-shadow: 0 10px 20px -5px rgba(59, 130, 246, 0.4);
        margin-top: 1rem;
    }

    .login-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 15px 25px -5px rgba(59, 130, 246, 0.5);
    }

    .error-text {
        color: #ef4444;
        font-size: 0.85rem;
        margin-top: 0.5rem;
        display: block;
    }

    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<div class="ambient-glow"></div>

<div class="login-wrapper">
    <div class="login-card">
        <div class="brand-header">
            <img src="{{ asset('images/logo.png') }}" alt="Cloud Tech" class="brand-logo">
            <h1 class="brand-title">Cloud Tech</h1>
            <p class="brand-subtitle">Sign in to your dashboard</p>
        </div>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="input-group">
                <input type="email" id="email" name="email" class="modern-input" value="{{ old('email') }}" required autofocus placeholder="Email Address">
                <i class="fas fa-envelope input-icon"></i>
                @error('email')
                    <span class="error-text">{{ $message }}</span>
                @enderror
            </div>

            <div class="input-group" x-data="{ show: false }">
                <input :type="show ? 'text' : 'password'" id="password" name="password" class="modern-input" required placeholder="Password">
                <i class="fas fa-lock input-icon"></i>
                
                <button type="button" @click="show = !show" style="position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); background: none; border: none; color: #94a3b8; cursor: pointer; padding: 0.5rem; transition: color 0.3s;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='#94a3b8'">
                    <i :class="show ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                </button>

                @error('password')
                    <span class="error-text">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="login-btn">
                <span style="display: flex; align-items: center; justify-content: center; gap: 0.8rem;">
                    Sign In <i class="fas fa-arrow-right"></i>
                </span>
            </button>
        </form>
    </div>
    
    <div style="text-align: center; margin-top: 2rem; color: #64748b; font-size: 0.85rem;">
        &copy; {{ date('Y') }} Cloud Tech. All rights reserved.
    </div>
</div>
@endsection
