@extends('layouts.app')

@section('content')
<style>
    @keyframes float {
        0%, 100% { transform: translateY(0) rotate(0deg); }
        50% { transform: translateY(-20px) rotate(5deg); }
    }
    
    @keyframes floatSlow {
        0%, 100% { transform: translateY(0) translateX(0); }
        25% { transform: translateY(-15px) translateX(10px); }
        75% { transform: translateY(10px) translateX(-10px); }
    }
    
    @keyframes glow {
        0%, 100% { box-shadow: 0 0 5px rgba(59, 130, 246, 0.3); }
        50% { box-shadow: 0 0 20px rgba(59, 130, 246, 0.6); }
    }
    
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-5px); }
        75% { transform: translateX(5px); }
    }
    
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    
    .float-animation {
        animation: float 4s ease-in-out infinite;
    }
    
    .float-slow {
        animation: floatSlow 8s ease-in-out infinite;
    }
    
    .glow-effect {
        animation: glow 2s ease-in-out infinite;
    }
    
    .shake-effect {
        animation: shake 0.3s ease-in-out;
    }
    
    .particle {
        position: fixed;
        opacity: 0.3;
        pointer-events: none;
        z-index: 0;
    }
    
    .input-fancy {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .input-fancy:focus-within {
        transform: translateY(-2px);
        border-color: var(--accent) !important;
        box-shadow: 0 4px 20px rgba(59, 130, 246, 0.2);
    }
    
    /* FIX: Remove white background from inputs */
    input, input:focus, input:active {
        background: transparent !important;
        background-color: transparent !important;
        -webkit-background-clip: text !important;
        background-clip: text !important;
    }
    
    input:-webkit-autofill,
    input:-webkit-autofill:hover,
    input:-webkit-autofill:focus,
    input:-webkit-autofill:active {
        -webkit-box-shadow: 0 0 0 30px var(--bg-tertiary) inset !important;
        -webkit-text-fill-color: var(--text-primary) !important;
        caret-color: var(--text-primary) !important;
    }
    
    .btn-fancy {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }
    
    .btn-fancy::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.3);
        transform: translate(-50%, -50%);
        transition: width 0.6s, height 0.6s;
    }
    
    .btn-fancy:hover::before {
        width: 300px;
        height: 300px;
    }
    
    /* Scrollbar for form if needed */
    .form-container {
        max-height: 85vh;
        overflow-y: auto;
    }
    
    .form-container::-webkit-scrollbar {
        width: 4px;
    }
    
    .form-container::-webkit-scrollbar-track {
        background: var(--bg-tertiary);
        border-radius: 4px;
    }
    
    .form-container::-webkit-scrollbar-thumb {
        background: var(--accent);
        border-radius: 4px;
    }
</style>

<!-- Animated Background Particles -->
<div id="particles-container" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; overflow: hidden; pointer-events: none; z-index: 0;"></div>

<div style="flex: 1; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, var(--bg-primary) 0%, var(--bg-secondary) 100%); min-height: 100vh; padding: 16px; position: relative; z-index: 1;">
    
    <!-- Theme Toggle Button -->
    <button onclick="toggleTheme()" 
        style="position: fixed; top: 16px; right: 16px; width: 40px; height: 40px; border: 2px solid var(--border); background: var(--bg-secondary); backdrop-filter: blur(10px); border-radius: 50%; cursor: pointer; z-index: 100; display: flex; align-items: center; justify-content: center; transition: all 0.3s ease; box-shadow: 0 4px 12px rgba(0,0,0,0.1);"
        onmouseover="this.style.transform='rotate(15deg)'; this.style.borderColor='var(--accent)'"
        onmouseout="this.style.transform='rotate(0deg)'; this.style.borderColor='var(--border)'">
        <span id="auth-theme-icon" class="material-symbols-outlined" style="color: var(--text-primary); font-size: 20px;">dark_mode</span>
    </button>

    <!-- Main Card - COMPACT SIZE -->
    <div style="max-width: 900px; width: 100%; display: flex; background: var(--bg-secondary); border-radius: 24px; overflow: hidden; box-shadow: 0 20px 40px -12px rgba(0, 0, 0, 0.25); border: 1px solid var(--border);">
        
        <!-- Left Side - Visual Graphics (COMPACT) -->
        <div style="flex: 0.8; background: linear-gradient(135deg, #1e3a8a, #581c87, #1e1b4d); padding: 32px 24px; display: flex; flex-direction: column; justify-content: center; position: relative; overflow: hidden;">
            
            <!-- Animated Shapes -->
            <div style="position: absolute; top: -40px; right: -40px; width: 150px; height: 150px; background: rgba(255,255,255,0.08); border-radius: 50%; animation: spin 20s linear infinite;"></div>
            <div style="position: absolute; bottom: -30px; left: -30px; width: 120px; height: 120px; background: rgba(255,255,255,0.05); border-radius: 50%; animation: spin 15s linear infinite reverse;"></div>
            
            <!-- Logo -->
            <div class="float-animation" style="text-align: center; margin-bottom: 20px; position: relative; z-index: 1;">
                <div style="width: 60px; height: 60px; background: linear-gradient(135deg, #3b82f6, #a855f7); border-radius: 18px; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px auto; box-shadow: 0 8px 20px rgba(59,130,246,0.3);">
                    <span class="material-symbols-outlined" style="color: white; font-size: 32px;">smart_toy</span>
                </div>
                <h2 style="font-size: 22px; font-weight: 800; color: white; margin-bottom: 6px;">Welcome Back!</h2>
                <p style="font-size: 11px; color: rgba(255,255,255,0.8);">Your AI learning assistant</p>
            </div>
            
            <!-- Features List (COMPACT) -->
            <div style="position: relative; z-index: 1; margin-top: 16px;">
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 12px;">
                    <div style="width: 28px; height: 28px; background: rgba(255,255,255,0.15); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                        <span class="material-symbols-outlined" style="color: white; font-size: 16px;">auto_awesome</span>
                    </div>
                    <span style="color: white; font-size: 12px;">AI-Powered Learning</span>
                </div>
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 12px;">
                    <div style="width: 28px; height: 28px; background: rgba(255,255,255,0.15); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                        <span class="material-symbols-outlined" style="color: white; font-size: 16px;">quiz</span>
                    </div>
                    <span style="color: white; font-size: 12px;">Interactive Quizzes</span>
                </div>
                <div style="display: flex; align-items: center; gap: 10px;">
                    <div style="width: 28px; height: 28px; background: rgba(255,255,255,0.15); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                        <span class="material-symbols-outlined" style="color: white; font-size: 16px;">auto_stories</span>
                    </div>
                    <span style="color: white; font-size: 12px;">Smart Flashcards</span>
                </div>
            </div>
        </div>
        
        <!-- Right Side - Login Form (COMPACT) -->
        <div style="flex: 1.2; padding: 32px 28px; background: var(--bg-secondary);">
            
            <!-- Header -->
            <div style="text-align: center; margin-bottom: 24px;">
                <div style="display: inline-flex; align-items: center; gap: 6px; background: var(--bg-tertiary); padding: 4px 12px; border-radius: 50px; margin-bottom: 16px;">
                    <span class="material-symbols-outlined" style="color: var(--accent); font-size: 14px;">lock_open</span>
                    <span style="font-size: 10px; color: var(--text-muted);">Secure Login</span>
                </div>
                <h3 style="font-size: 24px; font-weight: 700; color: var(--text-primary); margin-bottom: 6px;">Sign In</h3>
                <p style="font-size: 12px; color: var(--text-muted);">Access your dashboard</p>
            </div>

            <!-- Login Form -->
            <form method="POST" action="{{ route('login') }}" id="loginForm">
                @csrf

                @if($errors->any())
                <div id="error-alert" style="background: linear-gradient(135deg, rgba(239, 68, 68, 0.1), rgba(239, 68, 68, 0.05)); border: 1px solid var(--accent-red); border-radius: 12px; padding: 10px 14px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                    <span class="material-symbols-outlined" style="color: var(--accent-red); font-size: 20px;">error_outline</span>
                    <p style="color: var(--text-secondary); font-size: 12px; margin: 0;">{{ $errors->first() }}</p>
                </div>
                @endif

                <!-- Email Field -->
                <div style="margin-bottom: 18px;">
                    <label style="display: block; font-size: 12px; font-weight: 600; color: var(--text-secondary); margin-bottom: 6px;">Email Address</label>
                    <div class="input-fancy" style="display: flex; align-items: center; gap: 10px; background-color: var(--bg-tertiary); border: 2px solid {{ $errors->has('email') ? 'var(--accent-red)' : 'var(--border)' }}; border-radius: 12px; padding: 0 14px;">
                        <span class="material-symbols-outlined" style="color: var(--text-muted); font-size: 18px;">email</span>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                            style="width: 100%; background: transparent !important; border: none; color: var(--text-primary); font-size: 14px; padding: 14px 0; outline: none; font-family: monospace;">
                    </div>
                    <div id="email-error" style="font-size: 10px; color: var(--accent-red); margin-top: 4px; display: none;"></div>
                </div>

                <!-- Password Field -->
                <div style="margin-bottom: 14px;">
                    <label style="display: block; font-size: 12px; font-weight: 600; color: var(--text-secondary); margin-bottom: 6px;">Password</label>
                    <div class="input-fancy" style="display: flex; align-items: center; gap: 10px; background-color: var(--bg-tertiary); border: 2px solid var(--border); border-radius: 12px; padding: 0 14px;">
                        <span class="material-symbols-outlined" style="color: var(--text-muted); font-size: 18px;">lock</span>
                        <input type="password" name="password" id="password" required
                            style="width: 100%; background: transparent !important; border: none; color: var(--text-primary); font-size: 14px; padding: 14px 0; outline: none; font-family: monospace;">
                        <button type="button" onclick="togglePassword()" style="background: none; border: none; cursor: pointer; color: var(--text-muted); padding: 6px;">
                            <span id="password-toggle-icon" class="material-symbols-outlined" style="font-size: 18px;">visibility</span>
                        </button>
                    </div>
                </div>

                <!-- Remember Me & Forgot Password -->
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 22px;">
                    <label style="display: flex; align-items: center; gap: 6px; cursor: pointer;">
                        <input type="checkbox" name="remember" id="remember" style="width: 14px; height: 14px; cursor: pointer; accent-color: var(--accent);">
                        <span style="font-size: 12px; color: var(--text-secondary);">Remember me</span>
                    </label>
                    <a href="{{ route('password.request') }}" style="font-size: 12px; color: var(--accent); text-decoration: none;">Forgot password?</a>
                </div>

                <!-- Login Button -->
                <button type="submit" id="login-btn" class="btn-fancy"
                    style="width: 100%; border: none; background: linear-gradient(135deg, var(--accent), var(--accent-purple)); color: white; font-family: monospace; font-size: 14px; font-weight: 700; padding: 12px; cursor: pointer; border-radius: 12px; transition: all 0.3s;">
                    <span id="btn-text">Sign In</span>
                    <span id="btn-spinner" style="display: none; position: absolute; left: 50%; top: 50%; transform: translate(-50%, -50%);">
                        <div style="width: 18px; height: 18px; border: 2px solid white; border-top-color: transparent; border-radius: 50%; animation: spin 0.6s linear infinite;"></div>
                    </span>
                </button>

                <!-- Divider -->
                <div style="display: flex; align-items: center; margin: 20px 0;">
                    <div style="flex: 1; height: 1px; background: linear-gradient(90deg, transparent, var(--border), transparent);"></div>
                    <span style="padding: 0 12px; font-size: 11px; color: var(--text-muted);">or</span>
                    <div style="flex: 1; height: 1px; background: linear-gradient(90deg, transparent, var(--border), transparent);"></div>
                </div>

                <!-- Guest Mode Button -->
                <a href="{{ route('guest.mode') }}" class="btn-fancy"
                    style="display: flex; align-items: center; justify-content: center; gap: 8px; width: 100%; border: 2px solid var(--accent-green); background: transparent; color: var(--accent-green); font-family: monospace; font-size: 13px; font-weight: 600; padding: 10px; cursor: pointer; text-decoration: none; border-radius: 12px; transition: all 0.3s; margin-bottom: 20px;">
                    <span class="material-symbols-outlined" style="font-size: 18px;">person_outline</span>
                    Continue as Guest
                </a>

                <!-- Register Link -->
                <p style="text-align: center; font-size: 12px; color: var(--text-muted);">
                    New to ELI?
                    <a href="{{ route('register') }}" style="color: var(--accent); text-decoration: none; font-weight: 600;">Create account</a>
                </p>
            </form>
        </div>
    </div>
</div>

<script>
    function toggleTheme() {
        const body = document.body;
        const icon = document.getElementById('auth-theme-icon');
        body.classList.toggle('light-mode');
        const isLight = body.classList.contains('light-mode');
        icon.textContent = isLight ? 'light_mode' : 'dark_mode';
        localStorage.setItem('eli_theme', isLight ? 'light' : 'dark');
    }

    function togglePassword() {
        const passwordField = document.getElementById('password');
        const icon = document.getElementById('password-toggle-icon');
        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            icon.textContent = 'visibility_off';
        } else {
            passwordField.type = 'password';
            icon.textContent = 'visibility';
        }
    }

    const form = document.getElementById('loginForm');
    const emailInput = document.getElementById('email');
    const emailError = document.getElementById('email-error');
    const loginBtn = document.getElementById('login-btn');
    const btnText = document.getElementById('btn-text');
    const btnSpinner = document.getElementById('btn-spinner');

    form.addEventListener('submit', function(e) {
        let isValid = true;
        const email = emailInput.value.trim();
        
        if (!email) {
            emailError.textContent = 'Email is required';
            emailError.style.display = 'block';
            emailInput.parentElement.style.borderColor = '#ef4444';
            isValid = false;
        } else if (!email.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
            emailError.textContent = 'Valid email required';
            emailError.style.display = 'block';
            emailInput.parentElement.style.borderColor = '#ef4444';
            isValid = false;
        } else {
            emailError.style.display = 'none';
            emailInput.parentElement.style.borderColor = 'var(--border)';
        }
        
        if (!isValid) {
            e.preventDefault();
        } else {
            btnText.style.opacity = '0';
            btnSpinner.style.display = 'block';
            loginBtn.style.opacity = '0.7';
        }
    });

    emailInput.addEventListener('input', () => {
        emailError.style.display = 'none';
        emailInput.parentElement.style.borderColor = 'var(--border)';
    });

    // Create particles
    function createParticles() {
        const container = document.getElementById('particles-container');
        const colors = ['#3b82f6', '#a855f7', '#22c55e', '#eab308', '#ef4444'];
        for (let i = 0; i < 40; i++) {
            const particle = document.createElement('div');
            particle.className = 'particle';
            const size = Math.random() * 4 + 1;
            particle.style.width = size + 'px';
            particle.style.height = size + 'px';
            particle.style.background = colors[Math.floor(Math.random() * colors.length)];
            particle.style.borderRadius = Math.random() > 0.5 ? '50%' : '2px';
            particle.style.left = Math.random() * 100 + '%';
            particle.style.top = Math.random() * 100 + '%';
            particle.style.animation = `floatSlow ${Math.random() * 10 + 5}s ease-in-out infinite`;
            container.appendChild(particle);
        }
    }
    createParticles();

    const savedTheme = localStorage.getItem('eli_theme');
    if (savedTheme === 'light') {
        document.body.classList.add('light-mode');
        document.getElementById('auth-theme-icon').textContent = 'light_mode';
    }
</script>
@endsection