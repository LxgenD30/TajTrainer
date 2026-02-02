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
            background-color: var(--light-cream);
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
            max-width: 1400px;
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
        
        /* Innovative Navigation */
        .main-nav {
            background-color: var(--white);
            border-radius: 20px;
            padding: 15px;
            margin: 30px auto;
            max-width: 1200px;
            box-shadow: 0 8px 25px var(--shadow);
            position: relative;
            overflow: hidden;
        }
        
        .nav-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .nav-item {
            flex: 1;
            min-width: 120px;
            text-align: center;
            padding: 15px 10px;
            border-radius: 15px;
            transition: all 0.3s ease;
            position: relative;
            cursor: pointer;
            margin: 5px;
            text-decoration: none;
            display: block;
        }
        
        .nav-item.active {
            background-color: rgba(10, 92, 54, 0.1);
            transform: translateY(-5px);
        }
        
        .nav-item.active:before {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 30px;
            height: 4px;
            background-color: var(--gold);
            border-radius: 2px;
        }
        
        .nav-icon {
            width: 60px;
            height: 60px;
            margin: 0 auto 10px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            color: var(--primary-green);
            background-color: rgba(10, 92, 54, 0.08);
            transition: all 0.3s ease;
        }
        
        .nav-item.active .nav-icon {
            background-color: var(--primary-green);
            color: var(--white);
            transform: scale(1.1);
        }
        
        .nav-item:hover:not(.active) {
            background-color: rgba(10, 92, 54, 0.05);
        }
        
        .nav-item:hover:not(.active) .nav-icon {
            transform: translateY(-5px);
        }
        
        .nav-label {
            font-family: 'El Messiri', sans-serif;
            font-weight: 600;
            font-size: 1rem;
            color: var(--dark-green);
        }
        
        /* Dashboard Content */
        .dashboard-content {
            padding: 20px 0 60px;
        }
        
        /* Alert Messages */
        .alert {
            padding: 15px 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .alert-success {
            background-color: rgba(46, 139, 87, 0.1);
            border-left: 4px solid var(--light-green);
            color: var(--dark-green);
        }
        
        .alert-error {
            background-color: rgba(220, 53, 69, 0.1);
            border-left: 4px solid #dc3545;
            color: #721c24;
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
        <div class="container header-container">
            <a href="{{ route('home') }}" class="logo">
                <i class="fas fa-book-quran logo-icon"></i>
                <div class="logo-text">
                    <span class="logo-font">Taj</span>Trainer
                </div>
            </a>
            
            <div class="user-profile">
                <div class="user-avatar">{{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 2)) }}</div>
                <div class="user-info">
                    <h3>{{ Auth::user()->name ?? 'User' }}</h3>
                    <p>@yield('user-role', 'Student')</p>
                </div>
            </div>
        </div>
    </header>

    <!-- Innovative Navigation -->
    <nav class="main-nav">
        <div class="container nav-container">
            @yield('navigation')
        </div>
    </nav>

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
        // Navigation functionality
        document.querySelectorAll('.nav-item').forEach(item => {
            item.addEventListener('click', function(e) {
                // If it's a form (logout), don't prevent default
                if (this.tagName === 'FORM' || this.querySelector('form')) {
                    return;
                }
                
                // For anchor tags with href, let them navigate normally
                if (this.tagName === 'A' && this.getAttribute('href')) {
                    return;
                }
                
                // Remove active class from all nav items
                document.querySelectorAll('.nav-item').forEach(nav => {
                    nav.classList.remove('active');
                });
                
                // Add active class to clicked item
                this.classList.add('active');
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

        @yield('extra-scripts')
    </script>
</body>
</html>
