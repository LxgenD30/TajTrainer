"""
Advanced Tajweed Analyzer for Quran Recitation
Uses Tarteel AI's Whisper model for accurate Arabic Quran transcription
Analyzes 3 specific Tajweed rules using ACTUAL AUDIO ANALYSIS:
1. Madd (Elongation) - Pitch stability + duration measurement
2. Idgham Bila Ghunnah (Merging without nasalization - ر ل) - Formant analysis
3. Idgham Bi Ghunnah (Merging with nasalization - و م ن ي) - Nasal formant detection
Uses OpenAI for intelligent feedback generation
Uses Parselmouth (Praat) for professional phonetic analysis
"""

# CRITICAL: Limit OpenBLAS threads for shared hosting environments
# Must be set BEFORE importing numpy/scipy to prevent threading errors
import os
os.environ['OPENBLAS_NUM_THREADS'] = '1'
os.environ['MKL_NUM_THREADS'] = '1'
os.environ['OMP_NUM_THREADS'] = '1'
os.environ['NUMEXPR_NUM_THREADS'] = '1'

import sys
import json
import platform
import librosa
import soundfile as sf
import numpy as np
from scipy.signal import find_peaks
import warnings
import re
import html
import tempfile
from difflib import SequenceMatcher
warnings.filterwarnings('ignore')

MUQATTAAT_LETTER_NAMES = {
    'ا': 'الف',
    'ل': 'لام',
    'م': 'ميم',
    'ح': 'حا',
    'ي': 'يا',
    'ط': 'طا',
    'س': 'سين',
    'ك': 'كاف',
    'ه': 'ها',
    'ع': 'عين',
    'ر': 'را',
    'ص': 'صاد',
    'ق': 'قاف',
    'ن': 'نون',
}

MUQATTAAT_SEQUENCES = (
    'الم', 'المص', 'الر', 'المر', 'كهيعص', 'طه', 'طسم', 'طس',
    'يس', 'ص', 'حم', 'عسق', 'حمعسق', 'ق', 'ن'
)

MUQATTAAT_CANONICAL = set(MUQATTAAT_LETTER_NAMES.values())


def _muqattaat_sequence_to_phrase(sequence):
    return ' '.join(MUQATTAAT_LETTER_NAMES[ch] for ch in sequence if ch in MUQATTAAT_LETTER_NAMES)


def _build_muqattaat_phrase_patterns():
    patterns = []
    for sequence in MUQATTAAT_SEQUENCES:
        phrase = _muqattaat_sequence_to_phrase(sequence)
        if not phrase:
            continue

        compact_phrase = phrase.replace(' ', '')
        patterns.append((rf'(?<!\S){re.escape(sequence)}(?!\S)', phrase))
        patterns.append((rf'(?<!\S){re.escape(compact_phrase)}(?!\S)', phrase))

    return patterns


MUQATTAAT_PHRASE_PATTERNS = _build_muqattaat_phrase_patterns()

# Import Parselmouth for advanced phonetic analysis
try:
    import parselmouth
    from parselmouth.praat import call
    PARSELMOUTH_AVAILABLE = True
except ImportError:
    PARSELMOUTH_AVAILABLE = False
    print(json.dumps({
        "warning": "Parselmouth not installed. Install with: pip install praat-parselmouth",
        "fallback": "Using basic librosa analysis"
    }), file=sys.stderr)

# Setup FFmpeg path
def setup_ffmpeg():
    """Setup FFmpeg path based on operating system"""
    env_ffmpeg_path = os.environ.get('FFMPEG_PATH', '')
    if env_ffmpeg_path and os.path.exists(env_ffmpeg_path):
        if env_ffmpeg_path not in os.environ.get('PATH', ''):
            os.environ['PATH'] = env_ffmpeg_path + os.pathsep + os.environ.get('PATH', '')
        return
    
    system = platform.system()
    
    if system == 'Windows':
        windows_paths = [
            r"C:\ffmpeg\bin",
            r"C:\ffmpeg\ffmpeg-master-latest-win64-gpl\bin",
            r"C:\Program Files\ffmpeg\bin",
        ]
        for path in windows_paths:
            if os.path.exists(path) and path not in os.environ.get('PATH', ''):
                os.environ['PATH'] = path + os.pathsep + os.environ.get('PATH', '')
                return
    
    elif system in ['Linux', 'Darwin']:
        unix_paths = ["/usr/bin", "/usr/local/bin", "/opt/homebrew/bin"]
        for path in unix_paths:
            if os.path.exists(path) and path not in os.environ.get('PATH', ''):
                os.environ['PATH'] = path + os.pathsep + os.environ.get('PATH', '')
                return

setup_ffmpeg()

class TajweedAnalyzer:
    def __init__(self, audio_path, expected_text="", use_whisper=True, use_openai=True, reference_audio_path=None, tajweed_html=""):
        """Initialize with audio file path and expected Quranic text"""
        self.audio_path = audio_path
        self.expected_text = expected_text
        self.tajweed_html = tajweed_html or ''
        self._last_transcription = ''
        self.use_whisper = use_whisper
        self.use_openai = use_openai
        self.reference_audio_path = reference_audio_path
        
        # Convert webm to wav if needed (for Parselmouth compatibility)
        self.converted_audio_path = None
        if audio_path.lower().endswith('.webm'):
            self.converted_audio_path = self.convert_webm_to_wav(audio_path)
            audio_for_analysis = self.converted_audio_path
        else:
            audio_for_analysis = audio_path
        
        self.y, self.sr = librosa.load(audio_for_analysis, sr=16000)  # 16kHz for Whisper
        self.duration = librosa.get_duration(y=self.y, sr=self.sr)
        
        # Validate that the audio contains actual sound content
        # RMS energy threshold: below this is considered silent
        rms_energy = float(np.sqrt(np.mean(self.y ** 2)))
        self.is_silent = rms_energy < 0.005 or self.duration < 0.5
        if self.is_silent:
            print(json.dumps({
                "warning": "Audio appears to be silent or too short",
                "duration": round(self.duration, 2),
                "rms_energy": round(rms_energy, 6)
            }), file=sys.stderr)
        
        # Load reference audio if provided
        self.y_ref, self.sr_ref = None, None
        self.duration_ref = 0
        if reference_audio_path and os.path.exists(reference_audio_path):
            try:
                self.y_ref, self.sr_ref = librosa.load(reference_audio_path, sr=16000)
                self.duration_ref = librosa.get_duration(y=self.y_ref, sr=self.sr_ref)
                print(json.dumps({
                    "status": "reference_loaded",
                    "message": f"Reference audio loaded: {self.duration_ref:.2f}s"
                }), file=sys.stderr)
            except Exception as e:
                print(json.dumps({"warning": f"Failed to load reference audio: {str(e)}"}), file=sys.stderr)
        
        # Load Whisper model if requested
        self.whisper_model = None
        self.whisper_processor = None
        if self.use_whisper:
            self.load_whisper_model()
        
        # Detect which rules apply to this verse
        self.has_madd = self.detect_madd_in_text()
        self.has_idgham_bila = self.detect_idgham_bila_in_text()
        self.has_idgham_bi = self.detect_idgham_bi_in_text()
        self.has_muqattaat = self.detect_muqattaat_in_expected_text()
    
    def load_whisper_model(self):
        """Load Tarteel AI's Whisper model for Arabic Quran ASR"""
        try:
            from transformers import WhisperProcessor, WhisperForConditionalGeneration
            import torch
            
            model_name = "tarteel-ai/whisper-base-ar-quran"
            
            # Check if model is cached, if not, show loading message
            cache_dir = os.path.expanduser("~/.cache/huggingface/transformers")
            model_cache = os.path.join(cache_dir, "models--tarteel-ai--whisper-base-ar-quran")
            
            if not os.path.exists(model_cache):
                print(json.dumps({
                    "status": "downloading_model",
                    "message": "First time setup: Downloading Tarteel Whisper model (~290MB)..."
                }), file=sys.stderr)
            
            self.whisper_processor = WhisperProcessor.from_pretrained(model_name)
            self.whisper_model = WhisperForConditionalGeneration.from_pretrained(model_name)
            
            # Use GPU if available
            device = "cuda" if torch.cuda.is_available() else "cpu"
            self.whisper_model = self.whisper_model.to(device)

            print(json.dumps({
                "status": "model_loaded",
                "device": device
            }), file=sys.stderr)
            
        except Exception as e:
            print(json.dumps({
                "status": "model_load_failed",
                "error": str(e),
                "fallback": "Using MFCC-based analysis only"
            }), file=sys.stderr)
            self.whisper_model = None
            self.whisper_processor = None
    
    def convert_webm_to_wav(self, webm_path):
        """Convert webm to wav for Parselmouth compatibility"""
        try:
            import subprocess

            # Create temp wav file
            wav_path = webm_path.rsplit('.', 1)[0] + '_converted.wav'

            # Convert using ffmpeg. -fflags +genpts +avoid_negative_ts handle
            # MediaRecorder webm files that lack duration metadata, which can
            # otherwise cause decoders to read only part of the recording.
            cmd = [
                'ffmpeg', '-fflags', '+genpts', '-avoid_negative_ts', 'make_zero',
                '-i', webm_path, '-ar', '16000', '-ac', '1', '-y', wav_path,
            ]
            subprocess.run(cmd, stdout=subprocess.DEVNULL, stderr=subprocess.DEVNULL, check=True)

            return wav_path
        except Exception as e:
            print(json.dumps({
                "warning": f"Failed to convert webm to wav: {str(e)}",
                "fallback": "Will attempt analysis with original file"
            }), file=sys.stderr)
            return webm_path
    
    def transcribe_with_whisper(self):
        """Transcribe audio using Tarteel AI's Whisper model, with OpenAI API fallback."""
        # Do not attempt transcription on silent or near-silent audio.
        if self.is_silent:
            print(json.dumps({
                "status": "transcription_skipped",
                "reason": "Audio is silent or too short - skipping Whisper to avoid hallucination"
            }), file=sys.stderr)
            return None

        # 1) Try the local Tarteel Whisper model first.
        transcription = self._transcribe_local_whisper()
        if transcription:
            return transcription

        # 2) Fallback to OpenAI Whisper API if the local model is unavailable.
        print(json.dumps({
            "status": "transcription_fallback",
            "message": "Local Whisper model unavailable or empty - falling back to OpenAI Whisper API"
        }), file=sys.stderr)
        return self._transcribe_with_openai_api()

    def _transcribe_segment(self, segment, sr):
        """Transcribe a single audio segment with the local Whisper model."""
        try:
            import torch

            input_features = self.whisper_processor(
                segment,
                sampling_rate=sr,
                return_tensors="pt"
            ).input_features

            device = next(self.whisper_model.parameters()).device
            input_features = input_features.to(device)

            with torch.no_grad():
                try:
                    predicted_ids = self.whisper_model.generate(
                        input_features,
                        task='transcribe',
                        language='ar',
                        num_beams=3,
                        do_sample=False,
                    )
                except Exception:
                    # Older generation configs are incompatible with the
                    # `language` argument on newer transformers; retry without
                    # it so the model's stored Arabic decoder ids are used.
                    predicted_ids = self.whisper_model.generate(
                        input_features,
                        num_beams=3,
                        do_sample=False,
                    )

            transcription = self.whisper_processor.batch_decode(
                predicted_ids,
                skip_special_tokens=True
            )[0].strip()

            # Muqatta'at-heavy verses benefit from lexical canonicalization.
            if transcription and self.has_muqattaat:
                transcription = self.normalize_muqattaat_text(transcription)

            return transcription if transcription else None

        except Exception as e:
            print(json.dumps({
                "status": "transcription_segment_failed",
                "error": str(e)
            }), file=sys.stderr)
            return None

    def _transcribe_local_whisper(self):
        """Transcribe using the locally loaded Tarteel Whisper model."""
        if not self.whisper_model or not self.whisper_processor:
            return None

        try:
            # Chunk long audio into ~28s segments to avoid the model's
            # token-length limit truncating multi-ayah recitations.
            chunk_sec = 28.0
            sr = self.sr
            step = int(chunk_sec * sr)
            overlap = int(0.5 * sr)  # small overlap so words aren't cut mid-way

            if self.duration <= chunk_sec:
                return self._transcribe_segment(self.y, sr)

            chunks = []
            start = 0
            total = len(self.y)
            while start < total:
                seg_start = max(0, start - overlap)
                seg_end = min(start + step, total)
                seg = self.y[seg_start:seg_end]
                text = self._transcribe_segment(seg, sr)
                if text:
                    chunks.append(text)
                if seg_end >= total:
                    break
                start = seg_end

            return ' '.join(chunks) if chunks else None

        except Exception as e:
            print(json.dumps({
                "status": "transcription_failed",
                "error": str(e)
            }), file=sys.stderr)
            return None

    def _transcribe_with_openai_api(self):
        """Transcribe using OpenAI's hosted Whisper API (whisper-1)."""
        try:
            api_key = os.environ.get('OPENAI_API_KEY')
            if not api_key:
                print(json.dumps({
                    "status": "openai_transcription_skipped",
                    "reason": "OPENAI_API_KEY not set - cannot use Whisper API fallback"
                }), file=sys.stderr)
                return None

            from openai import OpenAI

            client = OpenAI(api_key=api_key)
            audio_file = self.converted_audio_path if self.converted_audio_path else self.audio_path

            with open(audio_file, 'rb') as f:
                response = client.audio.transcriptions.create(
                    model='whisper-1',
                    file=f,
                    language='ar',
                    response_format='verbose_json',
                    temperature=0,
                )

            transcription = (response.text or '').strip()

            if transcription and self.has_muqattaat:
                transcription = self.normalize_muqattaat_text(transcription)

            print(json.dumps({
                "status": "openai_transcription_success",
                "length": len(transcription)
            }), file=sys.stderr)

            return transcription if transcription else None

        except Exception as e:
            print(json.dumps({
                "status": "openai_transcription_failed",
                "error": str(e)
            }), file=sys.stderr)
            return None

    def detect_pause_segments(self, min_pause_seconds=1.8, top_db=35):
        """Detect long silence segments to mark recitation pauses."""
        pauses = []

        try:
            intervals = librosa.effects.split(self.y, top_db=top_db)
            if len(intervals) < 2:
                return pauses

            for idx in range(len(intervals) - 1):
                end_current = intervals[idx][1]
                start_next = intervals[idx + 1][0]
                pause_duration = (start_next - end_current) / float(self.sr)

                if pause_duration >= min_pause_seconds:
                    pause_midpoint = ((end_current + start_next) / 2.0) / float(self.sr)
                    pauses.append({
                        'time': round(float(pause_midpoint), 2),
                        'duration': round(float(pause_duration), 2),
                    })
        except Exception as e:
            print(json.dumps({
                "status": "pause_detection_failed",
                "error": str(e)
            }), file=sys.stderr)

        return pauses

    def add_pause_markers_to_transcription(self, transcription, pause_segments):
        """Insert ۝ between words at detected long pause positions."""
        if not transcription:
            return transcription

        words = transcription.split()
        if len(words) < 2 or not pause_segments or self.duration <= 0:
            return transcription

        boundaries = len(words) - 1
        marker_positions = set()

        for pause in pause_segments:
            pause_time = float(pause.get('time', 0) or 0)
            ratio = max(0.0, min(1.0, pause_time / float(self.duration)))
            position = int(round(ratio * boundaries))
            position = max(1, min(boundaries, position))
            marker_positions.add(position)

        if not marker_positions:
            return transcription

        rebuilt = []
        for idx, word in enumerate(words):
            if idx in marker_positions:
                rebuilt.append('۝')
            rebuilt.append(word)

        return ' '.join(rebuilt)

    def detect_muqattaat_in_expected_text(self):
        """Detect whether expected text contains Muqatta'at patterns."""
        if not self.expected_text:
            return False

        probe = html.unescape(str(self.expected_text))
        probe = re.sub(r'[\u0000-\u007F]+', ' ', probe)
        probe = re.sub(r'<\|[^|]+\|>', ' ', probe)
        probe = re.sub(r'<[^>]+>', ' ', probe)
        probe = probe.replace('۝', ' ')
        probe = re.sub(r'[\u064B-\u065F\u0670\u06D6-\u06ED]', '', probe)
        probe = probe.replace('ـ', '')
        probe = probe.replace('أ', 'ا').replace('إ', 'ا').replace('آ', 'ا').replace('ٱ', 'ا')
        probe = re.sub(r'[^\u0600-\u06FF\s]', ' ', probe)
        probe = re.sub(r'\s+', ' ', probe).strip()

        if not probe:
            return False

        tokens = probe.split()
        if any(token in MUQATTAAT_SEQUENCES for token in tokens):
            return True

        compact = ''.join(tokens)
        return any(sequence in compact for sequence in MUQATTAAT_SEQUENCES)

    def expand_compact_muqattaat_token(self, token):
        """Expand compact Muqatta'at forms such as الم or كهيعص into letter names."""
        if not token:
            return None

        compact = re.sub(r'(.)\1{1,}', r'\1', token)
        if compact in MUQATTAAT_SEQUENCES:
            return [MUQATTAAT_LETTER_NAMES[ch] for ch in compact if ch in MUQATTAAT_LETTER_NAMES]

        return None

    def canonicalize_muqattaat_word(self, word):
        """Map stretched/variant Muqatta'at letter names to canonical names."""
        if not word:
            return word

        if word in MUQATTAAT_CANONICAL:
            return word

        condensed = re.sub(r'(.)\1{1,}', r'\1', word)
        if condensed in MUQATTAAT_CANONICAL:
            return condensed

        # Guard against over-normalizing regular words.
        if len(condensed) < 2 or len(condensed) > 5:
            return condensed

        best_match = None
        best_ratio = 0.0
        for candidate in MUQATTAAT_CANONICAL:
            ratio = SequenceMatcher(None, condensed, candidate).ratio()
            if ratio > best_ratio:
                best_ratio = ratio
                best_match = candidate

        if best_match and best_ratio >= 0.80:
            return best_match

        return condensed

    def normalize_muqattaat_text(self, text):
        """Normalize Muqatta'at phrases for fair comparison in ASR scoring."""
        if not text:
            return ''

        text = html.unescape(str(text))
        text = re.sub(r'[\u064B-\u065F\u0670\u06D6-\u06ED]', '', text)
        text = text.replace('ـ', '')
        text = text.replace('أ', 'ا').replace('إ', 'ا').replace('آ', 'ا').replace('ٱ', 'ا')
        text = re.sub(r'[^\u0600-\u06FF\s]', ' ', text)
        text = re.sub(r'\s+', ' ', text).strip()

        if not text:
            return ''

        normalized_words = []
        tokens = text.split()
        idx = 0

        while idx < len(tokens):
            token = tokens[idx]

            expanded = self.expand_compact_muqattaat_token(token)
            if expanded:
                normalized_words.extend(expanded)
                idx += 1
                continue

            if len(token) == 1 and token in MUQATTAAT_LETTER_NAMES:
                run = []
                j = idx
                while j < len(tokens) and len(tokens[j]) == 1 and tokens[j] in MUQATTAAT_LETTER_NAMES and len(run) < 6:
                    run.append(tokens[j])
                    j += 1

                best_len = 0
                for candidate_len in range(len(run), 1, -1):
                    sequence = ''.join(run[:candidate_len])
                    if sequence in MUQATTAAT_SEQUENCES:
                        best_len = candidate_len
                        break

                if best_len > 0:
                    normalized_words.extend(MUQATTAAT_LETTER_NAMES[ch] for ch in run[:best_len])
                    idx += best_len
                    continue

            normalized_words.append(self.canonicalize_muqattaat_word(token))
            idx += 1

        normalized_text = ' '.join(normalized_words)
        for pattern, replacement in MUQATTAAT_PHRASE_PATTERNS:
            normalized_text = re.sub(pattern, replacement, normalized_text)

        return re.sub(r'\s+', ' ', normalized_text).strip()

    def normalize_arabic_for_accuracy(self, text):
        """Normalize Arabic text for fair similarity scoring."""
        if not text:
            return ''

        text = html.unescape(str(text))
        text = re.sub(r'<\|[^|]+\|>', ' ', text)
        text = re.sub(r'<[^>]+>', ' ', text)
        text = text.replace('۝', ' ')

        # Remove harakat and Quran annotation marks.
        text = re.sub(r'[\u064B-\u065F\u0670\u06D6-\u06ED]', '', text)
        text = text.replace('ـ', '')

        # Normalize common alif forms.
        text = text.replace('أ', 'ا').replace('إ', 'ا').replace('آ', 'ا').replace('ٱ', 'ا')
        text = text.replace('ى', 'ي')
        text = text.replace('ة', 'ه')

        # Normalize small Quranic glyph forms.
        text = text.replace('\u06E5', 'و').replace('\u06E6', 'ي')

        # Keep Arabic letters, digits, and whitespace only.
        text = re.sub(r'[^\u0600-\u06FF0-9\s]', ' ', text)
        text = re.sub(r'\s+', ' ', text)

        if self.has_muqattaat:
            text = self.normalize_muqattaat_text(text)

        return text.strip()

    def calculate_word_accuracy(self, transcription, expected):
        """Lenient, order-independent word accuracy.

        Each expected word is fuzzy-matched against the remaining transcription
        words (SequenceMatcher ratio >= 0.6), tolerating Whisper word splits,
        extra words, and minor letter differences. Returns (hit_rate, avg_ratio).
        """
        trans_words = transcription.split()
        expected_words = expected.split()

        if not expected_words or not trans_words:
            return 0.0, 0.0

        from difflib import SequenceMatcher

        used = [False] * len(trans_words)
        matched = 0
        total_ratio = 0.0
        for ew in expected_words:
            best = 0.0
            best_j = -1
            for j, tw in enumerate(trans_words):
                if used[j]:
                    continue
                r = SequenceMatcher(None, ew, tw).ratio()
                if r > best:
                    best = r
                    best_j = j
            if best_j >= 0 and best >= 0.6:
                used[best_j] = True
                matched += 1
                total_ratio += best

        hit_rate = matched / len(expected_words) * 100.0
        avg_ratio = total_ratio / len(expected_words) * 100.0
        return hit_rate, avg_ratio
    
    def detect_madd_in_text(self):
        """Check if expected text contains Madd rules."""
        if not self.expected_text and not self.tajweed_html:
            return False
        # Primary: tajweed markup explicitly tags Madd with madda_* classes.
        if 'madda_' in self.tajweed_html:
            return True
        # Fallback: Madd letters ا (alif), و (waw), ي (ya)
        madd_letters = ['ا', 'و', 'ي', 'آ', 'ى']
        return any(letter in self.expected_text for letter in madd_letters)
    
    def detect_idgham_bila_in_text(self):
        """Check if text has Idgham Bila Ghunnah (Noon Sakin/Tanween followed by ر or ل)."""
        if not self.expected_text and not self.tajweed_html:
            return False
        # Primary: tajweed markup tags it idgham_wo_ghunnah / idgham_no_ghunnah.
        if 'idgham_wo_ghunnah' in self.tajweed_html or 'idgham_no_ghunnah' in self.tajweed_html:
            return True
        # Fallback regex: allow intervening ا/ى after tanween (e.g. غَفُورًا رَّحِيمًا).
        patterns = [
            r'نْ\s*[رل]',
            r'نۢ\s*[رل]',
            r'ن(?:[\u064B-\u065F\u0670\u06D6-\u06ED]*)\s*[رل]',
            r'[\u064B\u064C\u064D](?:[\u064B-\u065F\u0670\u06D6-\u06ED\u0627\u0649\s]*)[رل]',
        ]
        return any(re.search(pattern, self.expected_text) for pattern in patterns)
    
    def detect_idgham_bi_in_text(self):
        """Check if text has Idgham Bi Ghunnah (Noon Sakin/Tanween followed by و م ن ي)."""
        if not self.expected_text and not self.tajweed_html:
            return False
        # Primary: tajweed markup tags it idgham_ghunnah / idgham_bi_ghunnah.
        if 'idgham_ghunnah' in self.tajweed_html or 'idgham_bi_ghunnah' in self.tajweed_html:
            return True
        # Fallback regex: allow intervening ا/ى after tanween (e.g. هُدًى مِّن).
        patterns = [
            r'نْ\s*[ومني]',
            r'نۢ\s*[ومني]',
            r'ن(?:[\u064B-\u065F\u0670\u06D6-\u06ED]*)\s*[ومني]',
            r'[\u064B\u064C\u064D](?:[\u064B-\u065F\u0670\u06D6-\u06ED\u0627\u0649\s]*)[ومني]',
        ]
        return any(re.search(pattern, self.expected_text) for pattern in patterns)
    
    # ── Rule scoring helpers ──────────────────────────────────────────────────
    def _norm_token(self, s):
        """Light letter-only normalization for matching (diacritics stripped)."""
        s = (s or '')
        s = re.sub(r'[\u064B-\u065F\u0670\u06D6-\u06ED]', '', s)
        s = s.replace('ـ', '')
        s = s.replace('أ', 'ا').replace('إ', 'ا').replace('آ', 'ا').replace('ٱ', 'ا')
        s = s.replace('ى', 'ي').replace('ة', 'ه')
        s = s.replace('\u06E5', 'و').replace('\u06E6', 'ي')
        s = re.sub(r'[^\u0600-\u06FF]', '', s)
        return s

    def _compact_norm(self, s):
        """Normalize and remove all spaces for substring matching."""
        return re.sub(r'\s+', '', self._norm_token(s))

    def _occurrence_phrase(self, span_text):
        """Expand an occurrence span into its full expected word/phrase."""
        plain = self.expected_text or ''
        if not span_text:
            return ''
        idx = plain.find(span_text)
        if idx < 0:
            return span_text
        start = idx
        while start > 0 and '\u0600' <= plain[start - 1] <= '\u06FF':
            start -= 1
        end = idx + len(span_text)
        while end < len(plain) and '\u0600' <= plain[end] <= '\u06FF':
            end += 1
        return plain[start:end].strip()

    def _rule_occurrence_phrases(self, class_prefixes, fallback_regex):
        """Extract occurrence phrases for a rule from tajweed markup (regex fallback)."""
        spans = []
        if self.tajweed_html:
            for m in re.finditer(r'<tajweed class="?([a-zA-Z_]+)"?>(.*?)</tajweed>', self.tajweed_html, re.S):
                cls = m.group(1)
                if any(cls.startswith(p) for p in class_prefixes):
                    spans.append(m.group(2))
        if not spans and self.expected_text:
            matches = re.findall(fallback_regex, self.expected_text)
            spans = [m if isinstance(m, str) else m[0] for m in matches]
        phrases = []
        for s in spans:
            if not s:
                continue
            phrase = self._occurrence_phrase(s) or s
            phrases.append(phrase)
        seen = set()
        uniq = []
        for p in phrases:
            k = self._compact_norm(p)
            if k and k not in seen:
                seen.add(k)
                uniq.append(p)
        return uniq

    def _verify_occurrences(self, phrases):
        """Verify each expected phrase against the transcribed recitation."""
        trans_compact = self._compact_norm(self._last_transcription)
        total = len(phrases)
        correct = 0
        details = []
        issues = []
        for phrase in phrases:
            pc = self._compact_norm(phrase)
            if pc and pc in trans_compact:
                correct += 1
                details.append({'word': phrase, 'status': 'correct', 'note': 'Correctly produced'})
            else:
                issues.append({
                    'word': phrase,
                    'issue': 'Not clearly produced in the recitation',
                    'recommendation': 'Review this occurrence against the reference recitation and repeat it clearly.',
                })
        return total, correct, details, issues

    def _deterministic_rule_feedback(self, label, total, correct, percentage):
        """Minimal, percentage-consistent feedback used when OpenAI is unavailable."""
        pct = round(float(percentage or 0))
        if pct == 100:
            tone = 'Perfect - every occurrence was applied correctly.'
        elif pct >= 90:
            tone = 'Excellent - nearly all occurrences correct.'
        elif pct >= 80:
            tone = 'Very good - a few occurrences need attention.'
        elif pct >= 70:
            tone = 'Good - several occurrences need improvement.'
        elif pct >= 60:
            tone = 'Fair - more than a quarter of occurrences were missed.'
        elif pct >= 50:
            tone = 'Needs work - about half the occurrences were missed.'
        else:
            tone = 'Needs significant improvement - most occurrences were missed.'
        return f'{correct}/{total} occurrences correct ({pct}%). {tone}'

    def _generate_rule_feedbacks(self, stats):
        """Short per-rule feedback via OpenAI (filtered per rule), with deterministic fallback."""
        fallback = {}
        applicable = {}
        for key, st in stats.items():
            fallback[key] = self._deterministic_rule_feedback(
                st['label'], st['total'], st['correct'], st['percentage']
            )
            if st['total'] > 0:
                applicable[key] = st

        if not applicable or not self.use_openai:
            return fallback

        api_key = os.environ.get('OPENAI_API_KEY')
        if not api_key:
            return fallback

        try:
            from openai import OpenAI

            client = OpenAI(api_key=api_key)
            lines = []
            for key, st in applicable.items():
                missed = '; '.join(i.get('word', '') for i in st['issues'][:5]) or 'none'
                lines.append(
                    f"- {st['label']}: {st['correct']}/{st['total']} correct ({st['percentage']}%). Missed: {missed}"
                )
            prompt = (
                'For EACH item below, write ONE short sentence of Tajweed feedback.\n'
                'Only talk about that specific rule. Keep each sentence under 20 words.\n'
                'Be specific and helpful.\n\n'
                + '\n'.join(lines) +
                '\n\nReturn ONLY a JSON object mapping the exact item label to its one-sentence feedback, '
                'e.g. {"Madd (Elongation)":"...","Idgham Bila Ghunnah":"...","Idgham Bi Ghunnah":"..."}'
            )
            resp = client.chat.completions.create(
                model='gpt-4o',
                messages=[
                    {'role': 'system', 'content': 'You are an expert Quran Tajweed teacher. Reply with minimal, one-sentence feedback per rule only. Valid JSON only.'},
                    {'role': 'user', 'content': prompt},
                ],
                max_tokens=220,
                temperature=0.3,
            )
            text = (resp.choices[0].message.content or '').strip()
            if text.startswith('```'):
                text = text.split('```')[1]
                if text.startswith('json'):
                    text = text[4:]
            data = json.loads(text)
            for key, st in applicable.items():
                fb = data.get(st['label'])
                if isinstance(fb, str) and fb.strip():
                    fallback[key] = fb.strip()[:220]
        except Exception as e:
            print(json.dumps({'status': 'rule_feedback_failed', 'error': str(e)}), file=sys.stderr)

        return fallback

    def _greedy_match_end(self, tokens, exp_words, start):
        """Greedily match exp_words in tokens from start; return index after last matched token."""
        if not exp_words:
            return start
        idx = start
        last = start
        skipped = 0
        for w in exp_words:
            if not w:
                continue
            found = False
            for j in range(idx, min(len(tokens), idx + 10)):
                if tokens[j] == w:
                    idx = j + 1
                    last = idx
                    found = True
                    break
            if not found:
                skipped += 1
                idx = min(len(tokens), idx + 1)
                if skipped > max(3, len(exp_words)):
                    return None
        return last if last > start else None

    def _align_ayah_boundaries(self, transcription):
        """Insert ۝ between ayahs by aligning the transcription to the expected text."""
        if not transcription or not self.expected_text or '۝' not in self.expected_text:
            return transcription
        t_tokens = transcription.split()
        if len(t_tokens) < 2:
            return transcription
        n_tokens = [self._norm_token(t) for t in t_tokens]
        ayahs = [p.strip() for p in self.expected_text.split('۝') if p.strip()]
        boundaries = []
        pos = 0
        for phrase in ayahs[:-1]:
            exp_words = [self._norm_token(w) for w in phrase.split()]
            end = self._greedy_match_end(n_tokens, exp_words, pos)
            if end is not None:
                pos = end
                boundaries.append(pos)
        if not boundaries:
            return transcription
        inserted = set(boundaries)
        rebuilt = []
        for i, tok in enumerate(t_tokens):
            rebuilt.append(tok)
            if (i + 1) in inserted:
                rebuilt.append('۝')
        return ' '.join(rebuilt)

    def analyze_madd(self):
        """Analyze Madd (Elongation) by counting expected occurrences and verifying them in the recitation."""
        results = {
            'total_elongations': 0,
            'correct_elongations': 0,
            'issues': [],
            'percentage': 0,
            'details': [],
            'rule_applicable': self.has_madd
        }

        if not self.has_madd:
            results['percentage'] = 100
            results['details'].append({'note': 'Not present in this verse'})
            return results

        phrases = self._rule_occurrence_phrases(
            ['madda_normal', 'madda_permissible', 'madda_obligatory', 'madda_prolonged', 'madda_necessary'],
            r'[\u064E\u064F\u0650][اويآى]'
        )
        if not self._last_transcription:
            results['total_elongations'] = len(phrases)
            results['percentage'] = 100
            results['details'].append({'note': 'No recitation transcription available - assumed acceptable'})
            return results
        total, correct, details, issues = self._verify_occurrences(phrases)
        results['total_elongations'] = total
        results['correct_elongations'] = correct
        results['details'] = details
        results['issues'] = issues
        results['percentage'] = round((correct / total) * 100, 2) if total else 0
        return results

    def analyze_idgham_bila_ghunnah(self):
        """
        Analyze Idgham Bila Ghunnah by counting expected occurrences
        (Noon Sakin/Tanween -> \u0631 \u0644) and verifying them in the recitation.
        """
        results = {
            'total_occurrences': 0,
            'correct_pronunciation': 0,
            'issues': [],
            'percentage': 0,
            'details': [],
            'rule_applicable': self.has_idgham_bila
        }

        if not self.has_idgham_bila:
            results['percentage'] = 100
            results['details'].append({'note': 'Not present in this verse'})
            return results

        phrases = self._rule_occurrence_phrases(
            ['idgham_wo_ghunnah', 'idgham_no_ghunnah'],
            r'[\u064B\u064C\u064D](?:[\u064B-\u065F\u0670\u06D6-\u06ED\u0627\u0649\s]*)[\u0631\u0644]'
        )
        if not self._last_transcription:
            results['total_occurrences'] = len(phrases)
            results['percentage'] = 100
            results['details'].append({'note': 'No recitation transcription available - assumed acceptable'})
            return results
        total, correct, details, issues = self._verify_occurrences(phrases)
        results['total_occurrences'] = total
        results['correct_pronunciation'] = correct
        results['details'] = details
        results['issues'] = issues
        results['percentage'] = round((correct / total) * 100, 2) if total else 0
        return results

    def analyze_idgham_bi_ghunnah(self):
        """
        Analyze Idgham Bi Ghunnah by counting expected occurrences
        (Noon Sakin/Tanween -> \u0648 \u0645 \u0646 \u064a) and verifying them in the recitation.
        """
        results = {
            'total_occurrences': 0,
            'correct_pronunciation': 0,
            'issues': [],
            'percentage': 0,
            'details': [],
            'rule_applicable': self.has_idgham_bi
        }

        if not self.has_idgham_bi:
            results['percentage'] = 100
            results['details'].append({'note': 'Not present in this verse'})
            return results

        phrases = self._rule_occurrence_phrases(
            ['idgham_ghunnah', 'idgham_bi_ghunnah'],
            r'[\u064B\u064C\u064D](?:[\u064B-\u065F\u0670\u06D6-\u06ED\u0627\u0649\s]*)[\u0648\u0645\u0646\u064a]'
        )
        if not self._last_transcription:
            results['total_occurrences'] = len(phrases)
            results['percentage'] = 100
            results['details'].append({'note': 'No recitation transcription available - assumed acceptable'})
            return results
        total, correct, details, issues = self._verify_occurrences(phrases)
        results['total_occurrences'] = total
        results['correct_pronunciation'] = correct
        results['details'] = details
        results['issues'] = issues
        results['percentage'] = round((correct / total) * 100, 2) if total else 0
        return results

    def get_rule_feedback_contexts(self, analysis_results):
        """Build normalized rule contexts for display and feedback filtering."""
        definitions = [
            ('madd_analysis', 'Madd (Elongation)', ['madd', 'elongation', 'مد']),
            ('idgham_bila_ghunnah_analysis', 'Idgham Bila Ghunnah', ['idgham bila', 'idgham billa', 'without nasalization', 'بلا']),
            ('idgham_bi_ghunnah_analysis', 'Idgham Bi Ghunnah', ['idgham bi', 'with nasalization', 'ghunnah', 'غنة']),
        ]

        contexts = []
        for key, label, keywords in definitions:
            rule = analysis_results.get(key, {}) or {}
            contexts.append({
                'key': key,
                'label': label,
                'applicable': bool(rule.get('rule_applicable', True)),
                'percentage': float(rule.get('percentage', 0) or 0),
                'issues_count': len(rule.get('issues', []) or []),
                'keywords': keywords,
            })

        return contexts

    def filter_feedback_by_applicability(self, feedback_obj, rule_contexts):
        """Remove improvement/strength entries that refer to rules not present in the verse."""
        if not isinstance(feedback_obj, dict):
            return feedback_obj

        excluded_keywords = []
        for context in rule_contexts:
            if not context.get('applicable', True):
                excluded_keywords.extend(context.get('keywords', []))

        if not excluded_keywords:
            return feedback_obj

        def mentions_excluded(text):
            lower = str(text).lower()
            return any(keyword in lower for keyword in excluded_keywords)

        strengths = feedback_obj.get('strengths', [])
        if isinstance(strengths, list):
            feedback_obj['strengths'] = [s for s in strengths if not mentions_excluded(s)]

        improvements = feedback_obj.get('improvements', [])
        if isinstance(improvements, list):
            filtered = []
            for item in improvements:
                if isinstance(item, dict):
                    issue = item.get('issue', '')
                    suggestion = item.get('suggestion', '')
                    if mentions_excluded(issue) or mentions_excluded(suggestion):
                        continue
                else:
                    if mentions_excluded(item):
                        continue
                filtered.append(item)
            feedback_obj['improvements'] = filtered

        next_steps = feedback_obj.get('next_steps', '')
        if isinstance(next_steps, str) and mentions_excluded(next_steps):
            feedback_obj['next_steps'] = 'Keep practicing the rules that appear in this verse and compare with the reference recitation.'

        return feedback_obj
    
    def generate_openai_feedback(self, analysis_results):
        """Generate intelligent feedback using OpenAI GPT"""
        if not self.use_openai:
            return None
        
        try:
            from openai import OpenAI
            
            # Get API key from environment variable
            api_key = os.environ.get('OPENAI_API_KEY')
            if not api_key:
                print(json.dumps({
                    "status": "openai_skipped",
                    "reason": "OPENAI_API_KEY environment variable not set"
                }), file=sys.stderr)
                return None
            
            # Initialize OpenAI client with proper API key
            client = OpenAI(api_key=api_key)
            
            # Get transcription accuracy
            transcription = analysis_results.get('whisper_transcription', '')
            expected = analysis_results.get('expected_text', '')

            rule_contexts = self.get_rule_feedback_contexts(analysis_results)
            applicable_rules = [rule for rule in rule_contexts if rule.get('applicable', True)]
            non_applicable_rules = [rule for rule in rule_contexts if not rule.get('applicable', True)]
            
            # Use the stored, lenient accuracy metrics so the AI feedback always
            # agrees with the numbers shown on the grade page.
            overall = analysis_results.get('overall_score', {}) or {}
            word_accuracy = overall.get('word_accuracy')
            pronunciation_accuracy = overall.get('pronunciation_accuracy')
            text_accuracy = pronunciation_accuracy if pronunciation_accuracy is not None \
                else (word_accuracy if word_accuracy is not None else 0.0)
            word_accuracy_display = word_accuracy if word_accuracy is not None else text_accuracy
            
            # Get reference comparison if available
            ref_comparison = analysis_results.get('reference_comparison', {})
            ref_similarity = ref_comparison.get('overall_similarity', 0) if ref_comparison else 0

            tajweed_lines = []
            for rule in applicable_rules:
                tajweed_lines.append(
                    f"- {rule['label']}: {rule['percentage']}% correct ({rule['issues_count']} issues)"
                )

            if not tajweed_lines:
                tajweed_lines.append('- No target rules are present in this verse.')

            for rule in non_applicable_rules:
                tajweed_lines.append(f"- {rule['label']}: Not present in this verse")

            tajweed_summary = "\n".join(tajweed_lines)
            
            # Prepare analysis summary for GPT
            prompt = f"""You are an expert Quran Tajweed teacher. Analyze this student's recitation and provide constructive, accurate feedback.

CRITICAL: Base your feedback on ALL metrics below, especially pronunciation accuracy.

Expected Quranic Text: {expected}
Student's Transcription: {transcription}
Pronunciation Accuracy: {text_accuracy:.1f}%
Word Accuracy: {word_accuracy_display:.1f}%

Reference Audio Comparison:
- Overall Similarity: {ref_similarity:.1f}%
- Pitch Match: {ref_comparison.get('pitch_similarity', 0):.1f}%
- Tempo Match: {ref_comparison.get('tempo_similarity', 0):.1f}%

Tajweed Rules Analysis:
{tajweed_summary}

Overall Score: {analysis_results['overall_score']['score']}%

IMPORTANT GUIDELINES:
- If pronunciation/word accuracy < 50%, flag MAJOR pronunciation/articulation errors
- If pronunciation/word accuracy is 50-70%, note moderate pronunciation issues
- If pronunciation/word accuracy > 70%, acknowledge correct articulation as a strength
- If reference similarity < 50%, mention pitch/rhythm issues
- If Tajweed scores are low, explain WHICH rules need work and WHY
- Be specific about what letters/sounds are mispronounced
- If transcription doesn't match expected text, identify the incorrect words
- Do NOT give improvement points for rules labeled "Not present in this verse"

Provide feedback in this EXACT JSON format:
{{
  "summary": "Brief 2-3 sentence overview highlighting main issues or strengths",
  "strengths": ["strength 1 (only if accuracy > 70%)", "strength 2"],
  "improvements": [
    {{"issue": "specific pronunciation/Tajweed problem", "suggestion": "how to fix it with practice tips"}},
    {{"issue": "specific problem", "suggestion": "how to fix it"}}
  ],
  "next_steps": "Specific practice recommendation focusing on weakest area"
}}

Be honest, specific, and constructive. Students need ACCURATE feedback to improve."""

            # Use GPT-4o for the best quality and cost-effectiveness
            response = client.chat.completions.create(
                model="gpt-4o",  # or "gpt-3.5-turbo" for maximum cost savings
                messages=[
                    {"role": "system", "content": "You are an expert Quran Tajweed teacher. You MUST respond with valid JSON only, no other text."},
                    {"role": "user", "content": prompt}
                ],
                max_tokens=500,
                temperature=0.7
            )
            
            feedback_text = response.choices[0].message.content.strip()
            
            # Remove markdown code blocks if present (```json ... ```)
            if feedback_text.startswith('```'):
                feedback_text = feedback_text.split('```')[1]
                if feedback_text.startswith('json'):
                    feedback_text = feedback_text[4:]
                feedback_text = feedback_text.strip()
            
            # Try to parse as JSON
            try:
                feedback_obj = json.loads(feedback_text)
                feedback_obj = self.filter_feedback_by_applicability(feedback_obj, rule_contexts)
                print(json.dumps({
                    "status": "openai_success",
                    "model": response.model,
                    "tokens_used": response.usage.total_tokens if hasattr(response, 'usage') else 0
                }), file=sys.stderr)
                return feedback_obj
            except json.JSONDecodeError as je:
                print(json.dumps({
                    "status": "json_parse_failed",
                    "error": str(je),
                    "raw_response": feedback_text[:200]
                }), file=sys.stderr)
                # Fallback to simple format if JSON parsing fails
                fallback_feedback = {
                    "summary": feedback_text,
                    "strengths": [],
                    "improvements": [],
                    "next_steps": ""
                }
                return self.filter_feedback_by_applicability(fallback_feedback, rule_contexts)
            
        except Exception as e:
            error_str = str(e)
            error_type = type(e).__name__
            print(json.dumps({
                "status": "openai_error",
                "error_type": error_type,
                "error": error_str[:500]  # Limit error message length
            }), file=sys.stderr)
            
            # Generate intelligent fallback feedback based on analysis results
            return self.generate_fallback_feedback(analysis_results, error_str)
    
    def generate_fallback_feedback(self, analysis_results, error_reason=""):
        """Generate intelligent fallback feedback when OpenAI API is unavailable"""
        score = analysis_results['overall_score']['score']
        rule_contexts = self.get_rule_feedback_contexts(analysis_results)

        rule_definitions = {
            'madd_analysis': {
                'strength': 'Good application of Madd (elongation) rules',
                'issue_template': 'Madd (elongation) needs attention ({issues} issues detected)',
                'suggestion': 'Practice holding vowels for the correct duration (2-6 counts). Listen to reference audio carefully.',
                'next_step': "Focus on Madd rules. Listen to Sheikh Alafasy's reference audio multiple times before practicing."
            },
            'idgham_bila_ghunnah_analysis': {
                'strength': 'Strong understanding of Idgham Bila Ghunnah (merging without nasalization)',
                'issue_template': 'Idgham Bila Ghunnah requires work ({issues} issues found)',
                'suggestion': 'Focus on merging Noon Sakin with Raa (ر) and Lam (ل) without nasalization. Practice slowly.',
                'next_step': 'Work on Idgham Bila Ghunnah. Record yourself again and compare with the reference to hear the differences.'
            },
            'idgham_bi_ghunnah_analysis': {
                'strength': 'Excellent use of Idgham Bi Ghunnah (merging with nasalization)',
                'issue_template': 'Idgham Bi Ghunnah needs improvement ({issues} issues detected)',
                'suggestion': 'Practice nasalization (ghunnah) when merging with letters و م ن ي. Hold for 2 counts.',
                'next_step': "Practice Idgham Bi Ghunnah with nasalization. Listen to Sheikh Alafasy's reference audio multiple times before practicing."
            }
        }

        applicable_rules = [rule for rule in rule_contexts if rule.get('applicable', True)]
        
        # Determine strengths based on scores
        strengths = []
        for rule in applicable_rules:
            rule_key = rule.get('key')
            rule_info = rule_definitions.get(rule_key)
            if rule_info and rule.get('percentage', 0) >= 80:
                strengths.append(rule_info['strength'])

        if score >= 85:
            strengths.append("Overall strong Tajweed application")
        elif score >= 70:
            strengths.append("Solid foundation in Tajweed principles")
        else:
            strengths.append("Completed recitation with effort")

        if not applicable_rules:
            strengths.append('The selected verse has no target Madd/Idgham rule occurrences.')
        
        # Determine improvements needed
        improvements = []
        for rule in applicable_rules:
            rule_key = rule.get('key')
            rule_info = rule_definitions.get(rule_key)
            if not rule_info:
                continue

            issues_count = int(rule.get('issues_count', 0) or 0)
            if issues_count > 0:
                improvements.append({
                    "issue": rule_info['issue_template'].format(issues=issues_count),
                    "suggestion": rule_info['suggestion']
                })
        
        # Generate summary
        if score >= 90:
            summary = "Excellent recitation! Your Tajweed application is very strong. Continue practicing to maintain this level."
        elif score >= 80:
            summary = "Very good recitation! You demonstrate solid understanding of Tajweed rules with minor areas for improvement."
        elif score >= 70:
            summary = "Good effort! You show understanding of key Tajweed principles. Focus on the specific areas below to improve."
        elif score >= 60:
            summary = "Your recitation shows potential. Consistent practice on the identified areas will lead to significant improvement."
        else:
            summary = "Keep practicing! Tajweed mastery takes time. Focus on one rule at a time and listen carefully to reference audio."
        
        # Add API failure context if error occurred
        if 'insufficient_quota' in error_reason.lower() or '429' in error_reason:
            summary += " (Note: AI-enhanced feedback temporarily unavailable due to API quota. Analysis based on acoustic analysis.)"
        elif error_reason:
            summary += " (Note: Advanced AI feedback unavailable. Analysis based on acoustic analysis.)"
        
        # Generate next steps
        # Select most relevant next step based on weakest applicable rule
        if applicable_rules:
            weakest = min(applicable_rules, key=lambda rule: rule.get('percentage', 0))
            weakest_key = weakest.get('key')
            next_steps = rule_definitions.get(weakest_key, {}).get(
                'next_step',
                'Focus on one Tajweed rule at a time rather than trying to perfect everything at once.'
            )
        else:
            next_steps = 'Keep strengthening pronunciation accuracy and rhythm by repeating the verse with the reference recitation.'
        
        feedback_obj = {
            "summary": summary,
            "strengths": strengths if strengths else ["Submission completed"],
            "improvements": improvements,
            "next_steps": next_steps
        }
        return self.filter_feedback_by_applicability(feedback_obj, rule_contexts)
    
    def compare_with_reference(self):
        """
        Compare student audio with reference recitation
        Uses MFCC + DTW (Dynamic Time Warping) for similarity comparison
        Analyzes pitch, rhythm, and pronunciation differences
        """
        if self.y_ref is None:
            return None
        
        try:
            from scipy.spatial.distance import euclidean
            from fastdtw import fastdtw
            
            # Extract MFCC features from both audios
            mfcc_student = librosa.feature.mfcc(y=self.y, sr=self.sr, n_mfcc=13)
            mfcc_reference = librosa.feature.mfcc(y=self.y_ref, sr=self.sr_ref, n_mfcc=13)
            
            # Transpose for DTW (time steps x features)
            mfcc_student = mfcc_student.T
            mfcc_reference = mfcc_reference.T
            
            # Calculate DTW distance
            distance, path = fastdtw(mfcc_student, mfcc_reference, dist=euclidean)
            
            # Normalize distance by length
            normalized_distance = distance / max(len(mfcc_student), len(mfcc_reference))
            
            # Convert to similarity score (0-100)
            # Lower distance = higher similarity
            # Typical range: 20-100 for distance, we invert it
            similarity_score = max(0, 100 - (normalized_distance * 5))
            
            # Extract pitch contours
            pitch_student = librosa.yin(self.y, fmin=80, fmax=400, sr=self.sr)
            pitch_reference = librosa.yin(self.y_ref, fmin=80, fmax=400, sr=self.sr_ref)
            
            # Compare pitch stability (lower std = more stable)
            pitch_student_valid = pitch_student[~np.isnan(pitch_student)]
            pitch_reference_valid = pitch_reference[~np.isnan(pitch_reference)]
            
            if len(pitch_student_valid) > 0 and len(pitch_reference_valid) > 0:
                pitch_diff = abs(np.mean(pitch_student_valid) - np.mean(pitch_reference_valid))
                pitch_similarity = max(0, 100 - pitch_diff)
            else:
                pitch_similarity = 50
            
            # Analyze rhythm (tempo comparison)
            tempo_student, _ = librosa.beat.beat_track(y=self.y, sr=self.sr)
            tempo_reference, _ = librosa.beat.beat_track(y=self.y_ref, sr=self.sr_ref)
            tempo_diff = abs(tempo_student - tempo_reference)
            tempo_similarity = max(0, 100 - tempo_diff)
            
            # Overall comparison score
            overall_similarity = (similarity_score * 0.5 + pitch_similarity * 0.3 + tempo_similarity * 0.2)
            
            # Generate feedback based on similarity
            if overall_similarity >= 85:
                feedback = "Excellent! Your recitation closely matches the reference."
                grade = "Excellent"
            elif overall_similarity >= 70:
                feedback = "Very good recitation with minor differences from the reference."
                grade = "Very Good"
            elif overall_similarity >= 55:
                feedback = "Good attempt. Practice to match the reference more closely."
                grade = "Good"
            else:
                feedback = "Keep practicing. Listen carefully to the reference recitation."
                grade = "Needs Improvement"
            
            return {
                'has_reference': True,
                'reference_duration': round(float(self.duration_ref), 2),
                'student_duration': round(float(self.duration), 2),
                'overall_similarity': round(float(overall_similarity), 2),
                'pronunciation_similarity': round(float(similarity_score), 2),
                'pitch_similarity': round(float(pitch_similarity), 2),
                'tempo_similarity': round(float(tempo_similarity), 2),
                'grade': grade,
                'feedback': feedback,
                'details': {
                    'dtw_distance': round(float(normalized_distance), 2),
                    'student_avg_pitch': round(float(np.mean(pitch_student_valid)), 1) if len(pitch_student_valid) > 0 else 0,
                    'reference_avg_pitch': round(float(np.mean(pitch_reference_valid)), 1) if len(pitch_reference_valid) > 0 else 0,
                    'student_tempo': round(float(tempo_student), 1),
                    'reference_tempo': round(float(tempo_reference), 1)
                }
            }
            
        except ImportError:
            # fastdtw not installed, use simpler comparison
            return self.simple_audio_comparison()
        except Exception as e:
            print(json.dumps({
                "status": "comparison_error",
                "error": str(e)
            }), file=sys.stderr)
            return {
                'has_reference': True,
                'error': str(e),
                'feedback': 'Could not compare with reference due to technical error.'
            }
    
    def simple_audio_comparison(self):
        """Simple comparison without DTW library"""
        try:
            # Extract basic features
            mfcc_student = librosa.feature.mfcc(y=self.y, sr=self.sr, n_mfcc=13)
            mfcc_reference = librosa.feature.mfcc(y=self.y_ref, sr=self.sr_ref, n_mfcc=13)
            
            # Pad to same length
            max_len = max(mfcc_student.shape[1], mfcc_reference.shape[1])
            if mfcc_student.shape[1] < max_len:
                mfcc_student = np.pad(mfcc_student, ((0, 0), (0, max_len - mfcc_student.shape[1])))
            if mfcc_reference.shape[1] < max_len:
                mfcc_reference = np.pad(mfcc_reference, ((0, 0), (0, max_len - mfcc_reference.shape[1])))
            
            # Calculate cosine similarity
            from numpy.linalg import norm
            similarity = np.dot(mfcc_student.flatten(), mfcc_reference.flatten()) / (norm(mfcc_student.flatten()) * norm(mfcc_reference.flatten()))
            similarity_score = (similarity + 1) * 50  # Scale to 0-100
            
            if similarity_score >= 80:
                grade = "Excellent"
                feedback = "Very close to the reference recitation!"
            elif similarity_score >= 65:
                grade = "Good"
                feedback = "Good recitation. Minor improvements needed."
            else:
                grade = "Needs Improvement"
                feedback = "Keep practicing with the reference audio."
            
            return {
                'has_reference': True,
                'overall_similarity': round(float(similarity_score), 2),
                'grade': grade,
                'feedback': feedback,
                'note': 'Basic comparison (install fastdtw for detailed analysis)'
            }
        except Exception as e:
            return {
                'has_reference': True,
                'error': str(e)
            }
    
    def analyze(self):
        """Run complete Tajweed analysis"""
        # Short-circuit immediately if the audio is silent or empty
        if self.is_silent:
            return {
                'audio_file': self.audio_path,
                'duration': round(float(self.duration), 2),
                'error': 'No audio detected. Please record or upload a valid audio file.',
                'is_silent': True,
                'whisper_transcription': None,
                'expected_text': self.expected_text,
                'madd_analysis': {'percentage': 0, 'issues': [], 'details': []},
                'idgham_bila_ghunnah_analysis': {'percentage': 0, 'issues': [], 'details': []},
                'idgham_bi_ghunnah_analysis': {'percentage': 0, 'issues': [], 'details': []},
                'overall_score': {
                    'score': 0,
                    'grade': 'No Submission',
                    'feedback': 'No audio was detected in the recording. Please try again with a working microphone.'
                },
                'ai_feedback': None
            }

        # Get Whisper transcription first so rule analyses can verify against it.
        whisper_transcription_raw = None
        whisper_transcription = None
        pause_segments = []
        if self.whisper_model:
            whisper_transcription_raw = self.transcribe_with_whisper()
            if whisper_transcription_raw:
                pause_segments = self.detect_pause_segments()
                # Prefer ayah-boundary separators when the alignment succeeds;
                # otherwise fall back to audio-pause markers.
                ayah_marked = self._align_ayah_boundaries(whisper_transcription_raw)
                expected_ayahs = self.expected_text.count('۝') if '۝' in self.expected_text else 0
                if expected_ayahs > 0 and ayah_marked.count('۝') >= expected_ayahs:
                    whisper_transcription = ayah_marked
                else:
                    whisper_transcription = self.add_pause_markers_to_transcription(
                        whisper_transcription_raw,
                        pause_segments
                    )
            else:
                whisper_transcription = whisper_transcription_raw

        self._last_transcription = whisper_transcription_raw or whisper_transcription or ''

        madd = self.analyze_madd()
        idgham_bila = self.analyze_idgham_bila_ghunnah()
        idgham_bi = self.analyze_idgham_bi_ghunnah()

        # Generate short, per-rule feedback (OpenAI with deterministic fallback).
        rule_feedbacks = self._generate_rule_feedbacks({
            'madd_analysis': {
                'label': 'Madd (Elongation)',
                'total': madd.get('total_elongations', 0),
                'correct': madd.get('correct_elongations', 0),
                'percentage': madd.get('percentage', 0),
                'issues': madd.get('issues', []),
            },
            'idgham_bila_ghunnah_analysis': {
                'label': 'Idgham Bila Ghunnah',
                'total': idgham_bila.get('total_occurrences', 0),
                'correct': idgham_bila.get('correct_pronunciation', 0),
                'percentage': idgham_bila.get('percentage', 0),
                'issues': idgham_bila.get('issues', []),
            },
            'idgham_bi_ghunnah_analysis': {
                'label': 'Idgham Bi Ghunnah',
                'total': idgham_bi.get('total_occurrences', 0),
                'correct': idgham_bi.get('correct_pronunciation', 0),
                'percentage': idgham_bi.get('percentage', 0),
                'issues': idgham_bi.get('issues', []),
            },
        })
        madd['rule_feedback'] = rule_feedbacks['madd_analysis']
        idgham_bila['rule_feedback'] = rule_feedbacks['idgham_bila_ghunnah_analysis']
        idgham_bi['rule_feedback'] = rule_feedbacks['idgham_bi_ghunnah_analysis']
        
        # Compare with reference audio if provided
        reference_comparison = None
        if self.y_ref is not None:
            reference_comparison = self.compare_with_reference()
        
        results = {
            'audio_file': self.audio_path,
            'duration': round(float(self.duration), 2),
            'whisper_transcription': whisper_transcription,
            'whisper_transcription_raw': whisper_transcription_raw,
            'expected_text': self.expected_text,
            'pause_markers': {
                'count': len(pause_segments),
                'segments': pause_segments,
            },
            'rules_detected': {
                'madd': self.has_madd,
                'idgham_bila_ghunnah': self.has_idgham_bila,
                'idgham_bi_ghunnah': self.has_idgham_bi,
                'muqattaat': self.has_muqattaat,
            },
            'madd_analysis': madd,
            'idgham_bila_ghunnah_analysis': idgham_bila,
            'idgham_bi_ghunnah_analysis': idgham_bi,
            'overall_score': self.calculate_overall_score(madd, idgham_bila, idgham_bi, whisper_transcription, reference_comparison),
            'reference_comparison': reference_comparison
        }
        
        # Generate OpenAI feedback if enabled
        if self.use_openai:
            ai_feedback = self.generate_openai_feedback(results)
            if ai_feedback:
                ai_feedback = self.filter_feedback_by_applicability(
                    ai_feedback,
                    self.get_rule_feedback_contexts(results)
                )
                results['ai_feedback'] = ai_feedback
        
        return results
    
    def calculate_overall_score(self, madd, idgham_bila, idgham_bi, transcription=None, ref_comparison=None):
        """Calculate overall score with Tajweed as the primary factor."""
        scores = []
        
        # Add Tajweed rule scores
        if self.has_madd:
            scores.append(madd['percentage'])
        if self.has_idgham_bila:
            scores.append(idgham_bila['percentage'])
        if self.has_idgham_bi:
            scores.append(idgham_bi['percentage'])
        
        tajweed_score = (sum(scores) / len(scores)) if scores else None

        # Calculate pronunciation/word accuracy from normalized transcription.
        pronunciation_accuracy = None
        word_accuracy = None
        if transcription and self.expected_text:
            normalized_transcription = self.normalize_arabic_for_accuracy(transcription)
            normalized_expected = self.normalize_arabic_for_accuracy(self.expected_text)

            if normalized_transcription and normalized_expected:
                word_accuracy, pronunciation_accuracy = self.calculate_word_accuracy(
                    normalized_transcription,
                    normalized_expected
                )

        reference_similarity = None
        if ref_comparison and isinstance(ref_comparison, dict):
            reference_similarity = ref_comparison.get('overall_similarity')

        # If transcription is unavailable, keep scoring centered around available signals.
        if word_accuracy is None:
            if tajweed_score is not None:
                word_accuracy = tajweed_score
            elif reference_similarity is not None:
                word_accuracy = reference_similarity
            else:
                word_accuracy = 0.0
        if pronunciation_accuracy is None:
            pronunciation_accuracy = word_accuracy

        components = []
        # Keep overall grade mostly aligned with Tajweed rule analysis.
        if tajweed_score is not None:
            components.append(('tajweed_rules_score', tajweed_score, 0.75))

        components.append(('word_accuracy', word_accuracy, 0.20))

        if reference_similarity is not None:
            components.append(('reference_similarity', reference_similarity, 0.05))
        
        # Weighted overall score using only applicable components.
        weight_total = sum(weight for _, _, weight in components)
        if weight_total > 0:
            final_score = sum(value * weight for _, value, weight in components) / weight_total
        else:
            final_score = 0
        
        return {
            'score': round(final_score, 2),
            'pronunciation_accuracy': round(pronunciation_accuracy, 2),
            'word_accuracy': round(word_accuracy, 2),
            'reference_similarity': round(reference_similarity, 2) if reference_similarity is not None else None,
            'tajweed_rules_score': round(tajweed_score, 2) if tajweed_score is not None else None,
            'grade': self.get_grade(final_score),
            'feedback': self.get_feedback(final_score)
        }
    
    def get_grade(self, score):
        """Convert score to grade"""
        if score >= 90:
            return 'Excellent'
        elif score >= 80:
            return 'Very Good'
        elif score >= 70:
            return 'Good'
        elif score >= 60:
            return 'Satisfactory'
        else:
            return 'Needs Improvement'
    
    def get_feedback(self, score):
        """Generate feedback based on score"""
        if score >= 90:
            return 'Mashallah! Excellent application of Tajweed rules.'
        elif score >= 80:
            return 'Very good recitation with proper Tajweed.'
        elif score >= 70:
            return 'Good effort. Continue practicing these rules.'
        elif score >= 60:
            return 'Satisfactory. Focus on consistent application.'
        else:
            return 'Needs more practice. Review the rules with a teacher.'

def main():
    """Main function"""
    # Force UTF-8 encoding for output (critical for Arabic text)
    if sys.platform == 'win32':
        import io
        sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8')
        sys.stderr = io.TextIOWrapper(sys.stderr.buffer, encoding='utf-8')
    
    if len(sys.argv) < 2:
        print(json.dumps({
            'error': 'Usage: python tajweed_analyzer.py <audio_file> [expected_text] [--reference=<path>] [--no-whisper] [--no-openai]'
        }))
        sys.exit(1)
    
    audio_path = sys.argv[1]
    expected_text = ""
    reference_audio = None
    tajweed_html = ""
    
    task = 'analyze'
    # Parse arguments
    for arg in sys.argv[2:]:
        if arg.startswith('--reference='):
            reference_audio = arg.split('=', 1)[1]
        elif arg.startswith('--tajweed='):
            tajweed_html = arg.split('=', 1)[1]
        elif arg.startswith('--task='):
            task = arg.split('=', 1)[1]
        elif not arg.startswith('--'):
            expected_text = arg

    # Check for flags
    use_whisper = '--no-whisper' not in sys.argv
    use_openai = '--no-openai' not in sys.argv

    # Transcription-only mode: fast path for live memorization recording
    if task == 'transcribe':
        try:
            analyzer = TajweedAnalyzer(audio_path, use_whisper=True, use_openai=False)
            if analyzer.is_silent:
                print(json.dumps({'transcription': '', 'warning': 'Silent or empty audio detected'}))
            else:
                transcription = analyzer.transcribe_with_whisper()
                print(json.dumps({'transcription': transcription or ''}))
        except Exception as e:
            import traceback
            print(json.dumps({'transcription': '', 'error': str(e)}))
        sys.exit(0)

    try:
        analyzer = TajweedAnalyzer(audio_path, expected_text, use_whisper, use_openai, reference_audio, tajweed_html)
        results = analyzer.analyze()
        print(json.dumps(results, ensure_ascii=False, indent=2))
    except FileNotFoundError:
        print(json.dumps({
            'error': f'Audio file not found: {audio_path}'
        }))
        sys.exit(1)
    except Exception as e:
        import traceback
        print(json.dumps({
            'error': str(e),
            'traceback': traceback.format_exc()
        }))
        sys.exit(1)

if __name__ == '__main__':
    main()
