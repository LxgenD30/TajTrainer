# Audio File Storage & Playback Troubleshooting Guide

## 🚨 Current Issue
- **Error**: "Failed - No file" 
- **File**: `1769947508_697f417450594.webm`
- **Problem**: Audio files are being uploaded but not accessible via web URL
- **Score**: 0/100 (analyzer can't access audio file)

---

## Root Cause Analysis

The file `1769947508_697f417450594.webm` is likely saved on the server but not accessible because:

1. **Storage symlink is missing or broken** (most likely)
2. **File permissions are wrong**
3. **File path in database is incorrect**
4. **APP_URL in .env is wrong**

---

## ✅ Step-by-Step Fix on cPanel

### Step 0: Find Your Correct Paths (IMPORTANT for Subdomains!)

Since you're using a subdomain (`tajtrainer.tajweedflow.com`), first identify your paths:

```bash
# Find your current username
whoami

# Find where you are currently
pwd

# Find your Laravel installation
find ~ -name "artisan" -type f 2>/dev/null | grep tajtrainer
```

Common subdomain structures:
- `/home/USERNAME/public_html/` (if subdomain points to subfolder)
- `/home/USERNAME/tajtrainer.tajweedflow.com/` (subdomain-specific directory)
- `/home/USERNAME/subdomains/tajtrainer/public_html/`

**For the rest of this guide, replace `~/public_html` with your actual Laravel root path!**

---

### Step 1: Check if Storage Symlink Exists

```bash
# Replace ~/public_html with your actual path found in Step 0
cd ~/public_html  # OR cd ~/tajtrainer.tajweedflow.com
ls -la public/storage
```

**Expected Output**:
```
lrwxrwxrwx 1 tajweedf tajweedf 25 Feb 01 12:00 storage -> ../storage/app/public
```

**If you see**: "No such file or directory" → **Symlink is missing** (this is your problem!)

---

### Step 2: Create Storage Symlink

#### Option A: Using Laravel Artisan (Recommended)
```bash
# Use your actual Laravel root path
cd ~/public_html  # OR cd ~/tajtrainer.tajweedflow.com
php artisan storage:link
```

**Expected Output**:
```
The [public/storage] link has been connected to [storage/app/public].
The links have been created.
```

#### Option B: Manual Symlink Creation (if artisan fails)
```bash
# Use your actual Laravel root path
cd ~/public_html/public  # OR cd ~/tajtrainer.tajweedflow.com/public
ln -sf ../storage/app/public storage
```

Verify it was created:
```bash
# Use your actual path
ls -la ~/public_html/public/storage
# Should show: storage -> ../storage/app/public
```

---

### Step 3: Verify File Actually Exists on Server

```bash
# Use your actual Laravel root path
cd ~/public_html  # OR cd ~/tajtrainer.tajweedflow.com
ls -lh storage/app/public/submissions/ | tail -20
```

**Look for**: `1769947508_697f417450594.webm` in the list

**If file exists**:
- Check file size (should be > 0 bytes)
- Check permissions (should be `rw-r--r--` or 644)
- Check owner (should be your cPanel user)

**If file doesn't exist**:
- Audio upload is failing
- Check Laravel logs for errors

---

### Step 4: Fix File Permissions (if needed)

```bash
# First, find your username
USER=$(whoami)
echo "Your username is: $USER"

# Fix directory permissions (use your actual path)
chmod 755 ~/public_html/storage/app/public/submissions/

# Fix file permissions
chmod 644 ~/public_html/storage/app/public/submissions/*

# Ensure correct ownership (use your actual username and path)
chown -R $USER:$USER ~/public_html/storage/app/public/submissions/
# For subdomain, you might need:
# chown -R $USER:$USER ~/tajtrainer.tajweedflow.com/storage/app/public/submissions/
```

---

### Step 5: Test Storage URL Generation

Create a test script to verify URL generation:

```bash
# Use your actual Laravel root path
cd ~/public_html  # OR cd ~/tajtrainer.tajweedflow.com
php artisan tinker
```

In tinker, run:
```php
// Check if file exists in storage
\Storage::disk('public')->exists('submissions/1769947508_697f417450594.webm');
// Should return: true

// Check generated URL
\Storage::url('submissions/1769947508_697f417450594.webm');
// Should return: /storage/submissions/1769947508_697f417450594.webm

// Check full URL
env('APP_URL') . '/storage/submissions/1769947508_697f417450594.webm';
// Should return: https://tajtrainer.tajweedflow.com/storage/submissions/1769947508_697f417450594.webm

// Exit tinker
exit
```

---

### Step 6: Verify APP_URL in .env

```bash
# Use your actual Laravel root path
cd ~/public_html  # OR cd ~/tajtrainer.tajweedflow.com
grep APP_URL .env
```

**Expected Output**:
```
APP_URL=https://tajtrainer.tajweedflow.com
```

**If wrong**, edit `.env`:
```bash
nano .env
# Change APP_URL to: https://tajtrainer.tajweedflow.com
# Save with Ctrl+X, then Y, then Enter
```

Then clear config cache:
```bash
php artisan config:clear
php artisan cache:clear
```

---

### Step 7: Test File Access via Browser

Open browser and try to access the file directly:
```
https://tajtrainer.tajweedflow.com/storage/submissions/1769947508_697f417450594.webm
```

**Expected**: File downloads or plays

**If 404 Not Found**:
- Symlink is missing or broken → Go back to Step 2
- File doesn't exist → Go back to Step 3
- .htaccess blocking access → Check Step 8

**If 403 Forbidden**:
- File permissions wrong → Go back to Step 4

**If 500 Internal Server Error**:
- Check PHP error log in cPanel File Manager

---

### Step 8: Check .htaccess Configuration

Verify `.htaccess` in `public_html/public/` allows storage access:

```bash
cat ~/public_html/public/.htaccess | grep -A10 "RewriteEngine On"
```

**Should contain**:
```apache
RewriteEngine On

# Handle storage symlink
RewriteCond %{REQUEST_URI} !^/storage/
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^ index.php [L]
```

**If missing** the storage exception, the RewriteRule might be blocking storage access. The current setup should be fine, but verify storage URLs work.

---

## 🔍 Detailed Diagnostics

### Check Laravel Logs

```bash
# Use your actual Laravel root path
cd ~/public_html  # OR cd ~/tajtrainer.tajweedflow.com
tail -100 storage/logs/laravel.log | grep -i "audio\|submission\|storage"
```

Look for:
- ✅ "✓ Audio file uploaded successfully: submissions/FILENAME.webm"
- ✅ "✓ Submission saved successfully with ID: X"
- ❌ "Failed to save audio file"
- ❌ "File is invalid or empty"
- ❌ "Storage exception"

### Check Actual Submission in Database

```bash
# Use your actual Laravel root path
cd ~/public_html  # OR cd ~/tajtrainer.tajweedflow.com
php artisan tinker
```

```php
// Find the submission by file name
$sub = \App\Models\AssignmentSubmission::where('audio_file_path', 'like', '%1769947508_697f417450594%')->first();

if ($sub) {
    echo "Found submission ID: " . $sub->id . "\n";
    echo "Audio path: " . $sub->audio_file_path . "\n";
    echo "Student ID: " . $sub->student_id . "\n";
    echo "Status: " . $sub->status . "\n";
    echo "Score: " . ($sub->score ?? 'not graded') . "\n";
    
    // Check if file exists
    $exists = \Storage::disk('public')->exists($sub->audio_file_path);
    echo "File exists in storage: " . ($exists ? 'YES' : 'NO') . "\n";
    
    // Generate URL
    $url = \Storage::url($sub->audio_file_path);
    echo "Generated URL: " . $url . "\n";
    
    // Full URL
    echo "Full URL: " . env('APP_URL') . $url . "\n";
} else {
    echo "Submission not found!\n";
}

exit
```

---

## 🎯 Common Issues & Solutions

### Issue 1: Symlink Creation Fails
**Error**: "symlink(): Protocol error"

**Cause**: cPanel restrictions on symlinks

**Solution**: Use relative symlink
```bash
# Use your actual Laravel root path
cd ~/public_html/public  # OR cd ~/tajtrainer.tajweedflow.com/public
rm -f storage  # Remove broken symlink if exists
ln -sf ../storage/app/public storage
```

### Issue 2: File Saves but Returns 404
**Cause**: Symlink broken or pointing to wrong location

**Check**:
```bash
# Use your actual Laravel root path
cd ~/public_html/public  # OR cd ~/tajtrainer.tajweedflow.com/public
readlink storage
# Should output: ../storage/app/public
```

**Fix**:
```bash
rm storage
ln -sf ../storage/app/public storage
```

### Issue 3: Permission Denied
**Cause**: Wrong file owner or permissions

**Fix**:
```bash
# Get your username first
USER=$(whoami)

# Reset all storage permissions (use your actual path)
cd ~/public_html  # OR cd ~/tajtrainer.tajweedflow.com
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
chown -R $USER:$USER storage/
chown -R $USER:$USER bootstrap/cache/
```

### Issue 4: cPanel File Manager Shows Different Structure
**Cause**: cPanel shows `public_html/` as root, but Laravel expects `public/` subfolder

**Verify Structure**:

**Option A - Main Domain or Subfolder Setup**:
```
~/public_html/          (Laravel root)
├── app/
├── bootstrap/
├── config/
├── database/
├── public/             (Web root - THIS should be document root)
│   ├── index.php
│   ├── storage/        (symlink to ../storage/app/public)
│   └── .htaccess
├── storage/
│   └── app/
│       └── public/
│           └── submissions/
│               └── 1769947508_697f417450594.webm
└── vendor/
```

**Option B - Subdomain-Specific Directory**:
```
~/tajtrainer.tajweedflow.com/   (Laravel root for subdomain)
├── app/
├── bootstrap/
├── config/
├── database/
├── public/                      (Web root for subdomain)
│   ├── index.php
│   ├── storage/                 (symlink to ../storage/app/public)
│   └── .htaccess
├── storage/
│   └── app/
│       └── public/
│           └── submissions/
└── vendor/
```

**If your structure is different**:
- Document root might be set to `public_html/` instead of `public_html/public/`
- Files might be in wrong directory
- Need to adjust symlink path

---

## 🧪 Complete Test Procedure

Run these commands in sequence:

```bash
#!/bin/bash
echo "=== TajTrainer Storage Diagnostic ==="
echo ""

# Detect Laravel installation path
echo "0. Detecting Laravel installation..."
USER=$(whoami)
echo "   Username: $USER"

if [ -f ~/public_html/artisan ]; then
    LARAVEL_ROOT=~/public_html
    echo "   ✓ Found at: ~/public_html"
elif [ -f ~/tajtrainer.tajweedflow.com/artisan ]; then
    LARAVEL_ROOT=~/tajtrainer.tajweedflow.com
    echo "   ✓ Found at: ~/tajtrainer.tajweedflow.com"
else
    echo "   ⚠ Laravel not found in standard locations"
    echo "   Please enter your Laravel root path:"
    read LARAVEL_ROOT
fi
echo ""

# 1. Check if file exists
echo "1. Checking if audio file exists..."
FILE="storage/app/public/submissions/1769947508_697f417450594.webm"
if [ -f $LARAVEL_ROOT/$FILE ]; then
    echo "✓ File exists"
    ls -lh $LARAVEL_ROOT/$FILE
else
    echo "✗ File NOT found at $LARAVEL_ROOT/$FILE"
fi
echo ""

# 2. Check symlink
echo "2. Checking storage symlink..."
if [ -L $LARAVEL_ROOT/public/storage ]; then
    echo "✓ Symlink exists"
    echo "   Points to: $(readlink $LARAVEL_ROOT/public/storage)"
else
    echo "✗ Symlink does NOT exist"
    echo "   Creating symlink..."
    cd $LARAVEL_ROOT/public
    ln -sf ../storage/app/public storage
    echo "   ✓ Symlink created"
fi
echo ""

# 3. Check permissions
echo "3. Checking permissions..."
ls -ld $LARAVEL_ROOT/storage/app/public/submissions/
ls -lh $LARAVEL_ROOT/storage/app/public/submissions/1769947508_697f417450594.webm 2>/dev/null || echo "File not found"
echo ""

# 4. Check APP_URL
echo "4. Checking APP_URL..."
cd $LARAVEL_ROOT
grep "APP_URL=" .env
echo ""

# 5. Test URL access
echo "5. Test URL (copy this and open in browser):"
echo "   https://tajtrainer.tajweedflow.com/storage/submissions/1769947508_697f417450594.webm"
echo ""

echo "=== Diagnostic Complete ==="
```

Save this as `diagnose_storage.sh` and run:
```bash
chmod +x diagnose_storage.sh
./diagnose_storage.sh
```

---

## 🔧 Quick Fix Script

If you want to fix everything at once:

```bash
#!/bin/bash
echo "Fixing TajTrainer storage..."

# Detect Laravel installation
USER=$(whoami)
echo "Username: $USER"

if [ -f ~/public_html/artisan ]; then
    LARAVEL_ROOT=~/public_html
    echo "Laravel root: ~/public_html"
elif [ -f ~/tajtrainer.tajweedflow.com/artisan ]; then
    LARAVEL_ROOT=~/tajtrainer.tajweedflow.com
    echo "Laravel root: ~/tajtrainer.tajweedflow.com"
else
    echo "Laravel not found. Please enter path:"
    read LARAVEL_ROOT
fi

cd $LARAVEL_ROOT

# 1. Create symlink
echo "Creating storage symlink..."
cd public
rm -f storage
ln -sf ../storage/app/public storage
cd ..

# 2. Fix permissions
echo "Fixing permissions..."
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
find storage/app/public/submissions -type f -exec chmod 644 {} \;
chown -R $USER:$USER storage/
chown -R $USER:$USER bootstrap/cache/

# 3. Clear cache
echo "Clearing cache..."
php artisan storage:link
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# 4. Verify
echo ""
echo "Verification:"
echo "Symlink: $(readlink public/storage)"
echo "Submissions folder:"
ls -lh storage/app/public/submissions/ | tail -5

echo ""
echo "✓ Fix complete! Now test in browser:"
echo "https://tajtrainer.tajweedflow.com/storage/submissions/1769947508_697f417450594.webm"
```

Save as `fix_storage.sh` and run:
```bash
chmod +x fix_storage.sh
./fix_storage.sh
```

---

## 📊 Expected Results After Fix

### Browser Test
✅ Direct URL access works:
```
https://tajtrainer.tajweedflow.com/storage/submissions/1769947508_697f417450594.webm
```
- File downloads or plays
- No 404 or 403 errors

### Audio Player
✅ In assignment view:
- Audio player appears
- Play button works
- No "Failed - No file" error
- No "Audio playback error" message

### Tajweed Analysis
✅ Analyzer can access file:
- Score is NOT 0/100
- Score is NOT always 75/100
- Actual Tajweed percentages display
- AI feedback shows specific advice

### Laravel Logs
✅ No errors related to storage:
```bash
tail -f storage/logs/laravel.log | grep -i error
# Should see no file storage errors
```

---

## 🆘 If Still Not Working

### 1. Check cPanel Error Log
```
File Manager → error_log (in public_html root)
```
Look for PHP errors related to file access

### 2. Enable Debug Mode Temporarily
Edit `.env`:
```bash
APP_DEBUG=true
```
Refresh the page and check detailed error message

**IMPORTANT**: Set back to `false` after debugging:
```bash
APP_DEBUG=false
```

### 3. Verify Document Root
In cPanel → Domains → Manage → Find your subdomain (tajtrainer.tajweedflow.com)

Document Root should point to the `public` folder of your Laravel installation.

**Possible correct paths**:
```
/home/USERNAME/public_html/public
/home/USERNAME/tajtrainer.tajweedflow.com/public
/home/USERNAME/subdomains/tajtrainer/public_html/public
```

**Wrong paths** (will cause issues):
```
/home/USERNAME/public_html              (missing /public)
/home/USERNAME/tajtrainer.tajweedflow.com   (missing /public)
```

**How to check**: Run `whoami` in terminal to get USERNAME, then verify the path exists.

### 4. Contact Hosting Support
If all above fails, ask hosting support to:
1. Enable symlink creation for your account
2. Verify file permissions are correct
3. Check if mod_rewrite is enabled
4. Verify no security restrictions blocking `/storage/` access

---

## 📝 Summary Checklist

After running fixes, verify:

- [ ] Symlink exists: `ls -la public/storage`
- [ ] File exists: `ls -lh storage/app/public/submissions/1769947508_697f417450594.webm`
- [ ] Permissions correct: `755` for dirs, `644` for files
- [ ] APP_URL correct in `.env`
- [ ] Direct URL works in browser
- [ ] Audio player works in app
- [ ] Analyzer returns real scores (not 0 or 75)
- [ ] No errors in Laravel logs

---

**Next Step**: Run the diagnostic script and share the output with me. The most likely issue is missing storage symlink!
