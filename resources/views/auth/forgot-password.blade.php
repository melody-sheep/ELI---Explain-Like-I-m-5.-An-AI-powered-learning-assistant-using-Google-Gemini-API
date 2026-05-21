@extends('layouts.app')

@section('content')
<div style="flex: 1; display: flex; align-items: center; justify-content: center; background-color: var(--bg-primary); min-height: 100vh;">
    <div style="max-width: 400px; width: 100%; padding: 32px;">
        <!-- Logo -->
        <div style="text-align: center; margin-bottom: 32px;">
            <div style="width: 60px; height: 60px; border: 2px solid var(--accent); display: flex; align-items: center; justify-content: center; margin: 0 auto 16px auto; background: linear-gradient(135deg, var(--accent), var(--accent-purple)); border-radius: 16px;">
                <span class="material-symbols-outlined" style="color: white; font-size: 32px;">smart_toy</span>
            </div>
            <h2 style="font-size: 24px; font-weight: 700; color: var(--text-primary); margin-bottom: 8px;">Forgot Password</h2>
            <p style="font-size: 13px; color: var(--text-muted);">Enter your email and we’ll send reset instructions</p>
        </div>

        @if (session('status'))
            <div style="margin-bottom: 16px; padding: 12px 14px; background-color: var(--bg-tertiary); border: 2px solid var(--border); border-radius: 10px; color: var(--text-primary); font-family: monospace; font-size: 13px;">
                {{ session('status') }}
            </div>
        @endif

        <!-- Forgot Password Form -->
        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <!-- Email -->
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 12px; font-weight: 600; color: var(--text-secondary); margin-bottom: 6px;">Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                    style="width: 100%; background-color: var(--bg-tertiary); border: 2px solid var(--border); color: var(--text-primary); font-family: monospace; font-size: 13px; padding: 12px 14px; border-radius: 10px; outline: none; transition: all 0.2s;">
                @error('email')
                    <p style="color: var(--accent-red); font-size: 11px; margin-top: 6px;">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Button -->
            <button type="submit"
                style="width: 100%; border: none; background: linear-gradient(135deg, var(--accent), var(--accent-hover)); color: white; font-family: monospace; font-size: 13px; font-weight: 600; padding: 12px; cursor: pointer; border-radius: 10px; transition: all 0.2s;">
                Send Reset Link
            </button>

            <!-- Back to Login -->
            <p style="text-align: center; margin-top: 24px; font-size: 12px; color: var(--text-muted);">
                Remembered your password?
                <a href="{{ route('login') }}" style="color: var(--accent); text-decoration: none; font-weight: 600;">Sign in</a>
            </p>
        </form>
    </div>
</div>
@endsection

