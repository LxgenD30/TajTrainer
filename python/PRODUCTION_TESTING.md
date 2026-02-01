# Production Testing Guide for cPanel Shared Hosting

## Issue Fixed
**OpenBLAS Threading Problem**: Shared hosting servers limit the number of threads per user. NumPy/SciPy use OpenBLAS which defaults to creating 32 threads, causing "Resource temporarily unavailable" errors and segmentation faults.

**Solution**: Force single-threaded mode for all numerical libraries by setting environment variables before importing numpy.

---

## Step 1: Pull Latest Code

```bash
cd ~/repositories/tajtrainerV2  # Or wherever your git repo is
git pull origin main
```

You should see:
- `python/check_dependencies.py` updated
- `python/tajweed_analyzer.py` updated

---

## Step 2: Verify Thread Limiting

Check that both files have these environment variables at the top:

```bash
cd ~/repositories/tajtrainerV2/python
grep -A3 "OPENBLAS_NUM_THREADS" check_dependencies.py tajweed_analyzer.py
```

Expected output:
```
check_dependencies.py:
os.environ['OPENBLAS_NUM_THREADS'] = '1'
os.environ['MKL_NUM_THREADS'] = '1'
os.environ['OMP_NUM_THREADS'] = '1'
os.environ['NUMEXPR_NUM_THREADS'] = '1'

tajweed_analyzer.py:
os.environ['OPENBLAS_NUM_THREADS'] = '1'
os.environ['MKL_NUM_THREADS'] = '1'
os.environ['OMP_NUM_THREADS'] = '1'
os.environ['NUMEXPR_NUM_THREADS'] = '1'
```

---

## Step 3: Run Dependency Checker

```bash
cd ~/repositories/tajtrainerV2/python
python3 check_dependencies.py
```

### Expected Result (Success)
```
============================================================
TajTrainer Python Dependencies Check
============================================================

Python Version: 3.9.25
Python Executable: /usr/local/bin/python3
Environment:
  OPENBLAS_NUM_THREADS: 1
  Working Directory: /home/tajweedf/repositories/tajtrainerV2/python

Checking dependencies...

✓ numpy                v2.3.5          - numpy
✓ scipy                v1.16.3         - scipy
✓ librosa              v0.11.0         - librosa
✓ parselmouth          v0.4.7          - parselmouth (Praat integration)
✓ fastdtw              vunknown        - fastdtw (Dynamic Time Warping)
✓ openai               v2.16.0         - openai (GPT-4 API)
✓ requests             v2.32.5         - requests (HTTP client)
✓ soundfile            v0.13.1         - soundfile (Audio I/O)

============================================================
Functional Tests
============================================================

Testing Parselmouth (Praat Integration)...
✓ Parselmouth functional
  - Successfully created Sound object
  - Extracted pitch: 75.0 - 600.0 Hz
  - Extracted 3 formants
  - Measured intensity

Testing FastDTW (Dynamic Time Warping)...
✓ FastDTW functional
  - DTW distance: X.XX

============================================================
✓✓✓ All systems ready! ✓✓✓
============================================================
```

### What If There Are Still Issues?

#### Issue: "CPU dispatcher tracer already initialized"
**Status**: ⚠ WARNING (not critical)
**Reason**: Some packages trigger this warning but still work
**Action**: Safe to ignore if other tests pass

#### Issue: Still getting pthread_create errors
**Possible causes**:
1. Old Python processes still running
   ```bash
   killall python3
   python3 check_dependencies.py
   ```

2. Need to set in shell profile
   ```bash
   echo 'export OPENBLAS_NUM_THREADS=1' >> ~/.bashrc
   echo 'export MKL_NUM_THREADS=1' >> ~/.bashrc
   echo 'export OMP_NUM_THREADS=1' >> ~/.bashrc
   echo 'export NUMEXPR_NUM_THREADS=1' >> ~/.bashrc
   source ~/.bashrc
   ```

3. Python cache needs clearing
   ```bash
   cd ~/repositories/tajtrainerV2/python
   find . -type d -name "__pycache__" -exec rm -rf {} +
   rm -rf ~/.cache/pip
   ```

#### Issue: Segmentation fault still occurs
**Cause**: Some packages incompatible with shared hosting
**Options**:
1. Try older numpy version
   ```bash
   pip3 install --user --force-reinstall numpy==1.24.3 scipy==1.10.1
   ```

2. Contact hosting support to increase RLIMIT_NPROC

3. Consider VPS/dedicated server for heavy numerical computing

---

## Step 4: Test Tajweed Analyzer Directly

Create a test audio file or use an existing one:

```bash
cd ~/repositories/tajtrainerV2
python3 python/tajweed_analyzer.py "/path/to/audio.mp3" "بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ"
```

### Expected Output
JSON with analysis results:
```json
{
  "status": "success",
  "tajweed_scores": {
    "madd_elongation_accuracy": 85.5,
    "idgham_bila_ghunnah_accuracy": 78.2,
    "idgham_bi_ghunnah_accuracy": 92.1,
    "overall_tajweed_score": 85.3
  },
  "ai_feedback": "..."
}
```

### If It Fails
Check for:
1. **Missing audio file**: Ensure path is correct
2. **Module errors**: Re-run dependency check
3. **Python errors**: Check Laravel logs at `storage/logs/laravel.log`

---

## Step 5: Copy to Web Directory

If code is in a git repository outside public_html:

```bash
# Sync Python files to web directory
cp -r ~/repositories/tajtrainerV2/python ~/public_html/
```

Or if using symlinks:
```bash
cd ~/public_html
ln -sf ~/repositories/tajtrainerV2/python python
```

---

## Step 6: Test Through Laravel

### A. Submit a Test Assignment

1. Login as student
2. Go to an assignment
3. Record or upload audio
4. Submit

### B. Check Laravel Logs

```bash
tail -f ~/public_html/storage/logs/laravel.log | grep -i python
```

Look for:
- ✅ "Python audio analysis completed"
- ✅ Tajweed scores > 0 and < 100
- ✅ No "ModuleNotFoundError"
- ✅ No "pthread_create failed"
- ❌ "No output from analysis script" = Python crashed

### C. Check Submission Result

Expected behavior:
- ✅ Score is NOT 75/100 (unless coincidentally accurate)
- ✅ Tajweed percentages shown (Madd, Idgham, etc.)
- ✅ AI feedback displays with specific advice
- ✅ Audio playback works
- ✅ Download button works

---

## Step 7: Performance Monitoring

### Check Python Execution Time
```bash
tail -f ~/public_html/storage/logs/laravel.log | grep "Python execution time"
```

Expected: 5-30 seconds per submission
Concerning: >60 seconds (may timeout)

### If Too Slow
Single-threaded mode is slower. Consider:
1. Optimize audio file size (compress to 64kbps MP3)
2. Increase PHP timeout in `.htaccess`:
   ```
   php_value max_execution_time 300
   ```
3. Use job queue for background processing

---

## Step 8: Set Global Environment (Optional)

To ensure environment variables persist across all Python executions:

### Option A: Shell Profile (Recommended)
```bash
cat >> ~/.bashrc << 'EOF'
# Limit threading for numerical libraries (shared hosting)
export OPENBLAS_NUM_THREADS=1
export MKL_NUM_THREADS=1
export OMP_NUM_THREADS=1
export NUMEXPR_NUM_THREADS=1
EOF
source ~/.bashrc
```

### Option B: Apache Environment (.htaccess)
Add to `public_html/.htaccess`:
```apache
<IfModule mod_env.c>
    SetEnv OPENBLAS_NUM_THREADS 1
    SetEnv MKL_NUM_THREADS 1
    SetEnv OMP_NUM_THREADS 1
    SetEnv NUMEXPR_NUM_THREADS 1
</IfModule>
```

### Option C: PHP Environment (config/app.php)
Add to `config/app.php`:
```php
// Set thread limits for Python numerical libraries
putenv('OPENBLAS_NUM_THREADS=1');
putenv('MKL_NUM_THREADS=1');
putenv('OMP_NUM_THREADS=1');
putenv('NUMEXPR_NUM_THREADS=1');
```

---

## Troubleshooting Common Issues

### Issue: "No such file or directory: python3"
**Fix**: Use full path in StudentController.php
```php
$pythonPath = '/usr/local/bin/python3'; // or find with: which python3
```

### Issue: "Permission denied" on tajweed_analyzer.py
**Fix**: Make executable
```bash
chmod +x ~/public_html/python/tajweed_analyzer.py
```

### Issue: Audio file not found
**Fix**: Check storage symlink
```bash
cd ~/public_html
ls -la public/storage  # Should point to ../storage/app/public
php artisan storage:link  # If not linked
```

### Issue: OpenAI API errors
**Fix**: Verify API key in `.env`
```bash
grep OPENAI_API_KEY ~/public_html/.env
```

### Issue: Still getting 75/100 scores
**Causes**:
1. Python script failing silently → Check logs
2. Wrong Python path → Use absolute path
3. Timeout → Increase max_execution_time
4. No OpenAI key → AI feedback fails, triggers fallback

---

## Success Criteria Checklist

- [ ] Dependency check passes without pthread errors
- [ ] No segmentation faults
- [ ] Direct Python test produces JSON output
- [ ] Web submission completes successfully
- [ ] Score is NOT always 75/100
- [ ] Tajweed percentages display correctly
- [ ] AI feedback shows specific advice
- [ ] Audio playback and download work
- [ ] No errors in Laravel logs
- [ ] Response time < 30 seconds

---

## Contact Points

If still having issues after following this guide:

1. **Check Laravel logs**: `storage/logs/laravel.log`
2. **Check PHP error log**: Usually at `~/public_html/error_log`
3. **Server info**: Run `python3 -m sysconfig` to see compilation flags
4. **Hosting support**: Ask about RLIMIT_NPROC and thread limits

---

## Performance Expectations

| Metric | Shared Hosting | VPS/Dedicated |
|--------|---------------|---------------|
| Analysis Time | 5-30 seconds | 2-10 seconds |
| Concurrent Users | 1-3 | 10+ |
| Max Audio Length | 5 minutes | 20+ minutes |
| Thread Count | 1 (forced) | 4-8 (optimal) |

Single-threaded mode is a necessary compromise for shared hosting stability.
