import { Composition } from 'remotion';
import { Demo, FPS, totalFrames } from './Demo';

export const RemotionRoot: React.FC = () => (
  <Composition
    id="RedhopperDemo"
    component={Demo}
    durationInFrames={totalFrames}
    fps={FPS}
    width={1920}
    height={1080}
  />
);
