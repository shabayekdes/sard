import tailwindcss from '@tailwindcss/vite';
import react from '@vitejs/plugin-react';
import laravel from 'laravel-vite-plugin';
import { resolve } from 'node:path';
import { defineConfig } from 'vite';

function stripUseClientDirective(): import('vite').Plugin {
  return {
    name: 'strip-use-client-directive',
    enforce: 'pre' as const,
    transform(code, id) {
      if (
        id.endsWith('.js') ||
        id.endsWith('.ts') ||
        id.endsWith('.tsx') ||
        id.endsWith('.mjs')
      ) {
        if (code.includes('"use client"') || code.includes("'use client'")) {
          return code.replace(/['"]use client['"];?\s*/g, '');
        }
      }
    },
  };
}

export default defineConfig({
    base: './',
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/css/dark-mode.css', 'resources/js/app.tsx'],
            ssr: 'resources/js/ssr.tsx',
            refresh: true,
        }),
        react(),
        stripUseClientDirective(),
        tailwindcss(),
    ],
    server: {
        host: 'localhost',
        headers: {
            'Access-Control-Allow-Origin': '*',
            'Access-Control-Allow-Methods': 'GET,POST,PUT,DELETE,OPTIONS',
            'Access-Control-Allow-Headers': '*',
        },
        watch: {
            ignored: ['**/vendor/**', '**/node_modules/**']
        },
        fs: {
            allow: ['..']
        }
    },

    esbuild: {
        jsx: 'automatic',
        jsxImportSource: 'react',
    },
    resolve: {
        alias: {
            'ziggy-js': resolve(__dirname, 'vendor/tightenco/ziggy'),
        },
    },
    build: {
        rollupOptions: {
            output: {
                manualChunks(id) {
                    if (id.includes('node_modules')) {
                        if (id.includes('recharts')) return 'recharts';
                        if (id.includes('@tiptap')) return 'tiptap';
                        if (id.includes('@hello-pangea/dnd')) return 'dnd';
                        if (id.includes('@radix-ui')) return 'radix-ui';
                        if (id.includes('lucide-react')) return 'lucide';
                        if (id.includes('@inertiajs')) return 'inertia';
                        if (id.includes('date-fns') || id.includes('clsx') || id.includes('tailwind-merge')) return 'utils';
                        if (!id.includes('recharts') && !id.includes('@tiptap') && (id.includes('node_modules/react-dom/') || id.includes('node_modules/react/'))) return 'vendor';
                    }
                },
            },
        },
        assetsDir: 'assets',
        chunkSizeWarningLimit: 600,
    }
});