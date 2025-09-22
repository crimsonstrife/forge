import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import { viteStaticCopy } from 'vite-plugin-static-copy'

export default defineConfig({
  plugins: [
    laravel({
      input: [
        'resources/css/app.css',
        'resources/js/app.js',
        'resources/js/editor/tinymce-init.js',
        // plugin entries (no imports inside them)
        'resources/tiny-plugins/action-items/plugin.js',
        'resources/tiny-plugins/action-items/plugin.css',
        'resources/tiny-plugins/mentions-lite/plugin.js'
      ],
      refresh: true
    }),
    viteStaticCopy({
      targets: [
        {
          src: 'node_modules/@awesome.me/webawesome/dist/*',
          dest: 'vendor/webawesome'
        },
        {
          src: 'node_modules/tinymce/*',
          dest: 'vendor/tinymce'
        }
      ]
    })
  ],
  build: {
    rollupOptions: {
      output: {
        // predictable names in /public/build/assets
        entryFileNames: 'assets/[name]-[hash].js',
        assetFileNames: 'assets/[name]-[hash][extname]'
      }
    }
  }
})
