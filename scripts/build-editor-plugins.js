import * as esbuild from 'esbuild';
import { readFile, writeFile, mkdir } from 'fs/promises';
import { dirname, join } from 'path';

const plugins = [
    { id: 'action-items', entry: 'resources/tiny-plugins/action-items/plugin.js', css: 'resources/tiny-plugins/action-items/plugin.css' },
    { id: 'mentions-lite', entry: 'resources/tiny-plugins/mentions-lite/plugin.js', css: 'resources/tiny-plugins/mentions-lite/plugin.css' },
];

const outDir = 'public/plugins/'; // stable paths

await mkdir(outDir, { recursive: true });

for (const p of plugins) {
    // JS: non-min
    await esbuild.build({
        entryPoints: [p.entry],
        outfile: join(outDir, `${p.id}.js`),
        bundle: true,
        format: 'iife',   // important: loadable via <script> (no ESM)
        minify: false,
        sourcemap: true,
        banner: { js: '/* tinymce external plugin */' },
    });
    // JS: min
    await esbuild.build({
        entryPoints: [p.entry],
        outfile: join(outDir, `${p.id}.min.js`),
        bundle: true,
        format: 'iife',
        minify: true,
        sourcemap: true,
    });

    // CSS : non-min + min if file exists
    try {
        const cssPath = p.css;
        const css = await readFile(cssPath, 'utf8');
        const nonMinOut = join(outDir, `${p.id}.css`);
        const minOut = join(outDir, `${p.id}.min.css`);
        await writeFile(nonMinOut, css);
        const { code } = await esbuild.transform(css, { loader: 'css', minify: true, sourcemap: true });
        await writeFile(minOut, code);
    } catch { /* no CSS for this plugin, skip */ }
}

if (process.argv.includes('--watch')) {
    // Optional: watch mode for dev
    console.log('Watching editor plugins… (Ctrl+C to stop)');
    for (const p of plugins) {
        esbuild.context({
            entryPoints: [p.entry],
            outfile: join(outDir, `${p.id}.min.js`),
            bundle: true, format: 'iife', minify: true, sourcemap: true,
        }).then(ctx => ctx.watch());
    }
}
