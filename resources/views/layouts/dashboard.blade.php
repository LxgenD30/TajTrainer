<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'TajTrainer Dashboard | Master Quranic Tajweed')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Reem+Kufi+Fun:wght@400;500;600;700&family=Amiri:wght@400;700&family=El+Messiri:wght@400;500;600;700&family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        :root {
            --b-bg: #F5F6FC;
            --b-card: #ffffff;
            --b-purple: #4F3FD4;
            --b-purple-dark: #3D2FC0;
            --b-purple-soft: #EDEBFB;
            --b-purple-light: #AEADD0;
            --b-orange: #EC5B29;
            --b-orange-soft: #FDEFEA;
            --b-pink: #D9C6C0;
            --b-muted: #9697AF;
            --b-text: #2D393F;
            --b-tan: #AF9882;
            --b-line: #E6E8F2;
            --sidebar-w: 264px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Cairo', sans-serif;
            color: var(--b-text);
            background: var(--b-bg);
            line-height: 1.6;
            overflow-x: hidden;
            min-height: 100vh;
            font-size: 16px;
        }

        h1, h2, h3, h4 {
            font-family: 'El Messiri', serif;
            color: var(--b-text);
            font-weight: 800;
        }

        a { text-decoration: none; color: inherit; }

        /* ============ APP LAYOUT ============ */
        .app-layout {
            display: flex;
            min-height: 100vh;
        }

        /* ============ SIDEBAR ============ */
        .app-sidebar {
            width: var(--sidebar-w);
            background: var(--b-card);
            border-right: 1px solid var(--b-line);
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            z-index: 200;
            display: flex;
            flex-direction: column;
            padding: 22px 16px;
            box-shadow: 4px 0 24px rgba(79, 63, 212, 0.06);
            transition: transform 0.28s ease;
        }

        .sidebar-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 6px 10px 18px;
            border-bottom: 1px solid var(--b-line);
        }

        .sidebar-logo .logo-ico {
            width: 44px;
            height: 44px;
            border-radius: 14px;
            background: linear-gradient(135deg, var(--b-purple), var(--b-purple-light));
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            box-shadow: 0 8px 18px rgba(79, 63, 212, 0.3);
            flex-shrink: 0;
        }

        .sidebar-logo .logo-txt {
            font-family: 'El Messiri', serif;
            font-size: 1.45rem;
            font-weight: 800;
            color: var(--b-text);
            line-height: 1;
        }

        .sidebar-logo .logo-txt span { color: var(--b-purple); }

        .sidebar-nav {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 6px;
            margin-top: 22px;
            overflow-y: auto;
            overflow-x: hidden;
        }

        .sidebar-nav::-webkit-scrollbar { width: 5px; }
        .sidebar-nav::-webkit-scrollbar-thumb { background: var(--b-purple-light); border-radius: 5px; }

        .nav-section {
            font-size: 0.72rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            color: var(--b-muted);
            padding: 10px 14px 4px;
            margin-top: 8px;
        }
        .nav-section:first-child { margin-top: 0; }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 13px 16px;
            border-radius: 13px;
            color: var(--b-text);
            font-family: 'Cairo', sans-serif;
            font-weight: 700;
            font-size: 1.02rem;
            cursor: pointer;
            border: none;
            background: transparent;
            text-align: left;
            width: 100%;
            transition: all 0.2s ease;
        }

        form.nav-item { padding: 0; }
        form.nav-item button {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 13px 16px;
            width: 100%;
            background: none;
            border: none;
            cursor: pointer;
            color: inherit;
            font-family: inherit;
            font-size: inherit;
            font-weight: inherit;
            text-align: left;
            border-radius: 13px;
            transition: all 0.2s ease;
        }

        .nav-item:hover, form.nav-item button:hover {
            background: var(--b-purple-soft);
            color: var(--b-purple);
        }

        .nav-item.active, form.nav-item button.active {
            background: linear-gradient(135deg, var(--b-purple), #6a5cf0);
            color: #fff;
            box-shadow: 0 8px 18px rgba(79, 63, 212, 0.3);
        }

        .nav-icon {
            width: 24px;
            text-align: center;
            font-size: 1.15rem;
            flex-shrink: 0;
        }

        .nav-label {
            flex: 1;
            font-family: 'Cairo', sans-serif;
            font-weight: 700;
        }

        /* Sidebar footer / profile */
        .sidebar-footer {
            border-top: 1px solid var(--b-line);
            padding-top: 14px;
            margin-top: 14px;
        }

        .sidebar-profile {
            position: relative;
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px;
            border-radius: 14px;
            cursor: pointer;
            transition: background 0.2s ease;
        }

        .sidebar-profile:hover { background: var(--b-bg); }

        .sidebar-avatar {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--b-orange), #f5855f);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 1.05rem;
            overflow: hidden;
            flex-shrink: 0;
        }
        .sidebar-avatar img { width: 100%; height: 100%; object-fit: cover; }

        .sidebar-user-info { flex: 1; min-width: 0; }
        .sidebar-user-info .un { font-weight: 800; font-size: 1rem; color: var(--b-text); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .sidebar-user-info .ur { font-size: 0.8rem; color: var(--b-muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

        .sidebar-profile .chev { color: var(--b-muted); font-size: 0.75rem; }

        .profile-dropdown {
            position: absolute;
            bottom: calc(100% + 8px);
            left: 0;
            right: 0;
            background: #fff;
            border: 1px solid var(--b-line);
            border-radius: 14px;
            box-shadow: 0 14px 34px rgba(79, 63, 212, 0.16);
            overflow: hidden;
            opacity: 0;
            visibility: hidden;
            transform: translateY(8px);
            transition: all 0.22s ease;
            z-index: 300;
        }

        .sidebar-profile.active .profile-dropdown {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .dropdown-item {
            display: flex;
            align-items: center;
            gap: 11px;
            padding: 13px 16px;
            color: var(--b-text);
            font-family: 'Cairo', sans-serif;
            font-weight: 700;
            font-size: 0.98rem;
            text-decoration: none;
            width: 100%;
            border: none;
            background: none;
            cursor: pointer;
            text-align: left;
            transition: background 0.18s ease;
        }

        .dropdown-item:hover { background: var(--b-purple-soft); color: var(--b-purple); }
        .dropdown-item i { width: 20px; color: var(--b-purple); text-align: center; }

        .dropdown-divider { height: 1px; background: var(--b-line); margin: 4px 0; }

        /* ============ MAIN CONTENT ============ */
        .app-main {
            flex: 1;
            margin-left: var(--sidebar-w);
            width: calc(100% - var(--sidebar-w));
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .app-content {
            flex: 1;
            width: 100%;
            padding: 0;
        }

        /* Mobile menu button */
        .mobile-menu-btn {
            display: none;
            position: fixed;
            top: 16px;
            left: 16px;
            z-index: 300;
            width: 46px;
            height: 46px;
            border-radius: 12px;
            border: none;
            background: linear-gradient(135deg, var(--b-purple), var(--b-purple-light));
            color: #fff;
            font-size: 1.2rem;
            cursor: pointer;
            box-shadow: 0 8px 20px rgba(79, 63, 212, 0.3);
        }

        .mobile-menu-btn:hover { transform: scale(1.06); }

        /* Overlay */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(45, 57, 63, 0.4);
            z-index: 190;
            opacity: 0;
            visibility: hidden;
            transition: all 0.25s ease;
        }

        /* ============ ALERTS ============ */
        .alert {
            padding: 16px 22px;
            border-radius: 14px;
            margin: 16px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 1.05rem;
            font-weight: 700;
            box-shadow: 0 6px 18px rgba(0,0,0,0.08);
            border: 1px solid transparent;
            animation: slideDown 0.3s ease-out;
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-14px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .alert i { font-size: 1.3rem; }

        .alert-success {
            background: #e5f7ec;
            border-color: #b7e6c8;
            color: #1f9d55;
        }

        .alert-error {
            background: var(--b-orange-soft);
            border-color: #f7c9b8;
            color: #b3421a;
        }

        /* ============ RESPONSIVE ============ */
        @media (max-width: 992px) {
            .app-sidebar {
                transform: translateX(-100%);
            }

            .app-sidebar.open {
                transform: translateX(0);
                box-shadow: 0 0 0 100vmax rgba(45, 57, 63, 0.4);
            }

            .app-main {
                margin-left: 0;
                width: 100%;
            }

            .mobile-menu-btn { display: flex; align-items: center; justify-content: center; }
            .sidebar-overlay { display: block; }
            .sidebar-overlay.show { opacity: 1; visibility: visible; }
        }

        @yield('extra-styles')
    </style>
</head>
<body>
    <!-- Mobile Menu Button -->
    <button class="mobile-menu-btn" id="mobileMenuBtn">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <div class="app-layout">
        <!-- ============ SIDEBAR ============ -->
        <aside class="app-sidebar" id="appSidebar">
            <a href="{{ route('home') }}" class="sidebar-logo">
                <div class="logo-ico"><i class="fas fa-book-quran"></i></div>
                <div class="logo-txt"><span>Taj</span>Trainer</div>
            </a>

            <nav class="sidebar-nav">
                @hasSection('navigation')
                    @yield('navigation')
                @else
                    @if(Auth::user()->role_id == 3)
                        @include('partials.teacher-nav')
                    @else
                        @include('partials.student-nav')
                    @endif
                @endif
            </nav>

            <div class="sidebar-footer">
                <div class="sidebar-profile" id="userProfile">
                    @if(Auth::user()->profile_picture)
                        <div class="sidebar-avatar">
                            <img src="{{ asset('storage/' . Auth::user()->profile_picture) }}" alt="Profile">
                        </div>
                    @else
                        <div class="sidebar-avatar">{{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 2)) }}</div>
                    @endif
                    <div class="sidebar-user-info">
                        <div class="un">{{ Auth::user()->name ?? 'User' }}</div>
                        <div class="ur">@yield('user-role', 'Student')</div>
                    </div>
                    <i class="fas fa-chevron-up chev"></i>

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
            </div>
        </aside>

        <!-- ============ MAIN CONTENT ============ -->
        <main class="app-main">
            <div class="app-content">
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
    </div>

    <script>
        // Profile dropdown toggle
        const userProfile = document.getElementById('userProfile');
        if (userProfile) {
            userProfile.style.cursor = 'pointer';
            userProfile.addEventListener('click', function (e) {
                e.stopPropagation();
                this.classList.toggle('active');
            });
            document.addEventListener('click', function (e) {
                if (!userProfile.contains(e.target)) {
                    userProfile.classList.remove('active');
                }
            });
        }

        // Mobile sidebar toggle
        const sidebar = document.getElementById('appSidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const menuBtn = document.getElementById('mobileMenuBtn');

        function closeSidebar() {
            if (sidebar) sidebar.classList.remove('open');
            if (overlay) overlay.classList.remove('show');
        }

        if (menuBtn) {
            menuBtn.addEventListener('click', function () {
                if (sidebar) sidebar.classList.toggle('open');
                if (overlay) overlay.classList.toggle('show');
            });
        }
        if (overlay) {
            overlay.addEventListener('click', closeSidebar);
        }

        // Close sidebar after clicking a nav link (mobile)
        document.querySelectorAll('.sidebar-nav .nav-item').forEach(function (item) {
            item.addEventListener('click', function () {
                if (window.innerWidth <= 992) closeSidebar();
            });
        });

        // Add animation to content on scroll
        document.addEventListener('DOMContentLoaded', function () {
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver(function (entries) {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, observerOptions);

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
