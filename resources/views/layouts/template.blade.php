<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', 'Dashboard') - TajTrainer</title>

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
                --primary-green: #0a5c36;
                --light-green: #2e8b57;
                --gold: #d4af37;
                --cream: #f5f5dc;
                --dark-green: #064e32;
                --white: #ffffff;
                --shadow: rgba(10, 92, 54, 0.15);
                --sidebar-width: 280px;
            }
            
            body {
                font-family: 'Cairo', sans-serif;
                background: linear-gradient(135deg, #f5f5dc 0%, #e8dcc4 50%, #d4c5a9 100%);
                min-height: 100vh;
                color: #2c3e1f;
                overflow-x: hidden;
            }
            
            /* Page Transition Animations */
            body.page-transitioning {
                pointer-events: none;
            }
            
            .page-transition-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: linear-gradient(135deg, var(--dark-green) 0%, var(--color-brown) 100%);
                opacity: 0;
                pointer-events: none;
                z-index: 9999;
                transition: opacity 0.2s ease;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            
            body.page-transitioning .page-transition-overlay {
                opacity: 1;
                pointer-events: all;
            }
            
            .page-transition-content {
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 20px;
                opacity: 0;
                transition: opacity 0.2s ease;
            }
            
            body.page-transitioning .page-transition-content {
                opacity: 1;
            }
            
            .page-transition-text {
                font-family: 'Amiri', serif;
                font-size: 3rem;
                font-weight: 700;
                position: relative;
                color: rgba(227, 216, 136, 0.3);
                text-shadow: 0 0 1px rgba(227, 216, 136, 0.5);
            }
            
            .page-transition-text::before {
                content: 'TajTrainer';
                position: absolute;
                left: 0;
                top: 0;
                color: var(--gold);
                width: 0%;
                overflow: hidden;
                white-space: nowrap;
                animation: fillText 0.6s ease-out forwards;
            }
            
            .page-transition-spinner {
                width: 40px;
                height: 40px;
                border: 3px solid rgba(227, 216, 136, 0.2);
                border-top: 3px solid var(--gold);
                border-radius: 50%;
                animation: spin 0.8s linear infinite;
            }
            
            @keyframes fillText {
                0% { width: 0%; }
                100% { width: 100%; }
            }
            
            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }
            
            .main-content {
                animation: fadeInUp 0.5s ease-out;
            }
            
            @keyframes fadeInUp {
                from {
                    opacity: 0;
                    transform: translateY(20px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
            
            /* Smooth transitions for all interactive elements */
            a, button, .nav-link, .btn, input, select, textarea {
                transition: all 0.3s ease;
            }
            
            /* Card entrance animations */
            .practice-card, .material-card, .assignment-card, .classroom-card,
            .stats-grid > *, .chart-card, .stat-card {
                animation: fadeInUp 0.6s ease-out backwards;
            }
            
            .practice-card:nth-child(1), .material-card:nth-child(1) { animation-delay: 0.1s; }
            .practice-card:nth-child(2), .material-card:nth-child(2) { animation-delay: 0.2s; }
            .practice-card:nth-child(3), .material-card:nth-child(3) { animation-delay: 0.3s; }
            .practice-card:nth-child(4), .material-card:nth-child(4) { animation-delay: 0.4s; }
            
            .islamic-pattern {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                opacity: 0.05;
                background-image: 
                    repeating-linear-gradient(45deg, transparent, transparent 35px, var(--gold) 35px, var(--gold) 70px),
                    repeating-linear-gradient(-45deg, transparent, transparent 35px, var(--gold) 35px, var(--gold) 70px);
                pointer-events: none;
                z-index: 0;
            }
            
            /* Decorative Stars */
            .decorative-star {
                position: fixed;
                color: var(--gold);
                opacity: 0.2;
                animation: twinkle 3s infinite;
                z-index: 0;
            }
            
            @keyframes twinkle {
                0%, 100% { opacity: 0.2; }
                50% { opacity: 0.4; }
            }
            
            .star-1 { top: 15%; left: 5%; font-size: 1.5rem; }
            .star-2 { top: 30%; right: 8%; font-size: 1.2rem; animation-delay: 1s; }
            .star-3 { bottom: 25%; left: 10%; font-size: 1.3rem; animation-delay: 2s; }
            .star-4 { top: 70%; right: 15%; font-size: 1.6rem; animation-delay: 0.5s; }
            
            /* Layout Container */
            .layout-container {
                display: flex;
                min-height: 100vh;
                position: relative;
                z-index: 1;
            }
            
            /* Sidebar */
            .sidebar {
                width: var(--sidebar-width);
                background: linear-gradient(180deg, var(--primary-green) 0%, var(--dark-green) 100%);
                backdrop-filter: blur(10px);
                border-right: 3px solid var(--gold);
                padding: 30px 0;
                position: fixed;
                height: 100vh;
                overflow-y: auto;
                box-shadow: 5px 0 30px rgba(0, 0, 0, 0.5);
                transition: transform 0.3s ease;
            }
            
            .sidebar-logo {
                padding: 0 25px 30px;
                border-bottom: 2px solid rgba(227, 216, 136, 0.3);
                margin-bottom: 30px;
            }
            
            .logo {
                font-family: 'Amiri', serif;
                font-size: 2rem;
                font-weight: 700;
                color: var(--gold);
                text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
                display: flex;
                align-items: center;
                gap: 12px;
                text-decoration: none;
            }
            
            .logo-icon {
                width: 45px;
                height: 45px;
                background: var(--gold);
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 1.5rem;
                color: var(--dark-green);
                box-shadow: 0 4px 15px rgba(212, 175, 55, 0.4);
                flex-shrink: 0;
            }
            
            /* Navigation Menu */
            .nav-menu {
                list-style: none;
                padding: 0 15px;
            }
            
            .nav-item {
                margin-bottom: 8px;
            }
            
            .nav-link {
                display: flex;
                align-items: center;
                gap: 12px;
                padding: 14px 18px;
                color: rgba(226, 241, 175, 0.8);
                text-decoration: none;
                border-radius: 12px;
                transition: all 0.3s ease;
                font-weight: 500;
                font-size: 1rem;
            }
            
            .nav-link:hover {
                background: rgba(227, 216, 136, 0.15);
                color: var(--gold);
                transform: translateX(5px);
            }
            
            .nav-link.active {
                background: var(--gold);
                color: var(--dark-green);
                box-shadow: 0 4px 15px rgba(227, 216, 136, 0.4);
            }
            
            .nav-icon {
                font-size: 1.3rem;
                width: 24px;
                text-align: center;
            }
            
            /* User Profile Section in Sidebar */
            .sidebar-user {
                padding: 20px 25px;
                border-top: 2px solid rgba(227, 216, 136, 0.3);
                margin-top: 30px;
            }
            
            .logout-form {
                margin: 0;
            }
            
            .btn-logout {
                width: 100%;
                padding: 12px;
                background: rgba(197, 48, 48, 0.2);
                color: #ffa8a8;
                border: 2px solid rgba(197, 48, 48, 0.6);
                border-radius: 10px;
                font-size: 0.95rem;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.3s ease;
                font-family: 'Cairo', sans-serif;
            }
            
            .btn-logout:hover {
                background: #c53030;
                border-color: #c53030;
                color: white;
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(197, 48, 48, 0.3);
            }
            
            .btn-primary {
                padding: 12px 24px;
                background: var(--primary-green);
                color: var(--light-green);
                border: 2px solid var(--primary-green);
                border-radius: 10px;
                font-size: 0.95rem;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.3s ease;
                font-family: 'Cairo', sans-serif;
                text-decoration: none;
                display: inline-block;
                text-align: center;
            }
            
            .btn-primary:hover {
                background: var(--gold);
                border-color: var(--gold);
                color: var(--dark-green);
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(227, 216, 136, 0.3);
            }
            
            .btn-secondary {
                padding: 12px 24px;
                background: rgba(31, 39, 27, 0.5);
                color: var(--light-green);
                border: 2px solid var(--primary-green);
                border-radius: 10px;
                font-size: 0.95rem;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.3s ease;
                font-family: 'Cairo', sans-serif;
                text-decoration: none;
                display: inline-block;
                text-align: center;
            }
            
            .btn-secondary:hover {
                background: rgba(31, 39, 27, 0.8);
                border-color: var(--gold);
                color: var(--gold);
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(227, 216, 136, 0.2);
            }
            
            /* Main Content */
            .main-content {
                flex: 1;
                margin-left: var(--sidebar-width);
                padding: 40px;
                min-height: 100vh;
            }
            
            /* Top Bar */
            .top-bar {
                background: linear-gradient(135deg, var(--primary-green) 0%, var(--light-green) 100%);
                backdrop-filter: blur(10px);
                border-radius: 20px;
                border: 2px solid var(--gold);
                padding: 20px 30px;
                margin-bottom: 40px;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
                display: flex;
                justify-content: space-between;
                align-items: center;
                gap: 20px;
            }
            
            .top-bar-left {
                flex: 1;
            }
            
            .top-bar-profile {
                display: flex;
                align-items: center;
                gap: 15px;
                padding: 10px 20px;
                background: rgba(0, 0, 0, 0.3);
                border-radius: 15px;
                border: 2px solid var(--gold);
            }
            
            .profile-picture {
                width: 50px;
                height: 50px;
                background: var(--gold);
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 1.5rem;
                color: var(--dark-green);
                flex-shrink: 0;
                box-shadow: 0 4px 15px rgba(212, 175, 55, 0.4);
            }
            
            .profile-info {
                display: flex;
                flex-direction: column;
            }
            
            .profile-name {
                color: var(--gold);
                font-weight: 700;
                font-size: 1rem;
                white-space: nowrap;
            }
            
            .profile-role {
                color: var(--white);
                font-size: 0.85rem;
                opacity: 0.9;
            }
            
            .page-title {
                font-family: 'Amiri', serif;
                font-size: 2.2rem;
                color: var(--gold);
                text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
                margin: 0;
                line-height: 1.2;
            }
            
            .page-subtitle {
                color: var(--light-green);
                font-size: 1rem;
                opacity: 0.9;
                margin-top: 5px;
                line-height: 1.4;
            }
            
            /* Content Card */
            .content-card {
                background: linear-gradient(135deg, rgba(10, 92, 54, 0.95) 0%, rgba(6, 78, 50, 0.95) 100%);
                backdrop-filter: blur(15px);
                border-radius: 25px;
                border: 3px solid var(--gold);
                box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4);
                padding: 40px;
                margin-bottom: 30px;
            }
            
            .card-header {
                border-bottom: 2px solid rgba(77, 139, 49, 0.3);
                padding-bottom: 20px;
                margin-bottom: 30px;
            }
            
            .card-title {
                font-family: 'Amiri', serif;
                font-size: 1.8rem;
                color: var(--gold);
                margin: 0;
            }
            
            .card-body {
                color: var(--light-green);
                line-height: 1.8;
                font-size: 1.1rem;
            }
            
            /* Welcome Card Specific */
            .welcome-card {
                text-align: center;
            }
            
            .welcome-icon {
                width: 100px;
                height: 100px;
                background: var(--primary-green);
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 3rem;
                margin: 0 auto 25px;
                color: var(--gold);
                box-shadow: 0 8px 30px rgba(77, 139, 49, 0.5);
            }
            
            .welcome-title {
                font-family: 'Amiri', serif;
                font-size: 2.5rem;
                color: var(--gold);
                margin-bottom: 20px;
            }
            
            .welcome-message {
                color: var(--light-green);
                font-size: 1.2rem;
                line-height: 1.8;
                max-width: 700px;
                margin: 0 auto;
            }
            
            /* Mobile Menu Toggle */
            .mobile-menu-toggle {
                display: none;
                position: fixed;
                top: 20px;
                left: 20px;
                z-index: 1000;
                background: var(--primary-green);
                color: var(--gold);
                border: none;
                width: 50px;
                height: 50px;
                border-radius: 50%;
                font-size: 1.5rem;
                cursor: pointer;
                box-shadow: 0 4px 15px rgba(77, 139, 49, 0.4);
                transition: all 0.3s ease;
            }
            
            .mobile-menu-toggle:hover {
                transform: scale(1.1);
            }
            
            /* Responsive */
            @media (max-width: 992px) {
                :root {
                    --sidebar-width: 0px;
                }
                
                .sidebar {
                    transform: translateX(-100%);
                    z-index: 999;
                }
                
                .sidebar.show {
                    transform: translateX(0);
                }
                
                .main-content {
                    margin-left: 0;
                    padding: 30px 20px;
                }
                
                .mobile-menu-toggle {
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }
                
                .page-title {
                    font-size: 1.8rem;
                }
                
                .content-card {
                    padding: 30px 20px;
                }
                
                .top-bar {
                    flex-direction: column;
                    align-items: flex-start;
                }
                
                .top-bar-profile {
                    width: 100%;
                    justify-content: center;
                }
            }
            
            @media (max-width: 576px) {
                .top-bar {
                    padding: 15px 20px;
                }
                
                .page-title {
                    font-size: 1.5rem;
                }
                
                .welcome-icon {
                    width: 80px;
                    height: 80px;
                    font-size: 2.5rem;
                }
                
                .welcome-title {
                    font-size: 2rem;
                }
                
                .welcome-message {
                    font-size: 1rem;
                }
            }
            
            /* Custom Scrollbar */
            .sidebar::-webkit-scrollbar {
                width: 6px;
            }
            
            .sidebar::-webkit-scrollbar-track {
                background: rgba(31, 39, 27, 0.5);
            }
            
            .sidebar::-webkit-scrollbar-thumb {
                background: var(--primary-green);
                border-radius: 3px;
            }
            
            .sidebar::-webkit-scrollbar-thumb:hover {
                background: var(--gold);
            }
        </style>
        
        @yield('styles')
        
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Mobile menu toggle
                const toggleBtn = document.querySelector('.mobile-menu-toggle');
                const sidebar = document.querySelector('.sidebar');
                const mainContent = document.querySelector('.main-content');
                
                if (toggleBtn) {
                    toggleBtn.addEventListener('click', function() {
                        sidebar.classList.toggle('show');
                    });
                    
                    // Close sidebar when clicking outside
                    mainContent.addEventListener('click', function() {
                        if (sidebar.classList.contains('show')) {
                            sidebar.classList.remove('show');
                        }
                    });
                }
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
        
        <!-- Page Transition Overlay -->
        <div class="page-transition-overlay">
            <div class="page-transition-content">
                <div class="page-transition-text">TajTrainer</div>
                <div class="page-transition-spinner"></div>
            </div>
        </div>
        
        <!-- Mobile Menu Toggle -->
        <button class="mobile-menu-toggle" aria-label="Toggle Menu">☰</button>
        
        <div class="layout-container">
            <!-- Sidebar -->
            <aside class="sidebar">
                <div class="sidebar-logo">
                    <div class="logo">
                        <div class="logo-icon">☪</div>
                        <span>TajTrainer</span>
                    </div>
                </div>
                
                <nav>
                    <ul class="nav-menu">
                        @if(Auth::check() && Auth::user()->role_id == 3)
                            {{-- Teacher Menu --}}
                            <li class="nav-item">
                                <a href="{{ route('teachers.index') }}" class="nav-link {{ request()->is('teachers*') ? 'active' : '' }}">
                                    <span class="nav-icon">🏠</span>
                                    <span>Dashboard</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('classroom.index') }}" class="nav-link {{ request()->is('classroom*') ? 'active' : '' }}">
                                    <span class="nav-icon">🏫</span>
                                    <span>My Classrooms</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('materials.index') }}" class="nav-link {{ request()->is('Materials*') ? 'active' : '' }}">
                                    <span class="nav-icon">📚</span>
                                    <span>Learning Materials</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('students.list') }}" class="nav-link {{ request()->is('students-list*') ? 'active' : '' }}">
                                    <span class="nav-icon">👥</span>
                                    <span>My Students</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('teachers.show', Auth::id()) }}" class="nav-link {{ request()->is('teachers/'.Auth::id()) ? 'active' : '' }}">
                                    <span class="nav-icon">👁️</span>
                                    <span>View My Profile</span>
                                </a>
                            </li>

                        @elseif(Auth::check() && Auth::user()->role_id == 2)
                            {{-- Student Menu --}}
                            <li class="nav-item">
                                <a href="{{ route('students.index') }}" class="nav-link {{ request()->is('students') ? 'active' : '' }}">
                                    <span class="nav-icon">🏠</span>
                                    <span>Dashboard</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('student.classes') }}" class="nav-link {{ request()->is('student/classes*') ? 'active' : '' }}">
                                    <span class="nav-icon">🏫</span>
                                    <span>My Classes</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('student.practice') }}" class="nav-link {{ request()->is('student/practice*') ? 'active' : '' }}">
                                    <span class="nav-icon">🎯</span>
                                    <span>Practice</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('student.progress') }}" class="nav-link {{ request()->is('student/progress*') ? 'active' : '' }}">
                                    <span class="nav-icon">📊</span>
                                    <span>My Progress</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('student.materials') }}" class="nav-link {{ request()->is('student/materials*') ? 'active' : '' }}">
                                    <span class="nav-icon">📚</span>
                                    <span>Learning Materials</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('students.show', Auth::id()) }}" class="nav-link {{ request()->is('students/'.Auth::id()) ? 'active' : '' }}">
                                    <span class="nav-icon">👁️</span>
                                    <span>View My Profile</span>
                                </a>
                            </li>

                        @else
                            {{-- Default Menu --}}
                            <li class="nav-item">
                                <a href="{{ route('home') }}" class="nav-link {{ request()->is('home') ? 'active' : '' }}">
                                    <span class="nav-icon">🏠</span>
                                    <span>Dashboard</span>
                                </a>
                            </li>
                        @endif
                    </ul>
                </nav>
                
                <div class="sidebar-user">
                    <form method="POST" action="{{ route('logout') }}" class="logout-form">
                        @csrf
                        <button type="submit" class="btn-logout">Logout</button>
                    </form>
                </div>
            </aside>
            
            <!-- Main Content -->
            <main class="main-content">
                <!-- Top Bar -->
                <div class="top-bar">
                    <div class="top-bar-left">
                        <h1 class="page-title">@yield('page-title', 'Dashboard')</h1>
                        <p class="page-subtitle">@yield('page-subtitle', 'Welcome to your learning dashboard')</p>
                    </div>
                    <div class="top-bar-profile">
                        <div class="profile-picture">👤</div>
                        <div class="profile-info">
                            <div class="profile-name">
                                @if(Auth::check())
                                    @if(Auth::user()->role_id == 2)
                                        {{ App\Models\Student::find(Auth::id())->name ?? 'Student' }}
                                    @elseif(Auth::user()->role_id == 3)
                                        {{ App\Models\Teacher::find(Auth::id())->name ?? 'Teacher' }}
                                    @else
                                        {{ Auth::user()->name ?? 'User' }}
                                    @endif
                                @else
                                    Guest
                                @endif
                            </div>
                            <div class="profile-role">{{ Auth::check() && Auth::user()->role ? Auth::user()->role->user_type : 'Guest' }}</div>
                        </div>
                    </div>
                </div>
                
                <!-- Page Content -->
                @yield('content')
            </main>
        </div>
        
        <!-- Page Transition Script -->
        <script>
            // Smooth page transitions
            document.addEventListener('DOMContentLoaded', function() {
                // Add smooth transition to all internal links
                const internalLinks = document.querySelectorAll('a[href^="/"], a[href^="{{ url("/") }}"]');
                
                internalLinks.forEach(link => {
                    // Skip links that open in new tab, are downloads, or have specific classes to skip
                    if (link.target === '_blank' || link.hasAttribute('download') || link.classList.contains('no-transition')) {
                        return;
                    }
                    
                    link.addEventListener('click', function(e) {
                        const href = this.getAttribute('href');
                        
                        // Skip if it's a form submit button or has data attributes
                        if (!href || href === '#' || href.startsWith('javascript:')) {
                            return;
                        }
                        
                        e.preventDefault();
                        
                        // Add transitioning class
                        document.body.classList.add('page-transitioning');
                        
                        // Navigate after animation
                        setTimeout(() => {
                            window.location.href = href;
                        }, 200);
                    });
                });
                
                // Remove transition class on page load
                setTimeout(() => {
                    document.body.classList.remove('page-transitioning');
                }, 100);
            });
            
            // Handle browser back/forward buttons
            window.addEventListener('pageshow', function(event) {
                if (event.persisted) {
                    document.body.classList.remove('page-transitioning');
                }
            });
        </script>
        
        @yield('scripts')
    </body>
</html>
