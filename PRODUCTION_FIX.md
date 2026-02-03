# PRODUCTION DEPLOYMENT CRITICAL FIX

## ⚠️ IMPORTANT: Change APP_DEBUG to false

In your production `.env` file on the server, change:

```env
APP_DEBUG=true  ← CHANGE THIS
```

To:

```env
APP_DEBUG=false  ← PRODUCTION SETTING
```

### Why this matters:
- `APP_DEBUG=true` exposes sensitive error details to users
- Shows database credentials, API keys, and file paths in errors
- **SECURITY RISK** in production environment

### How to fix on server:

```bash
# Edit .env file
nano .env

# Find this line:
APP_DEBUG=true

# Change to:
APP_DEBUG=false

# Save and exit (Ctrl+X, Y, Enter)

# Clear cache
php artisan config:clear
php artisan config:cache
```

## 🚀 Quick Setup Commands for Server

After pulling the latest code, run:

```bash
# Make setup script executable
chmod +x production_setup.sh

# Run setup script
./production_setup.sh
```

OR manually:

```bash
# Create audio directory
mkdir -p storage/app/public/audio

# Set permissions
chmod -R 755 storage bootstrap/cache
chmod -R 775 storage/logs

# Install Python dependencies (if needed)
pip3 install --user librosa soundfile praat-parselmouth openai fastdtw numpy scipy

# Laravel setup
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run system check
php comprehensive_system_check.php
```

## ✅ Expected Result

System check should show:
```
✅ Passed: 79/79 checks
Overall System Health: 100%
✅ SYSTEM READY FOR PRODUCTION DEPLOYMENT
```

## 📋 Checklist

- [ ] `APP_DEBUG=false` in .env
- [ ] `storage/app/public/audio` directory exists
- [ ] Python dependencies installed
- [ ] Caches cleared and rebuilt
- [ ] System check passes 100%
- [ ] Test login and assignment submission
