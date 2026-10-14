# Redhopper demo video (Remotion)

Səsləndirilmiş (Azərbaycan dilində) [redhopper.co](https://redhopper.co) demo videosu, [Remotion](https://github.com/remotion-dev/remotion) ilə hazırlanıb.

Hazır video: [`out/redhopper-demo.mp4`](out/redhopper-demo.mp4) — 1920×1080, 30 fps, ~2 dəq.

## Səhnələr

| # | Səhnə | Mənbə |
|---|-------|-------|
| 1 | Giriş (logo + slogan) | — |
| 2 | Əsas ekran, çat vidjetinə zoom | `redhopper.co/az` |
| 3 | Üç addımda hazır | landing scroll |
| 4 | İmkanlar | landing scroll |
| 5 | Kanallar və AI açarları | landing scroll |
| 6 | Qiymətlər | landing scroll |
| 7 | FAQ — AI cavab tapmasa | landing scroll |
| 8 | Operator girişi (`demo@chat.test` / `password`) | `redhopper.co/login` |
| 9 | Sonluq (CTA) | — |

Mətn və səsləndirmə mətni: `src/script.json` (`caption` — ekrandakı altyazı, `tts` — səsləndirilən mətn).

## Yenidən qurmaq

```bash
npm install
npm run capture   # Playwright ilə saytdan real skrinşotlar -> public/shots/
npm run voice     # offline TTS -> public/audio/*.wav + src/durations.json
npm run studio    # önizləmə
npm run render    # out/redhopper-demo.mp4
```

- **Səs**: Piper neural TTS (`tr_TR-dfki-medium` modeli) + espeak-ng Azərbaycan fonemləri — tam offline işləyir.
  Model: `https://github.com/k2-fsa/sherpa-onnx/releases/download/tts-models/vits-piper-tr_TR-dfki-medium.tar.bz2`,
  `pip install piper-tts`, `apt install espeak-ng`. Modelin yolu: `python3 scripts/voiceover.py <model.onnx>`.
- **Brauzer**: lokal Chromium üçün `REMOTION_CHROME=/path/to/headless_shell` və ya `--browser-executable`.
- Operator panelinin skrinşotu üçün `api.redhopper.co` və `challenges.cloudflare.com` (Turnstile) əlçatan olmalıdır;
  `capture.mjs` girişdən sonra `public/shots/dashboard.png` yazır.
