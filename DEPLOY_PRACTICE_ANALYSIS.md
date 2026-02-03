# Practice Analysis Deployment Guide

## Changes Deployed (Commit: aaa0b4c)

### 1. **Reference Audio Comparison**
- Frontend passes reference audio URL to backend
- Backend downloads reference audio from CDN
- Python analyzer receives reference audio with `--reference=` flag
- Audio comparison logic ready for DTW algorithm enhancement

### 2. **Comprehensive Console Logging** (For Presentation)
- `=== PRACTICE PAGE INITIALIZING ===`
- Step 1-11 logging during verse loading
- `=== ANALYSIS STARTED ===` with blob details
- `=== SERVER RESPONSE RECEIVED ===` with status
- `=== ANALYSIS COMPLETE ===` with score and transcription
- `=== ANALYSIS PRESENTATION COMPLETE ===`

### 3. **Transcription Display**
- Shows Whisper AI transcribed text in results
- RTL (right-to-left) Arabic text direction
- Removes special tokens like `<|ar|><|transcribe|>`
- Large font (1.5rem) for visibility
- Detailed breakdown: pronunciation, tajweed, makharij, fluency

### 4. **Bug Fixes**
- Fixed `NameError: temp_wav_file` in Python analyzer
- Fixed UTF-8 encoding for Arabic text on Windows console
- Proper error handling throughout analysis flow

---

## Production Deployment Steps

### Step 1: SSH to Production Server
```bash
ssh tajtrainer@tajweedflow.com
# Or use your configured SSH alias
```

### Step 2: Navigate to Application Directory
```bash
cd ~/tajtrainer
```

### Step 3: Pull Latest Code
```bash
git pull origin main
```

Expected output:
```
remote: Enumerating objects: 23, done.
remote: Counting objects: 100% (23/23), done.
Updating 627bfdc..aaa0b4c
Fast-forward
 app/Http/Controllers/StudentController.php | 40 +++++++++++++--
 python/tajweed_analyzer.py                 | 13 +++--
 resources/views/practice/index.blade.php   | 80 +++++++++++++++++++++++++++---
```

### Step 4: Clear Caches
```bash
php artisan view:clear
php artisan cache:clear
php artisan config:clear
```

### Step 5: Verify Permissions
```bash
chmod -R 775 storage/app/public/practice_recordings
chown -R tajtrainer:tajtrainer storage/app/public/practice_recordings
```

### Step 6: Test Python Analyzer
```bash
# Test UTF-8 encoding fix
python3 python/tajweed_analyzer.py --help

# Should output without errors (no UnicodeEncodeError)
```

---

## Testing Checklist

### 1. Console Logging Verification
1. Open practice page: https://tajweedflow.com/student/practice
2. Open browser console (F12)
3. **Initial Load:**
   - ✅ Should see: `=== Practice Page Initializing ===`
   - ✅ Should see: `Current Surah: X, Current Ayah: Y`

4. **Load New Verse:**
   - ✅ Click "Load New Verse" button
   - ✅ Should see: `Step 1: Button clicked - starting verse load...`
   - ✅ Should see: Steps 2-11 logging API calls and responses
   - ✅ Should see: `=== Verse loaded successfully! ===`

5. **Record Audio:**
   - ✅ Click "Start Recording"
   - ✅ Should see: `Recording started...`
   - ✅ Click "Stop Recording" after 5 seconds
   - ✅ Should see: `Recording stopped`

6. **Analyze Recording:**
   - ✅ Click "Analyze My Recitation"
   - ✅ Should see: `=== ANALYSIS STARTED ===`
   - ✅ Should see: `✓ Recording blob validated`
   - ✅ Should see: `Blob size: XXXXX bytes`
   - ✅ Should see: `Building request data:`
   - ✅ Should see: `  - Surah: X, Ayah: Y`
   - ✅ Should see: `  - Expected text: [Arabic text]`
   - ✅ Should see: `  - Reference audio: https://cdn.islamic.network/...`
   - ✅ Should see: `=== SERVER RESPONSE RECEIVED ===`
   - ✅ Should see: `Response status: 200`
   - ✅ Should see: `=== ANALYSIS COMPLETE ===`
   - ✅ Should see: `Accuracy score: XX%`
   - ✅ Should see: `Transcribed text: [Arabic text]`
   - ✅ Should see: `=== ANALYSIS PRESENTATION COMPLETE ===`

### 2. Transcription Display
1. After analysis completes
2. **Results Section Should Show:**
   - ✅ Overall accuracy percentage (large font)
   - ✅ Section header: "Your Recitation (Transcribed):"
   - ✅ Arabic transcribed text (1.5rem font, RTL direction)
   - ✅ No special tokens like `<|ar|>` or `<|transcribe|>`
   - ✅ Detailed Breakdown grid with 4 metrics:
     - Pronunciation Accuracy: XX%
     - Tajweed Rules: XX%
     - Makharij (Articulation): XX%
     - Fluency & Rhythm: XX%

### 3. Reference Audio Verification
1. Check Laravel logs:
   ```bash
   tail -f storage/logs/laravel.log
   ```

2. **Should See:**
   ```
   [2026-02-XX] local.INFO: Downloading reference audio from: https://cdn.islamic.network/quran/audio/128/ar.alafasy/XXX.mp3
   [2026-02-XX] local.INFO: ✓ Reference audio downloaded: /path/to/storage/app/public/practice_recordings/ref_1234567890_abcdef.mp3
   ```

3. **Verify Reference Files:**
   ```bash
   ls -lh storage/app/public/practice_recordings/ref_*
   ```
   - Should show downloaded MP3 files with `ref_` prefix

### 4. Python Analyzer Verification
1. **Manual Test:**
   ```bash
   cd ~/tajtrainer
   
   # Find a recent recording
   ls -lt storage/app/public/practice_recordings/ | head -5
   
   # Test with sample
   python3 python/tajweed_analyzer.py \
       storage/app/public/practice_recordings/recording_1234567890.webm \
       "بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ" \
       --reference=storage/app/public/practice_recordings/ref_1234567890_abcdef.mp3
   ```

2. **Expected Output (JSON):**
   ```json
   {
     "whisper_transcription": "بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ",
     "madd_analysis": {
       "percentage": 80.0,
       "details": {...}
     },
     "overall_score": {
       "score": 75.5,
       "grade": "Good"
     },
     "reference_comparison": {
       "similarity": 85.0
     }
   }
   ```

3. **Should NOT See:**
   - ❌ `NameError: name 'temp_wav_file' is not defined`
   - ❌ `UnicodeEncodeError: 'charmap' codec can't encode`
   - ❌ Any Python exceptions

---

## Troubleshooting

### Issue 1: Console Shows No Logs
**Symptom:** Browser console empty or missing presentation logs

**Solution:**
```bash
# Clear view cache
php artisan view:clear

# Hard refresh browser (Ctrl+Shift+R or Cmd+Shift+R)
```

### Issue 2: Transcription Not Displayed
**Symptom:** Results show score but no transcribed text

**Check:**
1. Python analyzer returned `whisper_transcription` in JSON
2. Check Laravel logs for Python output errors
3. Verify Whisper model downloaded:
   ```bash
   ls -lh ~/.cache/huggingface/hub/models--tarteel-ai--whisper-base-ar-quran
   ```

**Solution:**
```bash
# Pre-cache Whisper model
python3 -c "from transformers import WhisperProcessor; WhisperProcessor.from_pretrained('tarteel-ai/whisper-base-ar-quran')"
```

### Issue 3: Reference Audio Not Downloaded
**Symptom:** Logs show "reference_audio_url parameter missing"

**Check:**
1. Browser console: Verify `reference_audio_url` in FormData logs
2. Server logs: Check if URL validation failed

**Solution:**
- Verify `currentAudioUrl` is populated in JavaScript
- Check CORS if CDN blocks requests

### Issue 4: Low Accuracy Scores (0% or very low)
**Symptom:** Analysis returns but score always 0% or very low

**Root Cause:** Reference audio comparison DTW algorithm needs enhancement

**Next Steps:**
1. Verify reference audio is being loaded in Python:
   ```bash
   grep "reference_loaded" storage/logs/laravel.log
   ```
   
2. If reference not loading:
   - Check file path passed to Python
   - Verify MP3 file exists and is readable
   
3. If reference loading but score still low:
   - **Enhancement needed:** DTW (Dynamic Time Warping) algorithm in Python
   - File: `python/tajweed_analyzer.py` around line 900
   - Current implementation: Basic comparison
   - **Improvement:** Implement MFCC feature comparison with DTW

---

## Performance Optimization (Optional)

### 1. Pre-cache Whisper Model
```bash
# Download model to cache directory (first use only)
python3 -c "from transformers import WhisperProcessor, WhisperForConditionalGeneration; processor = WhisperProcessor.from_pretrained('tarteel-ai/whisper-base-ar-quran'); model = WhisperForConditionalGeneration.from_pretrained('tarteel-ai/whisper-base-ar-quran')"
```

### 2. Clean Old Reference Files (Cron Job)
```bash
# Add to crontab (run daily at 2 AM)
crontab -e

# Add line:
0 2 * * * find ~/tajtrainer/storage/app/public/practice_recordings/ref_* -mtime +7 -delete
```

### 3. Enable OPcache (PHP)
Check `phpinfo()` for OPcache status:
```bash
php -i | grep opcache.enable
```

---

## Success Criteria

✅ **Frontend:**
- Console shows all presentation logs (12+ log statements)
- Verse loads without errors
- Recording works smoothly
- Analysis submits successfully

✅ **Backend:**
- Reference audio downloads from CDN
- Files saved in `practice_recordings/` folder
- Python command includes `--reference=` flag
- JSON response includes `whisper_transcription`

✅ **Results Display:**
- Overall accuracy score visible
- Transcribed Arabic text shows in RTL
- Detailed breakdown grid displays 4 metrics
- No JavaScript errors in console

✅ **Python Analyzer:**
- No `NameError` or `UnicodeEncodeError`
- JSON output includes all expected fields
- Reference audio processed successfully

---

## Rollback Plan (If Issues Found)

```bash
# SSH to production
ssh tajtrainer@tajweedflow.com
cd ~/tajtrainer

# Rollback to previous commit
git reset --hard 627bfdc

# Clear caches
php artisan view:clear
php artisan cache:clear

# Verify rollback
git log -1 --oneline
```

---

## Next Development Phase

### 1. Enhance DTW Audio Comparison
**Goal:** Improve accuracy scores by implementing proper audio alignment

**File:** `python/tajweed_analyzer.py`

**Implementation:**
```python
from scipy.spatial.distance import euclidean
from fastdtw import fastdtw

def compare_with_reference(self, y_student, y_reference):
    # Extract MFCC features
    mfcc_student = librosa.feature.mfcc(y=y_student, sr=self.sr, n_mfcc=13)
    mfcc_reference = librosa.feature.mfcc(y=y_reference, sr=self.sr_ref, n_mfcc=13)
    
    # Compute DTW distance
    distance, path = fastdtw(mfcc_student.T, mfcc_reference.T, dist=euclidean)
    
    # Convert to similarity percentage
    max_distance = max(len(mfcc_student.T), len(mfcc_reference.T)) * 100
    similarity = max(0, (1 - distance / max_distance) * 100)
    
    return similarity
```

### 2. Error Highlighting in Transcription
**Goal:** Show specific Tajweed errors in transcribed text

**Example:**
```javascript
// Highlight words with errors
var transcription = "بِسْمِ <span class='error' title='Madd too short'>اللَّهِ</span> الرَّحْمَٰنِ";
```

### 3. Real-time Feedback During Recording
**Goal:** Show live waveform and volume meter

**Tools:**
- Web Audio API for visualization
- Canvas element for waveform display

---

## Contact & Support

- **Developer:** GitHub @LxgenD30
- **Repository:** https://github.com/LxgenD30/TajTrainer
- **Production:** https://tajweedflow.com
- **Logs Location:** `storage/logs/laravel.log`

---

**Deployment Date:** February 3, 2026  
**Commit Hash:** aaa0b4c  
**Branch:** main  
**Status:** ✅ Ready for Production
