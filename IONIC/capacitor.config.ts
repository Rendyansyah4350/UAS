import type { CapacitorConfig } from '@capacitor/cli';

const config: CapacitorConfig = {
  appId: 'com.rehalivan.eduvan', // Tetap gunakan App ID asli kelompok Anda
  appName: 'Eduvan Marketplace',
  webDir: 'www',
  plugins: {
    SplashScreen: {
      // 🟢 KUNCIAN 1: Tahan splash native 1 detik biar engine web-nya selesai loading di latar belakang
      launchShowDuration: 1000,
      launchAutoHide: true,

      // 🟢 KUNCIAN 2: Paksa warnanya biru gelap EduVan biar pas transisi gak ada kedipan warna lain
      backgroundColor: '#111827',
      androidSplashResourceName: 'transparent',
      splashImmersive: true,
    },
  },
};

export default config;
