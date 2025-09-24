import { readdir, stat, readFile, writeFile } from 'fs/promises';
import { join, extname } from 'path';
import * as esbuild from 'esbuild';

async function walk(dir, out=[]) {
    for (const e of await readdir(dir, { withFileTypes: true })) {
        const p = join(dir, e.name);
        if (e.isDirectory()) {
          await walk(p, out);
        } else {
          out.push(p);
        }
    }
    return out;
}

const root = 'public/build/vendor/tinymce';
const files = await walk(root);

for (const f of files) {
    if (f.endsWith('.js') && !f.endsWith('.min.js')) {
        const min = f.replace(/\.js$/, '.min.js');
        try { await stat(min); } catch {
            await esbuild.build({ entryPoints: [f], outfile: min, minify: true, bundle: false, format: 'iife' });
            console.log('minified', min);
        }
    }
    if (f.endsWith('.css') && !f.endsWith('.min.css')) {
        const min = f.replace(/\.css$/, '.min.css');
        try { await stat(min); } catch {
            const css = await readFile(f, 'utf8');
            const { code } = await esbuild.transform(css, { loader: 'css', minify: true });
            await writeFile(min, code);
            console.log('minified', min);
        }
    }
}
