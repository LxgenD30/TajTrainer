<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - TajTrainer</title>
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
            background: linear-gradient(rgba(10, 92, 54, 0.85), rgba(46, 139, 87, 0.9)), url('https://images.unsplash.com/photo-1562774053-701939374585?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1200&q=80');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .modal-container {
            background-color: var(--white);
            border-radius: 20px;
            width: 90%;
            max-width: 500px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            position: relative;
            animation: slideIn 0.3s ease-out;
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .logo-section {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .logo {
            display: inline-flex;
            align-items: center;
            gap: 15px;
            text-decoration: none;
        }
        
        .logo-icon {
            font-size: 2.5rem;
            color: var(--gold);
        }
        
        .logo-text {
            font-family: 'El Messiri', sans-serif;
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary-green);
        }
        
        .logo-text span {
            color: var(--gold);
        }
        
        h1, h2, h3, h4 {
            font-family: 'Reem Kufi Fun', sans-serif;
            color: var(--dark-green);
            font-weight: 600;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--dark-green);
            font-family: 'El Messiri', sans-serif;
        }
        
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
            font-family: 'Amiri', serif;
            transition: border-color 0.3s ease;
        }
        
        .form-group input:focus,
        .form-group select:focus {
            border-color: var(--primary-green);
            outline: none;
            box-shadow: 0 0 0 3px rgba(10, 92, 54, 0.1);
        }
        
        .error-message {
            color: #dc3545;
            font-size: 0.9rem;
            margin-top: 5px;
            font-family: 'El Messiri', sans-serif;
        }
        
        .submit-btn {
            width: 100%;
            padding: 15px;
            background-color: var(--primary-green);
            color: var(--white);
            border: none;
            border-radius: 50px;
            font-size: 1.1rem;
            font-family: 'El Messiri', sans-serif;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
        }
        
        .submit-btn:hover {
            background-color: var(--dark-green);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(10, 92, 54, 0.3);
        }
        
        .form-footer {
            text-align: center;
            margin-top: 20px;
            color: #666;
            font-family: 'El Messiri', sans-serif;
        }
        
        .form-footer a {
            color: var(--primary-green);
            font-weight: 600;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        
        .form-footer a:hover {
            color: var(--light-green);
            text-decoration: underline;
        }
        
        .back-link {
            text-align: center;
            margin-top: 20px;
        }
        
        .back-link a {
            color: var(--primary-green);
            text-decoration: none;
            font-family: 'El Messiri', sans-serif;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }
        
        .back-link a:hover {
            color: var(--light-green);
            transform: translateX(-5px);
        }
        
        @media (max-width: 576px) {
            .modal-container {
                padding: 30px 20px;
            }
            
            .logo-text {
                font-size: 1.5rem;
            }
            
            .logo-icon {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="modal-container">
        <div class="logo-section">
            <a href="{{ url('/') }}" class="logo">
                <i class="fas fa-book-quran logo-icon"></i>
                <div class="logo-text">
                    <span>Taj</span>Trainer
                </div>
            </a>
        </div>
        
        @yield('modal-content')
        
        <div class="back-link">
            <a href="{{ url('/') }}">
                <i class="fas fa-arrow-left"></i>
                Back to Home
            </a>
        </div>
    </div>
</body>
</html>
