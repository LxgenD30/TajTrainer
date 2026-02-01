# TajTrainer Deployment Guide

## Database Migrations

All migrations have been cleaned and organized for production deployment. They will execute in the following order:

### Migration Order

1. **0001_01_01_000000_create_base_tables.php**
   - Creates: `role`, `users`, `password_reset_tokens`, `sessions`, `cache`, `cache_locks`, `jobs`, `job_batches`, `failed_jobs`
   - Foundation tables for Laravel and authentication

2. **0001_01_01_000001_create_teachers_and_students_tables.php**
   - Creates: `teachers`, `students`
   - User role-specific tables

3. **0001_01_01_000002_create_classrooms_and_materials_tables.php**
   - Creates: `classrooms`, `materials`, `enrollment`
   - Core learning management tables

4. **0001_01_01_000003_create_assignments_and_submissions_tables.php**
   - Creates: `assignments`, `assignment_submissions`, `scores`
   - Assignment and grading system

5. **0001_01_01_000004_create_practice_sessions_table.php**
   - Creates: `practice_sessions`
   - Student practice tracking

6. **0001_01_01_000005_add_profile_fields_to_users_table.php**
   - Adds: `current_level`, `biodata` to `users` table
   - Extended user profile fields

7. **0001_01_01_000006_create_tajweed_error_logs_table.php**
   - Creates: `tajweed_error_logs`
   - MFCC analysis error tracking with indexes

8. **0001_01_01_000007_create_telegram_users_table.php**
   - Creates: `telegram_users`
   - Telegram bot integration

## Fresh Installation

### Local Development Setup

```bash
# 1. Clone repository
git clone <repository-url>
cd tajtrainer

# 2. Install dependencies
composer install
npm install

# 3. Environment setup
cp .env.example .env
php artisan key:generate

# 4. Configure .env
# Set database credentials
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tajtrainer
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Set Telegram bot (if using)
TELEGRAM_BOT_TOKEN=your_bot_token
TELEGRAM_BOT_USERNAME=YourBotUsername

# 5. Run migrations
php artisan migrate

# 6. Seed database with roles and test data
php artisan db:seed

# 7. Create symbolic link for storage
php artisan storage:link

# 8. Build frontend assets
npm run build

# 9. Configure FFmpeg path (for MFCC analysis)
# Set FFMPEG_PATH in .env if not using system FFmpeg
# Windows example: FFMPEG_PATH=C:\ffmpeg\bin
# Linux: Usually /usr/bin (no need to set if using apt package)

# 10. Configure Python path (for Tajweed analysis)
# Set PYTHON_PATH in .env if Python is not in system PATH
# Linux example: PYTHON_PATH=/usr/bin/python3
# Windows example: PYTHON_PATH=C:\Python310\python.exe

# 11. Install Python dependencies
cd python
pip install -r requirements.txt
cd ..

# 12. Set up permissions (Linux/Mac)
chmod -R 775 storage bootstrap/cache
```

### Hosting Deployment (Shared Hosting / VPS)

#### Option 1: Shared Hosting (cPanel)

**Prerequisites:**
- PHP 8.3+ with required extensions
- MySQL/MariaDB database
- SSH access (for Python/FFmpeg installation)
- SSL certificate

**Steps:**

1. **Prepare Your Files Locally**
   ```bash
   # Install production dependencies only
   composer install --optimize-autoloader --no-dev
   
   # Build assets for production
   npm run build
   
   # Remove node_modules to reduce upload size
   rm -rf node_modules
   ```

2. **Upload Files to Server**
   - Upload all files EXCEPT `.env` to your hosting (use FTP/SFTP)
   - Upload to a directory ABOVE public_html (e.g., `/home/username/tajtrainer`)
   - Move contents of `/public` folder to `public_html` or `www`

3. **Update public/index.php**
   Edit `public_html/index.php` to point to your Laravel installation:
   ```php
   // Change this line
   require __DIR__.'/../bootstrap/app.php';
   
   // To (adjust path based on your structure)
   require __DIR__.'/../tajtrainer/bootstrap/app.php';
   ```

4. **Create Database via cPanel**
   - Go to cPanel → MySQL Databases
   - Create a new database
   - Create a new user with strong password
   - Add user to database with ALL PRIVILEGES

5. **Configure .env on Server**
   - Create `.env` file in your Laravel root (not in public_html)
   - Copy from `.env.example` and configure:
   ```env
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://yourdomain.com
   
   DB_CONNECTION=mysql
   DB_HOST=localhost
   DB_DATABASE=your_database_name
   DB_USERNAME=your_database_user
   DB_PASSWORD=your_database_password
   
   TELEGRAM_BOT_TOKEN=your_token
   TELEGRAM_BOT_USERNAME=YourBot
   
   # Python and FFmpeg paths (if not in system PATH)
   PYTHON_PATH=/usr/bin/python3
   FFMPEG_PATH=/usr/bin
   ```

6. **Run Installation Commands via SSH**
   ```bash
   cd /home/username/tajtrainer
   
   # Generate application key
   php artisan key:generate
   
   # Run migrations
   php artisan migrate --force
   
   # Seed database
   php artisan db:seed
   
   # Create storage link
   php artisan storage:link
   
   # Cache configuration
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   
   # Set permissions
   chmod -R 775 storage bootstrap/cache
   ```

7. **Install Python Dependencies**
   ```bash
   cd /home/username/tajtrainer/python
   pip3 install -r requirements.txt --user
   ```

8. **Install FFmpeg** (if not installed)
   ```bash
   # For Ubuntu/Debian
   sudo apt-get update
   sudo apt-get install ffmpeg
   
   # For CentOS/RHEL
   sudo yum install ffmpeg
   
   # Check installation
   ffmpeg -version
   ```

9. **Configure Telegram Webhook**
   Visit in browser:
   ```
   https://api.telegram.org/bot<YOUR_TOKEN>/setWebhook?url=https://yourdomain.com/telegram/webhook
   ```

10. **Test Your Application**
    - Visit https://yourdomain.com
    - Register a test account
    - Try submitting a practice session
    - Test Telegram bot commands

#### Option 2: VPS/Dedicated Server (Ubuntu/Debian)

**Prerequisites:**
- Ubuntu 20.04+ or Debian 11+
- Root or sudo access
- Domain pointing to server IP
- Basic Linux knowledge

**Complete Server Setup:**

```bash
# 1. Update system
sudo apt-get update && sudo apt-get upgrade -y

# 2. Install Nginx
sudo apt-get install nginx -y

# 3. Install PHP 8.3 and extensions
sudo apt-get install software-properties-common -y
sudo add-apt-repository ppa:ondrej/php -y
sudo apt-get update
sudo apt-get install php8.3 php8.3-fpm php8.3-mysql php8.3-mbstring \
  php8.3-xml php8.3-bcmath php8.3-curl php8.3-zip php8.3-gd -y

# 4. Install MySQL
sudo apt-get install mysql-server -y
sudo mysql_secure_installation

# 5. Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# 6. Install Node.js and NPM
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt-get install nodejs -y

# 7. Install Python and pip
sudo apt-get install python3 python3-pip -y

# 8. Install FFmpeg
sudo apt-get install ffmpeg -y

# 9. Install Git
sudo apt-get install git -y

# 10. Create database
sudo mysql -u root -p
```
```sql
CREATE DATABASE tajtrainer CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'tajtrainer'@'localhost' IDENTIFIED BY 'strong_password_here';
GRANT ALL PRIVILEGES ON tajtrainer.* TO 'tajtrainer'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

```bash
# 11. Clone and setup application
cd /var/www
sudo git clone <your-repo-url> tajtrainer
cd tajtrainer
sudo chown -R www-data:www-data /var/www/tajtrainer

# 12. Install dependencies
composer install --optimize-autoloader --no-dev
npm install
npm run build

# 13. Configure environment
sudo cp .env.example .env
sudo nano .env
# Configure all settings (DB, Telegram, Python, FFmpeg)

# 14. Setup Laravel
php artisan key:generate
php artisan migrate --force
php artisan db:seed
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 15. Install Python dependencies
cd python
pip3 install -r requirements.txt
cd ..

# 16. Set permissions
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

**Configure Nginx:**

```bash
sudo nano /etc/nginx/sites-available/tajtrainer
```

Add this configuration:
```nginx
server {
    listen 80;
    server_name yourdomain.com www.yourdomain.com;
    root /var/www/tajtrainer/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    client_max_body_size 50M;
}
```

```bash
# Enable site
sudo ln -s /etc/nginx/sites-available/tajtrainer /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx

# Install SSL with Let's Encrypt
sudo apt-get install certbot python3-certbot-nginx -y
sudo certbot --nginx -d yourdomain.com -d www.yourdomain.com

# Setup auto-renewal
sudo systemctl enable certbot.timer
```

**Setup Queue Worker (Optional but Recommended):**

```bash
sudo nano /etc/supervisor/conf.d/tajtrainer-worker.conf
```

Add:
```ini
[program:tajtrainer-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/tajtrainer/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/tajtrainer/storage/logs/worker.log
stopwaitsecs=3600
```

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start tajtrainer-worker:*
```

**Setup Laravel Scheduler:**

```bash
sudo crontab -e -u www-data
```

Add:
```
* * * * * cd /var/www/tajtrainer && php artisan schedule:run >> /dev/null 2>&1
```

## User Roles

The system supports 2 user roles:
- **Students** (role_id: 2) - Practice, assignments, progress tracking
- **Teachers** (role_id: 3) - Classroom management, student monitoring, grading

**Note:** Self-learner role has been removed from production.

## Default Seeded Accounts

After running `php artisan db:seed`:
- **Student**: student@gmail.com / 12345678
- **Teacher**: teacher@gmail.com / 12345678

## Production Checklist

### Environment Configuration
- [ ] Set `APP_ENV=production` in .env
- [ ] Set `APP_DEBUG=false` in .env
- [ ] Configure proper `APP_URL` in .env (must be HTTPS)
- [ ] Set `DB_CONNECTION=mysql` (not sqlite)
- [ ] Configure database credentials (DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD)
- [ ] Set strong `APP_KEY` (run `php artisan key:generate`)
- [ ] Configure `SESSION_DRIVER=database` (recommended for hosting)
- [ ] Configure `CACHE_STORE=database` or `file`
- [ ] Set `QUEUE_CONNECTION=database` or `redis`

### Python & FFmpeg Configuration
- [ ] Install Python 3.10+ on server
- [ ] Install pip and Python dependencies: `pip install -r python/requirements.txt`
- [ ] Set `PYTHON_PATH` in .env if Python not in system PATH
- [ ] Install FFmpeg on server (apt-get install ffmpeg for Ubuntu/Debian)
- [ ] Set `FFMPEG_PATH` in .env if FFmpeg not in standard location
- [ ] Test Python script manually: `python3 python/tajweed_analyzer.py test.mp3`

### Telegram Bot Setup
- [ ] Create bot with BotFather and get token
- [ ] Set `TELEGRAM_BOT_TOKEN` in .env
- [ ] Set `TELEGRAM_BOT_USERNAME` in .env
- [ ] Set webhook URL (must be HTTPS): `https://yourdomain.com/telegram/webhook`
- [ ] Verify webhook with getWebhookInfo

### Laravel Optimization
- [ ] Run `composer install --optimize-autoloader --no-dev`
- [ ] Run `php artisan config:cache`
- [ ] Run `php artisan route:cache`
- [ ] Run `php artisan view:cache`
- [ ] Run `php artisan optimize`

### Database & Storage
- [ ] Run `php artisan migrate --force` on production database
- [ ] Run `php artisan db:seed` (if needed for roles/test data)
- [ ] Run `php artisan storage:link`
- [ ] Set permissions: `chmod -R 775 storage bootstrap/cache`
- [ ] Set ownership: `chown -R www-data:www-data storage bootstrap/cache` (Linux)
- [ ] Verify storage directories exist: `storage/app/public/submissions`, `storage/app/public/practice_recordings`

### Security & SSL
- [ ] Install SSL certificate (Let's Encrypt recommended)
- [ ] Configure HTTPS redirect in web server
- [ ] Set secure session settings in `.env`:
   ```
   SESSION_SECURE_COOKIE=true
   SESSION_SAME_SITE=lax
   ```
- [ ] Review `.gitignore` - ensure `.env` is not committed
- [ ] Set strong database password
- [ ] Disable directory listing in web server config

### Web Server Configuration
- [ ] Point document root to `/public` directory
- [ ] Configure URL rewriting for Laravel
- [ ] Set PHP version to 8.3 or higher
- [ ] Enable required PHP extensions (see Technology Requirements)
- [ ] Set PHP memory_limit to at least 256M
- [ ] Set max_upload_filesize and post_max_size appropriately (e.g., 50M)
- [ ] Configure CORS if needed for API

### Cron Jobs & Background Tasks
- [ ] Set up Laravel scheduler (add to crontab):
   ```bash
   * * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
   ```
- [ ] Set up queue worker (use supervisor or systemd):
   ```bash
   php artisan queue:work --tries=3
   ```

### Testing & Monitoring
- [ ] Test student registration and login
- [ ] Test practice session submission and analysis
- [ ] Test assignment creation and submission
- [ ] Test Telegram bot commands
- [ ] Test file uploads (audio, materials)
- [ ] Set up error monitoring (Sentry, Bugsnag, etc.)
- [ ] Set up uptime monitoring
- [ ] Configure backup schedule
- [ ] Test email functionality (password reset, notifications)
- [ ] Monitor storage/logs/laravel.log for errors

### Performance Optimization
- [ ] Enable OPcache for PHP
- [ ] Configure Redis/Memcached for caching (optional but recommended)
- [ ] Enable Gzip compression in web server
- [ ] Optimize images and assets
- [ ] Consider CDN for static assets
- [ ] Run `npm run build` for production assets

## Migration Rollback

If you need to rollback migrations:
```bash
# Rollback last batch
php artisan migrate:rollback

# Rollback all
php artisan migrate:reset

# Fresh migration (warning: deletes all data)
php artisan migrate:fresh --seed
```

## Database Structure

### Core Tables (8 total)
- `role` - User roles (Student, Teacher)
- `users` - Main user accounts
- `students` - Student-specific data
- `teachers` - Teacher-specific data
- `classrooms` - Virtual classrooms
- `materials` - Learning materials
- `enrollment` - Student-classroom relationships
- `assignments` - Teacher assignments
- `assignment_submissions` - Student submissions
- `scores` - Grading records
- `practice_sessions` - Practice tracking
- `tajweed_error_logs` - MFCC analysis results
- `telegram_users` - Bot integration

## Technology Requirements

**Server:**
- PHP ≥ 8.3
- MySQL ≥ 8.0
- Python ≥ 3.10
- FFmpeg (latest)
- Node.js ≥ 18
- Composer
- NPM/Yarn

**PHP Extensions:**
- BCMath
- Ctype
- Fileinfo
- JSON
- Mbstring
- OpenSSL
- PDO
- Tokenizer
- XML
- cURL

**Python Packages:**
- librosa 0.11.0
- numpy
- scipy
- soundfile

## Support

For issues or questions, refer to TECHNOLOGY_STACK.md for complete system documentation.

---

## Telegram Bot Integration Guide

This comprehensive guide will walk you through setting up the Telegram bot for TajTrainer from scratch. Follow each step carefully.

### Prerequisites

Before you begin, ensure you have:
- A Telegram account
- Access to your hosting server (cPanel, SSH, etc.)
- Your Laravel application fully installed and running
- SSL certificate configured (HTTPS is required for webhooks)

### Step 1: Create Your Telegram Bot

1. **Open Telegram** on your phone or desktop
2. **Search for "BotFather"** (official bot creation tool) or click: https://t.me/botfather
3. **Start a conversation** with BotFather by clicking "Start"
4. **Create a new bot** by sending the command:
   ```
   /newbot
   ```
5. **Choose a name** for your bot (e.g., "TajTrainer Assistant")
   - This is the display name users will see
6. **Choose a username** for your bot (must end in 'bot')
   - Example: `TajTrainerBot` or `MyTajweedBot`
   - This username must be unique across all Telegram bots
7. **Save your bot token** - BotFather will send you a message like:
   ```
   Use this token to access the HTTP API:
   1234567890:ABCdefGHIjklMNOpqrsTUVwxyz1234567
   ```
   - **IMPORTANT**: Keep this token secret! It's like a password for your bot

### Step 2: Configure Bot Settings (Optional but Recommended)

While still chatting with BotFather:

1. **Set a description** (shown when users first open your bot):
   ```
   /setdescription
   ```
   Select your bot, then send:
   ```
   Welcome to TajTrainer! I can help you track your Tajweed progress, view statistics, and get personalized feedback on your Quran recitation practice.
   ```

2. **Set an about text** (shown in bot's profile):
   ```
   /setabouttext
   ```
   Select your bot, then send:
   ```
   TajTrainer Bot - Your personal Tajweed learning assistant
   ```

3. **Set a profile picture** (optional):
   ```
   /setuserpic
   ```
   Upload an image for your bot's profile

4. **Set bot commands** (creates a helpful command menu):
   ```
   /setcommands
   ```
   Select your bot, then send:
   ```
   start - Start the bot and see welcome message
   link - Link your TajTrainer account
   progress - View your learning progress
   stats - View detailed statistics
   errors - View common Tajweed errors
   help - Show help and available commands
   ```

### Step 3: Configure Environment Variables

1. **Open your `.env` file** on your server
2. **Add the bot configuration**:
   ```env
   TELEGRAM_BOT_TOKEN=1234567890:ABCdefGHIjklMNOpqrsTUVwxyz1234567
   TELEGRAM_BOT_USERNAME=TajTrainerBot
   ```
   - Replace `1234567890:ABCdefGHIjklMNOpqrsTUVwxyz1234567` with your actual token
   - Replace `TajTrainerBot` with your bot's username (without @)

3. **Save the file** and clear Laravel's cache:
   ```bash
   php artisan config:clear
   php artisan cache:clear
   ```

### Step 4: Set Up the Webhook

The webhook tells Telegram where to send updates when users interact with your bot.

#### Method A: Using Browser (Easiest)

1. **Open your web browser**
2. **Visit this URL** (replace with your actual values):
   ```
   https://api.telegram.org/bot<YOUR_BOT_TOKEN>/setWebhook?url=https://yourdomain.com/telegram/webhook
   ```
   
   **Example**:
   ```
   https://api.telegram.org/bot1234567890:ABCdefGHIjklMNOpqrsTUVwxyz1234567/setWebhook?url=https://tajtrainer.com/telegram/webhook
   ```

3. **Check the response** - You should see:
   ```json
   {
     "ok": true,
     "result": true,
     "description": "Webhook was set"
   }
   ```

#### Method B: Using cURL (Command Line)

If you have SSH access:

```bash
curl -X POST "https://api.telegram.org/bot<YOUR_BOT_TOKEN>/setWebhook" \
  -d "url=https://yourdomain.com/telegram/webhook"
```

#### Method C: Using PHP Script

Create a temporary file `set_webhook.php` in your project root:

```php
<?php
$botToken = '1234567890:ABCdefGHIjklMNOpqrsTUVwxyz1234567';
$webhookUrl = 'https://yourdomain.com/telegram/webhook';

$url = "https://api.telegram.org/bot{$botToken}/setWebhook?url={$webhookUrl}";
$response = file_get_contents($url);
echo $response;
?>
```

Run it once by visiting: `https://yourdomain.com/set_webhook.php`

**Then DELETE this file for security!**

### Step 5: Verify Webhook Setup

1. **Check webhook info** by visiting in your browser:
   ```
   https://api.telegram.org/bot<YOUR_BOT_TOKEN>/getWebhookInfo
   ```

2. **Look for these fields**:
   ```json
   {
     "ok": true,
     "result": {
       "url": "https://yourdomain.com/telegram/webhook",
       "has_custom_certificate": false,
       "pending_update_count": 0,
       "last_error_date": 0
     }
   }
   ```

3. **Important checks**:
   - `url` should match your webhook URL
   - `pending_update_count` should be 0
   - No `last_error_date` or `last_error_message` fields

### Step 6: Test Your Bot

1. **Open Telegram** and search for your bot by username (e.g., `@TajTrainerBot`)
2. **Click "Start"** or send `/start`
3. **You should receive a welcome message** explaining how to link your account
4. **Try the `/help` command** to see all available commands

### Step 7: Link Your TajTrainer Account

To link your web account with the Telegram bot:

1. **Log in to TajTrainer** web application with your student account
2. **In Telegram**, send the command:
   ```
   /link YOUR_EMAIL
   ```
   Example:
   ```
   /link student@gmail.com
   ```

3. **The bot will confirm** your account is linked
4. **Now you can use**:
   - `/progress` - View your learning progress
   - `/stats` - See detailed statistics
   - `/errors` - Check common mistakes

### Troubleshooting

#### Bot doesn't respond

1. **Check webhook is set correctly**:
   ```
   https://api.telegram.org/bot<YOUR_BOT_TOKEN>/getWebhookInfo
   ```

2. **Check Laravel logs**:
   ```bash
   tail -f storage/logs/laravel.log
   ```

3. **Verify the webhook endpoint** is accessible:
   ```
   curl https://yourdomain.com/telegram/webhook
   ```
   (Should return method not allowed, which is fine)

4. **Ensure HTTPS is working** - HTTP won't work for webhooks!

#### "Unauthorized" or "Invalid token" errors

- Double-check your bot token in `.env`
- Make sure there are no extra spaces
- Clear config cache: `php artisan config:clear`

#### Bot receives messages but doesn't respond

1. **Check TelegramBotController.php** exists in `app/Http/Controllers/`
2. **Check route** exists in `routes/web.php`:
   ```php
   Route::post('/telegram/webhook', [TelegramBotController::class, 'webhook']);
   ```
3. **Check logs** for PHP errors:
   ```bash
   tail -f storage/logs/laravel.log
   ```

#### "/link command not working"

- Make sure you're using a valid email registered in TajTrainer
- Check database connection is working
- Verify users table has the email you're trying to link

### Security Best Practices

1. **Never share your bot token** - treat it like a password
2. **Don't commit `.env` to Git** - it should be in `.gitignore`
3. **Use HTTPS only** - Telegram requires SSL for webhooks
4. **Validate webhook requests** - the TelegramBotController already does this
5. **Regularly check logs** for suspicious activity
6. **Regenerate token if compromised**:
   - Send `/revoke` to BotFather
   - Then create a new token with `/newbot` or `/token`

### Advanced: Removing/Updating Webhook

**Delete webhook** (bot will stop receiving updates):
```
https://api.telegram.org/bot<YOUR_BOT_TOKEN>/deleteWebhook
```

**Update webhook** (change URL):
```
https://api.telegram.org/bot<YOUR_BOT_TOKEN>/setWebhook?url=https://newdomain.com/telegram/webhook
```

### Production Checklist for Telegram Bot

- [ ] Bot token added to `.env` on production server
- [ ] Bot username added to `.env` on production server
- [ ] Config cache cleared: `php artisan config:clear`
- [ ] Webhook set to production URL (must be HTTPS)
- [ ] Webhook verified with getWebhookInfo
- [ ] Test `/start` command works
- [ ] Test `/link` command with real account
- [ ] Test `/progress`, `/stats`, `/errors` commands
- [ ] Check Laravel logs for errors
- [ ] SSL certificate is valid and not expired
- [ ] Routes are accessible (check with curl)
- [ ] Database connection working
- [ ] Storage permissions correct (775)

### Getting Help

If you encounter issues:

1. **Check Laravel logs**: `storage/logs/laravel.log`
2. **Check webhook info**: `/getWebhookInfo` endpoint
3. **Test with Telegram Bot API**: Use online testers
4. **Review Telegram Bot API docs**: https://core.telegram.org/bots/api
5. **Check this project's GitHub issues** (if applicable)

### Useful Telegram Bot API Endpoints

All these URLs follow this pattern: `https://api.telegram.org/bot<YOUR_BOT_TOKEN>/<method>`

- `/getMe` - Get bot information
- `/getUpdates` - Get pending updates (for testing)
- `/setWebhook?url=<URL>` - Set webhook
- `/getWebhookInfo` - Check webhook status
- `/deleteWebhook` - Remove webhook

---

**Congratulations! 🎉** Your Telegram bot is now integrated with TajTrainer!

---

## Common Hosting Issues & Solutions

### Issue 1: "500 Internal Server Error"

**Causes:**
- Incorrect file permissions
- Missing `.env` file
- PHP version too old
- Missing PHP extensions

**Solutions:**
```bash
# Check and fix permissions
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Verify .env exists and is configured
ls -la .env

# Check PHP version (must be 8.3+)
php -v

# Check for missing extensions
php -m | grep -E 'mbstring|xml|pdo|curl|zip|gd|bcmath'
```

### Issue 2: "419 Page Expired" on Form Submission

**Cause:** Session not working properly

**Solution:**
```bash
# In .env, use database for sessions
SESSION_DRIVER=database
SESSION_LIFETIME=120

# Clear and recache
php artisan config:clear
php artisan cache:clear
php artisan config:cache
```

### Issue 3: Python Script Not Working

**Symptoms:**
- Practice submissions fail
- "Python returned empty output" error

**Solutions:**
```bash
# Test Python manually
cd /path/to/tajtrainer
python3 python/tajweed_analyzer.py storage/app/public/practice_recordings/test.mp3

# Check Python dependencies
pip3 list | grep -E 'librosa|numpy|scipy'

# Install missing dependencies
cd python
pip3 install -r requirements.txt

# Set PYTHON_PATH in .env
PYTHON_PATH=/usr/bin/python3

# Check FFmpeg is installed
ffmpeg -version

# If not, install it
sudo apt-get install ffmpeg
```

### Issue 4: File Upload Fails

**Symptoms:**
- Audio files don't upload
- "File too large" error

**Solutions:**
```bash
# Check PHP settings
php -i | grep -E 'upload_max_filesize|post_max_size|memory_limit'

# Edit PHP configuration (location varies)
sudo nano /etc/php/8.3/fpm/php.ini

# Update these values:
upload_max_filesize = 50M
post_max_size = 50M
memory_limit = 256M
max_execution_time = 300

# Restart PHP-FPM
sudo systemctl restart php8.3-fpm
```

### Issue 5: Telegram Bot Not Responding

**Symptoms:**
- Bot shows online but doesn't respond
- Webhook errors in getWebhookInfo

**Solutions:**
```bash
# Check webhook status
curl "https://api.telegram.org/bot<TOKEN>/getWebhookInfo"

# Look for errors in Laravel logs
tail -f storage/logs/laravel.log

# Verify route is accessible
curl -X POST https://yourdomain.com/telegram/webhook

# Delete and reset webhook
curl "https://api.telegram.org/bot<TOKEN>/deleteWebhook"
curl "https://api.telegram.org/bot<TOKEN>/setWebhook?url=https://yourdomain.com/telegram/webhook"

# Clear Laravel cache
php artisan config:clear
php artisan cache:clear
```

### Issue 6: Storage Link Broken

**Symptoms:**
- Uploaded files show 404
- Images don't display

**Solutions:**
```bash
# Remove old symlink
rm public/storage

# Create new symlink
php artisan storage:link

# Verify symlink exists
ls -la public/storage

# Check storage permissions
chmod -R 775 storage
chown -R www-data:www-data storage
```

### Issue 7: Database Connection Failed

**Symptoms:**
- "SQLSTATE[HY000] [2002] Connection refused"
- "Access denied for user"

**Solutions:**
```bash
# Test database connection
mysql -u your_username -p your_database

# In .env, check these settings:
DB_HOST=localhost  # or 127.0.0.1
DB_PORT=3306
DB_DATABASE=tajtrainer
DB_USERNAME=your_username
DB_PASSWORD=your_password

# For socket connection issues, try:
DB_HOST=localhost
DB_SOCKET=/var/run/mysqld/mysqld.sock

# Clear config cache
php artisan config:clear
```

### Issue 8: CSS/JS Not Loading (404)

**Symptoms:**
- Styles broken
- JavaScript not working
- 404 on /build/* files

**Solutions:**
```bash
# Rebuild assets
npm install
npm run build

# Check public/build directory exists
ls -la public/build

# Verify APP_URL in .env matches your domain
APP_URL=https://yourdomain.com

# Clear and recache
php artisan config:clear
php artisan view:clear
php artisan cache:clear
```

### Issue 9: Composer/PHP Memory Exhausted

**Symptoms:**
- "Allowed memory size exhausted" during composer install

**Solutions:**
```bash
# Temporarily increase memory for composer
php -d memory_limit=-1 /usr/local/bin/composer install

# Or permanently increase in php.ini
memory_limit = 512M
```

### Issue 10: Permission Denied Errors

**Symptoms:**
- "Unable to write to storage/logs"
- "Permission denied" in logs

**Solutions:**
```bash
# Fix ownership (use your web server user)
sudo chown -R www-data:www-data /path/to/tajtrainer

# Fix permissions
sudo chmod -R 775 storage bootstrap/cache

# If using shared hosting (no sudo)
chmod -R 775 storage bootstrap/cache
```

---

## Performance Tuning for Production

### Enable OPcache

Edit `/etc/php/8.3/fpm/php.ini`:
```ini
opcache.enable=1
opcache.memory_consumption=256
opcache.interned_strings_buffer=16
opcache.max_accelerated_files=10000
opcache.revalidate_freq=2
```

### Use Redis for Caching (Optional)

```bash
# Install Redis
sudo apt-get install redis-server php8.3-redis -y

# In .env
CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

### Optimize Nginx

Add to your server block:
```nginx
# Gzip compression
gzip on;
gzip_types text/plain text/css application/json application/javascript text/xml application/xml application/xml+rss text/javascript;
gzip_comp_level 6;

# Browser caching
location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|eot)$ {
    expires 1y;
    add_header Cache-Control "public, immutable";
}
```

### Database Optimization

```sql
-- Add indexes for performance
USE tajtrainer;

-- Index for practice sessions lookups
CREATE INDEX idx_student_created ON practice_sessions(student_id, created_at);

-- Index for assignment submissions
CREATE INDEX idx_assignment_student ON assignment_submissions(assignment_id, student_id);

-- Index for error logs
CREATE INDEX idx_session_type ON tajweed_error_logs(practice_session_id, error_type);

-- Index for enrollments
CREATE INDEX idx_classroom_student ON enrollment(classroom_id, student_id);
```

---

## Maintenance Tasks

### Regular Backups

**Database Backup:**
```bash
# Daily backup script
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
mysqldump -u tajtrainer -p tajtrainer > /backups/tajtrainer_$DATE.sql
find /backups -name "tajtrainer_*.sql" -mtime +7 -delete
```

**File Backup:**
```bash
# Backup uploads and configs
tar -czf /backups/tajtrainer_files_$DATE.tar.gz \
  /var/www/tajtrainer/storage/app \
  /var/www/tajtrainer/.env
```

### Clear Old Data

```bash
# Clear old logs (keep 30 days)
find storage/logs -name "*.log" -mtime +30 -delete

# Clear old practice sessions (optional, adjust as needed)
php artisan tinker
>>> \DB::table('practice_sessions')->where('created_at', '<', now()->subMonths(6))->delete();
```

### Monitor Disk Space

```bash
# Check disk usage
df -h

# Check storage directory size
du -sh storage/app/public/*

# Find large files
find storage/app/public -type f -size +10M -exec ls -lh {} \;
```

### Update System

```bash
# Update application
cd /var/www/tajtrainer
git pull origin main
composer install --optimize-autoloader --no-dev
npm install
npm run build
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Update system packages
sudo apt-get update
sudo apt-get upgrade

# Update Python packages
cd python
pip3 install -r requirements.txt --upgrade
```

---

## Security Hardening

### 1. Secure .env File

```bash
chmod 600 .env
chown www-data:www-data .env
```

### 2. Disable PHP Version Display

In php.ini:
```ini
expose_php = Off
```

### 3. Configure Firewall

```bash
sudo ufw allow 22/tcp
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw enable
```

### 4. Enable Security Headers

Add to Nginx config:
```nginx
add_header X-Frame-Options "SAMEORIGIN" always;
add_header X-Content-Type-Options "nosniff" always;
add_header X-XSS-Protection "1; mode=block" always;
add_header Referrer-Policy "no-referrer-when-downgrade" always;
add_header Content-Security-Policy "default-src 'self' https: data: 'unsafe-inline' 'unsafe-eval';" always;
```

### 5. Regular Updates

```bash
# Setup automatic security updates (Ubuntu)
sudo apt-get install unattended-upgrades -y
sudo dpkg-reconfigure --priority=low unattended-upgrades
```

---
