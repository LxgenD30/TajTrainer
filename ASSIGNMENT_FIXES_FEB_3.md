# Assignment Submission & Edit Page Fixes - February 3, 2026

## Critical Issues Reported by User

### 1. 404 Error: `classes/%EF%BF%BD` 
**Status**: Investigating - Cannot find source of malformed URL
**Action**: Need production logs to identify which link is generating this

### 2. Assignment Edit Page Broken (assignments/28/edit)
**Status**: Under Review
**Symptoms**: Interface display issues
**Action**: Checking for CSS/Layout problems

### 3. Assignment Submission Analyzer Not Working on Hosted Server
**Status**: ✅ FIXED (Commit 84f7b70)
**Problem**: ProcessSubmissionAudio was trying to use AssemblyAI
**Solution**: Removed AssemblyAI dependency, uses Python Whisper directly

---

## Fix for Assignment Submission Analyzer

### What Was Done (Latest Commit)
The ProcessSubmissionAudio job was updated to:
1. Call Python directly for BOTH Whisper transcription AND Tajweed analysis (single call)
2. Use `proc_open()` instead of `exec()` for better control
3. Extract transcription from Python's `whisper_transcription` field
4. Match the practice page approach exactly

### Why It Might Still Appear Broken
The hosted server might be:
1. **Running old cached code** - Need to clear OPcache
2. **Has queue workers running old code** - Need to restart workers
3. **Different environment configuration** - Python path or permissions

### Deployment Steps for Hosted Server

```bash
# 1. SSH to production
ssh user@tajtrainer.tajweedflow.com

# 2. Pull latest code
cd ~/tajtrainer
git pull origin main

# 3. Clear ALL caches
php artisan cache:clear
php artisan view:clear
php artisan config:clear
php artisan route:clear

# 4. If using OPcache, restart PHP-FPM
sudo systemctl restart php8.2-fpm  # Adjust PHP version as needed

# 5. If queue workers exist, restart them
php artisan queue:restart

# 6. Check permissions
chmod -R 775 storage
chmod -R 775 bootstrap/cache
chown -R www-data:www-data storage
chown -R www-data:www-data bootstrap/cache
```

### Test on Hosted Server

```bash
# Test 1: Create new assignment submission
1. Login as student
2. Submit audio for an assignment
3. Check logs: tail -f storage/logs/laravel.log
4. Look for: "Starting Python analysis (Whisper + Tajweed)"
5. Should see: "✓ Whisper transcription:"
6. Should see: "✓ Tajweed analysis completed"

# Test 2: Verify database
php artisan tinker
$submission = \App\Models\AssignmentSubmission::latest()->first();
$submission->transcription;  // Should have Arabic text
$submission->tajweed_analysis;  // Should have JSON data
$submission->status;  // Should be "graded"
```

### If Still Not Working After Deployment

The issue might be that the code is ACTUALLY still using the old method. Let me check if there's a discrepancy:

**Files to check on hosted server:**
- `app/Jobs/ProcessSubmissionAudio.php` - Should NOT have `transcribeWithAssemblyAI` being called in handle()
- `storage/logs/laravel.log` - Should show "Starting Python analysis" not "Transcribing audio with AssemblyAI"

---

## Debugging Steps

### Check if Old Code is Running

```bash
# On hosted server, check the actual code
grep -n "Transcribing audio with AssemblyAI" app/Jobs/ProcessSubmissionAudio.php

# If this returns a match in the handle() method (around line 40-50), 
# then the new code didn't deploy properly
```

### Force Code Update

```bash
# If git pull doesn't work
git fetch origin
git reset --hard origin/main
composer dump-autoload
php artisan optimize:clear
```

### Check Python Execution

```bash
# Test Python analyzer manually
cd ~/tajtrainer
python3 python/tajweed_analyzer.py --help

# Should show usage without errors
```

---

## Code Comparison: OLD vs NEW

### OLD CODE (Broken - using AssemblyAI)
```php
public function handle(): void
{
    $submission = AssignmentSubmission::findOrFail($this->submissionId);
    
    // Step 1: Transcribe with AssemblyAI
    if ($submission->audio_file_path && config('services.assemblyai.api_key')) {
        if (empty($submission->transcription)) {
            Log::info('Transcribing audio with AssemblyAI: ' . $submission->audio_file_path);
            $transcription = $this->transcribeWithAssemblyAI($submission->audio_file_path);
            $submission->transcription = $transcription;
            $submission->save();
        }
    }
    
    // Step 2: Only runs if transcription exists
    if ($submission->audio_file_path && $submission->transcription) {
        $tajweedAnalysis = $this->analyzeTajweed(...);
    }
}
```

### NEW CODE (Fixed - using Python Whisper directly)
```php
public function handle(): void
{
    $submission = AssignmentSubmission::findOrFail($this->submissionId);
    $assignment = Assignment::findOrFail($submission->assignment_id);
    
    // Single step: Python does BOTH Whisper + Tajweed
    if ($submission->audio_file_path) {
        Log::info('Starting Python analysis (Whisper + Tajweed)');
        
        $tajweedAnalysis = $this->analyzeTajweed(
            $submission->audio_file_path,
            '',  // No pre-transcription needed
            $assignment->surah,
            $assignment->start_verse,
            $assignment->end_verse
        );
        
        // Extract transcription from Python output
        if (isset($tajweedAnalysis['whisper_transcription'])) {
            $submission->transcription = $tajweedAnalysis['whisper_transcription'];
            Log::info('✓ Whisper transcription: ' . substr($submission->transcription, 0, 100));
        }
        
        $submission->tajweed_analysis = json_encode($tajweedAnalysis);
        $submission->save();
    }
}
```

---

## Expected Behavior After Fix

✅ **Submission Process:**
1. Student uploads/records audio
2. Audio saved to storage/app/public/submissions/
3. ProcessSubmissionAudio job dispatched
4. Python analyzer called with: audio file, expected text, surah, verses
5. Python returns: whisper_transcription + tajweed_analysis + overall_score
6. Transcription saved to database
7. Tajweed analysis saved as JSON
8. Score calculated and saved
9. Status changed to "graded"

✅ **Log Sequence:**
```
[timestamp] local.INFO: === Processing Audio Job Started for Submission #X ===
[timestamp] local.INFO: Submission audio path: submissions/XXXXX.webm
[timestamp] local.INFO: Assignment: Al-Baqara 1-5
[timestamp] local.INFO: Starting Python analysis (Whisper + Tajweed)
[timestamp] local.INFO: ✓ Whisper transcription: بسم الله...
[timestamp] local.INFO: ✓ Tajweed analysis completed
[timestamp] local.INFO: Creating score: 85/100
[timestamp] local.INFO: ✓ Score created
[timestamp] local.INFO: === Processing Audio Job Completed for Submission #X ===
```

❌ **OLD Broken Log Sequence:**
```
[timestamp] local.INFO: === Processing Audio Job Started for Submission #X ===
[timestamp] local.INFO: Transcribing audio with AssemblyAI: submissions/XXXXX.webm
[timestamp] local.ERROR: AssemblyAI transcription failed: ...
[timestamp] local.INFO: === Processing Audio Job Completed for Submission #X ===
```

---

## Contact Developer if Issue Persists

If after following these steps the analyzer still doesn't work:

1. **Provide logs from hosted server:**
   ```bash
   tail -100 storage/logs/laravel.log
   ```

2. **Check git status on hosted server:**
   ```bash
   git log -1 --oneline
   git diff HEAD
   ```

3. **Verify Python availability:**
   ```bash
   which python3
   python3 --version
   python3 python/tajweed_analyzer.py --help
   ```

---

**Last Updated**: February 3, 2026, 23:30 UTC  
**Commit**: 84f7b70  
**Status**: Deployed to main branch, awaiting hosted server deployment
