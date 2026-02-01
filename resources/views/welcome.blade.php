<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>TajTrainer</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

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
            
            .container {
                position: relative;
                z-index: 1;
                max-width: 1400px;
                margin: 0 auto;
                padding: 0 20px;
            }
            
            /* Header */
            header {
                padding: 30px 0;
                display: flex;
                justify-content: space-between;
                align-items: center;
                border-bottom: 2px solid rgba(226, 241, 175, 0.1);
            }
            
            .logo {
                font-family: 'Amiri', serif;
                font-size: 2.5rem;
                font-weight: 700;
                color: var(--color-gold);
                text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
                display: flex;
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
            
            .nav-buttons {
                display: flex;
                gap: 15px;
            }
            
            .btn {
                padding: 12px 30px;
                border: none;
                border-radius: 25px;
                font-size: 1rem;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.3s ease;
                text-decoration: none;
                display: inline-block;
                font-family: 'Cairo', sans-serif;
            }
            
            .btn-login {
                background: transparent;
                color: var(--color-light-green);
                border: 2px solid var(--color-light-green);
            }
            
            .btn-login:hover {
                background: var(--color-light-green);
                color: var(--color-dark);
                transform: translateY(-2px);
                box-shadow: 0 5px 15px rgba(226, 241, 175, 0.3);
            }
            
            .btn-register {
                background: var(--color-dark-green);
                color: var(--color-light-green);
                border: 2px solid var(--color-dark-green);
            }
            
            .btn-register:hover {
                background: transparent;
                border-color: var(--color-dark-green);
                transform: translateY(-2px);
                box-shadow: 0 5px 15px rgba(77, 139, 49, 0.3);
            }
            
            .btn-logout {
                background: rgba(231, 76, 60, 0.8);
                color: white;
                border: 2px solid rgba(231, 76, 60, 0.8);
            }
            
            .btn-logout:hover {
                background: rgba(231, 76, 60, 1);
                border-color: rgba(231, 76, 60, 1);
                transform: translateY(-2px);
                box-shadow: 0 5px 15px rgba(231, 76, 60, 0.4);
            }
            
            /* Hero Section */
            .hero {
                padding: 100px 0;
                text-align: center;
            }
            
            .hero h1 {
                font-family: 'Amiri', serif;
                font-size: 6rem;
                color: var(--color-gold);
                margin-bottom: 30px;
                text-shadow: 4px 4px 8px rgba(0, 0, 0, 0.8);
                line-height: 1.2;
                font-weight: 700;
            }
            
            .hero .arabic-text {
                font-size: 2.5rem;
                color: var(--color-light-green);
                margin-bottom: 40px;
                font-weight: 700;
                text-shadow: 3px 3px 6px rgba(0, 0, 0, 0.8);
                letter-spacing: 1px;
            }
            
            .hero p {
                font-size: 1.6rem;
                color: var(--color-light-green);
                max-width: 900px;
                margin: 0 auto 50px;
                line-height: 2;
                font-weight: 500;
                text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.8);
            }
            
            .hero-cta {
                display: flex;
                justify-content: center;
                gap: 20px;
                margin-top: 40px;
            }
            
            .btn-primary {
                background: var(--color-dark-green);
                color: var(--color-gold);
                padding: 18px 45px;
                font-size: 1.2rem;
                box-shadow: 0 8px 20px rgba(77, 139, 49, 0.4);
            }
            
            .btn-primary:hover {
                background: var(--color-gold);
                color: var(--color-dark);
                transform: translateY(-3px);
                box-shadow: 0 12px 30px rgba(227, 216, 136, 0.4);
            }
            
            .btn-secondary {
                background: transparent;
                color: var(--color-gold);
                border: 2px solid var(--color-gold);
                padding: 18px 45px;
                font-size: 1.2rem;
            }
            
            .btn-secondary:hover {
                background: var(--color-gold);
                color: var(--color-dark);
                transform: translateY(-3px);
            }
            
            /* Features Section - Slideshow */
            .features {
                padding: 80px 0;
            }
            
            .slideshow-container {
                background: rgba(70, 63, 58, 0.6);
                padding: 60px 80px;
                border-radius: 30px;
                border: 3px solid var(--color-dark-green);
                box-shadow: 0 20px 60px rgba(77, 139, 49, 0.4);
                backdrop-filter: blur(10px);
                position: relative;
                min-height: 500px;
                max-width: 900px;
                margin: 0 auto;
            }
            
            .slide {
                display: none;
                text-align: center;
                animation: slideIn 0.8s ease-in-out;
            }
            
            .slide.active {
                display: block;
            }
            
            @keyframes slideIn {
                0% {
                    opacity: 0;
                    transform: translateX(100px);
                }
                100% {
                    opacity: 1;
                    transform: translateX(0);
                }
            }
            
            .slide.fade-out {
                animation: slideOut 0.5s ease-in-out;
            }
            
            @keyframes slideOut {
                0% {
                    opacity: 1;
                    transform: translateX(0);
                }
                100% {
                    opacity: 0;
                    transform: translateX(-100px);
                }
            }
            
            .feature-icon {
                width: 120px;
                height: 120px;
                background: var(--color-dark-green);
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 4rem;
                margin: 0 auto 30px;
                color: var(--color-gold);
                box-shadow: 0 8px 30px rgba(77, 139, 49, 0.5);
            }
            
            .slide h3 {
                font-family: 'Amiri', serif;
                font-size: 2.8rem;
                color: var(--color-gold);
                margin-bottom: 25px;
                text-align: center;
            }
            
            .slide p {
                color: var(--color-light-green);
                line-height: 1.9;
                text-align: center;
                opacity: 0.95;
                font-size: 1.2rem;
                max-width: 700px;
                margin: 0 auto;
            }
            
            /* Navigation Controls */
            .slide-nav {
                display: flex;
                justify-content: center;
                gap: 12px;
                margin-top: 40px;
            }
            
            .slide-dot {
                width: 14px;
                height: 14px;
                border-radius: 50%;
                background: rgba(226, 241, 175, 0.3);
                border: 2px solid var(--color-light-green);
                cursor: pointer;
                transition: all 0.3s ease;
            }
            
            .slide-dot:hover {
                background: rgba(226, 241, 175, 0.5);
                transform: scale(1.2);
            }
            
            .slide-dot.active {
                background: var(--color-gold);
                border-color: var(--color-gold);
                transform: scale(1.3);
            }
            
            /* Slide Counter */
            .slide-counter {
                position: absolute;
                top: 20px;
                right: 30px;
                color: var(--color-gold);
                font-size: 1rem;
                font-weight: 600;
            }
            
            /* Islamic Decorative Elements */
            .decorative-star {
                position: absolute;
                color: var(--color-gold);
                opacity: 0.3;
                animation: twinkle 3s infinite;
            }
            
            @keyframes twinkle {
                0%, 100% { opacity: 0.3; }
                50% { opacity: 0.6; }
            }
            
            .star-1 { top: 20%; left: 10%; font-size: 2rem; }
            .star-2 { top: 40%; right: 15%; font-size: 1.5rem; animation-delay: 1s; }
            .star-3 { bottom: 30%; left: 20%; font-size: 1.8rem; animation-delay: 2s; }
            .star-4 { top: 60%; right: 10%; font-size: 2.2rem; animation-delay: 0.5s; }
            
            /* Footer */
            footer {
                text-align: center;
                padding: 40px 0;
                border-top: 2px solid rgba(226, 241, 175, 0.1);
                color: var(--color-light-green);
                opacity: 0.8;
            }
            
            /* Responsive */
            @media (max-width: 768px) {
                .hero h1 {
                    font-size: 3rem;
                }
                
                .hero .arabic-text {
                    font-size: 1.8rem;
                }
                
                .hero p {
                    font-size: 1.2rem;
                }
                
                .logo {
                    font-size: 1.8rem;
                }
                
                .hero-cta {
                    flex-direction: column;
                    align-items: center;
                }
                
                .btn {
                    padding: 10px 25px;
                    font-size: 0.9rem;
                }
                
                .nav-buttons {
                    flex-direction: column;
                }
                
                .slideshow-container {
                    padding: 40px 30px;
                    min-height: 400px;
                }
                
                .slide h3 {
                    font-size: 2rem;
                }
                
                .slide p {
                    font-size: 1rem;
                }
                
                .feature-icon {
                    width: 90px;
                    height: 90px;
                    font-size: 3rem;
                }
            }
        </style>
        
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                let currentSlide = 0;
                const slides = document.querySelectorAll('.slide');
                const dots = document.querySelectorAll('.slide-dot');
                const totalSlides = slides.length;
                const counter = document.querySelector('.slide-counter');
                
                function showSlide(index) {
                    // Handle wrapping
                    if (index >= totalSlides) {
                        currentSlide = 0;
                    } else if (index < 0) {
                        currentSlide = totalSlides - 1;
                    } else {
                        currentSlide = index;
                    }
                    
                    // Hide all slides and deactivate dots
                    slides.forEach(slide => {
                        slide.classList.remove('active');
                    });
                    dots.forEach(dot => {
                        dot.classList.remove('active');
                    });
                    
                    // Show current slide and activate dot
                    slides[currentSlide].classList.add('active');
                    dots[currentSlide].classList.add('active');
                    
                    // Update counter
                    counter.textContent = `${currentSlide + 1} / ${totalSlides}`;
                }
                
                function nextSlide() {
                    showSlide(currentSlide + 1);
                }
                
                function prevSlide() {
                    showSlide(currentSlide - 1);
                }
                
                // Dot navigation
                dots.forEach((dot, index) => {
                    dot.addEventListener('click', () => {
                        showSlide(index);
                    });
                });
                
                // Keyboard navigation
                document.addEventListener('keydown', (e) => {
                    if (e.key === 'ArrowRight') nextSlide();
                    if (e.key === 'ArrowLeft') prevSlide();
                });
                
                // Auto-play - automatically advance every 4 seconds
                setInterval(nextSlide, 4000);
                
                // Initialize
                showSlide(0);
            });
        </script>
    </head>
    <body>
        <!-- Islamic Pattern Background -->
        <div class="islamic-pattern"></div>
        
        <!-- Decorative Stars -->
        <div class="decorative-star star-1">✦</div>
        <div class="decorative-star star-2">✧</div>
        <div class="decorative-star star-3">✦</div>
        <div class="decorative-star star-4">✧</div>
        
        <div class="container">
            <!-- Header -->
            <header>
                <div class="logo">
                    <div class="logo-icon">☪</div>
                    <span>TajTrainer</span>
                </div>
                
                @if (Route::has('login'))
                    <nav class="nav-buttons">
                        @auth
                            @php
                                // Role-based dashboard routing
                                $dashboardRoute = '/home'; // default
                                $user = Auth::user();
                                
                                if ($user->role_id == 2) {
                                    $dashboardRoute = route('student.classes');
                                } elseif ($user->role_id == 3) {
                                    $dashboardRoute = route('teachers.index');
                                }
                            @endphp
                            <a href="{{ $dashboardRoute }}" class="btn btn-login">Dashboard</a>
                            <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn btn-logout" style="cursor: pointer;">Logout</button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-login">Log in</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="btn btn-register">Register</a>
                            @endif
                        @endauth
                    </nav>
                @endif
            </header>
            
            <!-- Hero Section -->
            <section class="hero">
                <h1>TajTrainer</h1>
                <div class="arabic-text">Ai Web-Based Tajweed Detection Error and Learning</div>
                <p>
                    Your comprehensive Islamic learning platform. Master the Quran with Tajweed, 
                    deepen your understanding of Islamic teachings, and connect with a global community 
                    of learners on a spiritual journey towards excellence.
                </p>
                
                <div class="hero-cta">
                    <a href="{{ route('register') }}" class="btn btn-primary">Start Your Journey</a>
                    <a href="#features" class="btn btn-secondary">Learn More</a>
                </div>
            </section>
            
            <!-- Features Section -->
            <section class="features" id="features">
                <div class="slideshow-container">
                    <!-- Slide Counter -->
                    <div class="slide-counter">1 / 6</div>
                    
                    <!-- Slides -->
                    <div class="slide active">
                        <div class="feature-icon">📖</div>
                        <h3>Quran Recitation</h3>
                        <p>
                            Learn proper Quran recitation with Tajweed rules through interactive lessons 
                            and expert guidance from certified instructors.
                        </p>
                    </div>
                    
                    <div class="slide">
                        <div class="feature-icon">🕌</div>
                        <h3>Islamic Studies</h3>
                        <p>
                            Explore comprehensive courses on Fiqh, Hadith, Seerah, and Islamic history 
                            to strengthen your knowledge and faith.
                        </p>
                    </div>
                    
                    <div class="slide">
                        <div class="feature-icon">👥</div>
                        <h3>Community Learning</h3>
                        <p>
                            Join a vibrant community of learners worldwide. Share knowledge, ask questions, 
                            and grow together in faith.
                        </p>
                    </div>
                    
                    <div class="slide">
                        <div class="feature-icon">⏰</div>
                        <h3>Flexible Schedule</h3>
                        <p>
                            Learn at your own pace with on-demand lessons and live classes that fit 
                            your busy lifestyle and time zone.
                        </p>
                    </div>
                    
                    <div class="slide">
                        <div class="feature-icon">🎓</div>
                        <h3>Certified Teachers</h3>
                        <p>
                            Study under qualified scholars and teachers with Ijazah certification, 
                            ensuring authentic Islamic education.
                        </p>
                    </div>
                    
                    <div class="slide">
                        <div class="feature-icon">📱</div>
                        <h3>Multi-Device Access</h3>
                        <p>
                            Access your lessons anytime, anywhere on any device - desktop, tablet, 
                            or mobile for seamless learning.
                        </p>
                    </div>
                    
                    <!-- Navigation Dots -->
                    <div class="slide-nav">
                        <span class="slide-dot active"></span>
                        <span class="slide-dot"></span>
                        <span class="slide-dot"></span>
                        <span class="slide-dot"></span>
                        <span class="slide-dot"></span>
                        <span class="slide-dot"></span>
                    </div>
                </div>
            </section>
        </div>
        
        <!-- Footer -->
        <footer>
            <div class="container">
                <p>© {{ date('Y') }} TajTrainer. All rights reserved.</p>
            </div>
        </footer>
    </body>
</html>
