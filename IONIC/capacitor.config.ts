import type { CapacitorConfig } from '@capacitor/cli';

const config: CapacitorConfig = {
  appId: 'com.rehalivan.eduvan',
  appName: 'Eduvan Marketplace',
  webDir: 'www',
  plugins: {
    SplashScreen: {
      launchShowDuration: 3000,
      launchAutoHide: true,
      backgroundColor: "#111827",
      androidScaleType: "CENTER_CROP"
    }
  }
};

export default config;
