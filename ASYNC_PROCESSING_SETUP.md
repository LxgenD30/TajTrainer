# Async Audio Processing Setup

## Overview
The assignment submission now uses **Laravel queues** to process audio in the background, eliminating timeout issues on shared hosting.

## How It Works

### 1. **Submission Flow**
```
Student submits → Save to database → Dispatch job → Return success immediately
                                          ↓
                        [Background Processing 1-2 minutes]
                                          ↓
                  Transcribe (AssemblyAI) → Analyze (Python) → Create Score
```

### 2. **Status Flow**
- **`submitted`**: Audio is being processed in background
- **`graded`**: Processing complete, score created

### 3. **User Experience**
- ✅ **Instant feedback**: "Assignment submitted! Audio is being analyzed..."
- 🔄 **Auto-refresh**: Page refreshes every 10 seconds while processing
- 📊 **Real-time status**: Shows "🔄 Analyzing..." badge with pulse animation
- ✅ **Final result**: Badge changes to "✓ Graded: X/100" when complete

## Production Deployment on cPanel

### Step 1: Pull Latest Code
```bash
cd ~/tajtrainer
git pull origin main
```

### Step 2: Configure Queue Driver
Edit `~/tajtrainer/.env`:
```env
QUEUE_CONNECTION=database
```

### Step 3: Run Queue Migration (if needed)
```bash
php artisan queue:table
php artisan migrate
```

### Step 4: Start Queue Worker
**Option A: Using cron job (recommended for shared hosting)**
Add to cPanel Cron Jobs (run every minute):
```bash
* * * * * cd /home/tajweedf/tajtrainer && php artisan queue:work --stop-when-empty --tries=3 --timeout=600 >> /dev/null 2>&1
```

**Option B: Using background process** (if allowed by host):
```bash
nohup php artisan queue:work --timeout=600 --tries=3 &
```

**Option C: Manual testing**:
```bash
php artisan queue:work --once
```

### Step 5: Verify Queue is Working
Check jobs table:
```bash
php artisan tinker
DB::table('jobs')->count();  // Should show pending jobs
DB::table('failed_jobs')->count();  // Should be 0
```

## Important Notes

### Python Dependencies
Make sure `soundfile` is installed for .m4a conversion:
```bash
pip install soundfile
```

### Queue Benefits
1. ✅ **No timeouts**: Processing happens in background
2. ✅ **No blank pages**: User gets immediate response
3. ✅ **Retry logic**: Failed jobs automatically retry (3 times)
4. ✅ **Error handling**: Failures logged, submission stays "submitted" for manual grading

### Troubleshooting

**If submissions stay "submitted" forever:**
```bash
# Check if queue worker is running
ps aux | grep queue:work

# Manually process one job
php artisan queue:work --once

# Check failed jobs
php artisan queue:failed
```

**If you see errors:**
```bash
# View logs
tail -50 ~/tajtrainer/storage/logs/laravel.log

# Retry failed jobs
php artisan queue:retry all
```

**If OpenAI quota exceeded:**
- Submissions will still work, just without AI feedback
- Python analyzer continues to provide Tajweed scores
- Error logged in Laravel logs

## Files Changed
- ✅ `app/Jobs/ProcessSubmissionAudio.php` - Background processing job
- ✅ `app/Http/Controllers/StudentController.php` - Dispatch job instead of inline processing
- ✅ `resources/views/students/classroom-show.blade.php` - Auto-refresh UI
- ✅ `python/tajweed_analyzer.py` - Fixed .m4a compatibility

## Testing Locally
```bash
# Start queue worker in one terminal
php artisan queue:work

# Submit assignment in browser
# Watch terminal for job processing logs
```

## No Database Changes
This implementation uses the **existing** `status` column in `assignment_submissions`:
- No migrations needed
- Works with cloud database immediately
- Compatible with existing data
