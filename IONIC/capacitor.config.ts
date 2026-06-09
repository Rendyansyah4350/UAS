import type { CapacitorConfig } from '@capacitor/cli';

const config: CapacitorConfig = {
  appId: 'com.rehalivan.eduvan', // Tetap gunakan App ID asli kelompok Anda
  appName: 'Eduvan Marketplace',
  webDir: 'www',

  // // 🟢 Gabungkan blok konfigurasi Server untuk Live Reload di sini
  //   server: {
  //     url: 'http://192.168.1.76:8101', // Sesuaikan dengan IP Wi-Fi laptop Anda saat ini
  //     cleartext: true,
  //   },

  plugins: {
    SplashScreen: {
      launchShowDuration: 3000,
      launchAutoHide: true,
      backgroundColor: '#111827',
      androidScaleType: 'CENTER_CROP',
      showSpinner: false,
    },
  },
};

export default config;
