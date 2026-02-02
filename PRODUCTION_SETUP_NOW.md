# IMMEDIATE FIX - Process Those 2 Pending Jobs

You have 2 jobs stuck in the queue. Let's process them right now:

## Step 1: Process the Pending Jobs Manually

```bash
# This will process the 2 pending jobs immediately
php artisan queue:work --once --tries=3 --timeout=600

# Run it again for the second job
php artisan queue:work --once --tries=3 --timeout=600

# Verify they're processed
php artisan tinker
DB::table('jobs')->count();  // Should be 0 now
exit
```

## Step 2: Set Up Cron Job (REQUIRED - Do This Now!)

Without this, every future submission will get stuck like these 2 jobs.

### Option A: Using cPanel Cron Jobs (RECOMMENDED)

1. **Log into cPanel**
2. **Go to**: Cron Jobs
3. **Add New Cron Job**:
   - Common Settings: **Every Minute** or set manually:
     - Minute: `*`
     - Hour: `*`
     - Day: `*`
     - Month: `*`
     - Weekday: `*`
   - Command:
     ```bash
     /usr/local/bin/php /home/tajweedf/tajtrainer/artisan queue:work --stop-when-empty --tries=3 --timeout=600 >> /dev/null 2>&1
     ```
4. **Click Add**

### Option B: Using SSH Crontab

```bash
# Edit crontab
crontab -e

# Add this line:
* * * * * cd /home/tajweedf/tajtrainer && /usr/local/bin/php artisan queue:work --stop-when-empty --tries=3 --timeout=600 >> /dev/null 2>&1

# Save and exit (Ctrl+X, then Y, then Enter)

# Verify it was added
crontab -l
```

### Option C: Keep a Background Worker Running (Alternative)

```bash
# Start worker in background
nohup php artisan queue:work --tries=3 --timeout=600 > /home/tajweedf/tajtrainer/storage/logs/queue-worker.log 2>&1 &

# Check if it's running
ps aux | grep "queue:work"

# To stop it later
ps aux | grep "queue:work"
kill <PID>
```

## Step 3: Verify Everything Works

```bash
# 1. Make sure no pending jobs
php artisan tinker
DB::table('jobs')->count();  // Should be 0
exit

# 2. Submit a NEW assignment

# 3. Check jobs are processed quickly
php artisan tinker
DB::table('jobs')->count();  // Should go from 1 -> 0 within seconds
exit

# 4. Check Laravel logs
tail -50 ~/tajtrainer/storage/logs/laravel.log
```

## What Each Option Does

| Option | How It Works | Best For |
|--------|-------------|----------|
| **Cron (A)** | Runs worker every minute, processes jobs, exits | Shared hosting, cPanel |
| **Crontab (B)** | Same as A, via command line | SSH access |
| **Background (C)** | Worker runs 24/7 | VPS/Dedicated server |

**For shared hosting (your case): Use Option A (cPanel Cron Jobs)**

## Expected Behavior After Setup

1. Student submits assignment
2. Job added to queue
3. Within 60 seconds: Cron job runs, processes the job
4. Student sees: "Analyzing..." → "✓ Graded: X/100"
5. Page auto-refreshes every 5 seconds

## Troubleshooting

### If jobs still not processing:

```bash
# Check PHP path
which php
# Use the output in your cron command

# Test cron command manually
cd /home/tajweedf/tajtrainer && /usr/local/bin/php artisan queue:work --once

# If it works, your cron is set up wrong
# If it fails, check the error
```

### If you get permission errors:

```bash
chmod +x /home/tajweedf/tajtrainer/artisan
```

### If you get "Class not found" errors:

```bash
cd ~/tajtrainer
composer dump-autoload
```

## Quick Checklist

- [ ] Process the 2 pending jobs manually (`php artisan queue:work --once` twice)
- [ ] Set up cron job in cPanel (every minute)
- [ ] Verify cron is working (submit test assignment)
- [ ] Check Laravel logs for any errors
- [ ] Confirm `.env` has `QUEUE_CONNECTION=database` and `OPENAI_API_KEY=sk-...`

## Need to See What's in Those Jobs?

```bash
php artisan tinker
DB::table('jobs')->select('id', 'queue', 'created_at')->get();
exit
```

This shows when they were created and which queue they're in.
