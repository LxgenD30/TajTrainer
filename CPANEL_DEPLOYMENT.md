# cPanel Deployment Checklist for TajTrainer

## ✅ Pre-Deployment Verification

All code has been tested locally:
- ✅ No syntax errors in PHP files
- ✅ No errors in Blade templates
- ✅ Python dependency checker passes all tests
- ✅ Parselmouth (Praat) functionality verified
- ✅ FastDTW functionality verified
- ✅ All dependencies installed locally

## 📋 Step-by-Step Deployment Guide

### 1. Upload Files to cPanel (Via Git or File Manager)

**Option A: Using Git Deployment (Recommended)**
```bash
# In cPanel Terminal
cd ~/public_html/tajtrainer  # Or your domain folder
git pull origin main
```

**Option B: Using File Manager**
- Upload all files except: `.git`, `node_modules`, `vendor`, `storage`, `.env`
- Keep existing `storage/` and `.env` files on server

---

### 2. Set Proper File Permissions

```bash
# In cPanel Terminal
cd ~/public_html/tajtrainer

# Storage and cache directories (read/write/execute)
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/

# Make sure web server can write to these
chown -R $USER:nobody storage/
chown -R $USER:nobody bootstrap/cache/
```

---

### 3. Install/Update Composer Dependencies

```bash
# In cPanel Terminal
cd ~/public_html/tajtrainer

# Update composer if needed
composer self-update

# Install dependencies (production mode)
composer install --optimize-autoloader --no-dev
```

---

### 4. Install Python Dependencies

**CRITICAL: This is where most deployments fail!**

```bash
# Check Python version (must be 3.8+)
python3 --version

# If Python 3.8+ not available, install via cPanel Python Selector
# Or contact hosting support

# Navigate to python directory
cd ~/public_html/tajtrainer/python

# Install dependencies
python3 -m pip install --user -r requirements.txt

# Or if pip3 is available:
pip3 install --user -r requirements.txt

# VERIFY INSTALLATION
python3 check_dependencies.py
```

**Expected Output:**
```
============================================================
TajTrainer Python Dependencies Check
============================================================
✓ numpy                v2.3.5           - numpy
✓ scipy                v1.16.3          - scipy
✓ librosa              v0.11.0          - librosa
✓ parselmouth          v0.4.7           - parselmouth (Praat integration)
✓ fastdtw              vunknown         - fastdtw (Dynamic Time Warping)
✓ openai               v2.16.0          - openai (GPT-4 API)
✓ requests             v2.32.5          - requests (HTTP client)
✓ soundfile            v0.13.1          - soundfile (Audio I/O)
============================================================
✓ All dependencies are installed and functional!
✓✓✓ All systems ready! ✓✓✓
```

**If Installation Fails:**
- Check if Python version is 3.8 or higher
- Try: `python3.9` or `python3.10` instead of `python3`
- Contact hosting support to enable Python and pip
- Some hosts require using Python Selector in cPanel

---

### 5. Configure Environment Variables

```bash
# Edit .env file
nano .env  # or use cPanel File Manager editor
```

**Add/Update these variables:**

```env
# Application
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

# Python Configuration
PYTHON_PATH=/usr/bin/python3
# Or wherever python3 is located: use `which python3` to find it

# OpenAI API (for AI feedback)
OPENAI_API_KEY=your-openai-api-key-here

# AssemblyAI (for transcription)
ASSEMBLYAI_API_KEY=your-assemblyai-key-here

# Database (verify these match cPanel MySQL)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password
```

**Find Python Path:**
```bash
which python3
# Example output: /usr/bin/python3
# Use this path in PYTHON_PATH
```

---

### 6. Create Storage Symbolic Link

```bash
cd ~/public_html/tajtrainer

# Remove old link if exists
rm -f public/storage

# Create new symbolic link
php artisan storage:link
```

**Verify:**
```bash
ls -la public/storage
# Should show: public/storage -> ../storage/app/public
```

---

### 7. Clear All Caches

```bash
cd ~/public_html/tajtrainer

# Clear application cache
php artisan cache:clear

# Clear config cache
php artisan config:clear

# Clear route cache
php artisan route:clear

# Clear view cache
php artisan view:clear

# Recreate optimized cache (production)
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

### 8. Run Database Migrations (If Needed)

```bash
# Check if new migrations exist
php artisan migrate:status

# Run migrations
php artisan migrate --force
```

---

### 9. Test Python Analyzer on Server

```bash
cd ~/public_html/tajtrainer

# Test with a sample command
python3 python/tajweed_analyzer.py "/path/to/test/audio.mp3" "بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ"

# Check if it outputs JSON with analysis results
```

---

### 10. Monitor Laravel Logs

```bash
# Tail the log file to see real-time errors
tail -f storage/logs/laravel.log

# Or view recent entries
tail -100 storage/logs/laravel.log | grep -i error
```

---

## 🧪 Post-Deployment Testing

### Test Checklist:

1. **✅ Website Loads**
   - Visit your domain
   - Verify no 500 errors
   - Check homepage displays correctly

2. **✅ Audio Submission**
   - Login as student
   - Submit an assignment with audio
   - Check if submission is saved

3. **✅ Audio Playback**
   - Go to assignment view page
   - Verify audio player loads
   - Verify audio plays correctly
   - Test download button

4. **✅ Python Analyzer**
   - Check Laravel logs: `tail -f storage/logs/laravel.log`
   - Look for: `=== Python Tajweed Analysis Started ===`
   - Verify: `=== Python Analysis Successful ===`
   - Check for: `Has AI feedback: Yes`

5. **✅ AI Feedback Display**
   - View submitted assignment
   - Verify AI feedback box appears
   - Check if feedback shows:
     - Performance Summary
     - Your Strengths
     - Areas for Improvement
     - Next Steps

6. **✅ Scores**
   - Verify submission doesn't get default 75 score
   - Check Tajweed analysis shows actual percentages
   - Verify scores match analysis

---

## 🐛 Troubleshooting Common Issues

### Issue 1: "No output from analysis script"

**Symptoms:**
- All submissions get 75 score
- AI feedback shows error message
- Log shows: "No output from Python script"

**Solutions:**
```bash
# Check Python dependencies
cd python
python3 check_dependencies.py

# If fails, reinstall:
pip3 install --user -r requirements.txt

# Check Python path in .env
which python3
# Update PYTHON_PATH in .env to match

# Clear config cache
cd ..
php artisan config:clear

# Test manually
python3 python/tajweed_analyzer.py "test.mp3" "test text"
```

---

### Issue 2: "ModuleNotFoundError: No module named 'parselmouth'"

**Symptoms:**
- Log shows Python import errors
- AI feedback box shows dependency error

**Solutions:**
```bash
# Install missing package
pip3 install --user praat-parselmouth

# Or install all:
pip3 install --user -r python/requirements.txt

# Verify
python3 -c "import parselmouth; print('OK')"
```

---

### Issue 3: Audio playback not working

**Symptoms:**
- Audio player shows error
- Download button broken

**Solutions:**
```bash
# Check storage link
ls -la public/storage

# Recreate if missing
rm -f public/storage
php artisan storage:link

# Check file permissions
chmod -R 775 storage/app/public/
ls -la storage/app/public/submissions/

# Check file exists
ls -la storage/app/public/submissions/
```

---

### Issue 4: 500 Internal Server Error

**Symptoms:**
- White screen or 500 error
- Website won't load

**Solutions:**
```bash
# Check error logs
tail -50 storage/logs/laravel.log

# Check permissions
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/

# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Check .env file exists and is readable
ls -la .env
cat .env | grep APP_KEY  # Should not be empty
```

---

### Issue 5: Python timeout (large files)

**Symptoms:**
- Log shows: "Python analysis timed out"
- Only small files work

**Solutions:**
1. Increase timeout in `StudentController.php` line ~1424:
   ```php
   $timeout = 600; // Change from 300 to 600 seconds
   ```

2. Check server PHP execution time:
   ```bash
   php -i | grep max_execution_time
   ```

3. Increase in `.htaccess` if needed:
   ```apache
   php_value max_execution_time 600
   ```

---

## 📊 Monitoring After Deployment

### Check Logs Regularly (First 24-48 Hours)

```bash
# Real-time monitoring
tail -f storage/logs/laravel.log

# Filter Python errors
tail -f storage/logs/laravel.log | grep Python

# Check for errors
grep -i error storage/logs/laravel.log | tail -20
```

### Key Metrics to Watch:
- ✅ Python analyzer success rate (should be >90%)
- ✅ Average processing time (<60 seconds for normal files)
- ✅ Error types and frequencies
- ✅ AI feedback generation success rate

---

## 🔐 Security Checklist

- [ ] `.env` file has proper permissions (600): `chmod 600 .env`
- [ ] `APP_DEBUG=false` in production
- [ ] Database credentials are secure
- [ ] OpenAI API key is set and not exposed
- [ ] Storage folder is not directly web-accessible
- [ ] Composer dependencies are production-only

---

## 📞 Support

If you encounter issues:

1. **Check Laravel logs**: `storage/logs/laravel.log`
2. **Run dependency checker**: `python3 python/check_dependencies.py`
3. **Verify environment**: `php artisan config:show`
4. **Check Python execution**: Test manually with a sample audio file
5. **Review documentation**: `python/DEPLOYMENT_GUIDE.md`

---

## ✨ Success Indicators

You'll know deployment is successful when:

1. ✅ Website loads without errors
2. ✅ Students can submit audio recordings
3. ✅ Audio playback works on student and teacher pages
4. ✅ Python analyzer executes successfully (check logs)
5. ✅ Submissions get unique scores (not all 75)
6. ✅ AI feedback displays with personalized suggestions
7. ✅ Tajweed analysis shows actual percentages
8. ✅ Download button works for audio files
9. ✅ No "No output from analysis script" errors
10. ✅ Logs show: "=== Python Analysis Successful ==="

---

## 🚀 Quick Command Reference

```bash
# Navigate to project
cd ~/public_html/tajtrainer

# Update code
git pull origin main

# Install PHP dependencies
composer install --optimize-autoloader --no-dev

# Install Python dependencies
pip3 install --user -r python/requirements.txt

# Check Python setup
python3 python/check_dependencies.py

# Set permissions
chmod -R 775 storage/ bootstrap/cache/

# Create storage link
php artisan storage:link

# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan config:cache

# Monitor logs
tail -f storage/logs/laravel.log
```

---

## 📝 Notes

- **Python 3.8+ is REQUIRED** - older versions won't work with parselmouth
- **OpenAI API key is REQUIRED** - without it, AI feedback won't generate
- **Storage link is CRITICAL** - audio playback won't work without it
- **Permissions matter** - wrong permissions cause 500 errors and failed uploads
- **Check logs frequently** - first few days after deployment are critical

---

**Last Updated:** February 1, 2026  
**Version:** 2.0 (Phonetic Analysis with Parselmouth & DTW)
