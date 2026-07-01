#!/usr/bin/env python3
"""
memorization_checker.py
Word-level Quranic recitation checker using whisper-timestamped (or standard whisper).

Usage:
    python memorization_checker.py <audio_file_path> <target_arabic_text>

Output JSON:
{
    "is_perfect":     bool,
    "accuracy_score": float,   # 0.0 – 1.0
    "flagged_words":  [
        {
            "word":       "arabic_word",
            "start_time": float | null,
            "end_time":   float | null,
            "confidence": float,
            "error_type": "low_confidence" | "omission" | "substitution"
        }
    ]
}
"""

import sys
import re
import json
import os
from typing import List, Dict, Optional, Any, Tuple

CONFIDENCE_THRESHOLD = 0.75
MODEL_SIZE = "base"  # lightweight; change to "small" for better Arabic accuracy


# ── Arabic normalisation ──────────────────────────────────────────────────────

_TASHKEEL  = re.compile(r'[\u0610-\u061A\u064B-\u065F\u0670\u0671]')
_TATWEEL   = re.compile(r'\u0640')
_ALEF_MAP  = {'\u0622': '\u0627', '\u0623': '\u0627', '\u0625': '\u0627', '\u0671': '\u0627'}


def normalize_arabic(text: str) -> str:
    """Strip diacritics, normalize alef variants, collapse whitespace."""
    text = _TASHKEEL.sub('', text)
    text = _TATWEEL.sub('', text)
    for variant, canon in _ALEF_MAP.items():
        text = text.replace(variant, canon)
    text = text.replace('\u200c', '').replace('\u200d', '').replace('\ufeff', '')
    return _TASHKEEL.sub('', re.sub(r'\s+', ' ', text)).strip()


# ── Model loading ─────────────────────────────────────────────────────────────

def _load_model() -> Tuple[Any, Any, bool]:
    """
    Try whisper_timestamped first (gives per-word confidence natively).
    Fall back to standard openai-whisper with word_timestamps=True.
    Returns (model, whisper_module, is_timestamped).
    """
    try:
        import whisper_timestamped as whisper  # type: ignore
        model = whisper.load_model(MODEL_SIZE)
        return model, whisper, True
    except ImportError:
        pass

    try:
        import whisper  # type: ignore  # openai-whisper
        model = whisper.load_model(MODEL_SIZE)
        return model, whisper, False
    except ImportError:
        raise RuntimeError(
            "Neither 'whisper-timestamped' nor 'openai-whisper' is installed. "
            "Run: pip install whisper-timestamped"
        )


# ── Transcription ─────────────────────────────────────────────────────────────

def _transcribe(model: Any, whisper_mod: Any, is_timestamped: bool,
                audio_path: str, target_text: str) -> List[Dict]:
    """
    Transcribe the audio file and return a list of word dicts:
      { word, start, end, confidence }
    Raises FileNotFoundError / ValueError on bad input.
    """
    if not os.path.isfile(audio_path):
        raise FileNotFoundError(f"Audio file not found: {audio_path}")

    audio = whisper_mod.load_audio(audio_path)
    if audio is None or (hasattr(audio, '__len__') and len(audio) == 0):
        raise ValueError("Audio file is empty or could not be decoded.")

    words: List[Dict] = []

    if is_timestamped:
        # whisper_timestamped: each word has a "confidence" key directly
        result = whisper_mod.transcribe(
            model, audio,
            language="ar",
            initial_prompt=target_text,
            verbose=False,
        )
        for segment in result.get("segments", []):
            for w in segment.get("words", []):
                text = w.get("text", "").strip()
                if not text:
                    continue
                words.append({
                    "word":       text,
                    "start":      round(float(w.get("start", 0.0)), 3),
                    "end":        round(float(w.get("end", 0.0)), 3),
                    "confidence": round(float(w.get("confidence", 0.0)), 4),
                })
    else:
        # openai-whisper: word-level probability via word_timestamps=True
        result = whisper_mod.transcribe(
            model, audio_path,
            language="ar",
            initial_prompt=target_text,
            word_timestamps=True,
            verbose=False,
        )
        for segment in result.get("segments", []):
            for w in segment.get("words", []):
                text = w.get("word", "").strip()
                if not text:
                    continue
                # openai-whisper stores per-word prob as "probability"
                conf = float(w.get("probability", 0.0))
                words.append({
                    "word":       text,
                    "start":      round(float(w.get("start", 0.0)), 3),
                    "end":        round(float(w.get("end", 0.0)), 3),
                    "confidence": round(conf, 4),
                })

    return words


# ── Comparison ────────────────────────────────────────────────────────────────

def _compare(transcribed_words: List[Dict], target_text: str) -> List[Dict]:
    """
    Cross-examine transcribed words against the target.
    Returns a list of flagged word dicts (low_confidence | omission | substitution).
    """
    target_tokens = normalize_arabic(target_text).split()
    trans_norm    = [normalize_arabic(w["word"]) for w in transcribed_words]

    flagged: List[Dict] = []

    # ── 1. Flag low-confidence transcribed words ──────────────────────────
    for w in transcribed_words:
        if w["confidence"] < CONFIDENCE_THRESHOLD:
            flagged.append({
                "word":       w["word"],
                "start_time": w["start"],
                "end_time":   w["end"],
                "confidence": w["confidence"],
                "error_type": "low_confidence",
            })

    # ── 2. Align transcription against target (sliding window) ────────────
    WINDOW = 5
    used_trans   = [False] * len(trans_norm)
    matched_tgt  = set()

    for ti, tword in enumerate(target_tokens):
        lo = max(0, ti - WINDOW)
        hi = min(len(trans_norm), ti + WINDOW + 1)
        for ri in range(lo, hi):
            if not used_trans[ri] and trans_norm[ri] == tword:
                matched_tgt.add(ti)
                used_trans[ri] = True
                break

    # ── 3. Flag substitutions: transcribed words not matched to any target token ──
    for i, w in enumerate(transcribed_words):
        if used_trans[i]:
            continue
        norm = trans_norm[i]
        if not norm:
            continue
        already = any(f["word"] == w["word"] and f["error_type"] == "low_confidence"
                      for f in flagged)
        if not already and norm not in target_tokens:
            flagged.append({
                "word":       w["word"],
                "start_time": w["start"],
                "end_time":   w["end"],
                "confidence": w["confidence"],
                "error_type": "substitution",
            })

    # ── 4. Flag omissions: target tokens absent from transcription ─────────
    for ti, tword in enumerate(target_tokens):
        if ti not in matched_tgt:
            flagged.append({
                "word":       tword,
                "start_time": None,
                "end_time":   None,
                "confidence": 0.0,
                "error_type": "omission",
            })

    return flagged


# ── Public API ────────────────────────────────────────────────────────────────

def analyze(audio_path: str, target_text: str) -> Dict:
    """
    Full analysis pipeline. Returns the result dict ready for JSON serialisation.
    """
    model, whisper_mod, is_timestamped = _load_model()
    words = _transcribe(model, whisper_mod, is_timestamped, audio_path, target_text)

    if not words:
        return {
            "is_perfect":     False,
            "accuracy_score": 0.0,
            "flagged_words":  [],
            "error":          "No words were transcribed from the audio.",
        }

    # Accuracy = fraction of transcribed words with confidence ≥ threshold
    confident = sum(1 for w in words if w["confidence"] >= CONFIDENCE_THRESHOLD)
    accuracy  = round(confident / len(words), 4) if words else 0.0

    flagged    = _compare(words, target_text)
    is_perfect = len(flagged) == 0

    return {
        "is_perfect":     is_perfect,
        "accuracy_score": accuracy,
        "flagged_words":  flagged,
    }


# ── CLI ───────────────────────────────────────────────────────────────────────

def main() -> None:
    if len(sys.argv) < 3:
        print(json.dumps({
            "error": "Usage: memorization_checker.py <audio_file_path> <target_arabic_text>"
        }))
        sys.exit(1)

    audio_path  = sys.argv[1]
    target_text = sys.argv[2]

    try:
        result = analyze(audio_path, target_text)
        print(json.dumps(result, ensure_ascii=False))
    except (FileNotFoundError, ValueError) as exc:
        print(json.dumps({"error": str(exc)}))
        sys.exit(1)
    except Exception as exc:
        print(json.dumps({"error": f"Unexpected error: {exc}"}))
        sys.exit(1)


if __name__ == "__main__":
    main()


# ── Arabic text normalization ─────────────────────────────────────────────────

TASHKEEL = re.compile(r'[\u0610-\u061A\u064B-\u065F\u0670\u0671]')
TATWEEL  = re.compile(r'\u0640')

ALEF_VARIANTS = {
    '\u0622': '\u0627',  # Alef with Madda
    '\u0623': '\u0627',  # Alef with Hamza above
    '\u0625': '\u0627',  # Alef with Hamza below
    '\u0671': '\u0627',  # Alef Wasla
}

def normalize_arabic(text: str) -> str:
    """Remove diacritics, normalize alef variants, collapse whitespace."""
    text = TASHKEEL.sub('', text)
    text = TATWEEL.sub('', text)
    for variant, canonical in ALEF_VARIANTS.items():
        text = text.replace(variant, canonical)
    # Remove tatweel and zero-width characters
    text = text.replace('\u200c', '').replace('\u200d', '').replace('\ufeff', '')
    text = re.sub(r'\s+', ' ', text).strip()
    return text


# ── Quran Foundation API ─────────────────────────────────────────────────────

def get_surah_verses(surah_number: int) -> list:
    """Fetch all verse texts for a surah from Quran Foundation API."""
    url = (
        f"https://apis.quran.foundation/content/api/v4/quran/verses/uthmani_tajweed"
        f"?chapter_number={surah_number}&per_page=300"
    )
    req = urllib.request.Request(url, headers={"Accept": "application/json"})
    try:
        with urllib.request.urlopen(req, timeout=10) as resp:
            data = json.loads(resp.read().decode())
            return data.get("verses", [])
    except urllib.error.URLError as e:
        raise RuntimeError(f"Failed to fetch verses for Surah {surah_number}: {e}")


def get_verse_text(surah_number: int, verse_number: int) -> str:
    """Return the uthmani_tajweed text for a single verse."""
    verses = get_surah_verses(surah_number)
    for verse in verses:
        key = verse.get("verse_key", "")
        _, v_num = key.split(":", 1)
        if int(v_num) == verse_number:
            return verse.get("text_uthmani_tajweed", "")
    raise ValueError(f"Verse {surah_number}:{verse_number} not found in API response.")


# ── Comparison ────────────────────────────────────────────────────────────────

def compare_texts(transcribed: str, expected: str) -> dict:
    """
    Word-level accuracy between transcribed and expected Arabic text.
    Both inputs are normalized before comparison.
    Returns a dict with accuracy, matched_words, total_words.
    """
    t_words = normalize_arabic(transcribed).split()
    e_words = normalize_arabic(expected).split()

    if not e_words:
        return {"accuracy": 0.0, "matched_words": 0, "total_words": 0}

    # Count matching words (order-insensitive within small sliding window)
    matched = 0
    used = [False] * len(t_words)
    window = 4  # allow up to 4-word positional shift for speech vs text differences
    for i, ew in enumerate(e_words):
        lo = max(0, i - window)
        hi = min(len(t_words), i + window + 1)
        for j in range(lo, hi):
            if not used[j] and t_words[j] == ew:
                matched += 1
                used[j] = True
                break

    total = len(e_words)
    accuracy = round(matched / total, 4) if total > 0 else 0.0
    return {
        "accuracy": accuracy,
        "matched_words": matched,
        "total_words": total,
        "accuracy_pct": round(accuracy * 100, 1),
    }


# ── CLI entry point ───────────────────────────────────────────────────────────

def main():
    if len(sys.argv) < 3:
        print(json.dumps({"error": "Usage: memorization_checker.py <surah> <verse> [transcribed_text]"}))
        sys.exit(1)

    try:
        surah_number  = int(sys.argv[1])
        verse_number  = int(sys.argv[2])
        transcribed   = sys.argv[3] if len(sys.argv) > 3 else ""

        expected_text = get_verse_text(surah_number, verse_number)
        result = compare_texts(transcribed, expected_text)
        result["expected"]    = expected_text
        result["transcribed"] = transcribed
        result["surah"]       = surah_number
        result["verse"]       = verse_number

        print(json.dumps(result, ensure_ascii=False))
    except (ValueError, RuntimeError) as e:
        print(json.dumps({"error": str(e)}))
        sys.exit(1)


if __name__ == "__main__":
    main()
