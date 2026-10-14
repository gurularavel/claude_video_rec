import '@fontsource/manrope/400.css';
import '@fontsource/manrope/600.css';
import '@fontsource/manrope/800.css';
import React from 'react';
import {
  AbsoluteFill,
  Audio,
  Easing,
  Img,
  Sequence,
  interpolate,
  spring,
  staticFile,
  useCurrentFrame,
  useVideoConfig,
} from 'remotion';
import script from './script.json';
import durations from './durations.json';
import layout from '../public/shots/layout.json';
import loginLayout from '../public/shots/login-layout.json';

export const FPS = 30;
const AUDIO_DELAY = 12; // frames of silence before the narration of each scene
const TAIL = 18; // frames after the narration ends

const BRAND = { 50: '#fdf3f4', 100: '#fbe5e7', 600: '#9b1f2a', 700: '#7f1822', 800: '#5f121a', 900: '#400810' };
const FONT = 'Manrope, Inter, sans-serif';

type SceneId = (typeof script)[number]['id'];

const scenes = script.map((s) => ({
  ...s,
  frames: Math.ceil((durations as Record<string, number>)[s.id] * FPS) + AUDIO_DELAY + TAIL,
}));
export const totalFrames = scenes.reduce((sum, s) => sum + s.frames, 0);

// ---------- shared pieces ----------

const ease = Easing.bezier(0.65, 0, 0.35, 1);

const FadeIn: React.FC<{ children: React.ReactNode; frames?: number }> = ({ children, frames = 12 }) => {
  const frame = useCurrentFrame();
  const { durationInFrames } = useVideoConfig();
  const opacity = interpolate(frame, [0, frames, durationInFrames - 8, durationInFrames], [0, 1, 1, 0], {
    extrapolateLeft: 'clamp',
    extrapolateRight: 'clamp',
  });
  return <AbsoluteFill style={{ opacity }}>{children}</AbsoluteFill>;
};

const Backdrop: React.FC = () => (
  <AbsoluteFill
    style={{
      background: `radial-gradient(circle at 15% 10%, ${BRAND[100]} 0%, transparent 45%),
        radial-gradient(circle at 90% 90%, #fdeee4 0%, transparent 40%), #fbf8f8`,
    }}
  />
);

// Browser window that shows a 1920px-wide viewport scaled down; children are positioned in page CSS px.
const BROWSER_W = 1520;
const SCALE = BROWSER_W / 1920;
const BAR_H = 48;

const Browser: React.FC<{ url: string; children: React.ReactNode; zoom?: number; origin?: string }> = ({
  url,
  children,
  zoom = 1,
  origin = '50% 50%',
}) => {
  const frame = useCurrentFrame();
  const enter = spring({ frame, fps: FPS, config: { damping: 200 } });
  return (
    <div
      style={{
        position: 'absolute',
        left: (1920 - BROWSER_W) / 2,
        top: 34,
        width: BROWSER_W,
        height: BAR_H + 1080 * SCALE,
        borderRadius: 18,
        overflow: 'hidden',
        background: 'white',
        boxShadow: '0 30px 80px rgba(64,8,16,0.18), 0 0 0 1px rgba(64,8,16,0.06)',
        transform: `translateY(${(1 - enter) * 40}px)`,
      }}
    >
      <div
        style={{
          height: BAR_H,
          display: 'flex',
          alignItems: 'center',
          gap: 10,
          padding: '0 18px',
          background: '#f4f1f1',
          borderBottom: '1px solid #e9e4e4',
        }}
      >
        {['#ff5f57', '#febc2e', '#28c840'].map((c) => (
          <div key={c} style={{ width: 13, height: 13, borderRadius: 7, background: c }} />
        ))}
        <div
          style={{
            marginLeft: 24,
            flex: 1,
            maxWidth: 640,
            height: 30,
            borderRadius: 8,
            background: 'white',
            display: 'flex',
            alignItems: 'center',
            padding: '0 14px',
            fontFamily: FONT,
            fontSize: 16,
            color: '#52525b',
          }}
        >
          🔒&nbsp;&nbsp;{url}
        </div>
      </div>
      <div style={{ position: 'relative', width: 1920 * SCALE, height: 1080 * SCALE, overflow: 'hidden' }}>
        <div
          style={{
            position: 'absolute',
            width: 1920,
            height: 1080,
            transform: `scale(${SCALE})`,
            transformOrigin: '0 0',
            overflow: 'hidden',
          }}
        >
          <div style={{ position: 'absolute', inset: 0, transform: `scale(${zoom})`, transformOrigin: origin }}>
            {children}
          </div>
        </div>
      </div>
    </div>
  );
};

const Caption: React.FC<{ text: string }> = ({ text }) => {
  const frame = useCurrentFrame();
  const p = spring({ frame: frame - 8, fps: FPS, config: { damping: 200 } });
  return (
    <div
      style={{
        position: 'absolute',
        left: 0,
        right: 0,
        bottom: 26,
        display: 'flex',
        justifyContent: 'center',
        opacity: p,
        transform: `translateY(${(1 - p) * 20}px)`,
      }}
    >
      <div
        style={{
          maxWidth: 1500,
          padding: '14px 30px',
          borderRadius: 999,
          background: BRAND[900],
          color: 'white',
          fontFamily: FONT,
          fontWeight: 600,
          fontSize: 30,
          lineHeight: 1.3,
          textAlign: 'center',
          boxShadow: '0 12px 30px rgba(64,8,16,0.25)',
        }}
      >
        {text}
      </div>
    </div>
  );
};

// Scrolls the full landing page screenshot between two offsets.
const PageScroll: React.FC<{ from: number; to: number; drift?: number }> = ({ from, to, drift = 60 }) => {
  const frame = useCurrentFrame();
  const { durationInFrames } = useVideoConfig();
  const moved = interpolate(frame, [0, 40], [from, to], { extrapolateRight: 'clamp', easing: ease });
  const extra = interpolate(frame, [40, durationInFrames], [0, drift], { extrapolateLeft: 'clamp' });
  const y = Math.min(moved + extra, layout.height - 1080);
  return <Img src={staticFile('shots/landing-full.png')} style={{ position: 'absolute', top: -y, left: 0, width: 1920 }} />;
};

const Cursor: React.FC<{ x: number; y: number; pressed?: boolean }> = ({ x, y, pressed }) => (
  <svg
    width={34}
    height={40}
    viewBox="0 0 24 28"
    style={{ position: 'absolute', left: x, top: y, transform: `scale(${pressed ? 0.85 : 1})`, transformOrigin: '0 0' }}
  >
    <path d="M2 1 L2 22 L7.5 16.5 L11 25 L14.5 23.5 L11 15 L19 15 Z" fill="#111" stroke="white" strokeWidth={1.6} />
  </svg>
);

const Ripple: React.FC<{ x: number; y: number; at: number }> = ({ x, y, at }) => {
  const frame = useCurrentFrame();
  const t = frame - at;
  if (t < 0 || t > 18) return null;
  const r = interpolate(t, [0, 18], [6, 46]);
  return (
    <div
      style={{
        position: 'absolute',
        left: x - r,
        top: y - r,
        width: r * 2,
        height: r * 2,
        borderRadius: '50%',
        border: `4px solid ${BRAND[600]}`,
        opacity: interpolate(t, [0, 18], [0.9, 0]),
      }}
    />
  );
};

// ---------- scenes ----------

const TitleCard: React.FC<{ title: string; subtitle: string; footer?: string }> = ({ title, subtitle, footer }) => {
  const frame = useCurrentFrame();
  const logo = spring({ frame, fps: FPS, config: { damping: 14 } });
  const text = spring({ frame: frame - 10, fps: FPS, config: { damping: 200 } });
  const foot = spring({ frame: frame - 22, fps: FPS, config: { damping: 200 } });
  return (
    <AbsoluteFill
      style={{
        background: `radial-gradient(circle at 30% 20%, #b8303d 0%, ${BRAND[700]} 45%, ${BRAND[900]} 100%)`,
        alignItems: 'center',
        justifyContent: 'center',
        fontFamily: FONT,
        color: 'white',
      }}
    >
      <div
        style={{
          padding: '22px 40px',
          borderRadius: 28,
          background: 'white',
          transform: `scale(${logo})`,
          boxShadow: '0 30px 60px rgba(0,0,0,0.25)',
        }}
      >
        <Img src={staticFile('shots/logo.png')} style={{ height: 96, display: 'block' }} />
      </div>
      <div
        style={{
          marginTop: 56,
          fontSize: 76,
          fontWeight: 800,
          letterSpacing: -1.5,
          textAlign: 'center',
          maxWidth: 1500,
          lineHeight: 1.1,
          whiteSpace: 'pre-line',
          opacity: text,
          transform: `translateY(${(1 - text) * 30}px)`,
        }}
      >
        {title}
      </div>
      <div style={{ marginTop: 24, fontSize: 36, opacity: text * 0.85, textAlign: 'center' }}>{subtitle}</div>
      {footer && (
        <div
          style={{
            marginTop: 54,
            padding: '18px 44px',
            borderRadius: 16,
            background: 'white',
            color: BRAND[700],
            fontSize: 38,
            fontWeight: 800,
            opacity: foot,
            transform: `scale(${0.9 + foot * 0.1})`,
          }}
        >
          {footer}
        </div>
      )}
    </AbsoluteFill>
  );
};

const HeroScene: React.FC = () => {
  const frame = useCurrentFrame();
  const { durationInFrames } = useVideoConfig();
  // Start on the whole hero, then push in on the chat widget mock-up
  const zoom = interpolate(frame, [durationInFrames * 0.45, durationInFrames * 0.75], [1, 1.45], {
    extrapolateLeft: 'clamp',
    extrapolateRight: 'clamp',
    easing: ease,
  });
  return (
    <Browser url="redhopper.co/az" zoom={zoom} origin="1250px 400px">
      <Img src={staticFile('shots/hero.png')} style={{ position: 'absolute', inset: 0, width: 1920 }} />
    </Browser>
  );
};

const LoginScene: React.FC = () => {
  const frame = useCurrentFrame();
  const { email, password, submit } = loginLayout;
  const center = (b: typeof email) => ({ x: b.x + 60, y: b.y + b.h / 2 });

  const clickEmail = 50;
  const clickPassword = 150;
  const clickSubmit = 240;
  const path = [
    { f: 10, x: 1350, y: 880 },
    { f: clickEmail - 4, ...center(email) },
    { f: clickPassword - 30, ...center(email) },
    { f: clickPassword - 4, ...center(password) },
    { f: clickSubmit - 34, ...center(password) },
    { f: clickSubmit - 4, x: submit.x + submit.w / 2, y: submit.y + submit.h / 2 },
  ];
  const frames = path.map((p) => p.f);
  const opts = { extrapolateLeft: 'clamp', extrapolateRight: 'clamp', easing: ease } as const;
  const cx = interpolate(frame, frames, path.map((p) => p.x), opts);
  const cy = interpolate(frame, frames, path.map((p) => p.y), opts);
  const pressed = [clickEmail, clickPassword, clickSubmit].some((c) => frame >= c && frame < c + 5);

  const shot = frame < clickEmail ? 'login-empty' : frame < clickPassword ? 'login-email' : 'login-filled';
  // Typing: hide not-yet-typed characters of the real screenshot with a field-coloured mask
  const typed = (start: number, chars: number, textW: number) =>
    interpolate(frame, [start, start + chars * 3], [0, textW], { extrapolateLeft: 'clamp', extrapolateRight: 'clamp' });
  const emailW = typed(clickEmail + 4, 'demo@chat.test'.length, 116);
  const passW = typed(clickPassword + 4, 'password'.length, 76);
  const mask = (b: typeof email, revealed: number) => (
    <div
      style={{ position: 'absolute', left: b.x + 12 + revealed, top: b.y + 4, width: b.w - 24 - revealed, height: b.h - 8, background: 'white' }}
    />
  );

  const zoom = interpolate(frame, [0, 40, clickSubmit + 20, clickSubmit + 50], [1, 1.35, 1.35, 1.05], opts);
  const card = spring({ frame: frame - clickSubmit - 30, fps: FPS, config: { damping: 200 } });

  return (
    <>
      <Browser url="redhopper.co/login" zoom={zoom} origin="960px 540px">
        <Img src={staticFile(`shots/${shot}.png`)} style={{ position: 'absolute', inset: 0, width: 1920 }} />
        {shot === 'login-email' && mask(email, emailW)}
        {shot === 'login-filled' && mask(password, passW)}
        <Ripple x={center(email).x} y={center(email).y} at={clickEmail} />
        <Ripple x={center(password).x} y={center(password).y} at={clickPassword} />
        <Ripple x={submit.x + submit.w / 2} y={submit.y + submit.h / 2} at={clickSubmit} />
        <Cursor x={cx} y={cy} pressed={pressed} />
      </Browser>
      <div
        style={{
          position: 'absolute',
          right: 120,
          top: 300,
          width: 470,
          padding: '30px 34px',
          borderRadius: 20,
          background: 'white',
          fontFamily: FONT,
          boxShadow: '0 24px 60px rgba(64,8,16,0.22)',
          borderLeft: `8px solid ${BRAND[600]}`,
          opacity: card,
          transform: `translateX(${(1 - card) * 60}px)`,
        }}
      >
        <div style={{ fontSize: 22, fontWeight: 800, color: BRAND[600], letterSpacing: 1 }}>
          OPERATOR GİRİŞİ
        </div>
        <div style={{ marginTop: 18, fontSize: 20, color: '#71717a' }}>Email</div>
        <div style={{ fontSize: 32, fontWeight: 800, color: BRAND[900] }}>demo@chat.test</div>
        <div style={{ marginTop: 14, fontSize: 20, color: '#71717a' }}>Şifrə</div>
        <div style={{ fontSize: 32, fontWeight: 800, color: BRAND[900] }}>password</div>
      </div>
    </>
  );
};

const SceneBody: React.FC<{ id: SceneId }> = ({ id }) => {
  switch (id) {
    case 'intro':
      return <TitleCard title={'Öz sənədlərinizdən cavab verən\nAI canlı çat'} subtitle="redhopper.co" />;
    case 'hero':
      return <HeroScene />;
    case 'how':
      return (
        <Browser url="redhopper.co/az#how">
          <PageScroll from={0} to={layout.how - 140} drift={40} />
        </Browser>
      );
    case 'features':
      return (
        <Browser url="redhopper.co/az#features">
          <PageScroll from={layout.how - 140} to={layout.features - 120} drift={50} />
        </Browser>
      );
    case 'channels':
      return (
        <Browser url="redhopper.co/az#features">
          <PageScroll from={layout.features - 120} to={layout.channels - 200} drift={40} />
        </Browser>
      );
    case 'pricing':
      return (
        <Browser url="redhopper.co/az#pricing">
          <PageScroll from={layout.channels - 200} to={layout.pricing - 60} drift={layout.faq - layout.pricing - 1080 + 40} />
        </Browser>
      );
    case 'faq':
      return (
        <Browser url="redhopper.co/az#faq">
          <PageScroll from={layout.pricing} to={layout.faq - 140} drift={30} />
        </Browser>
      );
    case 'login':
      return <LoginScene />;
    case 'outro':
      return (
        <TitleCard
          title="Saytınıza AI canlı çat qoşun"
          subtitle="14 gün pulsuz · kart tələb olunmur"
          footer="redhopper.co"
        />
      );
  }
};

export const Demo: React.FC = () => {
  let start = 0;
  return (
    <AbsoluteFill style={{ background: '#fbf8f8' }}>
      {scenes.map((s) => {
        const from = start;
        start += s.frames;
        const isCard = s.id === 'intro' || s.id === 'outro';
        return (
          <Sequence key={s.id} from={from} durationInFrames={s.frames} name={s.id}>
            <FadeIn>
              {!isCard && <Backdrop />}
              <SceneBody id={s.id} />
              {!isCard && <Caption text={s.caption} />}
            </FadeIn>
            <Sequence from={AUDIO_DELAY} layout="none">
              <Audio src={staticFile(`audio/${s.id}.wav`)} />
            </Sequence>
          </Sequence>
        );
      })}
    </AbsoluteFill>
  );
};
