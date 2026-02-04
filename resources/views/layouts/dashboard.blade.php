<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'TajTrainer Dashboard | Master Quranic Tajweed')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Reem+Kufi+Fun:wght@400;500;600;700&family=Amiri:wght@400;700&family=El+Messiri:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        :root {
            --primary-green: #0a5c36;
            --light-green: #2e8b57;
            --gold: #d4af37;
            --cream: #f5f5dc;
            --dark-green: #064e32;
            --white: #ffffff;
            --shadow: rgba(10, 92, 54, 0.15);
            --light-cream: #fafaf0;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Amiri', serif;
            color: #333;
            background: linear-gradient(135deg, #1f271b 0%, #2d3e2e 100%);
            line-height: 1.6;
            overflow-x: hidden;
            min-height: 100vh;
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
            max-width: 1600px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        /* Header Styles */
        .dashboard-header {
            background: linear-gradient(to right, var(--primary-green), var(--light-green));
            color: var(--white);
            padding: 20px 0;
            box-shadow: 0 4px 12px var(--shadow);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 30px;
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
        
        .user-profile {
            display: flex;
            align-items: center;
            gap: 15px;
            position: relative;
            cursor: pointer;
            padding: 8px 15px;
            border-radius: 50px;
            transition: background 0.3s ease;
            background: rgba(255, 255, 255, 0.05);
        }
        
        .user-profile:hover {
            background: rgba(255, 255, 255, 0.15);
        }
        
        .user-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--gold), var(--light-green));
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--white);
            font-size: 1.5rem;
            font-weight: bold;
        }
        
        .user-info h3 {
            color: var(--white);
            font-size: 1.2rem;
            margin-bottom: 5px;
        }
        
        .user-info p {
            font-size: 0.9rem;
            opacity: 0.9;
        }
        
        /* Profile Dropdown */
        .profile-dropdown {
            position: absolute;
            top: 100%;
            right: 0;
            margin-top: 10px;
            background: #f5f5f5;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            min-width: 220px;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.3s ease;
            z-index: 1000;
            overflow: hidden;
        }
        
        .user-profile.active .profile-dropdown {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }
        
        .dropdown-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 15px 20px;
            color: var(--dark-green);
            text-decoration: none;
            transition: background 0.2s ease;
            border: none;
            background: none;
            width: 100%;
            text-align: left;
            font-family: 'El Messiri', sans-serif;
            font-size: 1rem;
            cursor: pointer;
        }
        
        .dropdown-item:hover {
            background: rgba(10, 92, 54, 0.05);
        }
        
        .dropdown-item i {
            font-size: 1.2rem;
            color: var(--primary-green);
            width: 25px;
        }
        
        .dropdown-divider {
            height: 1px;
            background: rgba(10, 92, 54, 0.1);
            margin: 5px 0;
        }
        
        /* Innovative Navigation - Inside Header */
        .main-nav {
            flex: 1;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 10px;
            backdrop-filter: blur(10px);
        }
        
        .nav-container {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 5px;
            flex-wrap: wrap;
        }
        
        .nav-item {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: 25px;
            transition: all 0.3s ease;
            position: relative;
            cursor: pointer;
            text-decoration: none;
            color: rgba(255, 255, 255, 0.8);
            font-family: 'El Messiri', sans-serif;
            font-weight: 600;
            font-size: 0.95rem;
        }
        
        .nav-item:hover {
            background: rgba(255, 255, 255, 0.15);
            color: var(--white);
        }
        
        .nav-item.active {
            background: rgba(212, 175, 55, 0.9);
            color: var(--dark-green);
        }
        
        .nav-icon {
            font-size: 1.2rem;
        }
        
        .nav-label {
            font-family: 'El Messiri', sans-serif;
            font-weight: 600;
        }
        
        /* Dashboard Content */
        .dashboard-content {
            padding: 20px 0 60px;
        }
        
        /* Alert Messages */
        .alert {
            padding: 18px 25px;
            border-radius: 15px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 15px;
            font-size: 1.1rem;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
            border: 3px solid;
            animation: slideDown 0.3s ease-out;
        }
        
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .alert i {
            font-size: 1.5rem;
        }
        
        .alert-success {
            background: linear-gradient(135deg, rgba(46, 204, 113, 0.95), rgba(39, 174, 96, 0.95));
            border-color: #27ae60;
            color: white;
        }
        
        .alert-error {
            background: linear-gradient(135deg, rgba(231, 76, 60, 0.95), rgba(192, 57, 43, 0.95));
            border-color: #c0392b;
            color: white;
        }
        
        /* Responsive Design */
        @media (max-width: 992px) {
            .nav-container {
                justify-content: center;
            }
            
            .nav-item {
                min-width: 100px;
            }
        }
        
        @media (max-width: 768px) {
            .header-container {
                flex-direction: column;
                gap: 15px;
            }
            
            .nav-item {
                min-width: 80px;
                padding: 10px 5px;
            }
            
            .nav-icon {
                width: 50px;
                height: 50px;
                font-size: 1.5rem;
            }
            
            .nav-label {
                font-size: 0.9rem;
            }
        }
        
        @media (max-width: 576px) {
            .nav-container {
                flex-direction: column;
                align-items: center;
            }
            
            .nav-item {
                width: 100%;
                max-width: 300px;
                display: flex;
                align-items: center;
                justify-content: flex-start;
                text-align: left;
                padding: 15px;
            }
            
            .nav-icon {
                margin: 0 15px 0 0;
                flex-shrink: 0;
            }
            
            .nav-item.active:before {
                display: none;
            }
            
            .nav-item.active:after {
                content: '';
                position: absolute;
                right: 20px;
                top: 50%;
                transform: translateY(-50%);
                width: 10px;
                height: 10px;
                background-color: var(--gold);
                border-radius: 50%;
            }
        }

        @yield('extra-styles')
    </style>
</head>
<body>
    <!-- Dashboard Header -->
    <header class="dashboard-header">
        <div class="container">
            <div class="header-container">
                <a href="{{ route('home') }}" class="logo">
                    <i class="fas fa-book-quran logo-icon"></i>
                    <div class="logo-text">
                        <span class="logo-font">Taj</span>Trainer
                    </div>
                </a>
                
                <div class="user-profile" id="userProfile">
                    <div class="user-avatar">{{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 2)) }}</div>
                    <div class="user-info">
                        <h3>{{ Auth::user()->name ?? 'User' }}</h3>
                        <p>@yield('user-role', 'Student')</p>
                    </div>
                    <i class="fas fa-chevron-down" style="color: var(--gold); font-size: 0.8rem; margin-left: 5px;"></i>
                    
                    <!-- Profile Dropdown -->
                    <div class="profile-dropdown">
                        @if(Auth::user()->role_id == 3)
                            <a href="{{ route('teachers.show', Auth::id()) }}" class="dropdown-item">
                                <i class="fas fa-user-circle"></i>
                                <span>My Profile</span>
                            </a>
                        @else
                            <a href="{{ route('students.show', Auth::id()) }}" class="dropdown-item">
                                <i class="fas fa-user-circle"></i>
                                <span>My Profile</span>
                            </a>
                        @endif
                        <div class="dropdown-divider"></div>
                        <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                            @csrf
                            <button type="submit" class="dropdown-item">
                                <i class="fas fa-sign-out-alt"></i>
                                <span>Logout</span>
                            </button>
                        </form>
                    </div>
                </div>
                
                <!-- Innovative Navigation Inside Header -->
                <nav class="main-nav">
                    <div class="nav-container">
                        @hasSection('navigation')
                            @yield('navigation')
                        @else
                            @if(Auth::user()->role_id == 3)
                                @include('partials.teacher-nav')
                            @else
                                @include('partials.student-nav')
                            @endif
                        @endif
                    </div>
                </nav>
            </div>
        </div>
    </header>

    <!-- Dashboard Content -->
    <main class="dashboard-content">
        <div class="container">
            @if (session('success'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <div>
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <script>
        // Profile dropdown toggle
        const userProfile = document.getElementById('userProfile');
        if (userProfile) {
            userProfile.style.cursor = 'pointer'; 
            
            userProfile.addEventListener('click', function(e) {
                e.stopPropagation();
                this.classList.toggle('active');
                console.log('Profile clicked, active class is now:', this.classList.contains('active'));
            });

            document.addEventListener('click', function(e) {
                if (!userProfile.contains(e.target)) {
                    userProfile.classList.remove('active');
                }
            });
        }
        
        // Navigation functionality - only apply to main nav items, not dropdown items
        document.querySelectorAll('.main-nav .nav-item').forEach(item => {
            item.addEventListener('click', function(e) {
                // For anchor tags with href, let them navigate normally (don't prevent default)
                // The browser will handle the navigation
                console.log('Nav item clicked:', this.querySelector('.nav-label')?.textContent);
            });
        });
        
        // Add animation to content on scroll
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
            
            // Observe dashboard items
            document.querySelectorAll('.dashboard-item, .stat-card, .class-card, .assignment-card').forEach((item, index) => {
                item.style.opacity = '0';
                item.style.transform = 'translateY(20px)';
                item.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
                item.style.transitionDelay = `${index * 0.05}s`;
                observer.observe(item);
            });
        });
    </script>

    @yield('extra-scripts')
</body>
</html>
