# OpenAI Setup Guide for TajTrainer V2

## Overview
TajTrainer V2 uses OpenAI's GPT-3.5 Turbo to generate intelligent, personalized feedback for Tajweed recitation practice. This provides students with specific, actionable guidance based on their performance.

## Prerequisites
- OpenAI account
- Credit card (OpenAI requires payment setup, though costs are minimal)

## Setup Steps

### 1. Get OpenAI API Key

1. Go to [https://platform.openai.com/signup](https://platform.openai.com/signup)
2. Create an account or sign in
3. Navigate to **API Keys** section: [https://platform.openai.com/api-keys](https://platform.openai.com/api-keys)
4. Click **"Create new secret key"**
5. Give it a name (e.g., "TajTrainer Production")
6. **Copy the API key immediately** (you won't be able to see it again!)
   - Format: `sk-proj-...` or `sk-...`

### 2. Add API Key to Laravel

#### Local Development (.env file)
1. Open `c:\laragon\www\tajtrainerV2\.env`
2. Add this line:
```env
OPENAI_API_KEY=sk-proj-YOUR_ACTUAL_API_KEY_HERE
```

#### Production Server (cPanel)
1. Log into cPanel
2. Go to **File Manager**
3. Navigate to `/home/tajweedf/tajtrainer/`
4. Edit `.env` file
5. Add the same line:
```env
OPENAI_API_KEY=sk-proj-YOUR_ACTUAL_API_KEY_HERE
```
6. Save the file

### 3. Update Laravel Configuration

Add to `config/services.php`:

```php
return [
    // ... existing services

    'openai' => [
        'api_key' => env('OPENAI_API_KEY'),
    ],
];
```

### 4. Make API Key Available to Python

The Python script automatically reads from environment variables. Laravel should pass it:

```php
// In your controller
$env_vars = [
    'OPENAI_API_KEY' => config('services.openai.api_key'),
];

$command = "python " . base_path('python/tajweed_analyzer.py') . " $audioPath \"$expectedText\"";

foreach ($env_vars as $key => $value) {
    putenv("$key=$value");
}

$output = shell_exec($command);
```

### 5. Verify Setup

Test the analyzer with OpenAI enabled:

```powershell
cd c:\laragon\www\tajtrainerV2\python

$env:OPENAI_API_KEY = "sk-proj-YOUR_KEY_HERE"

python tajweed_analyzer.py "path/to/audio.mp3" "بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ"
```

Look for `"ai_feedback"` in the JSON output.

### 6. Optional: Disable OpenAI (for testing)

If you want to test without OpenAI:

```powershell
python tajweed_analyzer.py "audio.mp3" "text" --no-openai
```

## Cost Estimation

### GPT-3.5 Turbo Pricing (as of 2024)
- **Input**: $0.50 per 1M tokens (~$0.0005 per 1K tokens)
- **Output**: $1.50 per 1M tokens (~$0.0015 per 1K tokens)

### Per-Request Cost
Each feedback generation:
- Input: ~300 tokens = $0.00015
- Output: ~150 tokens = $0.000225
- **Total per request**: ~$0.000375 (less than 1 cent)

### Monthly Estimates
- 100 students, 10 practices each = 1,000 requests
- **Monthly cost**: ~$0.38 (less than 40 cents)
- 1,000 students = ~$38/month

## Security Best Practices

### 1. Never Commit API Keys
Add to `.gitignore`:
```
.env
.env.*
!.env.example
```

### 2. Rotate Keys Regularly
- Every 3-6 months, create a new key
- Delete old keys from OpenAI dashboard

### 3. Set Usage Limits
1. Go to [OpenAI Usage Limits](https://platform.openai.com/account/limits)
2. Set a monthly budget (e.g., $10)
3. Enable email alerts at 75% and 90%

### 4. Monitor Usage
Check your usage: [https://platform.openai.com/usage](https://platform.openai.com/usage)

## Troubleshooting

### Error: "OpenAI API key not found"
- Check `.env` file has `OPENAI_API_KEY=sk-...`
- Restart Laravel server: `php artisan config:clear`
- Verify Python script can access: `echo $env:OPENAI_API_KEY`

### Error: "Rate limit exceeded"
- OpenAI has rate limits for free tier
- Upgrade to paid tier for higher limits
- Implement caching to reduce API calls

### Error: "Invalid API key"
- Check key starts with `sk-`
- Ensure no extra spaces or quotes
- Verify key hasn't been revoked in OpenAI dashboard

### No Feedback in Results
- Check `$results['ai_feedback']` exists
- Look for error in stderr output
- Try with `--no-openai` flag to isolate issue

## Alternative: Run Without OpenAI

If you prefer not to use OpenAI (to save costs or for testing):

1. The analyzer works fine without it
2. You'll still get detailed Tajweed analysis
3. Just no AI-generated feedback
4. Use flag: `--no-openai`

## Support
- OpenAI Documentation: [https://platform.openai.com/docs](https://platform.openai.com/docs)
- OpenAI Community: [https://community.openai.com/](https://community.openai.com/)
