# Quick Fix: Initialize Git on cPanel Server

If you got the "not a git repository" error, you need to initialize Git on your server.

## Option A: Use cPanel Terminal (Fastest)

1. Log into cPanel
2. Open "Terminal" 
3. Run these commands:

```bash
# Go to your Laravel folder
cd /home/tajweedf/tajtrainer

# Initialize git
git init

# Add your GitHub repository as remote
git remote add origin https://github.com/LxgenD30/TajTrainer.git

# Fetch the repository
git fetch origin

# Checkout main branch
git checkout -b main origin/main

# Verify it worked
git status
```

**Expected output:**
```
On branch main
Your branch is up to date with 'origin/main'.
```

## Option B: Use Simplified Deploy Script (No Git Required)

If you can't access Terminal, I've created a simpler version:

1. **Upload `deploy-simplified.php`** to cPanel (same location as deploy.php)
2. **Rename it** to `deploy.php` (replace the old one)
3. **Run this command in cPanel Terminal:**
   ```bash
   cd /home/tajweedf/tajtrainer
   git init
   git remote add origin https://github.com/LxgenD30/TajTrainer.git
   git fetch
   git checkout -b main origin/main
   ```
4. **Test deployment** again

## After Fixing

Test your deployment:
```
https://tajtrainer.tajweedflow.com/deploy.php?token=KVgdeJcmqv49sSuhnLHxjQ8GpNrP0ltR
```

Check the log:
```
https://tajtrainer.tajweedflow.com/deployment.log
```

## What We're Doing

The issue is that your Laravel folder (`/home/tajweedf/tajtrainer`) exists but isn't a Git repository yet. We need to:
1. Initialize Git there
2. Connect it to your GitHub repository
3. Pull the code

Then the automatic deployment will work!
