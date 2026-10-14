"""Offline Azerbaijani voiceover for the demo video.

Uses a Piper neural voice (Turkish dfki model, closest available to Azerbaijani)
driven by espeak-ng Azerbaijani phonemes, so pronunciation follows Azerbaijani spelling.
Writes public/audio/<scene>.wav and src/durations.json.

Usage: python3 scripts/voiceover.py [path/to/model.onnx]
"""
import json
import sys
import wave
from pathlib import Path

import numpy as np
from piper import PiperVoice, SynthesisConfig

ROOT = Path(__file__).resolve().parent.parent
MODEL = Path(sys.argv[1] if len(sys.argv) > 1 else "/opt/tts/vits-piper-tr_TR-dfki-medium/tr_TR-dfki-medium.onnx")

# Azerbaijani phonemes the Turkish voice was never trained on -> closest Turkish sounds
PHONEME_MAP = {"x": "h", "c": "k", "ç": "k"}

voice = PiperVoice.load(MODEL, config_path=str(MODEL) + ".json")
voice.config.espeak_voice = "az"
_phonemize = voice.phonemize


def phonemize(text):
    sentences = []
    for s in _phonemize(text):
        s = [PHONEME_MAP.get(p, p) for p in s]
        # espeak-ng "az" renders t before ü as [ts] (ötürmə -> œtsyrmæ); keep a plain t
        s = [p for i, p in enumerate(s) if not (p == "s" and 0 < i and s[i - 1] == "t" and "".join(s[i + 1 : i + 3]).lstrip("ˈˌ")[:1] == "y")]
        sentences.append(s)
    return sentences


voice.phonemize = phonemize

syn = SynthesisConfig(length_scale=1.08, noise_scale=0.6, noise_w_scale=0.7)
rate = voice.config.sample_rate
pause = np.zeros(int(rate * 0.35), dtype=np.int16)

out_dir = ROOT / "public" / "audio"
out_dir.mkdir(parents=True, exist_ok=True)
durations = {}
for scene in json.loads((ROOT / "src" / "script.json").read_text()):
    parts = []
    for chunk in voice.synthesize(scene["tts"], syn_config=syn):
        parts += [chunk.audio_int16_array, pause]
    audio = np.concatenate(parts)
    with wave.open(str(out_dir / f"{scene['id']}.wav"), "wb") as w:
        w.setnchannels(1)
        w.setsampwidth(2)
        w.setframerate(rate)
        w.writeframes(audio.tobytes())
    durations[scene["id"]] = round(len(audio) / rate, 3)
    print(f"{scene['id']}: {durations[scene['id']]}s")

(ROOT / "src" / "durations.json").write_text(json.dumps(durations, indent=2) + "\n")
