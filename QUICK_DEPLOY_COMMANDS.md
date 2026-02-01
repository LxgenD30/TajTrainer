# Quick Deployment Commands for TajTrainer V2

## Initial Setup (One-time)
```powershell
# 1. Initialize Git
git init

# 2. Add remote repository (CHANGE THE URL!)
git remote add origin https://github.com/yourusername/tajtrainer-v2.git

# 3. Add all files
git add .

# 4. First commit
git commit -m "Initial commit - TajTrainer V2"

# 5. Push to GitHub
git branch -M main
git push -u origin main
```

---

## Daily Deployment (After Initial Setup)

### Simple 3-Step Process:
```powershell
# 1. Add all changed files
git add .

# 2. Commit with a message
git commit -m "Describe your changes here"

# 3. Push to GitHub (auto-deploys to cPanel!)
git push origin main
```

---

## Useful Git Commands

### Check what changed
```powershell
git status
```

### Add specific files only
```powershell
git add path/to/file.php
git add app/Http/Controllers/StudentController.php
```

### View commit history
```powershell
git log --oneline -10
```

### Undo last commit (keep changes)
```powershell
git reset --soft HEAD~1
```

### Pull latest changes (if working from multiple computers)
```powershell
git pull origin main
```

### Create a new branch for testing
```powershell
git checkout -b feature-name
git push origin feature-name
```

### Switch back to main branch
```powershell
git checkout main
```

---

## Deployment Verification

### Check deployment log on server
```
https://tajtrainer.tajweedflow.com/deployment.log
```

### Check GitHub webhook status
1. Go to: https://github.com/LxgenD30/TajTrainer/settings/hooks
2. Click on your webhook
3. View "Recent Deliveries"
4. Green checkmark = successful deployment ✓

---

## Emergency: Rollback to Previous Version

```powershell
# 1. View commit history
git log --oneline

# 2. Copy the commit hash you want to revert to (e.g., abc1234)

# 3. Reset to that commit
git reset --hard abc1234

# 4. Force push (BE CAREFUL!)
git push origin main --force

# 5. Webhook will auto-deploy the old version
```

---

## Common Issues & Fixes

### Issue: "Permission denied"
```powershell
# Check your Git credentials
git config --global user.email
git config --global user.name

# Or use GitHub Personal Access Token
# Settings → Developer settings → Personal access tokens → Generate new token
# Then use: https://TOKEN@github.com/yourusername/repo.git
```

### Issue: "Merge conflicts"
```powershell
# Pull latest changes first
git pull origin main

# Fix conflicts in files (marked with <<<< ==== >>>>)
# Then:
git add .
git commit -m "Resolved merge conflicts"
git push origin main
```

### Issue: "Already up to date" but changes not deployed
```powershell
# Trigger webhook manually by making empty commit
git commit --allow-empty -m "Trigger deployment"
git push origin main
```

---

## Best Practices

1. **Commit often** with meaningful messages
2. **Test locally** before pushing
3. **Never commit** `.env` file or sensitive credentials
4. **Check deployment.log** after every push
5. **Backup database** before running migrations on production

---

**Bookmark this file for quick reference!**
