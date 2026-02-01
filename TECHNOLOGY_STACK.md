# TajTrainer - Software & Technology Stack

## 📋 Project Summary
**TajTrainer** is a Laravel-based Quran Tajweed learning platform with AI-powered pronunciation analysis using Python MFCC (Mel-Frequency Cepstral Coefficients) for real-time feedback.

---

## 🛠️ Core Technologies

### Backend
- **Laravel 11.46.2** - PHP web framework
- **PHP 8.3.28** - Server-side language
- **MySQL** - Database (via Laragon)
- **Composer** - PHP dependency manager

### Frontend
- **Blade Templates** - Laravel templating engine
- **Tailwind CSS** - Utility-first CSS framework
- **Vite** - Frontend build tool
- **JavaScript (Vanilla)** - Interactive features
- **CSS3 Animations** - Page transitions, hover effects

### Python AI Analysis
- **Python 3.13** - Machine learning runtime
- **librosa 0.11.0** - Audio analysis library
- **NumPy** - Numerical computing
- **SciPy** - Scientific computing
- **FFmpeg N-122378** - Audio processing backend

### External Services
- **Telegram Bot API** - Bot integration (longman/telegram-bot 0.83.1)
- **Al-Quran Cloud API** - Quran verses, translations, audio

### Development Tools
- **Laragon** - Local development environment (Windows)
- **LocalTunnel** - Local webhook testing
- **Git** - Version control
- **NPM** - Node package manager

---

## 📦 Key Dependencies

### PHP Packages (composer.json)
```json
{
  "laravel/framework": "^11.0",
  "laravel/ui": "^4.5",
  "longman/telegram-bot": "^0.83.1",
  "guzzlehttp/guzzle": "^7.10"
}
```

### Python Packages (requirements.txt)
```
librosa==0.11.0
numpy
scipy
audioread
```

### Node Packages (package.json)
```json
{
  "tailwindcss": "^3.4.0",
  "vite": "^5.0",
  "laravel-vite-plugin": "^1.0"
}
```

---

## 🎯 Key Features

### 1. **MFCC-Based Tajweed Analysis**
- **File**: `python/tajweed_analyzer.py` (328 lines)
- **Technology**: librosa MFCC with 13 coefficients
- **Rules Detected**:
  - Madd (elongation) - ≥0.3s threshold
  - Noon Sakin/Tanween (Idhar, Idgham, Iqlab, Ikhfa)
- **Analysis**: Zero-crossing rate (0.05-0.4), spectral contrast, RMS energy

### 2. **Practice System**
- Real-time audio recording (WebRTC MediaRecorder)
- WebM audio format
- PHP `proc_open()` execution of Python analyzer
- Detailed error tracking with timestamps

### 3. **Progress Tracking**
- Practice history with scores
- Error history and patterns
- Dashboard with Chart.js visualizations
- Streak tracking

### 4. **Telegram Bot Integration**
- Commands: `/start`, `/link`, `/progress`, `/stats`, `/errors`, `/help`
- Webhook: `/telegram/webhook`
- Database: `telegram_users` table

### 5. **User Roles**
- **Teachers** (role_id: 3) - Classroom management, grading
- **Students** (role_id: 2) - Practice, progress tracking

### 6. **UI/UX Enhancements**
- Smooth page transitions (200ms fade)
- Loading animation (TajTrainer text fill)
- Hoverable error details
- Islamic-themed design (gold/green palette)

---

## 📁 Project Structure

```
tajtrainer/
├── app/
│   ├── Http/Controllers/
│   │   ├── StudentController.php (1444 lines)
│   │   ├── TeacherController.php
│   │   ├── TelegramBotController.php (380 lines)
│   │   └── MaterialController.php
│   └── Models/
│       ├── User.php
│       ├── Student.php
│       ├── Teacher.php
│       ├── Classroom.php
│       ├── Material.php
│       └── TelegramUser.php
├── database/
│   └── migrations/
│       ├── *_create_users_table.php
│       ├── *_create_practice_history_table.php
│       ├── *_create_error_history_table.php
│       └── *_create_telegram_users_table.php
├── python/
│   └── tajweed_analyzer.py (328 lines)
├── resources/
│   └── views/
│       ├── layouts/template.blade.php (829 lines)
│       ├── practice/index.blade.php (870 lines)
│       └── students/progress.blade.php
└── routes/
    └── web.php (188 lines)
```

---

## 🔧 Configuration Files

### Environment (.env)
```env
APP_NAME=TajTrainer
DB_CONNECTION=mysql
DB_DATABASE=tajtrainer_db

TELEGRAM_BOT_TOKEN=your_token
TELEGRAM_BOT_USERNAME=your_bot_name
```

### Python Analyzer Settings
- **FFmpeg Path**: `C:\ffmpeg\ffmpeg-master-latest-win64-gpl\bin`
- **Sample Rate**: 22050 Hz
- **Hop Length**: 512 samples
- **MFCC Coefficients**: 13

---

## 📊 Database Schema

### Core Tables
- `users` - User accounts (int unsigned id)
- `role` - User roles (student, teacher)
- `students` - Student profiles
- `teachers` - Teacher profiles
- `classrooms` - Class management
- `materials` - Learning resources
- `practice_history` - Practice sessions with scores
- `error_history` - Tajweed error logs
- `telegram_users` - Telegram account linking

---

## 🚀 Deployment Requirements

### Server
- PHP ≥ 8.3
- MySQL ≥ 8.0
- Python ≥ 3.10
- FFmpeg installed
- 512MB+ RAM
- SSL certificate (for Telegram webhook)

### Production Setup
1. Set `APP_ENV=production` in .env
2. Run `php artisan config:cache`
3. Set Telegram webhook to production URL
4. Configure FFmpeg path in Python script
5. Install Python dependencies: `pip install -r requirements.txt`

---

## 📝 License & Credits

- **Framework**: Laravel (MIT License)
- **Audio Analysis**: librosa (ISC License)
- **Quran Data**: Al-Quran Cloud API
- **Icons**: Unicode emoji
- **Fonts**: Google Fonts (Amiri, Cairo)

---

## 📧 Support

For technical issues or questions about the software stack, refer to:
- Laravel: https://laravel.com/docs
- librosa: https://librosa.org/doc
- Telegram Bot API: https://core.telegram.org/bots/api
