#!/usr/bin/env python3
"""
memorization_checker.py
Lightweight memorization accuracy checker using Quran Foundation API.

Usage:
    python memorization_checker.py <surah_number> <verse_number> [transcribed_text]

Output: JSON  { "accuracy": 0.87, "expected": "...", "matched_words": 13, "total_words": 15 }
"""

import sys
import re
import json
import unicodedata
import urllib.request
import urllib.error

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
