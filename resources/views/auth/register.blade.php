@extends('layouts.app')

@section('content')
<style>
    @keyframes float {
        0%, 100% { transform: translateY(0) rotate(0deg); }
        50% { transform: translateY(-15px) rotate(3deg); }
    }
    
    @keyframes floatSlow {
        0%, 100% { transform: translateY(0) translateX(0); }
        25% { transform: translateY(-10px) translateX(8px); }
        75% { transform: translateY(8px) translateX(-8px); }
    }
    
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-4px); }
        75% { transform: translateX(4px); }
    }
    
    .float-animation {
        animation: float 4s ease-in-out infinite;
    }
    
    .float-slow {
        animation: floatSlow 8s ease-in-out infinite;
    }
    
    .shake-effect {
        animation: shake 0.3s ease-in-out;
    }
    
    .particle {
        position: fixed;
        opacity: 0.25;
        pointer-events: none;
        z-index: 0;
    }
    
    .input-fancy {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .input-fancy:focus-within {
        transform: translateY(-2px);
        border-color: var(--accent) !important;
        box-shadow: 0 4px 15px rgba(59, 130, 246, 0.15);
    }
    
    /* FIX: Remove white background from inputs */
    input, input:focus, input:active {
        background: transparent !important;
        background-color: transparent !important;
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
        background: rgba(255, 255, 255, 0.25);
        transform: translate(-50%, -50%);
        transition: width 0.5s, height 0.5s;
    }
    
    .btn-fancy:hover::before {
        width: 250px;
        height: 250px;
    }
    
    .progress-bar {
        transition: width 0.3s ease;
    }
    
    .checklist-item {
        transition: all 0.2s ease;
    }
    
    .checklist-item.valid {
        color: var(--accent-green);
    }
    
    .checklist-item.invalid {
        color: var(--text-muted);
    }
    
    .form-scroll {
        max-height: 80vh;
        overflow-y: auto;
        padding-right: 4px;
    }
    
    .form-scroll::-webkit-scrollbar {
        width: 3px;
    }
    
    .form-scroll::-webkit-scrollbar-track {
        background: var(--bg-tertiary);
        border-radius: 3px;
    }
    
    .form-scroll::-webkit-scrollbar-thumb {
        background: var(--accent);
        border-radius: 3px;
    }
</style>

<!-- Animated Background Particles -->
<div id="particles-container" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; overflow: hidden; pointer-events: none; z-index: 0;"></div>

<div style="flex: 1; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, var(--bg-primary) 0%, var(--bg-secondary) 100%); min-height: 100vh; padding: 16px; position: relative; z-index: 1;">
    
    <!-- Theme Toggle Button -->
    <button onclick="toggleTheme()" 
        style="position: fixed; top: 16px; right: 16px; width: 40px; height: 40px; border: 2px solid var(--border); background: var(--bg-secondary); backdrop-filter: blur(10px); border-radius: 50%; cursor: pointer; z-index: 100; display: flex; align-items: center; justify-content: center; transition: all 0.3s ease;"
        onmouseover="this.style.transform='rotate(15deg)'; this.style.borderColor='var(--accent)'"
        onmouseout="this.style.transform='rotate(0deg)'; this.style.borderColor='var(--border)'">
        <span id="auth-theme-icon" class="material-symbols-outlined" style="color: var(--text-primary); font-size: 20px;">dark_mode</span>
    </button>

    <!-- Main Card - COMPACT -->
    <div style="max-width: 900px; width: 100%; display: flex; background: var(--bg-secondary); border-radius: 24px; overflow: hidden; box-shadow: 0 20px 40px -12px rgba(0, 0, 0, 0.25); border: 1px solid var(--border);">
        
        <!-- Left Side - Visual Graphics (COMPACT) -->
        <div style="flex: 0.8; background: linear-gradient(135deg, #1e3a8a, #581c87, #1e1b4d); padding: 32px 24px; display: flex; flex-direction: column; justify-content: center; position: relative; overflow: hidden;">
            
            <div style="position: absolute; top: -40px; right: -40px; width: 150px; height: 150px; background: rgba(255,255,255,0.08); border-radius: 50%; animation: spin 20s linear infinite;"></div>
            <div style="position: absolute; bottom: -30px; left: -30px; width: 120px; height: 120px; background: rgba(255,255,255,0.05); border-radius: 50%; animation: spin 15s linear infinite reverse;"></div>
            
            <div class="float-animation" style="text-align: center; margin-bottom: 20px; position: relative; z-index: 1;">
                <div style="width: 60px; height: 60px; background: linear-gradient(135deg, #3b82f6, #a855f7); border-radius: 18px; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px auto;">
                    <span class="material-symbols-outlined" style="color: white; font-size: 32px;">rocket_launch</span>
                </div>
                <h2 style="font-size: 22px; font-weight: 800; color: white; margin-bottom: 6px;">Join ELI!</h2>
                <p style="font-size: 11px; color: rgba(255,255,255,0.85);">Start learning today</p>
            </div>
            
            <div style="position: relative; z-index: 1; margin-top: 16px;">
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 12px;">
                    <div style="width: 28px; height: 28px; background: rgba(34, 197, 94, 0.15); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                        <span class="material-symbols-outlined" style="color: #22c55e; font-size: 16px;">check_circle</span>
                    </div>
                    <span style="color: white; font-size: 11px;">100% Free Forever</span>
                </div>
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 12px;">
                    <div style="width: 28px; height: 28px; background: rgba(59, 130, 246, 0.15); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                        <span class="material-symbols-outlined" style="color: #3b82f6; font-size: 16px;">auto_awesome</span>
                    </div>
                    <span style="color: white; font-size: 11px;">AI-Powered Learning</span>
                </div>
                <div style="display: flex; align-items: center; gap: 10px;">
                    <div style="width: 28px; height: 28px; background: rgba(168, 85, 247, 0.15); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                        <span class="material-symbols-outlined" style="color: #a855f7; font-size: 16px;">school</span>
                    </div>
                    <span style="color: white; font-size: 11px;">Interactive Learning</span>
                </div>
            </div>
        </div>
        
        <!-- Right Side - Register Form (COMPACT) -->
        <div style="flex: 1.2; padding: 28px 28px; background: var(--bg-secondary);">
            
            <div class="form-scroll">
                <!-- Header -->
                <div style="text-align: center; margin-bottom: 20px;">
                    <div style="display: inline-flex; align-items: center; gap: 6px; background: var(--bg-tertiary); padding: 4px 12px; border-radius: 50px; margin-bottom: 12px;">
                        <span class="material-symbols-outlined" style="color: var(--accent-green); font-size: 14px;">celebration</span>
                        <span style="font-size: 10px; color: var(--text-muted);">Start Free</span>
                    </div>
                    <h3 style="font-size: 22px; font-weight: 700; color: var(--text-primary); margin-bottom: 4px;">Create Account</h3>
                    <p style="font-size: 11px; color: var(--text-muted);">Join the learning revolution</p>
                </div>

                <!-- Register Form -->
                <form method="POST" action="{{ route('register') }}" id="registerForm">
                    @csrf

                    @if($errors->any())
                    <div style="background: rgba(239, 68, 68, 0.1); border: 1px solid var(--accent-red); border-radius: 10px; padding: 10px 12px; margin-bottom: 18px;">
                        @foreach($errors->all() as $error)
                            <p style="color: var(--accent-red); font-size: 11px; margin: 2px 0;">{{ $error }}</p>
                        @endforeach
                    </div>
                    @endif

                    <!-- Name -->
                    <div style="margin-bottom: 14px;">
                        <label style="display: block; font-size: 11px; font-weight: 600; color: var(--text-secondary); margin-bottom: 5px;">Full Name</label>
                        <div class="input-fancy" style="display: flex; align-items: center; gap: 10px; background-color: var(--bg-tertiary); border: 2px solid var(--border); border-radius: 10px; padding: 0 12px;">
                            <span class="material-symbols-outlined" style="color: var(--text-muted); font-size: 18px;">person</span>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required autofocus
                                style="width: 100%; background: transparent !important; border: none; color: var(--text-primary); font-size: 13px; padding: 12px 0; outline: none;">
                        </div>
                        <div id="name-error" style="font-size: 10px; color: var(--accent-red); margin-top: 4px; display: none;"></div>
                    </div>

                    <!-- Email -->
                    <div style="margin-bottom: 14px;">
                        <label style="display: block; font-size: 11px; font-weight: 600; color: var(--text-secondary); margin-bottom: 5px;">Email Address</label>
                        <div class="input-fancy" style="display: flex; align-items: center; gap: 10px; background-color: var(--bg-tertiary); border: 2px solid var(--border); border-radius: 10px; padding: 0 12px;">
                            <span class="material-symbols-outlined" style="color: var(--text-muted); font-size: 18px;">email</span>
                            <input type="email" name="email" id="email" value="{{ old('email') }}" required
                                style="width: 100%; background: transparent !important; border: none; color: var(--text-primary); font-size: 13px; padding: 12px 0; outline: none;">
                        </div>
                        <div id="email-error" style="font-size: 10px; color: var(--accent-red); margin-top: 4px; display: none;"></div>
                    </div>

                    <!-- Password -->
                    <div style="margin-bottom: 12px;">
                        <label style="display: block; font-size: 11px; font-weight: 600; color: var(--text-secondary); margin-bottom: 5px;">Password</label>
                        <div class="input-fancy" style="display: flex; align-items: center; gap: 10px; background-color: var(--bg-tertiary); border: 2px solid var(--border); border-radius: 10px; padding: 0 12px;">
                            <span class="material-symbols-outlined" style="color: var(--text-muted); font-size: 18px;">lock</span>
                            <input type="password" name="password" id="password" required
                                style="width: 100%; background: transparent !important; border: none; color: var(--text-primary); font-size: 13px; padding: 12px 0; outline: none;">
                            <button type="button" onclick="togglePassword('password', 'password-toggle-icon')" style="background: none; border: none; cursor: pointer; color: var(--text-muted); padding: 4px;">
                                <span id="password-toggle-icon" class="material-symbols-outlined" style="font-size: 18px;">visibility</span>
                            </button>
                        </div>
                    </div>

                    <!-- Password Strength (Compact) -->
                    <div id="strength-container" style="margin-bottom: 12px; display: none;">
                        <div style="display: flex; gap: 4px; margin-bottom: 6px;">
                            <div id="strength-bar1" class="progress-bar" style="flex: 1; height: 3px; background: var(--border); border-radius: 2px;"></div>
                            <div id="strength-bar2" class="progress-bar" style="flex: 1; height: 3px; background: var(--border); border-radius: 2px;"></div>
                            <div id="strength-bar3" class="progress-bar" style="flex: 1; height: 3px; background: var(--border); border-radius: 2px;"></div>
                            <div id="strength-bar4" class="progress-bar" style="flex: 1; height: 3px; background: var(--border); border-radius: 2px;"></div>
                        </div>
                        <p id="strength-text" style="font-size: 9px; color: var(--text-muted); margin-bottom: 6px;"></p>
                        <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                            <div id="check-length" style="display: flex; align-items: center; gap: 3px; font-size: 9px; color: var(--text-muted);">
                                <span class="material-symbols-outlined" style="font-size: 12px;">circle</span>
                                <span>8+ chars</span>
                            </div>
                            <div id="check-upper" style="display: flex; align-items: center; gap: 3px; font-size: 9px; color: var(--text-muted);">
                                <span class="material-symbols-outlined" style="font-size: 12px;">circle</span>
                                <span>Aa</span>
                            </div>
                            <div id="check-number" style="display: flex; align-items: center; gap: 3px; font-size: 9px; color: var(--text-muted);">
                                <span class="material-symbols-outlined" style="font-size: 12px;">circle</span>
                                <span>123</span>
                            </div>
                            <div id="check-special" style="display: flex; align-items: center; gap: 3px; font-size: 9px; color: var(--text-muted);">
                                <span class="material-symbols-outlined" style="font-size: 12px;">circle</span>
                                <span>!@#</span>
                            </div>
                        </div>
                    </div>

                    <!-- Confirm Password -->
                    <div style="margin-bottom: 16px;">
                        <label style="display: block; font-size: 11px; font-weight: 600; color: var(--text-secondary); margin-bottom: 5px;">Confirm Password</label>
                        <div class="input-fancy" style="display: flex; align-items: center; gap: 10px; background-color: var(--bg-tertiary); border: 2px solid var(--border); border-radius: 10px; padding: 0 12px;">
                            <span class="material-symbols-outlined" style="color: var(--text-muted); font-size: 18px;">lock</span>
                            <input type="password" name="password_confirmation" id="password_confirmation" required
                                style="width: 100%; background: transparent !important; border: none; color: var(--text-primary); font-size: 13px; padding: 12px 0; outline: none;">
                            <button type="button" onclick="togglePassword('password_confirmation', 'confirm-toggle-icon')" style="background: none; border: none; cursor: pointer; color: var(--text-muted); padding: 4px;">
                                <span id="confirm-toggle-icon" class="material-symbols-outlined" style="font-size: 18px;">visibility</span>
                            </button>
                        </div>
                        <div id="confirm-error" style="font-size: 10px; color: var(--accent-red); margin-top: 4px; display: none;"></div>
                    </div>

                    <!-- Terms -->
                    <div style="margin-bottom: 18px;">
                        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                            <input type="checkbox" id="terms" required style="accent-color: var(--accent); width: 14px; height: 14px;">
                            <span style="font-size: 10px; color: var(--text-secondary);">I agree to the <a href="#" style="color: var(--accent);">Terms</a></span>
                        </label>
                        <div id="terms-error" style="font-size: 10px; color: var(--accent-red); margin-top: 4px; display: none;"></div>
                    </div>

                    <!-- Register Button -->
                    <button type="submit" id="register-btn" class="btn-fancy"
                        style="width: 100%; border: none; background: linear-gradient(135deg, var(--accent), var(--accent-purple)); color: white; font-family: monospace; font-size: 13px; font-weight: 700; padding: 11px; cursor: pointer; border-radius: 10px;">
                        <span id="btn-text">Create Account</span>
                        <span id="btn-spinner" style="display: none; display: inline-block; width: 16px; height: 16px; border: 2px solid white; border-top-color: transparent; border-radius: 50%; animation: spin 0.6s linear infinite; margin-left: 8px;"></span>
                    </button>

                    <!-- Divider -->
                    <div style="display: flex; align-items: center; margin: 16px 0;">
                        <div style="flex: 1; height: 1px; background: linear-gradient(90deg, transparent, var(--border), transparent);"></div>
                        <span style="padding: 0 12px; font-size: 10px; color: var(--text-muted);">or</span>
                        <div style="flex: 1; height: 1px; background: linear-gradient(90deg, transparent, var(--border), transparent);"></div>
                    </div>

                    <!-- Guest Mode -->
                    <a href="{{ route('guest.mode') }}" class="btn-fancy"
                        style="display: flex; align-items: center; justify-content: center; gap: 8px; width: 100%; border: 2px solid var(--accent-green); background: transparent; color: var(--accent-green); font-family: monospace; font-size: 12px; font-weight: 600; padding: 9px; cursor: pointer; text-decoration: none; border-radius: 10px; margin-bottom: 16px;">
                        <span class="material-symbols-outlined" style="font-size: 16px;">person_outline</span>
                        Continue as Guest
                    </a>

                    <!-- Login Link -->
                    <p style="text-align: center; font-size: 11px; color: var(--text-muted);">
                        Already have an account?
                        <a href="{{ route('login') }}" style="color: var(--accent); text-decoration: none; font-weight: 600;">Sign in</a>
                    </p>
                </form>
            </div>
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

    function togglePassword(fieldId, iconId) {
        const field = document.getElementById(fieldId);
        const icon = document.getElementById(iconId);
        if (field.type === 'password') {
            field.type = 'text';
            icon.textContent = 'visibility_off';
        } else {
            field.type = 'password';
            icon.textContent = 'visibility';
        }
    }

    const passwordInput = document.getElementById('password');
    const strengthContainer = document.getElementById('strength-container');
    const bars = ['strength-bar1', 'strength-bar2', 'strength-bar3', 'strength-bar4'];
    const strengthText = document.getElementById('strength-text');
    
    const checkLength = document.getElementById('check-length');
    const checkUpper = document.getElementById('check-upper');
    const checkNumber = document.getElementById('check-number');
    const checkSpecial = document.getElementById('check-special');
    const confirmInput = document.getElementById('password_confirmation');
    const confirmError = document.getElementById('confirm-error');

    passwordInput.addEventListener('focus', () => {
        strengthContainer.style.display = 'block';
    });

    passwordInput.addEventListener('input', function() {
        const password = this.value;
        let strength = 0;
        
        const hasLength = password.length >= 8;
        const hasUpperLower = /[a-z]/.test(password) && /[A-Z]/.test(password);
        const hasNumber = /[0-9]/.test(password);
        const hasSpecial = /[^a-zA-Z0-9]/.test(password);
        
        function updateChecklist(element, isValid) {
            const icon = element.querySelector('span');
            if (isValid) {
                element.style.color = '#22c55e';
                icon.textContent = 'check_circle';
            } else {
                element.style.color = 'var(--text-muted)';
                icon.textContent = 'circle';
            }
        }
        
        updateChecklist(checkLength, hasLength);
        updateChecklist(checkUpper, hasUpperLower);
        updateChecklist(checkNumber, hasNumber);
        updateChecklist(checkSpecial, hasSpecial);
        
        if (hasLength) strength++;
        if (hasUpperLower) strength++;
        if (hasNumber) strength++;
        if (hasSpecial) strength++;
        
        const colors = ['#ef4444', '#f59e0b', '#eab308', '#22c55e'];
        const texts = ['Weak', 'Fair', 'Good', 'Strong'];
        
        for (let i = 0; i < 4; i++) {
            const bar = document.getElementById(bars[i]);
            if (i < strength) {
                bar.style.background = colors[strength-1];
            } else {
                bar.style.background = 'var(--border)';
            }
        }
        
        if (password.length > 0) {
            strengthText.textContent = `Strength: ${texts[strength-1] || 'Weak'}`;
            strengthText.style.color = colors[strength-1] || '#ef4444';
        }
        
        if (confirmInput.value.length > 0) checkPasswordMatch();
    });
    
    function checkPasswordMatch() {
        if (passwordInput.value !== confirmInput.value) {
            confirmError.textContent = 'Passwords do not match';
            confirmError.style.display = 'block';
            confirmInput.parentElement.style.borderColor = '#ef4444';
            return false;
        } else {
            confirmError.style.display = 'none';
            confirmInput.parentElement.style.borderColor = 'var(--border)';
            return true;
        }
    }
    
    confirmInput.addEventListener('input', checkPasswordMatch);

    const form = document.getElementById('registerForm');
    const nameInput = document.getElementById('name');
    const emailInput = document.getElementById('email');
    const termsCheckbox = document.getElementById('terms');
    const nameError = document.getElementById('name-error');
    const emailError = document.getElementById('email-error');
    const termsError = document.getElementById('terms-error');
    const registerBtn = document.getElementById('register-btn');
    const btnText = document.getElementById('btn-text');
    const btnSpinner = document.getElementById('btn-spinner');

    form.addEventListener('submit', function(e) {
        let isValid = true;
        
        if (!nameInput.value.trim()) {
            nameError.textContent = 'Name required';
            nameError.style.display = 'block';
            nameInput.parentElement.style.borderColor = '#ef4444';
            isValid = false;
        } else {
            nameError.style.display = 'none';
            nameInput.parentElement.style.borderColor = 'var(--border)';
        }
        
        const email = emailInput.value.trim();
        if (!email || !email.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
            emailError.textContent = 'Valid email required';
            emailError.style.display = 'block';
            emailInput.parentElement.style.borderColor = '#ef4444';
            isValid = false;
        } else {
            emailError.style.display = 'none';
            emailInput.parentElement.style.borderColor = 'var(--border)';
        }
        
        if (!termsCheckbox.checked) {
            termsError.style.display = 'block';
            isValid = false;
        } else {
            termsError.style.display = 'none';
        }
        
        if (!checkPasswordMatch()) isValid = false;
        
        if (!isValid) {
            e.preventDefault();
        } else {
            btnText.textContent = 'Creating...';
            btnSpinner.style.display = 'inline-block';
            registerBtn.style.opacity = '0.7';
        }
    });

    nameInput.addEventListener('input', () => nameError.style.display = 'none');
    emailInput.addEventListener('input', () => emailError.style.display = 'none');
    termsCheckbox.addEventListener('change', () => termsError.style.display = 'none');

    function createParticles() {
        const container = document.getElementById('particles-container');
        for (let i = 0; i < 40; i++) {
            const particle = document.createElement('div');
            particle.className = 'particle';
            const size = Math.random() * 3 + 1;
            particle.style.width = size + 'px';
            particle.style.height = size + 'px';
            particle.style.background = ['#3b82f6', '#a855f7', '#22c55e'][Math.floor(Math.random() * 3)];
            particle.style.borderRadius = Math.random() > 0.5 ? '50%' : '2px';
            particle.style.left = Math.random() * 100 + '%';
            particle.style.top = Math.random() * 100 + '%';
            particle.style.animation = `floatSlow ${Math.random() * 8 + 4}s ease-in-out infinite`;
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