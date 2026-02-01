# TajTrainer Python Analyzer Deployment Guide

## Overview
TajTrainer uses a Python-based audio analyzer that performs phonetic analysis on Quran recitation submissions. The analyzer uses Praat Parselmouth for formant analysis and FastDTW for reference audio comparison.

## Prerequisites

### Python Requirements
- Python 3.8 or higher
- pip (Python package manager)

### Required Python Packages
```bash
pip install -r requirements.txt
```

Key dependencies:
- `praat-parselmouth>=0.4.3` - Phonetic analysis (formants, pitch, intensity)
- `fastdtw>=0.3.4` - Dynamic Time Warping for reference comparison
- `librosa>=0.10.0` - Audio processing and feature extraction
- `numpy>=1.24.0` - Numerical operations
- `scipy>=1.10.0` - Scientific computing
- `openai>=1.0.0` - GPT-4 AI feedback generation
- `requests>=2.31.0` - HTTP requests for API calls

## Installation Steps

### 1. Check Current Python Environment
```bash
python3 --version
python3 -m pip --version
```

### 2. Install Dependencies
```bash
cd python/
pip install -r requirements.txt
```

Or on production with specific Python path:
```bash
/usr/bin/python3 -m pip install -r requirements.txt
```

### 3. Verify Installation
```bash
python3 check_dependencies.py
```

This will:
- Check all required packages are installed
- Verify package versions
- Test Parselmouth functionality
- Test FastDTW functionality
- Show detailed error messages if anything is missing

Expected output:
```
============================================================
TajTrainer Python Dependencies Check
============================================================

Python Version: 3.10.12
Python Executable: /usr/bin/python3

Checking dependencies...

✓ numpy                v1.24.3         - numpy
✓ scipy                v1.11.2         - scipy
✓ librosa              v0.10.1         - librosa
✓ parselmouth          v0.4.3          - parselmouth (Praat integration)
✓ fastdtw              v0.3.4          - fastdtw (Dynamic Time Warping)
✓ openai               v1.3.0          - openai (GPT-4 API)
✓ requests             v2.31.0         - requests (HTTP client)
✓ soundfile            v0.12.1         - soundfile (Audio I/O)

============================================================
✓ All dependencies are installed and functional!
============================================================
```

## Configuration

### 1. Laravel Environment Variables
Add to `.env`:
```env
# Python configuration
PYTHON_PATH=/usr/bin/python3

# OpenAI API for AI feedback
OPENAI_API_KEY=your-openai-api-key-here
```

### 2. Storage Permissions
Ensure Laravel can write to storage directories:
```bash
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/
chown -R www-data:www-data storage/
chown -R www-data:www-data bootstrap/cache/
```

### 3. Storage Link
Create symbolic link for public storage:
```bash
php artisan storage:link
```

## Troubleshooting

### Problem: "No output from analysis script"

**Symptoms:**
- All submissions get 75/100 score
- AI feedback not displayed
- Message: "No output from analysis script"

**Solutions:**
1. Check Laravel logs: `storage/logs/laravel.log`
2. Look for Python errors in log entries
3. Run dependency checker:
   ```bash
   cd python/
   python3 check_dependencies.py
   ```
4. Check Python path:
   ```bash
   which python3
   ```
5. Test analyzer manually:
   ```bash
   python3 tajweed_analyzer.py "/path/to/audio.mp3" "بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ"
   ```

### Problem: "Missing Python dependencies"

**Symptoms:**
- Log shows: "ModuleNotFoundError: No module named 'parselmouth'"
- AI feedback box shows dependency error

**Solutions:**
1. Install missing packages:
   ```bash
   pip install praat-parselmouth fastdtw
   ```
2. If using virtual environment:
   ```bash
   source /path/to/venv/bin/activate
   pip install -r requirements.txt
   ```
3. Check if correct Python is used:
   ```bash
   python3 -m pip list | grep parselmouth
   ```

### Problem: Audio playback not working

**Symptoms:**
- Audio player shows error
- Download button shows "file not available"

**Solutions:**
1. Check storage link exists:
   ```bash
   ls -la public/storage
   ```
2. Recreate if missing:
   ```bash
   php artisan storage:link
   ```
3. Check file permissions:
   ```bash
   ls -la storage/app/public/submissions/
   ```
4. Verify audio file path in database matches actual file location

### Problem: Python analyzer times out

**Symptoms:**
- Log shows: "Python analysis timed out after 300 seconds"
- Large audio files fail

**Solutions:**
1. Check audio file size and duration
2. Increase timeout in StudentController.php:
   ```php
   $timeout = 600; // Increase from 300 to 600 seconds
   ```
3. Check server resources (CPU, memory)
4. Consider compressing audio before analysis

### Problem: AI feedback not generating

**Symptoms:**
- Tajweed analysis works but no AI feedback
- Missing OpenAI API key

**Solutions:**
1. Check OpenAI API key in `.env`:
   ```env
   OPENAI_API_KEY=sk-...
   ```
2. Clear config cache:
   ```bash
   php artisan config:clear
   ```
3. Test API key:
   ```bash
   python3 -c "import openai; openai.api_key='your-key'; print('OK')"
   ```
4. Check OpenAI API credits/usage

## Testing

### Manual Test
1. Upload a test audio file
2. Check Laravel logs for detailed Python execution info:
   ```bash
   tail -f storage/logs/laravel.log
   ```
3. Look for these log entries:
   - `=== Python Tajweed Analysis Started ===`
   - `Python command: ...`
   - `Full command: ...`
   - `Python exit code: 0`
   - `=== Python Analysis Successful ===`

### Automated Test
Run the test script:
```bash
php artisan test --filter=TajweedAnalysisTest
```

## Performance Optimization

### 1. Cache Reference Audio
Reference audio from AlQuran.cloud is cached to `storage/app/reference_audio/`

### 2. Reduce Audio Processing Time
- Convert audio to mono 16kHz before analysis
- Limit audio duration (e.g., max 5 minutes)

### 3. Use Queue for Long Processing
Consider moving Python analysis to Laravel queue:
```php
ProcessTajweedAnalysis::dispatch($submission);
```

## Monitoring

### Check Logs
```bash
# Laravel logs
tail -f storage/logs/laravel.log | grep Python

# Web server logs
tail -f /var/log/apache2/error.log
tail -f /var/log/nginx/error.log
```

### Key Metrics
- Python analyzer success rate
- Average processing time per audio
- Error types and frequencies
- AI feedback generation success rate

## Production Checklist

- [ ] Python 3.8+ installed
- [ ] All dependencies installed (`check_dependencies.py` passes)
- [ ] `.env` configured with `PYTHON_PATH` and `OPENAI_API_KEY`
- [ ] Storage permissions correct (775)
- [ ] Storage link created (`php artisan storage:link`)
- [ ] Test submission works end-to-end
- [ ] Logs show successful Python execution
- [ ] AI feedback displays correctly
- [ ] Audio playback works
- [ ] Download button works
- [ ] Error messages are informative

## Support

For issues:
1. Check Laravel logs: `storage/logs/laravel.log`
2. Run dependency checker: `python3 check_dependencies.py`
3. Test Python analyzer manually
4. Check file permissions
5. Verify environment variables

## Recent Enhancements

### v2.0 - Phonetic Analysis (Current)
- Replaced simple peak detection with Praat Parselmouth formant analysis
- Added reference audio comparison using Dynamic Time Warping (DTW)
- Integrated OpenAI GPT-4 for personalized AI feedback
- Enhanced error logging and debugging
- Fixed audio playback and download issues
- Always show AI feedback box (with error messages if analysis fails)

### Key Features
1. **Formant Analysis**: Uses Praat to analyze vowel quality and Madd elongations
2. **Reference Comparison**: Downloads correct recitation from AlQuran.cloud and compares
3. **AI Feedback**: GPT-4 generates personalized improvement suggestions
4. **Comprehensive Logging**: Detailed logs for troubleshooting production issues
5. **Graceful Degradation**: Shows helpful error messages instead of silent failures
