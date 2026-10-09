# Redhopper — brag plan

**Input:** https://redhopper.co (live site, AZ locale). Demo dashboard login was not reachable (Cloudflare Turnstile + api.redhopper.co blocked by the sandbox network policy), so the video is built from the public site's real UI: the chat widget, copy, steps and pricing.

## Rubric
- **What:** AI live chat that answers from your own PDF documents, with human handoff.
- **For whom:** businesses with a website and customer questions (catalogs, rules, price lists).
- **Different:** answers cite the source PDF; unknown questions go to a live operator in real time; bring your own OpenAI / Claude / Gemini key; one-line embed; priced per operator.
- **Most impressive claim:** "AI müştəri suallarını 24/7 cavablandırsın. Cavab tapmadıqda söhbət dərhal canlı operatora ötürülür."
- **Visual hook:** the Redhopper widget answering a customer at 03:12 at night, with the "PDF · s.3" source chip.
- **Real UI shown:** the hero chat widget (indigo header, bubbles, source line), the three steps, the embed `<script>` snippet, the primary CTA.
- **Tone:** `app-store` — clean, smooth, confident; white + brand indigo (#4f46e5), Inter.
- **Share caption:** "Redhopper: öz PDF-lərinizdən 24/7 cavab verən AI canlı çat. Cavab tapmasa — dərhal operatora."

## Storyboard v2 (33.6s, 1920×1080, 30fps)

v2 adds, on request: charts (dashboard), SLA monitoring, and the three pricing packages. Dashboard and SLA screens are built in the site's design language with **illustrative demo data** (the real demo dashboard was unreachable from the sandbox); pricing is the real data from redhopper.co/pricing.

| # | Time | Scene |
|---|---|---|
| 1–3 | 0.0–11.5 | Hook, AI answer from PDF, operator handoff (unchanged) |
| 4 Charts | 11.55–16.1 | "Hər söhbət — qrafiklərdə." KPI tiles count up, daily conversations line chart (AI vs operator) draws, donut 85% AI |
| 5 SLA | 16.1–20.4 | Sidebar moves to SLA. Target rings (≤30 san / ≤2 dəq / ≤15 dəq), operator table with SLA bars, alert "SLA-ya 20 san qalıb" → auto-reassigned |
| 6 Setup | 20.45–24.9 | Three steps + embed snippet (unchanged) |
| 7 Packages | 24.95–29.5 | Başlanğıc 29 ₼ / Biznes 49 ₼ (highlighted) / Korporativ 99 ₼; prices count up, SLA row in Korporativ lights up |
| 8 Outro | 29.52–33.6 | Logo, CTA click at 31.52 |

## Storyboard v1 (20.0s)
| # | Time | Left (copy) | Right / visual | SFX |
|---|---|---|---|---|
| 1 Hook | 0.0–3.0 | "03:12" chip → "Müştəri sual verir. Siz yatırsınız." | Widget pops in; visitor types "Çatdırılma neçə gün çəkir?" | soft pop on send |
| 2 Answer | 3.0–7.5 | "Redhopper cavab verir — öz PDF-lərinizdən." | Typing dots → AI answer, then source chip "qaydalar.pdf · s.3"; a PDF page card highlights the line | pop, bong on source |
| 3 Handoff | 7.5–11.5 | "Cavab tapmadıqda — dərhal canlı operatora." | Visitor asks a new question → "Operatora ötürülür…" pill → Operator replies | pop, switch, pop |
| 4 Setup | 11.5–16.0 | "Üç addımda hazır" | 3 step cards on beats (PDF yükləyin / AI açarı: OpenAI · Claude · Gemini / Kodu yerləşdirin) + embed snippet types in | card slides, soft keys |
| 5 Outro | 16.0–20.0 | Logo, "Öz sənədlərinizdən cavab verən AI canlı çat" | "Pulsuz sınağa başla" button, cursor click at 17.5, "14 gün pulsuz · kart tələb olunmur", redhopper.co | soft impact + click |

Small illustrative chat text (the delivery question/answer, operator reply, filename) is product-in-use texture, not a claim.

## Audio
Music: happy-beats-business-moves-vol-1 (120 BPM; strong cues 16.02, 17.02, 17.52). Outro reveal at 16.0, CTA click at 17.5. Fade out last 1.2s. SFX soft and under the bed.
