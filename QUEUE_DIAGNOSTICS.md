# Queue Processing Diagnostics

## Issue: Audio Analysis Taking Too Long or Not Processing

### Root Cause Analysis

The audio analysis uses **Laravel Queue Jobs** which require a **queue worker** to be running. If you see:
- ✅ "Assignment submitted!" message
- 🔄 "Analyzing..." badge with spinner
- ❌ But it never completes (stays analyzing forever)

**This means: The queue worker is NOT running**

---

## Quick Fix - Deploy to Production

On your cPanel server, set up the queue worker:

```bash
# 1. Pull latest code
cd ~/tajtrainer
git pull origin main

# 2. Run migrations (creates jobs table)
php artisan migrate

# 3. Setup cron job in cPanel
# Go to cPanel → Cron Jobs → Add New Cron Job
# Set to run every minute (* * * * *)
# Command:
cd /home/tajweedf/tajtrainer && php artisan queue:work --stop-when-empty --tries=3 --timeout=600 >> /dev/null 2>&1
```

### How Queue Processing Works

```
Student Submits → Job Added to Database → Queue Worker Picks Up Job → Processes Audio → Updates Submission
                                    ↓
                         **WORKER MUST BE RUNNING**
```

Without the worker, jobs sit in the database forever.

---

## Verify Queue is Working

### Check 1: Jobs Table Exists
```bash
php artisan tinker
DB::table('jobs')->count();  # Should return a number (0 if no pending jobs)
exit
```

### Check 2: Test Job Dispatch
```bash
# Submit an assignment, then check:
php artisan tinker
DB::table('jobs')->count();  # Should be > 0 if jobs are queued
exit
```

### Check 3: Process One Job Manually
```bash
php artisan queue:work --once
```

This will process ONE job from the queue. If it works, your setup is correct and you just need the cron job.

### Check 4: View Failed Jobs
```bash
php artisan queue:failed
```

If jobs are failing, this shows why.

---

## Animation Not Showing

The spinner animation requires:
1. ✅ CSS in the page (already added)
2. ✅ Submission with status='submitted' AND no score
3. ❌ **Page must refresh to see animation**

**Solution**: The page auto-refreshes every 5 seconds. If you don't see the spinner:
- Hard refresh the browser (Ctrl+F5)
- Clear browser cache
- Check browser console for CSS errors

---

## Local Development Setup

For testing locally:

```bash
# Terminal 1: Start queue worker
php artisan queue:work

# Terminal 2: Start Laravel server
php artisan serve

# Now submit assignments and watch Terminal 1 for job processing
```

---

## Production Checklist

- [ ] `QUEUE_CONNECTION=database` in `.env`
- [ ] `OPENAI_API_KEY=sk-proj-...` in `.env`
- [ ] Jobs table exists (`php artisan migrate`)
- [ ] Cron job configured in cPanel (runs every minute)
- [ ] Python `soundfile` installed (`pip install soundfile`)
- [ ] Storage symlink correct (`ls -la ~/tajtrainer.com/storage`)

---

## Troubleshooting Specific Issues

### Issue: "Analyzing..." Never Completes
**Cause**: Queue worker not running
**Fix**: Set up cron job (see above)

### Issue: Jobs Go to Failed Queue
**Cause**: Python script error or API issue
**Check logs**:
```bash
tail -100 ~/tajtrainer/storage/logs/laravel.log
```

**Retry failed jobs**:
```bash
php artisan queue:retry all
```

### Issue: OpenAI Feedback Not Generated
**Cause**: API key not passed to Python script
**Check**: Look for "OpenAI API key configured: Yes" in logs
**Fix**: Make sure `.env` has `OPENAI_API_KEY=sk-proj-...`

### Issue: "Processing..." Shows But Completes Without Score
**Cause**: Python script failed but didn't throw exception
**Check**: Python script output in Laravel logs
**Look for**: `{"status": "openai_failed"}` or `parselmouth.PraatError`

---

## Performance Expectations

| Phase | Time | What's Happening |
|-------|------|------------------|
| Upload | 1-2s | Saving file to storage |
| Transcription | 10-30s | AssemblyAI API call |
| Tajweed Analysis | 30-60s | Python MFCC + Parselmouth |
| AI Feedback | 5-10s | OpenAI GPT-3.5-turbo |
| **Total** | **45-100s** | Full processing |

With queue worker: Page refreshes every 5 seconds, shows completion after ~1-2 minutes
Without queue worker: Jobs never process, stays "Analyzing..." forever

---

## Manual Processing (Emergency Fallback)

If queue is completely broken, you can temporarily change to sync processing:

```bash
# In .env
QUEUE_CONNECTION=sync
```

This processes immediately (no queue) but:
- ⚠️ Will timeout on shared hosting (30-60s limit)
- ⚠️ User sees blank page during processing
- ⚠️ Not recommended for production

Better to fix the queue worker.

---

## Need Help?

1. Check Laravel logs: `tail -100 ~/tajtrainer/storage/logs/laravel.log`
2. Check if worker is running: `ps aux | grep queue:work`
3. Check pending jobs: `php artisan queue:monitor`
4. Test one job manually: `php artisan queue:work --once`
