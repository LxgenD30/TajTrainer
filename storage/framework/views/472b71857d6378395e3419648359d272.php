<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

        <title><?php echo $__env->yieldContent('title', 'Dashboard'); ?> - TajTrainer</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=amiri:400,700|cairo:400,600,700&display=swap" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

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
                --sidebar-width: 280px;
                --primary-green: #0a5c36;
                --light-green: #2e8b57;
                --gold: #d4af37;
                --cream: #f5f5dc;
                --white: #ffffff;
                --sky-blue: #3498db;
                --coral: #e74c3c;
                --purple: #9b59b6;
                --turquoise: #1abc9c;
                --sunflower: #f39c12;
                --orange: #e67e22;
            }
            
            body {
                font-family: 'Cairo', sans-serif;
                background: linear-gradient(135deg, #0f1e13 0%, #0a1409 50%, #050a05 100%);
                min-height: 100vh;
                color: #e8e8e8;
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
                background: linear-gradient(135deg, var(--color-dark) 0%, var(--color-brown) 100%);
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
                color: var(--color-gold);
                width: 0%;
                overflow: hidden;
                white-space: nowrap;
                animation: fillText 0.6s ease-out forwards;
            }
            
            .page-transition-spinner {
                width: 40px;
                height: 40px;
                border: 3px solid rgba(227, 216, 136, 0.2);
                border-top: 3px solid var(--color-gold);
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
            
            /* Floating Navigation */
            .floating-nav {
                position: fixed;
                left: 50%;
                transform: translateX(-50%);
                top: 0;
                z-index: 1000;
                width: 95%;
                max-width: 1200px;
                background: rgba(255, 255, 255, 0.98);
                border-radius: 0 0 25px 25px;
                padding: 20px 30px;
                box-shadow: 0 15px 40px rgba(10, 92, 54, 0.35);
                backdrop-filter: blur(15px);
                border: 3px solid #2a2a2a;
                border-top: none;
                opacity: 0;
                visibility: hidden;
                transition: opacity 0.4s ease, visibility 0.4s ease;
            }
            
            .nav-trigger:hover ~ .floating-nav,
            .floating-nav:hover {
                opacity: 1;
                visibility: visible;
            }
            
            /* Navigation trigger area */
            .nav-trigger {
                position: fixed;
                top: 0;
                left: 50%;
                transform: translateX(-50%);
                width: 200px;
                height: 30px;
                z-index: 999;
                cursor: pointer;
                background: rgba(10, 92, 54, 0.3);
                border-radius: 0 0 15px 15px;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: all 0.3s ease;
            }
            
            .nav-trigger::after {
                content: '☰';
                color: rgba(212, 175, 55, 0.8);
                font-size: 1.2rem;
                animation: pulse 2s ease-in-out infinite;
            }
            
            .nav-trigger:hover {
                background: rgba(10, 92, 54, 0.5);
                height: 35px;
            }
            
            @keyframes pulse {
                0%, 100% { opacity: 0.8; }
                50% { opacity: 1; }
            }
            
            .nav-container {
                display: flex;
                justify-content: space-around;
                align-items: stretch;
                gap: 8px;
            }
            
            .nav-item {
                flex: 1;
                min-width: 90px;
                max-width: 120px;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                text-align: center;
                padding: 15px 8px;
                border-radius: 18px;
                transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
                position: relative;
                cursor: pointer;
                background: transparent;
            }
            
            .nav-item.active {
                background: linear-gradient(135deg, var(--primary-green), var(--light-green));
                transform: translateY(-10px);
                box-shadow: 0 8px 20px rgba(10, 92, 54, 0.3);
            }
            
            .nav-item.active:before {
                content: '';
                position: absolute;
                top: -8px;
                left: 50%;
                transform: translateX(-50%);
                width: 20px;
                height: 4px;
                background-color: var(--gold);
                border-radius: 2px;
            }
            
            .nav-icon i {
                display: block;
                line-height: 1;
                font-style: normal;
            }
            
            .nav-item.active .nav-icon {
                background-color: var(--white);
                color: var(--primary-green);
                transform: scale(1.15) rotate(5deg);
                box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
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
                font-size: 0.85rem;
                color: #1a1a1a;
                transition: color 0.3s ease;
                line-height: 1.2;
                margin-top: 4px;
            }
            
            .nav-item.active .nav-label {
                color: var(--white);
                font-weight: 700;
            }
            
            /* Color-coded navigation icons */
            .nav-item[data-target="dashboard"] .nav-icon { color: var(--primary-green); }
            .nav-item[data-target="classes"] .nav-icon { color: var(--sky-blue); }
            .nav-item[data-target="practice"] .nav-icon { color: var(--coral); }
            .nav-item[data-target="progress"] .nav-icon { color: var(--purple); }
            .nav-item[data-target="materials"] .nav-icon { color: var(--turquoise); }
            .nav-item[data-target="profile"] .nav-icon { color: var(--sunflower); }
            .nav-item[data-target="logout"] .nav-icon { color: var(--orange); }
            
            @media (max-width: 1200px) {
                .floating-nav {
                    max-width: 700px;
                }
            }
            
            @media (max-width: 992px) {
                .layout-container {
                    padding-top: 30px;
                }
                
                .main-content {
                    padding: 30px 40px;
                }
                
                .floating-nav {
                    width: 96%;
                    padding: 15px 20px;
                }
                
                .nav-item {
                    min-width: 75px;
                    max-width: 100px;
                    padding: 12px 6px;
                }
                
                .nav-icon {
                    width: 48px;
                    height: 48px;
                    font-size: 1.4rem;
                    margin-bottom: 8px;
                }
                
                .nav-label {
                    font-size: 0.75rem;
                }
            }
            
            @media (max-width: 768px) {
                .layout-container {
                    padding-top: 25px;
                }
                
                .main-content {
                    padding: 20px 25px;
                }
                
                .floating-nav {
                    padding: 12px 15px;
                }
                
                .nav-container {
                    justify-content: space-around;
                    gap: 5px;
                }
                
                .nav-item {
                    min-width: 65px;
                    max-width: 85px;
                    padding: 10px 4px;
                }
                
                .nav-icon {
                    width: 42px;
                    height: 42px;
                    font-size: 1.3rem;
                    margin-bottom: 6px;
                }
                
                .nav-label {
                    font-size: 0.7rem;
                }
            }
            
            @media (max-width: 576px) {
                .layout-container {
                    padding-top: 20px;
                }
                
                .main-content {
                    padding: 15px 20px;
                }
                
                .floating-nav {
                    border-radius: 0 0 18px 18px;
                    padding: 10px 12px;
                }
                
                .nav-container {
                    overflow-x: auto;
                    justify-content: flex-start;
                    padding-bottom: 5px;
                    gap: 6px;
                }
                
                .nav-item {
                    min-width: 60px;
                    max-width: 75px;
                    flex: 0 0 auto;
                    padding: 8px 4px;
                }
                
                .nav-icon {
                    width: 38px;
                    height: 38px;
                    font-size: 1.2rem;
                }
                
                .nav-label {
                    font-size: 0.65rem;
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
                    repeating-linear-gradient(45deg, transparent, transparent 35px, var(--color-gold) 35px, var(--color-gold) 70px),
                    repeating-linear-gradient(-45deg, transparent, transparent 35px, var(--color-gold) 35px, var(--color-gold) 70px);
                pointer-events: none;
                z-index: 0;
            }
            
            /* Decorative Stars */
            .decorative-star {
                position: fixed;
                color: var(--color-gold);
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
                justify-content: center;
                min-height: 100vh;
                position: relative;
                z-index: 1;
                padding-top: 40px;
            }
            
            /* Sidebar */
            .sidebar {
                width: var(--sidebar-width);
                background: rgba(255, 255, 255, 0.95);
                backdrop-filter: blur(10px);
                border-right: 3px solid var(--color-dark-green);
                padding: 30px 0;
                position: fixed;
                height: 100vh;
                overflow-y: auto;
                box-shadow: 5px 0 20px rgba(0, 0, 0, 0.3);
                transition: transform 0.3s ease;
            }
            
            .sidebar-logo {
                padding: 0 25px 30px;
                border-bottom: 2px solid rgba(77, 139, 49, 0.3);
                margin-bottom: 30px;
            }
            
            .logo {
                font-family: 'Amiri', serif;
                font-size: 2rem;
                font-weight: 700;
                color: var(--color-gold);
                text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
                display: flex;
                align-items: center;
                gap: 12px;
                text-decoration: none;
            }
            
            .logo-icon {
                width: 45px;
                height: 45px;
                background: var(--color-dark-green);
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 1.5rem;
                color: var(--color-gold);
                box-shadow: 0 4px 15px rgba(77, 139, 49, 0.4);
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
                color: var(--color-light-green);
                text-decoration: none;
                border-radius: 12px;
                transition: all 0.3s ease;
                font-weight: 500;
                font-size: 1rem;
            }
            
            .nav-link:hover {
                background: rgba(77, 139, 49, 0.2);
                color: var(--color-gold);
                transform: translateX(5px);
            }
            
            .nav-link.active {
                background: var(--color-dark-green);
                color: var(--color-gold);
                box-shadow: 0 4px 15px rgba(77, 139, 49, 0.3);
            }
            
            .nav-icon {
                font-size: 1.3rem;
                width: 24px;
                text-align: center;
            }
            
            /* User Profile Section in Sidebar */
            .sidebar-user {
                padding: 20px 25px;
                border-top: 2px solid rgba(77, 139, 49, 0.3);
                margin-top: 30px;
            }
            
            .logout-form {
                margin: 0;
            }
            
            .btn-logout {
                width: 100%;
                padding: 12px;
                background: transparent;
                color: var(--color-light-green);
                border: 2px solid rgba(197, 48, 48, 0.5);
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
                background: var(--color-dark-green);
                color: var(--color-light-green);
                border: 2px solid var(--color-dark-green);
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
                background: var(--color-gold);
                border-color: var(--color-gold);
                color: var(--color-dark);
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(227, 216, 136, 0.3);
            }
            
            .btn-secondary {
                padding: 12px 24px;
                background: rgba(31, 39, 27, 0.5);
                color: var(--color-light-green);
                border: 2px solid var(--color-dark-green);
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
                border-color: var(--color-gold);
                color: var(--color-gold);
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(227, 216, 136, 0.2);
            }
            
            /* Main Content */
            .main-content {
                flex: 1;
                margin: 0 auto;
                max-width: 1600px;
                width: 100%;
                padding: 40px 60px;
                min-height: 100vh;
            }
            
            /* Top Bar */
            .top-bar {
                background: rgba(255, 255, 255, 0.95);
                backdrop-filter: blur(10px);
                border-radius: 20px;
                border: 2px solid var(--color-dark-green);
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
                background: rgba(255, 255, 255, 0.8);
                border-radius: 15px;
                border: 2px solid var(--color-dark-green);
            }
            
            .profile-picture {
                width: 50px;
                height: 50px;
                background: var(--color-dark-green);
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 1.5rem;
                color: var(--color-gold);
                flex-shrink: 0;
                box-shadow: 0 4px 15px rgba(77, 139, 49, 0.4);
            }
            
            .profile-info {
                display: flex;
                flex-direction: column;
            }
            
            .profile-name {
                color: #1a3a1a;
                font-weight: 700;
                font-size: 1rem;
                white-space: nowrap;
            }
            
            .profile-role {
                color: #2d5a2d;
                font-size: 0.85rem;
                opacity: 0.9;
            }
            
            .page-title {
                font-family: 'Amiri', serif;
                font-size: 2.2rem;
                color: #1a3a1a;
                text-shadow: none;
                margin: 0;
                line-height: 1.2;
            }
            
            .page-subtitle {
                color: #1a1a1a;
                font-size: 1rem;
                opacity: 0.9;
                margin-top: 5px;
                line-height: 1.4;
            }
            
            /* Content Card */
            .content-card {
                background: rgba(255, 255, 255, 0.98);
                backdrop-filter: blur(15px);
                border-radius: 25px;
                border: 3px solid var(--color-dark-green);
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
                color: var(--color-gold);
                margin: 0;
            }
            
            .card-body {
                color: var(--color-light-green);
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
                background: var(--color-dark-green);
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 3rem;
                margin: 0 auto 25px;
                color: var(--color-gold);
                box-shadow: 0 8px 30px rgba(77, 139, 49, 0.5);
            }
            
            .welcome-title {
                font-family: 'Amiri', serif;
                font-size: 2.5rem;
                color: var(--color-gold);
                margin-bottom: 20px;
            }
            
            .welcome-message {
                color: var(--color-light-green);
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
                background: var(--color-dark-green);
                color: var(--color-gold);
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
                background: var(--color-dark-green);
                border-radius: 3px;
            }
            
            .sidebar::-webkit-scrollbar-thumb:hover {
                background: var(--color-gold);
            }
        </style>
        
        <?php echo $__env->yieldContent('styles'); ?>
        
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Navigation is now handled by floating nav for students
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
        
        <!-- Navigation Trigger Area (Student Only) -->
        <?php if(Auth::check() && Auth::user()->role_id == 2): ?>
        <div class="nav-trigger" title="Hover to show navigation"></div>
        <?php endif; ?>
        
        <div class="layout-container">
            <!-- Main Content -->
            <main class="main-content">
                <!-- Top Bar -->
                <div class="top-bar">
                    <div class="top-bar-left">
                        <h1 class="page-title"><?php echo $__env->yieldContent('page-title', 'Dashboard'); ?></h1>
                        <p class="page-subtitle"><?php echo $__env->yieldContent('page-subtitle', 'Welcome to your learning dashboard'); ?></p>
                    </div>
                    <div class="top-bar-profile">
                        <div class="profile-picture">👤</div>
                        <div class="profile-info">
                            <div class="profile-name">
                                <?php if(Auth::check()): ?>
                                    <?php if(Auth::user()->role_id == 2): ?>
                                        <?php echo e(App\Models\Student::find(Auth::id())->name ?? 'Student'); ?>

                                    <?php elseif(Auth::user()->role_id == 3): ?>
                                        <?php echo e(App\Models\Teacher::find(Auth::id())->name ?? 'Teacher'); ?>

                                    <?php else: ?>
                                        <?php echo e(Auth::user()->name ?? 'User'); ?>

                                    <?php endif; ?>
                                <?php else: ?>
                                    Guest
                                <?php endif; ?>
                            </div>
                            <div class="profile-role"><?php echo e(Auth::check() && Auth::user()->role ? Auth::user()->role->user_type : 'Guest'); ?></div>
                        </div>
                    </div>
                </div>
                
                <!-- Page Content -->
                <?php echo $__env->yieldContent('content'); ?>
            </main>
        </div>
                <!-- Floating Navigation (Student Only) -->
        <?php if(Auth::check() && Auth::user()->role_id == 2): ?>
        <nav class="floating-nav">
            <div class="nav-container">
                <div class="nav-item <?php echo e(request()->is('students') ? 'active' : ''); ?>" data-target="dashboard" data-url="<?php echo e(route('students.index')); ?>">
                    <div class="nav-icon">
                        <i class="fas fa-home"></i>
                    </div>
                    <div class="nav-label">Dashboard</div>
                </div>
                
                <div class="nav-item <?php echo e(request()->is('student/classes*') ? 'active' : ''); ?>" data-target="classes" data-url="<?php echo e(route('student.classes')); ?>">
                    <div class="nav-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="nav-label">My Classes</div>
                </div>
                
                <div class="nav-item <?php echo e(request()->is('student/practice*') ? 'active' : ''); ?>" data-target="practice" data-url="<?php echo e(route('student.practice')); ?>">
                    <div class="nav-icon">
                        <i class="fas fa-microphone-alt"></i>
                    </div>
                    <div class="nav-label">Practice</div>
                </div>
                
                <div class="nav-item <?php echo e(request()->is('student/progress*') ? 'active' : ''); ?>" data-target="progress" data-url="<?php echo e(route('student.progress')); ?>">
                    <div class="nav-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="nav-label">My Progress</div>
                </div>
                
                <div class="nav-item <?php echo e(request()->is('student/materials*') ? 'active' : ''); ?>" data-target="materials" data-url="<?php echo e(route('student.materials')); ?>">
                    <div class="nav-icon">
                        <i class="fas fa-book-open"></i>
                    </div>
                    <div class="nav-label">Materials</div>
                </div>
                
                <div class="nav-item <?php echo e(request()->is('students/'.Auth::id()) ? 'active' : ''); ?>" data-target="profile" data-url="<?php echo e(route('students.show', Auth::id())); ?>">
                    <div class="nav-icon">
                        <i class="fas fa-user-circle"></i>
                    </div>
                    <div class="nav-label">Profile</div>
                </div>
                
                <div class="nav-item" data-target="logout">
                    <div class="nav-icon">
                        <i class="fas fa-sign-out-alt"></i>
                    </div>
                    <div class="nav-label">Logout</div>
                </div>
            </div>
        </nav>
        
        <!-- Hidden Logout Form -->
        <form id="logout-form" method="POST" action="<?php echo e(route('logout')); ?>" style="display: none;">
            <?php echo csrf_field(); ?>
        </form>
        <?php endif; ?>
                <!-- Page Transition Script -->
        <script>
            // Smooth page transitions
            document.addEventListener('DOMContentLoaded', function() {
                // Floating Navigation functionality
                document.querySelectorAll('.nav-item').forEach(item => {
                    item.addEventListener('click', function() {
                        const target = this.getAttribute('data-target');
                        const url = this.getAttribute('data-url');
                        
                        // Handle logout separately
                        if (target === 'logout') {
                            if (confirm('Are you sure you want to logout?')) {
                                document.getElementById('logout-form').submit();
                            }
                            return;
                        }
                        
                        // Navigate to URL with transition
                        if (url) {
                            document.body.classList.add('page-transitioning');
                            setTimeout(() => {
                                window.location.href = url;
                            }, 200);
                        }
                    });
                });
                
                // Add smooth transition to all internal links
                const internalLinks = document.querySelectorAll('a[href^="/"], a[href^="<?php echo e(url("/")); ?>"]');
                
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
        
        <?php echo $__env->yieldContent('scripts'); ?>
    </body>
</html><?php /**PATH C:\laragon\www\tajtrainerV2\resources\views/layouts/template.blade.php ENDPATH**/ ?>