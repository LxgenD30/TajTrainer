# 🚀 Final Steps - Complete Your Deployment Setup

## ✅ What You've Done So Far:
- [x] Git configured
- [x] GitHub repository created: https://github.com/LxgenD30/TajTrainer
- [x] Code pushed to GitHub successfully
- [x] deploy.php created with correct paths

---

## 🎯 NEXT: Set Up Git on Your cPanel Server

### Step 1: Log into cPanel Terminal
1. Go to your cPanel: https://tajweedflow.com:2083
2. Find and click **"Terminal"** (usually under Advanced section)

### Step 2: Run These Commands (Copy-Paste Each One)

```bash
# Go to your Laravel application folder
cd /home/tajweedf/tajtrainer

# Initialize git repository
git init

# Add your GitHub repository as remote
git remote add origin https://github.com/LxgenD30/TajTrainer.git

# Fetch all code from GitHub
git fetch origin

# Checkout the main branch
git checkout -b main origin/main

# Verify it worked
git status
```

**Expected output:**
```
On branch main
Your branch is up to date with 'origin/main'.
nothing to commit, working tree clean
```

---

## 🔗 THEN: Set Up GitHub Webhook (Auto-Deploy)

### Step 1: Go to GitHub Webhook Settings
Visit: https://github.com/LxgenD30/TajTrainer/settings/hooks

Click **"Add webhook"**

### Step 2: Configure Webhook

**Payload URL:**
```
https://tajtrainer.tajweedflow.com/deploy.php?token=KVgdeJcmqv49sSuhnLHxjQ8GpNrP0ltR
```

**Content type:**
- Select: `application/json`

**Which events:**
- Select: "Just the push event"

**Active:**
- ✓ Check this box

Click **"Add webhook"**

---

## 🧪 Test Your Setup

### Test 1: Manual Deployment
Visit this URL in your browser:
```
https://tajtrainer.tajweedflow.com/deploy.php?token=KVgdeJcmqv49sSuhnLHxjQ8GpNrP0ltR
```

**Expected result:** `{"status":"success"...}`

### Test 2: Check Deployment Log
```
https://tajtrainer.tajweedflow.com/deployment.log
```

Should show successful deployment!

### Test 3: Automatic Deployment
Make a small change and push:

```powershell
# In your local project
echo "Test" > test.txt
git add test.txt
git commit -m "Test auto-deployment"
git push origin main
```

Check webhook delivery: https://github.com/LxgenD30/TajTrainer/settings/hooks

---

## 📋 Quick Reference

### Your Setup:
- **GitHub Repo:** https://github.com/LxgenD30/TajTrainer
- **Website:** https://tajtrainer.tajweedflow.com
- **Deploy Script:** https://tajtrainer.tajweedflow.com/deploy.php?token=KVgdeJcmqv49sSuhnLHxjQ8GpNrP0ltR
- **Deploy Log:** https://tajtrainer.tajweedflow.com/deployment.log
- **Webhook Settings:** https://github.com/LxgenD30/TajTrainer/settings/hooks

### cPanel Paths:
- **Public Folder:** `/home/tajweedf/tajtrainer.com` (deploy.php is here)
- **Laravel App:** `/home/tajweedf/tajtrainer` (your code is here)

### Your Secret Token:
```
KVgdeJcmqv49sSuhnLHxjQ8GpNrP0ltR
```

---

## 🎊 After Setup Complete

Your workflow will be:
```powershell
# Make changes to your code
# Test locally

# Deploy to production (just 3 commands!)
git add .
git commit -m "Description of changes"
git push origin main

# Automatic deployment happens in 10-30 seconds!
```

---

## ⚠️ Important Notes

1. **Before running Terminal commands:** Make sure you have your current `.env` file backed up on the server
2. **The git checkout command** will replace files in `/home/tajweedf/tajtrainer` with code from GitHub
3. **Your .env file won't be affected** (it's in .gitignore)
4. **Upload deploy.php** to `/home/tajweedf/tajtrainer.com` if you haven't already

---

**Start with Step 1 (cPanel Terminal) and let me know if you need help!** 🚀
