<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Login - TajTrainer</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=amiri:400,700|cairo:400,600,700&display=swap" rel="stylesheet" />

        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }
            
            :root {
                --color-light-green: #E2F1AF;
                --color-dark-green: #4D8B31;
                --color-gold: #E3D888;
                --color-dark: #1F271B;
                --color-brown: #463F3A;
            }
            
            body {
                font-family: 'Cairo', sans-serif;
                background: linear-gradient(135deg, var(--color-dark) 0%, var(--color-brown) 100%);
                min-height: 100vh;
                color: var(--color-light-green);
                overflow-x: hidden;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 20px;
            }
            
            .islamic-pattern {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                opacity: 0.05;
                background-image: 
                    repeating-linear-gradient(45deg, transparent, transparent 35px, var(--color-gold) 35px, var(--color-gold) 70px),
                    repeating-linear-gradient(-45deg, transparent, transparent 35px, var(--color-gold) 35px, var(--color-gold) 70px);
                pointer-events: none;
                z-index: 0;
            }
            
            .decorative-star {
                position: fixed;
                color: var(--color-gold);
                opacity: 0.3;
                animation: twinkle 3s infinite;
            }
            
            @keyframes twinkle {
                0%, 100% { opacity: 0.3; }
                50% { opacity: 0.6; }
            }
            
            .star-1 { top: 10%; left: 10%; font-size: 2rem; }
            .star-2 { top: 20%; right: 15%; font-size: 1.5rem; animation-delay: 1s; }
            .star-3 { bottom: 15%; left: 20%; font-size: 1.8rem; animation-delay: 2s; }
            .star-4 { bottom: 20%; right: 10%; font-size: 2.2rem; animation-delay: 0.5s; }
            
            .login-container {
                position: relative;
                z-index: 1;
                max-width: 550px;
                width: 100%;
            }
            
            .login-card {
                background: rgba(70, 63, 58, 0.6);
                backdrop-filter: blur(15px);
                border-radius: 30px;
                border: 3px solid var(--color-dark-green);
                box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4);
                padding: 50px 40px;
            }
            
            .logo-section {
                text-align: center;
                margin-bottom: 40px;
            }
            
            .logo {
                font-family: 'Amiri', serif;
                font-size: 2.5rem;
                font-weight: 700;
                color: var(--color-gold);
                text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
                display: inline-flex;
                align-items: center;
                gap: 15px;
            }
            
            .logo-icon {
                width: 50px;
                height: 50px;
                background: var(--color-dark-green);
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 1.8rem;
                color: var(--color-gold);
                box-shadow: 0 4px 15px rgba(77, 139, 49, 0.4);
            }
            
            .login-title {
                font-family: 'Amiri', serif;
                font-size: 2rem;
                color: var(--color-gold);
                text-align: center;
                margin-bottom: 30px;
                text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
            }
            
            .form-group {
                margin-bottom: 25px;
            }
            
            label {
                display: block;
                color: var(--color-light-green);
                font-weight: 600;
                margin-bottom: 8px;
                font-size: 1rem;
                text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
            }
            
            input[type="email"],
            input[type="password"] {
                width: 100%;
                padding: 14px 18px;
                border: 2px solid var(--color-dark-green);
                border-radius: 12px;
                background: rgba(31, 39, 27, 0.5);
                color: var(--color-light-green);
                font-size: 1rem;
                font-family: 'Cairo', sans-serif;
                transition: all 0.3s ease;
            }
            
            input[type="email"]:focus,
            input[type="password"]:focus {
                outline: none;
                border-color: var(--color-gold);
                background: rgba(31, 39, 27, 0.7);
                box-shadow: 0 0 15px rgba(227, 216, 136, 0.3);
            }
            
            .remember-group {
                display: flex;
                align-items: center;
                gap: 10px;
                margin-bottom: 25px;
            }
            
            input[type="checkbox"] {
                width: 20px;
                height: 20px;
                cursor: pointer;
                accent-color: var(--color-dark-green);
            }
            
            .remember-group label {
                margin-bottom: 0;
                cursor: pointer;
            }
            
            .error-message {
                color: #ff6b6b;
                font-size: 0.9rem;
                margin-top: 5px;
                text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
            }
            
            .btn-login {
                width: 100%;
                padding: 16px;
                background: var(--color-dark-green);
                color: var(--color-gold);
                border: 2px solid var(--color-dark-green);
                border-radius: 25px;
                font-size: 1.2rem;
                font-weight: 700;
                cursor: pointer;
                transition: all 0.3s ease;
                font-family: 'Cairo', sans-serif;
                margin-top: 10px;
                box-shadow: 0 5px 20px rgba(77, 139, 49, 0.4);
            }
            
            .btn-login:hover {
                background: var(--color-gold);
                color: var(--color-dark);
                transform: translateY(-2px);
                box-shadow: 0 8px 25px rgba(227, 216, 136, 0.4);
            }
            
            .links-section {
                text-align: center;
                margin-top: 25px;
            }
            
            .links-section a {
                color: var(--color-light-green);
                text-decoration: none;
                font-weight: 600;
                transition: color 0.3s ease;
                display: inline-block;
                margin: 5px 10px;
            }
            
            .links-section a:hover {
                color: var(--color-gold);
            }
            
            @media (max-width: 768px) {
                .login-card {
                    padding: 35px 25px;
                }
                
                .logo {
                    font-size: 2rem;
                }
                
                .logo-icon {
                    width: 40px;
                    height: 40px;
                    font-size: 1.5rem;
                }
                
                .login-title {
                    font-size: 1.6rem;
                }
            }
        </style>
    </head>
    <body>
        <!-- Islamic Pattern Background -->
        <div class="islamic-pattern"></div>
        
        <!-- Decorative Stars -->
        <div class="decorative-star star-1">✦</div>
        <div class="decorative-star star-2">✧</div>
        <div class="decorative-star star-3">✦</div>
        <div class="decorative-star star-4">✧</div>
        
        <div class="login-container">
            <div class="login-card">
                <div class="logo-section">
                    <div class="logo">
                        <div class="logo-icon">☪</div>
                        <span>TajTrainer</span>
                    </div>
                </div>
                
                <h2 class="login-title">Welcome Back</h2>
                
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

                    <div class="remember-group">
                        <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                        <label for="remember">Remember Me</label>
                    </div>

                    <button type="submit" class="btn-login">
                        Login
                    </button>
                </form>
                
                <div class="links-section">
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}">Forgot Password?</a>
                    @endif
                    @if (Route::has('register'))
                        <span style="color: var(--color-light-green); opacity: 0.5;">|</span>
                        <a href="{{ route('register') }}">Create Account</a>
                    @endif
                    <br>
                    <a href="{{ url('/') }}">← Back to Home</a>
                </div>
            </div>
        </div>
    </body>
</html>
