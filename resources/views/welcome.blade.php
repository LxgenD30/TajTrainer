<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>TajTrainer | Master Quranic Tajweed</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Reem+Kufi+Fun:wght@400;500;600;700&family=Amiri:wght@400;700&family=El+Messiri:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-green: #0a5c36;
            --light-green: #2e8b57;
            --gold: #d4af37;
            --cream: #f5f5dc;
            --dark-green: #064e32;
            --white: #ffffff;
            --shadow: rgba(10, 92, 54, 0.15);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Amiri', serif;
            color: #333;
            background-color: var(--cream);
            line-height: 1.6;
            overflow-x: hidden;
        }
        
        h1, h2, h3, h4 {
            font-family: 'Reem Kufi Fun', sans-serif;
            color: var(--dark-green);
            font-weight: 600;
        }
        
        .logo-font {
            font-family: 'El Messiri', sans-serif;
            font-weight: 700;
        }
        
        .container {
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        /* Header Styles */
        header {
            background: linear-gradient(to right, var(--primary-green), var(--light-green));
            color: var(--white);
            padding: 15px 0;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 4px 12px var(--shadow);
        }
        
        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo {
            display: flex;
            align-items: center;
            gap: 15px;
            text-decoration: none;
        }
        
        .logo-icon {
            font-size: 2.5rem;
            color: var(--gold);
        }
        
        .logo-text {
            font-size: 1.8rem;
            letter-spacing: 1px;
            color: var(--white);
        }
        
        .logo-text span {
            color: var(--gold);
        }
        
        nav ul {
            display: flex;
            list-style: none;
            gap: 30px;
            align-items: center;
        }
        
        nav a {
            color: var(--white);
            text-decoration: none;
            font-family: 'El Messiri', sans-serif;
            font-size: 1.2rem;
            font-weight: 500;
            transition: all 0.3s ease;
            padding: 5px 10px;
            border-radius: 4px;
        }
        
        nav a:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: var(--gold);
        }
        
        .auth-buttons {
            display: flex;
            gap: 15px;
            margin-left: 20px;
        }
        
        .btn-login, .btn-register {
            padding: 8px 20px;
            border-radius: 50px;
            font-family: 'El Messiri', sans-serif;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid transparent;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-login {
            background-color: transparent;
            color: var(--white);
            border-color: var(--white);
        }
        
        .btn-login:hover {
            background-color: rgba(255, 255, 255, 0.1);
            transform: translateY(-3px);
        }
        
        .btn-register {
            background-color: var(--gold);
            color: var(--dark-green);
        }
        
        .btn-register:hover {
            background-color: var(--white);
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .mobile-menu-btn {
            display: none;
            background: none;
            border: none;
            color: var(--white);
            font-size: 1.8rem;
            cursor: pointer;
        }
        
        /* Hero Section */
        .hero {
            background: linear-gradient(rgba(10, 92, 54, 0.85), rgba(46, 139, 87, 0.9)), url('https://images.unsplash.com/photo-1562774053-701939374585?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1200&q=80');
            background-size: cover;
            background-position: center;
            color: var(--white);
            padding: 100px 0;
            text-align: center;
            position: relative;
        }
        
        .hero-content {
            max-width: 800px;
            margin: 0 auto;
            position: relative;
            z-index: 2;
        }
        
        .hero h1 {
            font-size: 3.5rem;
            color: var(--white);
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }
        
        .hero p {
            font-size: 1.4rem;
            margin-bottom: 30px;
            opacity: 0.95;
        }
        
        .cta-button {
            display: inline-block;
            background-color: var(--gold);
            color: var(--dark-green);
            padding: 15px 35px;
            font-size: 1.2rem;
            font-weight: 600;
            text-decoration: none;
            border-radius: 50px;
            transition: all 0.3s ease;
            font-family: 'El Messiri', sans-serif;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            margin-top: 10px;
        }
        
        .cta-button:hover {
            background-color: var(--white);
            transform: translateY(-5px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.25);
        }
        
        /* Main Content */
        .content-section {
            padding: 80px 0;
        }
        
        .section-title {
            text-align: center;
            font-size: 2.8rem;
            margin-bottom: 50px;
            position: relative;
        }
        
        .section-title:after {
            content: '';
            position: absolute;
            width: 100px;
            height: 5px;
            background-color: var(--gold);
            bottom: -15px;
            left: 50%;
            transform: translateX(-50%);
            border-radius: 5px;
        }
        
        /* Features Grid */
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 40px;
            margin-top: 40px;
        }
        
        .feature-card {
            background-color: var(--white);
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 20px var(--shadow);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            text-align: center;
            padding: 30px;
        }
        
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(10, 92, 54, 0.2);
        }
        
        .feature-icon {
            font-size: 3.5rem;
            color: var(--primary-green);
            margin-bottom: 20px;
        }
        
        .feature-card h3 {
            font-size: 1.8rem;
            margin-bottom: 15px;
        }
        
        .feature-card p {
            font-size: 1.1rem;
            color: #555;
        }
        
        /* Tajweed Rules Section */
        .tajweed-rules {
            background-color: rgba(10, 92, 54, 0.05);
            padding: 60px 0;
        }
        
        .rules-container {
            display: flex;
            flex-wrap: wrap;
            gap: 30px;
            justify-content: center;
            margin-top: 40px;
        }
        
        .rule-card {
            flex: 1;
            min-width: 300px;
            max-width: 350px;
            background-color: var(--white);
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 8px 16px var(--shadow);
        }
        
        .rule-header {
            background-color: var(--primary-green);
            color: var(--white);
            padding: 20px;
            text-align: center;
        }
        
        .rule-header h3 {
            color: var(--white);
            font-size: 1.5rem;
        }
        
        .rule-content {
            padding: 25px;
        }
        
        .rule-content p {
            margin-bottom: 15px;
            font-size: 1.1rem;
        }
        
        .arabic-text {
            font-size: 1.8rem;
            text-align: center;
            color: var(--primary-green);
            margin: 15px 0;
            font-family: 'Amiri', serif;
        }
        
        /* Learning Path */
        .learning-path {
            padding: 80px 0;
        }
        
        .path-container {
            display: flex;
            flex-direction: column;
            gap: 40px;
            max-width: 800px;
            margin: 40px auto 0;
            position: relative;
        }
        
        .path-container:before {
            content: '';
            position: absolute;
            width: 5px;
            height: 100%;
            background-color: var(--gold);
            left: 50%;
            transform: translateX(-50%);
            border-radius: 5px;
        }
        
        .path-step {
            display: flex;
            align-items: center;
            gap: 30px;
            position: relative;
        }
        
        .path-step:nth-child(odd) {
            flex-direction: row;
        }
        
        .path-step:nth-child(even) {
            flex-direction: row-reverse;
        }
        
        .step-icon {
            width: 80px;
            height: 80px;
            background-color: var(--primary-green);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--white);
            font-size: 2.5rem;
            flex-shrink: 0;
            z-index: 2;
        }
        
        .step-content {
            flex: 1;
            background-color: var(--white);
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 8px 16px var(--shadow);
        }
        
        .step-content h3 {
            color: var(--primary-green);
            margin-bottom: 10px;
            font-size: 1.5rem;
        }
        
        /* Footer */
        footer {
            background-color: var(--dark-green);
            color: var(--white);
            padding: 60px 0 30px;
        }
        
        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 40px;
            margin-bottom: 40px;
        }
        
        .footer-column h3 {
            color: var(--gold);
            font-size: 1.5rem;
            margin-bottom: 20px;
            font-family: 'El Messiri', sans-serif;
        }
        
        .footer-links {
            list-style: none;
        }
        
        .footer-links li {
            margin-bottom: 10px;
        }
        
        .footer-links a {
            color: var(--cream);
            text-decoration: none;
            transition: color 0.3s ease;
        }
        
        .footer-links a:hover {
            color: var(--gold);
        }
        
        .copyright {
            text-align: center;
            padding-top: 30px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.7);
        }
        
        /* Decorative Elements */
        .islamic-pattern {
            position: absolute;
            opacity: 0.05;
            pointer-events: none;
        }
        
        .pattern-1 {
            top: 10%;
            left: 5%;
            font-size: 8rem;
            color: var(--primary-green);
            transform: rotate(-15deg);
        }
        
        .pattern-2 {
            bottom: 10%;
            right: 5%;
            font-size: 8rem;
            color: var(--light-green);
            transform: rotate(15deg);
        }
        
        /* Tajweed Badge */
        .badge {
            display: inline-block;
            background-color: var(--gold);
            color: var(--dark-green);
            padding: 8px 20px;
            border-radius: 50px;
            font-weight: 600;
            margin: 10px 0;
            font-family: 'El Messiri', sans-serif;
        }
        
        /* Demo Player */
        .demo-player {
            background-color: var(--white);
            border-radius: 15px;
            padding: 25px;
            margin-top: 30px;
            box-shadow: 0 8px 16px var(--shadow);
            text-align: center;
        }
        
        .player-controls {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 20px;
            margin-top: 20px;
        }
        
        .player-btn {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background-color: var(--primary-green);
            color: var(--white);
            border: none;
            font-size: 1.2rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .player-btn:hover {
            background-color: var(--dark-green);
            transform: scale(1.1);
        }
        
        .player-title {
            font-size: 1.3rem;
            color: var(--primary-green);
            margin-bottom: 10px;
        }
        
        /* Responsive Design */
        @media (max-width: 992px) {
            .hero h1 {
                font-size: 2.8rem;
            }
            
            .section-title {
                font-size: 2.4rem;
            }
            
            .path-container:before {
                left: 40px;
            }
            
            .path-step {
                flex-direction: row !important;
                gap: 20px;
            }
            
            .step-icon {
                width: 60px;
                height: 60px;
                font-size: 1.8rem;
            }
        }
        
        @media (max-width: 768px) {
            .header-container {
                flex-wrap: wrap;
            }
            
            nav {
                width: 100%;
                order: 3;
            }
            
            nav ul {
                flex-wrap: wrap;
                justify-content: center;
                gap: 15px;
            }
            
            .auth-buttons {
                margin-left: 0;
                margin-top: 10px;
            }
            
            .hero {
                padding: 70px 0;
            }
            
            .hero h1 {
                font-size: 2.2rem;
            }
            
            .hero p {
                font-size: 1.2rem;
            }
            
            .section-title {
                font-size: 2rem;
            }
            
            .path-container:before {
                display: none;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <div class="container header-container">
            <a href="{{ url('/') }}" class="logo">
                <i class="fas fa-book-quran logo-icon"></i>
                <div class="logo-text logo-font">
                    <span>Taj</span>Trainer
                </div>
            </a>
            
            <nav>
                <ul>
                    <li><a href="#home">Home</a></li>
                    <li><a href="#features">Features</a></li>
                    <li><a href="#rules">Tajweed Rules</a></li>
                    <li><a href="#learning">Learning Path</a></li>
                    <li><a href="#about">About</a></li>
                    <div class="auth-buttons">
                        <a href="{{ route('login') }}" class="btn-login">Login</a>
                        <a href="{{ route('register') }}" class="btn-register">Register</a>
                    </div>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero" id="home">
        <div class="container hero-content">
            <h1>Master Quranic Recitation with Perfect Tajweed</h1>
            <p>TajTrainer combines traditional Islamic knowledge with modern technology to help you learn and perfect your Quran recitation according to Tajweed rules.</p>
            <a href="{{ route('register') }}" class="cta-button">Start Learning Now</a>
            
            <!-- Tajweed Demo Player -->
            <div class="demo-player">
                <div class="player-title">Listen to Perfect Tajweed</div>
                <div class="player-controls">
                    <button class="player-btn">
                        <i class="fas fa-play"></i>
                    </button>
                    <button class="player-btn">
                        <i class="fas fa-pause"></i>
                    </button>
                    <button class="player-btn">
                        <i class="fas fa-stop"></i>
                    </button>
                </div>
                <p style="margin-top: 15px; font-size: 0.9rem; color: #666;">Example: Surah Al-Fatiha with proper Makharij</p>
            </div>
        </div>
        
        <!-- Decorative Islamic Patterns -->
        <div class="islamic-pattern pattern-1">
            <i class="fas fa-star-and-crescent"></i>
        </div>
        <div class="islamic-pattern pattern-2">
            <i class="fas fa-mosque"></i>
        </div>
    </section>

    <!-- Features Section -->
    <section class="content-section" id="features">
        <div class="container">
            <h2 class="section-title">Why Choose TajTrainer?</h2>
            
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-microphone-alt"></i>
                    </div>
                    <h3>Voice Recognition</h3>
                    <p>Our advanced AI analyzes your recitation and provides instant feedback on pronunciation and Tajweed rules.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3>Progress Tracking</h3>
                    <p>Monitor your improvement with detailed analytics and personalized progress reports.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                    <h3>Certified Teachers</h3>
                    <p>Get feedback from certified Quran teachers and Tajweed experts with Ijazah certification.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Tajweed Rules Section -->
    <section class="tajweed-rules" id="rules">
        <div class="container">
            <h2 class="section-title">Essential Tajweed Rules</h2>
            
            <div class="rules-container">
                <div class="rule-card">
                    <div class="rule-header">
                        <h3>Makharij Al-Huruf</h3>
                        <p>Articulation Points</p>
                    </div>
                    <div class="rule-content">
                        <p>Learn the precise points of articulation for each Arabic letter to pronounce them correctly.</p>
                        <div class="arabic-text">قَالَ رَبِّ ٱغْفِرْ لِى</div>
                        <p><strong>Example:</strong> Distinguishing between ق (Qaf) and ك (Kaf)</p>
                    </div>
                </div>
                
                <div class="rule-card">
                    <div class="rule-header">
                        <h3>Ghunnah</h3>
                        <p>Nasalization</p>
                    </div>
                    <div class="rule-content">
                        <p>Master the nasal sound that accompanies ن (Noon) and م (Meem) when they have shaddah or are in specific positions.</p>
                        <div class="arabic-text">إِنَّا أَنزَلْنَـٰهُ</div>
                        <p><strong>Example:</strong> Proper nasalization in إِنَّا and أَنزَلْنَـٰهُ</p>
                    </div>
                </div>
                
                <div class="rule-card">
                    <div class="rule-header">
                        <h3>Idgham</h3>
                        <p>Merging</p>
                    </div>
                    <div class="rule-content">
                        <p>Learn when to merge certain letters when they appear together, with or without nasalization.</p>
                        <div class="arabic-text">مِن رَّبِّهِمْ</div>
                        <p><strong>Example:</strong> Merging ن into ر in مِن رَّبِّهِمْ</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Learning Path Section -->
    <section class="learning-path" id="learning">
        <div class="container">
            <h2 class="section-title">Your Learning Journey</h2>
            
            <div class="path-container">
                <div class="path-step">
                    <div class="step-icon">
                        <i class="fas fa-book-open"></i>
                    </div>
                    <div class="step-content">
                        <h3>Level 1: Foundations</h3>
                        <p>Learn Arabic alphabet pronunciation, basic Tajweed rules, and short Surahs. Focus on Makharij (articulation points) and Sifaat (characteristics of letters).</p>
                        <span class="badge">Beginner</span>
                    </div>
                </div>
                
                <div class="path-step">
                    <div class="step-icon">
                        <i class="fas fa-volume-up"></i>
                    </div>
                    <div class="step-content">
                        <h3>Level 2: Rules Application</h3>
                        <p>Apply Tajweed rules to longer verses. Practice Noon Saakin, Meem Saakin, Madd (elongation), and Qalqalah rules with immediate feedback.</p>
                        <span class="badge">Intermediate</span>
                    </div>
                </div>
                
                <div class="path-step">
                    <div class="step-icon">
                        <i class="fas fa-award"></i>
                    </div>
                    <div class="step-content">
                        <h3>Level 3: Mastery & Fluency</h3>
                        <p>Master advanced rules, rhythm (Waqf), and emotional delivery. Prepare for Ijazah certification with qualified teachers.</p>
                        <span class="badge">Advanced</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section class="content-section" id="about" style="background-color: rgba(10, 92, 54, 0.05);">
        <div class="container">
            <h2 class="section-title">About TajTrainer</h2>
            
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-history"></i>
                    </div>
                    <h3>Traditional + Modern</h3>
                    <p>We combine centuries-old Islamic teaching methods with cutting-edge technology to make Tajweed accessible to everyone.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-globe"></i>
                    </div>
                    <h3>Global Community</h3>
                    <p>Join thousands of students from over 50 countries learning Tajweed together in our supportive online community.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <h3>Learn Anywhere</h3>
                    <p>Access our platform on any device - desktop, tablet, or mobile. Practice Tajweed anytime, anywhere.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-column">
                    <h3>TajTrainer</h3>
                    <p>Master Quranic Tajweed with personalized feedback and expert guidance. Perfect your recitation one rule at a time.</p>
                    <div style="margin-top: 20px; font-size: 1.5rem;">
                        <a href="#" style="color: var(--gold); margin-right: 15px;"><i class="fab fa-facebook"></i></a>
                        <a href="#" style="color: var(--gold); margin-right: 15px;"><i class="fab fa-instagram"></i></a>
                        <a href="#" style="color: var(--gold);"><i class="fab fa-twitter"></i></a>
                    </div>
                </div>
                
                <div class="footer-column">
                    <h3>Quick Links</h3>
                    <ul class="footer-links">
                        <li><a href="#home">Home</a></li>
                        <li><a href="#features">Features</a></li>
                        <li><a href="#rules">Tajweed Rules</a></li>
                        <li><a href="#learning">Learning Path</a></li>
                        <li><a href="#about">About Us</a></li>
                    </ul>
                </div>
                
                <div class="footer-column">
                    <h3>Contact Us</h3>
                    <ul class="footer-links">
                        <li><i class="fas fa-map-marker-alt" style="margin-right: 10px;"></i> TajweedFlow Learning Center</li>
                        <li><i class="fas fa-envelope" style="margin-right: 10px;"></i> support@tajtrainer.com</li>
                        <li><i class="fas fa-clock" style="margin-right: 10px;"></i> Available 24/7</li>
                    </ul>
                </div>
            </div>
            
            <div class="copyright">
                <p>&copy; {{ date('Y') }} TajTrainer. All rights reserved.</p>
                <p>Perfecting Quranic recitation through technology and tradition.</p>
            </div>
        </div>
    </footer>

    <script>
        // Smooth scrolling for navigation links
        document.querySelectorAll('nav a:not(.btn-login):not(.btn-register)').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                const targetId = this.getAttribute('href');
                if (targetId.startsWith('#')) {
                    e.preventDefault();
                    const targetElement = document.querySelector(targetId);
                    if (targetElement) {
                        window.scrollTo({
                            top: targetElement.offsetTop - 80,
                            behavior: 'smooth'
                        });
                    }
                }
            });
        });
        
        // Add scroll animation to feature cards
        document.addEventListener('DOMContentLoaded', function() {
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };
            
            const observer = new IntersectionObserver(function(entries) {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, observerOptions);
            
            // Observe all cards
            document.querySelectorAll('.feature-card, .rule-card, .path-step').forEach(card => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                observer.observe(card);
            });
        });
    </script>
</body>
</html>
