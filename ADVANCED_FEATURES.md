# Advanced Tajweed Analyzer Implementation

## Overview
TajTrainer V2 now includes three advanced AI integrations for superior Tajweed analysis:

1. **Tarteel AI Whisper Model** - Specialized Arabic Quran ASR for accurate transcription
2. **AlQuran.cloud API** - Tajweed-colored text and reference audio
3. **OpenAI GPT-3.5** - Intelligent, personalized feedback generation

## Features

### 1. Tarteel AI Whisper Model

#### What is it?
Tarteel AI's Whisper model (`tarteel-ai/whisper-base-ar-quran`) is a fine-tuned version of OpenAI's Whisper specifically trained on Quranic Arabic recitation. This provides significantly better transcription accuracy than generic ASR models.

#### Why use it?
- **Specialized**: Trained specifically on Quran recitations
- **Free**: Open-source model from Hugging Face
- **Accurate**: Better understands Tajweed rules, elongations, and pronunciation
- **Offline**: Can run locally after initial download (~290MB)

#### How it works
```python
from transformers import WhisperProcessor, WhisperForConditionalGeneration

# Load model (first time downloads ~290MB)
model = WhisperForConditionalGeneration.from_pretrained("tarteel-ai/whisper-base-ar-quran")
processor = WhisperProcessor.from_pretrained("tarteel-ai/whisper-base-ar-quran")

# Transcribe audio
input_features = processor(audio, sampling_rate=16000, return_tensors="pt").input_features
predicted_ids = model.generate(input_features)
transcription = processor.batch_decode(predicted_ids)[0]
```

#### When it's used
- Automatically used when analyzing student recitations
- Provides accurate phoneme detection for Idgham rules
- Helps identify specific Tajweed issues

### 2. AlQuran.cloud API Integration

#### What is it?
AlQuran.cloud provides:
1. **Tajweed-colored text** with markers indicating Tajweed rules
2. **Reference audio** from master reciters (Mishary Rashid Alafasy)

#### Tajweed Markers
The API returns text with special markers:

```
Example: بِسْمِ [h:1[ٱ]للَّهِ [h:2[ٱ][l[ل]رَّحْمَ[n[ـٰ]نِ

Markers:
[a = Idgham Bi Ghunnah (idgham-with-ghunnah) - GREEN
[u = Idgham Bila Ghunnah (idgham-without-ghunnah) - BLUE  
[n = Madd Normal (2 counts) - RED
[p = Madd Permissible (2-6 counts) - YELLOW
[m = Madd Necessary (6 counts) - GREEN
[o = Madd Obligatory (4-5 counts) - PURPLE
[h:X = Hamza Wasl - GREY
[l = Lam Shamsi (solar lam) - LIME
[s = Lam Qamari (lunar lam) - GREY
[q = Qalqalah - ORANGE
```

#### Color Scheme (Standard Tajweed Colors)
- **GREEN**: Ghunnah (nasalization), necessary Madd
- **BLUE**: Idgham Bila Ghunnah
- **RED**: Normal Madd
- **YELLOW**: Permissible Madd
- **PURPLE**: Obligatory Madd
- **ORANGE**: Qalqalah
- **GREY**: Hamza Wasl, Lam Qamari

#### API Endpoints Used

**1. Get Tajweed-colored text:**
```
GET https://api.alquran.cloud/v1/ayah/{surah}:{verse}/quran-tajweed

Response:
{
  "code": 200,
  "data": {
    "number": 1,
    "text": "بِسْمِ [h:1[ٱ]للَّهِ [h:2[ٱ][l[ل]رَّحْمَ[n[ـٰ]نِ [h:3[ٱ][l[ل]رَّح[p[ِي]مِ",
    "surah": {...},
    "numberInSurah": 1,
    "juz": 1,
    ...
  }
}
```

**2. Get reference audio:**
```
GET https://api.alquran.cloud/v1/ayah/{surah}:{verse}/ar.alafasy

Response:
{
  "code": 200,
  "data": {
    "number": 1,
    "audio": "https://cdn.islamic.network/quran/audio/128/ar.alafasy/1.mp3",
    "text": "بِسْمِ ٱللَّهِ ٱلرَّحْمَـٰنِ ٱلرَّحِيمِ",
    ...
  }
}
```

#### Laravel Implementation

```php
// In StudentController.php

// Get tajweed-colored text
private function getQuranTajweedText($surah, $startVerse, $endVerse)
{
    $surahNumber = $this->getSurahNumber($surah);
    $verses = [];
    
    for ($verse = $startVerse; $verse <= $endVerse; $verse++) {
        $url = "https://api.alquran.cloud/v1/ayah/{$surahNumber}:{$verse}/quran-tajweed";
        $response = @file_get_contents($url);
        $data = json_decode($response, true);
        
        if ($data['code'] == 200) {
            $verses[] = $data['data']['text'];
        }
    }
    
    return implode(' ۝ ', $verses);
}

// Get reference audio URLs
private function getQuranAudioUrls($surah, $startVerse, $endVerse)
{
    $audioUrls = [];
    
    for ($verse = $startVerse; $verse <= $endVerse; $verse++) {
        $url = "https://api.alquran.cloud/v1/ayah/{$surahNumber}:{$verse}/ar.alafasy";
        $response = @file_get_contents($url);
        $data = json_decode($response, true);
        
        if ($data['code'] == 200) {
            $audioUrls[] = [
                'verse' => "{$surahNumber}:{$verse}",
                'url' => $data['data']['audio']
            ];
        }
    }
    
    return $audioUrls;
}
```

### 3. OpenAI Integration

#### What it does
Generates intelligent, personalized feedback based on:
- Overall Tajweed score
- Specific issues detected (Madd, Idgham Bila, Idgham Bi)
- Student's strengths and weaknesses
- Expected Quranic text

#### Example Feedback

**Input:**
```json
{
  "madd_analysis": {"percentage": 85, "issues": [...]},
  "idgham_bila_analysis": {"percentage": 90, "issues": [...]},
  "idgham_bi_analysis": {"percentage": 70, "issues": [...]},
  "overall_score": {"score": 81.67}
}
```

**Output:**
```
Mashallah, great effort on this recitation! Your Idgham Bila Ghunnah (90%) shows strong understanding of merging without nasalization. However, focus on improving your Idgham Bi Ghunnah (70%) - ensure you hold the dengung for 2 full counts when merging with و م ن ي. Practice with Sheikh Mishary's recitation to perfect the Ghunnah duration.
```

#### Python Implementation

```python
from openai import OpenAI

client = OpenAI(api_key=os.environ.get('OPENAI_API_KEY'))

response = client.chat.completions.create(
    model="gpt-3.5-turbo",
    messages=[
        {"role": "system", "content": "You are an expert Quran Tajweed teacher."},
        {"role": "user", "content": prompt}
    ],
    max_tokens=200,
    temperature=0.7
)

feedback = response.choices[0].message.content
```

## How They Work Together

### Analysis Flow

```
1. Student submits recitation audio
   ↓
2. Laravel controller:
   - Gets expected Quranic text (AlQuran.cloud API)
   - Gets tajweed-colored text (AlQuran.cloud quran-tajweed edition)
   - Gets reference audio URLs (AlQuran.cloud ar.alafasy edition)
   - Passes to Python analyzer
   ↓
3. Python analyzer:
   - Loads audio at 16kHz
   - Uses Tarteel Whisper to transcribe (phoneme-level accuracy)
   - Performs MFCC analysis for:
     * Madd detection (sustained vowels)
     * Idgham Bila detection (no nasalization)
     * Idgham Bi detection (with nasalization)
   - Combines Whisper + MFCC results
   - Generates OpenAI feedback
   ↓
4. Returns comprehensive results:
   {
     "whisper_transcription": "بسم الله الرحمن الرحيم",
     "tajweed_text": "بِسْمِ [h:1[ٱ]للَّهِ...",
     "reference_audio": [{url: "https://cdn.islamic.network/..."}],
     "madd_analysis": {...},
     "idgham_bila_analysis": {...},
     "idgham_bi_analysis": {...},
     "overall_score": {score: 85, grade: "Very Good"},
     "ai_feedback": "Mashallah, excellent work..."
   }
   ↓
5. Display to user:
   - Show tajweed-colored text with highlights
   - Play reference audio button
   - Show detailed analysis
   - Display AI feedback
```

## Frontend Display (TODO)

### Tajweed Text Display

Parse markers and apply CSS colors:

```javascript
function displayTajweedText(text) {
    // Parse markers: [a, [u, [n, [p, [m, [o, [h, [l, [s, [q
    const colorMap = {
        '[a': 'green',      // Idgham Bi Ghunnah
        '[u': 'blue',       // Idgham Bila Ghunnah
        '[n': 'red',        // Madd Normal
        '[p': 'yellow',     // Madd Permissible
        '[m': 'green',      // Madd Necessary
        '[o': 'purple',     // Madd Obligatory
        '[h': 'grey',       // Hamza Wasl
        '[l': 'lime',       // Lam Shamsi
        '[s': 'grey',       // Lam Qamari
        '[q': 'orange'      // Qalqalah
    };
    
    // Replace markers with styled spans
    let html = text;
    for (const [marker, color] of Object.entries(colorMap)) {
        const regex = new RegExp(`\\${marker}([^\\[\\]]+)`, 'g');
        html = html.replace(regex, `<span class="tajweed-${color}">$1</span>`);
    }
    
    return html;
}
```

```css
.tajweed-green { color: #22c55e; font-weight: 600; }
.tajweed-blue { color: #3b82f6; font-weight: 600; }
.tajweed-red { color: #ef4444; font-weight: 600; }
.tajweed-yellow { color: #eab308; font-weight: 600; }
.tajweed-purple { color: #a855f7; font-weight: 600; }
.tajweed-orange { color: #f97316; font-weight: 600; }
.tajweed-grey { color: #9ca3af; }
.tajweed-lime { color: #84cc16; font-weight: 600; }
```

### Reference Audio Player

```html
<div class="reference-audio">
    <h4>Reference Recitation (Sheikh Mishary Alafasy)</h4>
    @foreach($referenceAudio as $audio)
        <div class="verse-audio">
            <span>Verse {{ $audio['verse'] }}</span>
            <audio controls>
                <source src="{{ $audio['url'] }}" type="audio/mpeg">
            </audio>
        </div>
    @endforeach
</div>
```

### AI Feedback Display

```html
<div class="ai-feedback">
    <div class="feedback-icon">🤖 AI Feedback</div>
    <p>{{ $analysis['ai_feedback'] }}</p>
</div>
```

```css
.ai-feedback {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 1.5rem;
    border-radius: 12px;
    margin: 1rem 0;
}
```

## Setup Requirements

### 1. Python Packages

Install new dependencies:
```bash
cd python
pip install -r requirements.txt
```

Requirements (updated):
```
librosa==0.11.0
pydub==0.25.1
numpy>=1.24.0
scipy>=1.10.0
python-speech-features==0.6
arabic-reshaper>=3.0.0
transformers>=4.36.0     # NEW: For Tarteel Whisper
torch>=2.1.0             # NEW: Required by transformers
openai>=1.12.0           # NEW: For GPT feedback
requests>=2.31.0         # NEW: For API calls
```

### 2. Laravel Configuration

**Add to `.env`:**
```env
OPENAI_API_KEY=sk-proj-your-key-here
```

**Update `config/services.php`:**
```php
'openai' => [
    'api_key' => env('OPENAI_API_KEY'),
],
```

### 3. First Run

The first time the analyzer runs with Whisper:
- Downloads ~290MB model from Hugging Face
- Caches in `~/.cache/huggingface/transformers/`
- Subsequent runs use cached model (fast)

## Testing

### Test Locally (Windows PowerShell)

```powershell
cd c:\laragon\www\tajtrainerV2\python

# Set OpenAI API key
$env:OPENAI_API_KEY = "sk-proj-your-key-here"

# Test with all features
python tajweed_analyzer.py "path/to/test.mp3" "بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ"

# Test without Whisper (faster, MFCC only)
python tajweed_analyzer.py "path/to/test.mp3" "text" --no-whisper

# Test without OpenAI (no feedback)
python tajweed_analyzer.py "path/to/test.mp3" "text" --no-openai

# Test with neither (fastest, MFCC only)
python tajweed_analyzer.py "path/to/test.mp3" "text" --no-whisper --no-openai
```

### Expected Output

```json
{
  "audio_file": "test.mp3",
  "duration": 5.23,
  "whisper_transcription": "بسم الله الرحمن الرحيم",
  "expected_text": "بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ",
  "rules_detected": {
    "madd": true,
    "idgham_bila_ghunnah": false,
    "idgham_bi_ghunnah": false
  },
  "madd_analysis": {
    "total_elongations": 3,
    "correct_elongations": 2,
    "percentage": 66.67,
    "issues": [...]
  },
  "overall_score": {
    "score": 85.5,
    "grade": "Very Good",
    "feedback": "Very good recitation with proper Tajweed."
  },
  "ai_feedback": "Mashallah! Your Madd pronunciation is quite good..."
}
```

## Benefits Summary

### 1. Better Accuracy
- Tarteel Whisper: Specialized for Quran (not generic Arabic)
- MFCC + Whisper: Dual verification system
- AlQuran API: Authoritative reference text

### 2. Better Learning
- Tajweed colors: Visual learning aid
- Reference audio: Hear correct pronunciation
- AI feedback: Personalized guidance

### 3. Cost Effective
- Tarteel Whisper: **FREE** (open-source)
- AlQuran.cloud: **FREE** (no API key needed)
- OpenAI: ~$0.0004 per analysis (~40 cents per 1000 students)

### 4. Professional Quality
- Uses industry-standard models
- Matches Tajweed teaching standards
- Provides comprehensive feedback

## Troubleshooting

### Issue: Whisper model download fails
**Solution**: Check internet connection, or disable with `--no-whisper` flag

### Issue: OpenAI returns error
**Solution**: Verify API key in `.env`, check usage limits

### Issue: AlQuran.cloud API slow
**Solution**: Implement caching for frequently accessed verses

### Issue: Python packages conflict
**Solution**: Use virtual environment:
```bash
cd python
python -m venv venv
.\venv\Scripts\activate
pip install -r requirements.txt
```

## Next Steps

1. ✅ Install Python dependencies
2. ✅ Add OpenAI API key to `.env`
3. ✅ Update `config/services.php`
4. 🔄 Test analyzer locally
5. 🔄 Update frontend to display tajweed colors
6. 🔄 Add reference audio player
7. 🔄 Show AI feedback
8. 🔄 Deploy to production

## References

- Tarteel AI: https://huggingface.co/tarteel-ai/whisper-base-ar-quran
- AlQuran.cloud: https://alquran.cloud/api
- Tajweed Guide: https://alquran.cloud/tajweed-guide
- OpenAI API: https://platform.openai.com/docs
