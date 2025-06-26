import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import tailwindcss from '@tailwindcss/vite'
// import basicSsl from '@vitejs/plugin-basic-ssl'
import path from 'path'

const isProd = process.env.NODE_ENV === 'production';

export default defineConfig({
  base: isProd ? '/spa/' : '/',
  plugins: [
    vue(),
    tailwindcss(),
    // basicSsl(),
  ],
  build: {
    outDir: '../public/spa',
    emptyOutDir: true,
  },
  resolve: {
    alias: {
      '@': path.resolve(__dirname, './src'),
    },
  },
  server: {
    host: '0.0.0.0',
    port: 5173,
    strictPort: true,
    proxy: {
      '/api': {
        target: 'https://url-shortener.ddev.site',
        changeOrigin: true,
        secure: false,
      },
    },
    origin: `${process.env.DDEV_PRIMARY_URL.replace(/:\d+$/, "")}:5173`,
    watch: {
      usePolling: true,
      interval: 100,
    },
    hmr: {
      protocol: 'wss',
      host: 'url-shortener.ddev.site',
      port: 5173,
    },
    cors: {
      origin: /https?:\/\/([A-Za-z0-9\-\.]+)?(\.ddev\.site)(?::\d+)?$/,
    },
  }
})
