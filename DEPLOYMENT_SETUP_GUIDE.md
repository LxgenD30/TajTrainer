# TajTrainer V2 - GitHub Setup & Deployment Guide

## 📋 Quick Overview
This guide will help you:
1. Set up Git and GitHub for your TajTrainer V2 project
2. Configure automatic deployment to cPanel
3. Deploy your code automatically on every push

---

## 🚀 STEP 1: Prepare Your Local Repository

### 1.1 Check if Git is installed
```powershell
git --version
```
If not installed, download from: https://git-scm.com/download/win

### 1.2 Configure Git (First time only)
```powershell
git config --global user.name "Your Name"
git config --global user.email "your.email@example.com"
```

### 1.3 Initialize Git in your project (if not already done)
```powershell
cd c:\laragon\www\tajtrainerV2
git init
```

---

## 🌐 STEP 2: Create GitHub Repository

### 2.1 Create a new repository on GitHub
1. Go to https://github.com/new
2. Fill in:
   - **Repository name**: `tajtrainer-v2` (or your preferred name)
   - **Description**: "TajTrainer V2 - Quran Tajweed Learning Platform"
   - **Privacy**: Choose Private or Public
   - **DO NOT** check "Initialize with README" (you already have code)
3. Click **"Create repository"**

### 2.2 Copy the repository URL
GitHub will show you a URL like:
```
https://github.com/yourusername/tajtrainer-v2.git
```
Keep this handy!

---

## 📤 STEP 3: Push Your Code to GitHub

### 3.1 Add remote repository
```powershell
cd c:\laragon\www\tajtrainerV2
git remote add origin https://github.com/yourusername/tajtrainer-v2.git
```
**Replace `yourusername/tajtrainer-v2` with your actual repository path!**

### 3.2 Add all files to Git
```powershell
git add .
```

### 3.3 Make your first commit
```powershell
git commit -m "Initial commit - TajTrainer V2"
```

### 3.4 Push to GitHub
```powershell
git push -u origin main
```

**Note:** If your default branch is `master` instead of `main`, use:
```powershell
git branch -M main
git push -u origin main
```

### 3.5 Verify on GitHub
Visit your repository URL - you should see all your code!

---

## 🔧 STEP 4: Set Up cPanel Deployment

### 4.1 Understanding Your cPanel Structure

Your setup has TWO folders:
1. **`/home/tajweedf/tajtrainer.com`** - Public web folder (contains index.php, .htaccess)
   - This is what visitors access via https://tajtrainer.tajweedflow.com
   - **deploy.php goes HERE** (so it's web-accessible)

2. **`/home/tajweedf/tajtrainer`** - Laravel application folder (contains .env, app/, etc.)
   - This contains your actual Laravel code
   - **Git deploys code HERE**

### 4.2 Update deploy.php configuration

The `deploy.php` file is already configured with your paths:

```php
// CHANGE THIS LINE - Generate a random secret token
define('SECRET_TOKEN', 'your_super_secret_random_token_here');

// Update these paths to match your cPanel
define('REPO_PATH', '/home/yourusername/repositories/tajtrainer');
define('LARAVEL_PATH', '/home/yourusername/public_html');
```

**Your cPanel Structure:**
- **Username:** `tajweedf`
- **Laravel Application Folder:** `/home/tajweedf/tajtrainer` (contains .env, app/, database/, etc.)
- **Public Web Folder:** `/home/tajweedf/tajtrainer.com` (contains index.php, .htaccess - accessible via web)
- **Repository Path:** `/home/tajweedf/repositories/tajtrainer` (Git repository)

**Note:** deploy.php should be placed in the public folder (`tajtrainer.com`) but will deploy code to the Laravel app folder (`tajtrainer`).

### 4.2 Generate a secure token
Run this in PowerShell to generate a random token:
```powershell
-join ((48..57) + (65..90) + (97..122) | Get-Random -Count 32 | ForEach-Object {[char]$_})
```
Copy the output and paste it as your `SECRET_TOKEN`.

### 4.3 Upload deploy.php to cPanel
1. Log into cPanel → File Manager
2. Navigate to `/home/tajweedf/tajtrainer.com` (your public web folder)
3. Upload the `deploy.php` file to this folder
4. Set permissions to `644` (right-click → Change Permissions)

**Important:** deploy.php goes in the `tajtrainer.com` folder (web accessible), NOT in the `tajtrainer` folder!

### 4.4 Test the deployment script
Visit: `https://tajtrainer.tajweedflow.com/deploy.php?token=your_secret_token_here`

You should see a JSON response. Check `deployment.log` for details.

---

## 🔗 STEP 5: Set Up GitHub Webhook (Auto-Deploy)

### 5.1 Go to your GitHub repository settings
1. Visit your repo: `https://github.com/yourusername/tajtrainer-v2`
2. Click **"Settings"** (tab at the top)
3. Click **"Webhooks"** (left sidebar)
4. Click **"Add webhook"**

### 5.2 Configure the webhook
Fill in these values:

**Payload URL:**
```
https://tajtrainer.tajweedflow.com/deploy.php?token=your_secret_token_here
```
Replace `your_secret_token_here` with the SECRET_TOKEN from your deploy.php file.

**Note:** Use your full subdomain URL: `tajtrainer.tajweedflow.com`

**Content type:**
- Select: `application/json`

**Which events would you like to trigger this webhook?**
- Select: "Just the push event"

**Active:**
- ✓ Check this box

Click **"Add webhook"**

### 5.3 Test the webhook
1. Make a small change to any file locally
2. Commit and push:
   ```powershell
   git add .
   git commit -m "Test auto-deployment"
   git push origin main
   ```
3. Go to GitHub → Settings → Webhooks
4. Click on your webhook
5. Scroll down to "Recent Deliveries"
6. You should see a green checkmark ✓
7. Check `https://yourdomain.com/deployment.log` to see the deployment log

---

## 🎯 STEP 6: Daily Workflow (After Setup)

From now on, to deploy changes:

```powershell
# 1. Make your code changes in Laragon
# 2. Test locally

# 3. Commit your changes
git add .
git commit -m "Description of what you changed"

# 4. Push to GitHub (automatic deployment happens!)
git push origin main

# 5. Check deployment log (optional)
# Visit: https://tajtrainer.tajweedflow.com/deployment.log
```

That's it! Your code automatically deploys to cPanel! 🎉

---

## 🔐 STEP 7: Security Recommendations

### 7.1 Protect deploy.php
Add this to your `.htaccess` in your subdomain root (same folder as deploy.php):

```apache
# Protect deployment script
<Files "deploy.php">
    Order Deny,Allow
    Deny from all
    Allow from 192.30.252.0/22
    Allow from 185.199.108.0/22
    Allow from 140.82.112.0/20
</Files>

# Protect deployment log
<Files "deployment.log">
    Order Deny,Allow
    Deny from all
</Files>
```

These IPs are GitHub's webhook IPs.

### 7.2 Never commit sensitive files
Your `.gitignore` already excludes:
- `.env` (database credentials)
- `vendor/` (composer packages)
- `node_modules/` (npm packages)
- `storage/` logs and uploads

---

## 🛠️ Troubleshooting

### Problem: "Permission denied" errors
**Solution:** SSH into cPanel and run:
```bash
chmod -R 755 /home/tajweedf/tajtrainer/storage
chmod -R 755 /home/tajweedf/tajtrainer/bootstrap/cache
chown -R tajweedf:tajweedf /home/tajweedf/tajtrainer/storage
chown -R tajweedf:tajweedf /home/tajweedf/tajtrainer/bootstrap/cache
```

### Problem: "Composer not found"
**Solution:** Update deploy.php, change composer command to:
```php
'/usr/local/bin/composer install --no-dev --optimize-autoloader'
```
Or find composer path: `which composer` in cPanel terminal

### Problem: Webhook says "Connection refused"
**Solution:** Check if deploy.php is in the correct directory and accessible via browser first.

### Problem: Changes not appearing after deployment
**Solution:** 
1. Check deployment.log for errors
2. Clear Laravel cache manually in cPanel terminal:
   ```bash
   php artisan cache:clear
   php artisan config:clear
   php artisan view:clear
   ```

---

## 📝 Optional: Set Up Git Version Control in cPanel

If your cPanel has "Git Version Control":

1. cPanel → Files → Git Version Control
2. Click "Create"
3. Fill in:
   - Clone URL: `https://github.com/yourusername/tajtrainer-v2.git`
   - Repository Path: `/home/tajweedf/repositories/tajtrainer`
   - Repository Name: `tajtrainer`
4. Click "Create"
5. After creation, click "Manage"
6. Click "Pull or Deploy" tab
7. Set deployment path to: `/home/tajweedf/tajtrainer`
8. Click "Deploy HEAD Commit"

This gives you a GUI to manually pull updates if the webhook ever fails.

---

## ✅ Verification Checklist

- [ ] Git installed and configured
- [ ] GitHub repository created
- [ ] Code pushed to GitHub successfully
- [ ] deploy.php uploaded to cPanel with correct paths
- [ ] SECRET_TOKEN changed to a random string
- [ ] Webhook added in GitHub settings
- [ ] Test push triggered automatic deployment
- [ ] deployment.log shows successful deployment
- [ ] Website works after deployment

---

## 🎓 Git Commands Cheat Sheet

```powershell
# Check status of changes
git status

# Add specific file
git add path/to/file.php

# Add all changes
git add .

# Commit with message
git commit -m "Your message"

# Push to GitHub
git push origin main

# Pull latest changes (if working from multiple computers)
git pull origin main

# View commit history
git log --oneline

# Undo last commit (keep changes)
git reset --soft HEAD~1

# Discard all local changes
git reset --hard HEAD
```

---

## 📞 Need Help?

If you encounter issues:
1. Check `deployment.log`: https://tajtrainer.tajweedflow.com/deployment.log
2. Check GitHub webhook delivery status in repo settings
3. Verify all paths in deploy.php match your subdomain paths (check cPanel > Domains > Subdomains)
4. Ensure cPanel has sufficient permissions
5. Confirm subdomain is properly configured and points to correct document root

---

**Your deployment is now automated! Happy coding! 🚀**
