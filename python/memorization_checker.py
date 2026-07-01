#!/usr/bin/env python3
"""
memorization_checker.py
Word-level Quranic recitation checker using whisper-timestamped (or standard whisper).

Usage:
    python memorization_checker.py <audio_file_path> <target_arabic_text>

Output JSON:
{
    "is_perfect":     bool,
    "accuracy_score": float,   # 0.0 â€“ 1.0
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
import unicodedata
from typing import List, Dict, Optional, Any, Tuple

CONFIDENCE_THRESHOLD        = 0.70  # standard Arabic words
CONFIDENCE_THRESHOLD_HUKUM  = 0.85  # words with Tajweed/Hukum markers (Small Waw/Ya, Madd etc.)
MODEL_SIZE = "base"  # standard whisper fallback

# Quran-specific fine-tuned Whisper models (tried before standard whisper)
QURAN_MODEL_IDS = [
    "tarteel-ai/whisper-base-ar-quran",
    "MaddoggProduction/whisper-small-quran-lora-dataset-mix",
]

# â”€â”€ Hukum detection â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
_HUKUM_RE = re.compile(r'[\u06D6-\u06E6\u0653]')

def has_hukum(word: str) -> bool:
    """True if word contains special Tajweed/Hukum markers (Small Waw \u06e5, Small Ya \u06e6, Maddah etc.)."""
    return bool(_HUKUM_RE.search(word))

# â”€â”€ Arabic normalisation â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€

# NOTE: \u0671 (Alef Wasla) intentionally excluded — converted to plain Alef via _ALEF_MAP.
# NOTE: \u0670 (Dagger Alef) intentionally excluded — converted to plain Alef below (it
#       represents a phonetic long-vowel alef in Uthmani script, e.g. \u0639\u064e\u0640\u0670 -> \u0639\u0627).
_TASHKEEL  = re.compile(r'[\u0610-\u061A\u064B-\u065F]')
_TATWEEL   = re.compile(r'\u0640')
_ALEF_MAP  = {'\u0622': '\u0627', '\u0623': '\u0627', '\u0625': '\u0627', '\u0671': '\u0627'}


def normalize_arabic(text: str) -> str:
    """
    Full normalisation for comparison:
    - NFC canonical form (aligns complex Unicode sequences like \u064a\u0670\u0653)
    - Strip tashkeel/diacritics
    - Normalize Alef variants including Alef Wasla (\u0671 -> \u0627)
    - Convert Small Waw (\u06e5 \u06e5) and Small Ya (\u06e6 \u06e6) to base letters
    - Strip Quranic annotation marks (U+06D6-U+06FF)
    - Normalize ta marbuta (\u0629 -> \u0647) and alef maqsura (\u0649 -> \u064a)
    - Strip \u0627\u0644 after connector when followed by sun letter (assimilation in recitation)
    - Normalize \u0637 -> \u062a (speech engines often transcribe \u0637 as \u062a)
    - Collapse whitespace
    """
    text = unicodedata.normalize('NFC', text)
    text = _TASHKEEL.sub('', text)
    text = _TATWEEL.sub('', text)
    text = text.replace('\u0670', '')          # strip dagger alef (long vowel handled by medial-alef strip below)
    for variant, canon in _ALEF_MAP.items():
        text = text.replace(variant, canon)
    # Small Waw / Small Ya (Madd Silah markers) -> base letters
    text = text.replace('\u06E5', '\u0648')   # \u06e5 -> \u0648
    text = text.replace('\u06E6', '\u064A')   # \u06e6 -> \u064a
    # Strip Quranic annotation marks (small high letters, stops, sajda signs, etc.)
    text = re.sub(r'[\u06D6-\u06E4\u06E7-\u06FF]', '', text)
    # ta marbuta -> ha
    text = text.replace('\u0629', '\u0647')
    # alef maqsura -> ya
    text = text.replace('\u0649', '\u064A')
    # Strip medial alef: handles dagger-alef long vowels (\u0630\u0627\u0644\u0643 \u2192 \u0630\u0644\u0643, \u0639\u0627\u0644\u0645\u064a\u0646 \u2192 \u0639\u0644\u0645\u064a\u0646, \u0643\u062a\u0627\u0628 \u2192 \u0643\u062a\u0628)
    # Alef surrounded by two Arabic chars = optional long vowel, strip from both sides
    text = re.sub(r'([\u0600-\u06FF])\u0627([\u0600-\u06FF])', r'\1\2', text)
    # Strip \u0627\u0644 after word-initial connector before sun letter (assimilation)
    text = re.sub(r'(?<=[\u0648\u0628\u0641\u0644\u0643])\u0627\u0644(?=[\u062a\u062b\u062f\u0630\u0631\u0632\u0633\u0634\u0635\u0636\u0637\u0638\u0646])', '', text)
    # \u0637 -> \u062a (emphatic ta misrecognised by speech engines)
    text = text.replace('\u0637', '\u062a')
    text = text.replace('\u200c', '').replace('\u200d', '').replace('\ufeff', '')
    return re.sub(r'\s+', ' ', text).strip()


# â”€â”€ Model loading â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€

def _load_model() -> Tuple[Any, Any, str]:
    """
    Model loading priority:
    1. Quran-specific fine-tuned Whisper via HuggingFace transformers
    2. whisper_timestamped (per-word confidence natively)
    3. Standard openai-whisper fallback
    Returns (model, whisper_module_or_None, backend).
    backend: "hf" | "timestamped" | "standard"
    """
    # 1. Try Quran-specific HuggingFace models
    for model_id in QURAN_MODEL_IDS:
        try:
            from transformers import pipeline  # type: ignore
            pipe = pipeline(
                "automatic-speech-recognition",
                model=model_id,
                return_timestamps="word",
            )
            return pipe, None, "hf"
        except Exception:
            continue

    # 2. whisper_timestamped
    try:
        import whisper_timestamped as whisper  # type: ignore
        model = whisper.load_model(MODEL_SIZE)
        return model, whisper, "timestamped"
    except ImportError:
        pass

    # 3. Standard openai-whisper
    try:
        import whisper  # type: ignore
        model = whisper.load_model(MODEL_SIZE)
        return model, whisper, "standard"
    except ImportError:
        raise RuntimeError(
            "No supported ASR library found. Install one of:\n"
            "  pip install transformers torch   (Quran-specific model, recommended)\n"
            "  pip install whisper-timestamped  (alternative)\n"
            "  pip install openai-whisper       (basic fallback)"
        )


# â”€â”€ Transcription â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€

def _transcribe(model: Any, whisper_mod: Any, backend: str,
                audio_path: str, target_text: str) -> List[Dict]:
    """
    Transcribe audio, return list of {word, start, end, confidence}.
    backend: "hf" | "timestamped" | "standard"
    """
    if not os.path.isfile(audio_path):
        raise FileNotFoundError(f"Audio file not found: {audio_path}")

    words: List[Dict] = []

    if backend == "hf":
        # HuggingFace transformers pipeline (model IS the pipeline callable)
        result = model(
            audio_path,
            generate_kwargs={"language": "arabic", "task": "transcribe"},
        )
        for chunk in result.get("chunks", []):
            text = (chunk.get("text") or "").strip()
            if not text:
                continue
            ts = chunk.get("timestamp") or (0.0, 0.0)
            words.append({
                "word":       text,
                "start":      round(float(ts[0] or 0.0), 3),
                "end":        round(float(ts[1] or 0.0), 3),
                "confidence": 1.0,  # HF pipeline does not expose per-word probs
            })
        return words

    # Shared audio loading for whisper backends
    audio = whisper_mod.load_audio(audio_path)
    if audio is None or (hasattr(audio, '__len__') and len(audio) == 0):
        raise ValueError("Audio file is empty or could not be decoded.")

    if backend == "timestamped":
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
    else:  # standard openai-whisper
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
                words.append({
                    "word":       text,
                    "start":      round(float(w.get("start", 0.0)), 3),
                    "end":        round(float(w.get("end", 0.0)), 3),
                    "confidence": round(float(w.get("probability", 0.0)), 4),
                })

    return words
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


# â”€â”€ Comparison â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€

def _compare(transcribed_words: List[Dict], target_text: str) -> List[Dict]:
    """
    Cross-examine transcribed words against the target.
    Returns a list of flagged word dicts.
    Hukum words (containing special Tajweed markers) use a higher confidence threshold.
    """
    target_tokens_raw = target_text.split()
    target_tokens     = [normalize_arabic(t) for t in target_tokens_raw]
    trans_norm        = [normalize_arabic(w["word"]) for w in transcribed_words]

    flagged: List[Dict] = []

    # â”€â”€ 1. Flag low-confidence transcribed words â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    for w in transcribed_words:
        if w["confidence"] < CONFIDENCE_THRESHOLD:
            flagged.append({
                "word":       w["word"],
                "start_time": w["start"],
                "end_time":   w["end"],
                "confidence": w["confidence"],
                "error_type": "low_confidence",
            })

    # â”€â”€ 2. Align transcription against target (sliding window) â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    WINDOW = 5
    used_trans  = [False] * len(trans_norm)
    matched_tgt : Dict[int, int] = {}  # target_idx -> transcribed_idx

    for ti, tword in enumerate(target_tokens):
        lo = max(0, ti - WINDOW)
        hi = min(len(trans_norm), ti + WINDOW + 1)
        for ri in range(lo, hi):
            if not used_trans[ri] and trans_norm[ri] == tword:
                matched_tgt[ti] = ri
                used_trans[ri]  = True
                break

    # â”€â”€ 3. Flag Hukum words matched with insufficient confidence â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    for ti, ri in matched_tgt.items():
        if has_hukum(target_tokens_raw[ti]):
            w = transcribed_words[ri]
            if w["confidence"] < CONFIDENCE_THRESHOLD_HUKUM:
                already = any(
                    f["word"] == w["word"] and f["error_type"] in ("low_confidence", "low_confidence_hukum")
                    for f in flagged
                )
                if not already:
                    flagged.append({
                        "word":       w["word"],
                        "start_time": w["start"],
                        "end_time":   w["end"],
                        "confidence": w["confidence"],
                        "error_type": "low_confidence_hukum",
                    })

    # â”€â”€ 4. Flag substitutions: transcribed words not matched to any target â”€
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

    # â”€â”€ 5. Flag omissions: target tokens absent from transcription â”€â”€â”€â”€â”€â”€â”€â”€â”€
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


# â”€â”€ Public API â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€

def analyze(audio_path: str, target_text: str) -> Dict:
    """
    Full analysis pipeline. Returns the result dict ready for JSON serialisation.
    """
    model, whisper_mod, backend = _load_model()
    words = _transcribe(model, whisper_mod, backend, audio_path, target_text)

    if not words:
        return {
            "is_perfect":     False,
            "accuracy_score": 0.0,
            "flagged_words":  [],
            "error":          "No words were transcribed from the audio.",
        }

    # Accuracy = fraction of transcribed words with confidence â‰¥ threshold
    confident = sum(1 for w in words if w["confidence"] >= CONFIDENCE_THRESHOLD)
    accuracy  = round(confident / len(words), 4) if words else 0.0

    flagged    = _compare(words, target_text)
    is_perfect = len(flagged) == 0

    return {
        "is_perfect":     is_perfect,
        "accuracy_score": accuracy,
        "flagged_words":  flagged,
    }


# â”€â”€ CLI â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€

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
