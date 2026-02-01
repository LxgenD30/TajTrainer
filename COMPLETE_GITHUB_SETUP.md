# Complete GitHub Setup for TajTrainer V2
## Step-by-Step Instructions from Start to Finish

---

## ✅ Prerequisites Check

Before starting, verify:
- [x] Git is installed (you already checked: `git --version`)
- [x] You have a GitHub account
- [x] deploy.php is configured with your paths
- [x] You're in the project directory: `C:\laragon\www\tajtrainerV2`

---

## 🚀 PART 1: Configure Git (One-Time Setup)

Open PowerShell in your project folder and run:

```powershell
# Set your name (appears in commits)
git config --global user.name "Your Full Name"

# Set your email (must match your GitHub email)
git config --global user.email "your.email@example.com"

# Verify configuration
git config --global --list
```

**Expected output:**
```
user.name=Your Full Name
user.email=your.email@example.com
```

---

## 🌐 PART 2: Create GitHub Repository

### Step 1: Go to GitHub
1. Open browser: https://github.com/new
2. Log into your GitHub account

### Step 2: Fill in Repository Details
- **Repository name:** `tajtrainer-v2`
- **Description:** `TajTrainer V2 - Quran Tajweed Learning Platform with AI Analysis`
- **Privacy:** Choose **Private** (recommended) or Public
- **Important:** DO NOT check these boxes:
  - ❌ Add a README file
  - ❌ Add .gitignore
  - ❌ Choose a license
  
  (You already have these files!)

### Step 3: Create Repository
Click the green **"Create repository"** button

### Step 4: Copy Your Repository URL
GitHub will show you a page with setup instructions. Copy the URL that looks like:
```
https://github.com/yourusername/tajtrainer-v2.git
```

**Keep this URL handy!** You'll need it in the next step.

---

## 📤 PART 3: Push Your Code to GitHub

Run these commands in PowerShell (one at a time):

```powershell
# 1. Initialize Git repository
git init
```
**Expected output:** `Initialized empty Git repository in C:/laragon/www/tajtrainerV2/.git/`

```powershell
# 2. Add all files to staging
git add .
```
**Expected output:** Nothing (silence is good!)

```powershell
# 3. Check what will be committed
git status
```
**Expected output:** Long list of green files

```powershell
# 4. Make your first commit
git commit -m "Initial commit - TajTrainer V2"
```
**Expected output:** 
```
[main (root-commit) abc1234] Initial commit - TajTrainer V2
XXX files changed, YYYY insertions(+)
```

```powershell
# 5. Rename branch to 'main' (GitHub standard)
git branch -M main
```
**Expected output:** Nothing

```powershell
# 6. Add your GitHub repository as remote
# REPLACE 'yourusername' with your actual GitHub username!
git remote add origin https://github.com/yourusername/tajtrainer-v2.git
```
**Expected output:** Nothing

```powershell
# 7. Verify remote was added
git remote -v
```
**Expected output:**
```
origin  https://github.com/yourusername/tajtrainer-v2.git (fetch)
origin  https://github.com/yourusername/tajtrainer-v2.git (push)
```

```powershell
# 8. Push code to GitHub
git push -u origin main
```

**You may be prompted for credentials:**
- **Username:** Your GitHub username
- **Password:** Your GitHub Personal Access Token (NOT your GitHub password!)

**Note:** If you don't have a token, see "Getting GitHub Token" section below.

**Expected output:**
```
Enumerating objects: XXX, done.
Counting objects: 100% (XXX/XXX), done.
Writing objects: 100% (XXX/XXX), done.
To https://github.com/yourusername/tajtrainer-v2.git
 * [new branch]      main -> main
```

### ✅ Verify on GitHub
1. Go to: `https://github.com/yourusername/tajtrainer-v2`
2. You should see all your files!

---

## 🔑 Getting GitHub Personal Access Token (If Needed)

If Git asks for password:

1. Go to: https://github.com/settings/tokens
2. Click **"Generate new token"** → **"Generate new token (classic)"**
3. Fill in:
   - **Note:** `TajTrainer Deployment`
   - **Expiration:** 90 days (or No expiration)
   - **Scopes:** Check `repo` (all sub-items)
4. Click **"Generate token"**
5. **COPY THE TOKEN!** (You can't see it again)
6. Use this token as your password when Git asks

**Pro Tip:** Save the token in a password manager!

---

## 🔐 PART 4: Prepare deploy.php for Upload

### Step 1: Verify Secret Token
Open `deploy.php` and check line 15:
```php
define('SECRET_TOKEN', 'KVgdeJcmqv49sSuhnLHxjQ8GpNrP0ltR');
```

**Keep this token!** You'll need it for the webhook.

### Step 2: Upload to cPanel
1. Log into cPanel: https://tajweedflow.com:2083 (or your cPanel URL)
2. Click **"File Manager"**
3. Navigate to: `/home/tajweedf/tajtrainer.com`
4. Click **"Upload"** button (top toolbar)
5. Select `deploy.php` from your computer
6. Wait for upload to complete
7. Right-click on `deploy.php` → **"Change Permissions"**
8. Enter: `644` → Click **"Change Permissions"**

### Step 3: Test Deploy Script
Open browser and visit:
```
https://tajtrainer.tajweedflow.com/deploy.php?token=KVgdeJcmqv49sSuhnLHxjQ8GpNrP0ltR
```

**Expected result:**
```json
{"status":"success","message":"Deployment completed successfully","timestamp":"2026-02-01 12:34:56"}
```

**If you see errors:**
- Check the deployment.log: `https://tajtrainer.tajweedflow.com/deployment.log`
- The first deployment may fail (no git repo yet) - that's OK!

---

## 🔗 PART 5: Set Up GitHub Webhook (Auto-Deploy)

### Step 1: Go to Repository Settings
1. Visit: `https://github.com/yourusername/tajtrainer-v2`
2. Click **"Settings"** tab (top right)
3. Click **"Webhooks"** (left sidebar)
4. Click **"Add webhook"** button

### Step 2: Configure Webhook
Fill in these exact values:

**Payload URL:**
```
https://tajtrainer.tajweedflow.com/deploy.php?token=KVgdeJcmqv49sSuhnLHxjQ8GpNrP0ltR
```

**Content type:**
- Select: `application/json`

**Secret:**
- Leave empty

**SSL verification:**
- Keep: "Enable SSL verification" (if your site has SSL)

**Which events would you like to trigger this webhook?**
- Select: **"Just the push event"**

**Active:**
- ✓ Check this box

### Step 3: Save
Click **"Add webhook"** button

### Step 4: Verify Webhook Was Created
You should see a green checkmark ✓ next to your webhook after a few seconds.

Click on the webhook to see details.

---

## 🧪 PART 6: Test Automatic Deployment

### Test 1: Make a Small Change
```powershell
# Create a test file
echo "# Test deployment" > TEST_DEPLOY.txt

# Commit the change
git add TEST_DEPLOY.txt
git commit -m "Test automatic deployment"

# Push to GitHub (triggers auto-deploy!)
git push origin main
```

### Test 2: Check Webhook Delivery
1. Go to: `https://github.com/yourusername/tajtrainer-v2/settings/hooks`
2. Click on your webhook
3. Scroll to **"Recent Deliveries"**
4. Click on the top delivery
5. Check:
   - **Response:** Should be 200 ✓
   - **Response body:** Should show success JSON

### Test 3: Check Deployment Log
Visit: `https://tajtrainer.tajweedflow.com/deployment.log`

**You should see:**
```
[2026-02-01 12:34:56] ======================================
[2026-02-01 12:34:56] DEPLOYMENT STARTED
[2026-02-01 12:34:56] Triggered by: 140.82.115.XXX
[2026-02-01 12:34:56] ======================================
[2026-02-01 12:34:56] Executing: Pull latest changes
...
[2026-02-01 12:34:58] ======================================
[2026-02-01 12:34:58] DEPLOYMENT COMPLETED SUCCESSFULLY!
[2026-02-01 12:34:58] ======================================
```

### Test 4: Verify on Website
1. Visit: `https://tajtrainer.tajweedflow.com`
2. Your site should work normally
3. Check if TEST_DEPLOY.txt exists in your files

---

## 🎉 PART 7: Clean Up (Optional)

If the test worked, remove the test file:

```powershell
# Remove test file
git rm TEST_DEPLOY.txt
git commit -m "Remove test file"
git push origin main
```

This will automatically deploy again and remove the test file!

---

## 🎓 PART 8: Your New Daily Workflow

From now on, to update your live site:

```powershell
# 1. Make changes to your code in Laragon
# 2. Test locally at http://localhost/tajtrainerV2

# 3. When ready to deploy, run these 3 commands:
git add .
git commit -m "Description of what you changed"
git push origin main

# 4. Automatic deployment happens in 10-30 seconds!
# 5. Check: https://tajtrainer.tajweedflow.com
```

That's it! Your code automatically goes live! 🚀

---

## 📊 Monitoring & Maintenance

### Check Deployment Status Anytime:
```
https://tajtrainer.tajweedflow.com/deployment.log
```

### Check GitHub Webhook History:
```
https://github.com/yourusername/tajtrainer-v2/settings/hooks
```

### Manual Deployment (If Needed):
Visit webhook URL directly:
```
https://tajtrainer.tajweedflow.com/deploy.php?token=KVgdeJcmqv49sSuhnLHxjQ8GpNrP0ltR
```

---

## 🛡️ Security Best Practices

### 1. Protect deployment.log (Add to .htaccess)

In cPanel File Manager, edit `/home/tajweedf/tajtrainer.com/.htaccess`

Add these lines:
```apache
# Protect deployment files
<Files "deployment.log">
    Order Deny,Allow
    Deny from all
</Files>

<Files "deploy.php">
    Order Deny,Allow
    Deny from all
    # Allow GitHub webhook IPs
    Allow from 192.30.252.0/22
    Allow from 185.199.108.0/22
    Allow from 140.82.112.0/20
</Files>
```

### 2. Never Commit Sensitive Files
These are already in .gitignore:
- `.env` (database passwords)
- `vendor/` (composer packages)
- `storage/logs/` (logs may contain sensitive data)

### 3. Backup Before Major Changes
Before deploying big changes:
1. Backup database in cPanel → phpMyAdmin
2. Download `/home/tajweedf/tajtrainer` folder

---

## 🆘 Troubleshooting

### Problem: "Authentication failed" when pushing
**Solution:** Use Personal Access Token instead of password
- Generate token at: https://github.com/settings/tokens
- Use token as password when Git asks

### Problem: Webhook shows red X
**Solution:** 
1. Check deploy.php is uploaded to correct folder
2. Verify URL is accessible in browser
3. Check token matches in both webhook URL and deploy.php

### Problem: Deployment succeeds but changes not visible
**Solution:**
```bash
# SSH into cPanel Terminal and run:
cd /home/tajweedf/tajtrainer
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

### Problem: "Permission denied" errors in deployment.log
**Solution:**
```bash
# In cPanel Terminal:
chmod -R 755 /home/tajweedf/tajtrainer/storage
chmod -R 755 /home/tajweedf/tajtrainer/bootstrap/cache
```

### Problem: Git says "fatal: not a git repository"
**Solution:** You're in the wrong folder
```powershell
cd C:\laragon\www\tajtrainerV2
```

### Problem: Files not syncing to repository folder
**Solution:** 
- Check if REPO_PATH exists: `/home/tajweedf/repositories/tajtrainer`
- If not, create it or remove the rsync step from deploy.php

---

## ✅ Setup Complete Checklist

- [ ] Git configured with name and email
- [ ] GitHub repository created
- [ ] Code pushed to GitHub successfully
- [ ] deploy.php uploaded to `/home/tajweedf/tajtrainer.com`
- [ ] deploy.php permissions set to 644
- [ ] Deploy script tested manually (returns success JSON)
- [ ] GitHub webhook created with correct URL
- [ ] Test deployment completed successfully
- [ ] deployment.log shows successful deployment
- [ ] Website works: https://tajtrainer.tajweedflow.com
- [ ] .htaccess protection added (optional but recommended)

---

## 🎯 Quick Reference

### Your URLs:
- **Website:** https://tajtrainer.tajweedflow.com
- **Deploy Script:** https://tajtrainer.tajweedflow.com/deploy.php?token=KVgdeJcmqv49sSuhnLHxjQ8GpNrP0ltR
- **Deploy Log:** https://tajtrainer.tajweedflow.com/deployment.log
- **GitHub Repo:** https://github.com/yourusername/tajtrainer-v2

### Your Paths:
- **Public Folder:** `/home/tajweedf/tajtrainer.com`
- **Laravel App:** `/home/tajweedf/tajtrainer`
- **Git Repo:** `/home/tajweedf/repositories/tajtrainer`

### Daily Commands:
```powershell
git add .
git commit -m "Your message"
git push origin main
```

---

**🎊 Congratulations! Your automatic deployment is now set up!**

Every time you push to GitHub, your live site updates automatically! 🚀
