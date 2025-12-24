import {defineConfig} from 'vite';
import {resolve} from 'path';
// import fs from 'fs-extra';
import {fileURLToPath} from 'url';
import {glob} from "glob";
// import glob from 'glob';
import path from 'path'

const __dirname = fileURLToPath(new URL('.', import.meta.url));

/**
 * Custom Vite plugin to copy npm modules to web/npm directory
 */
    // const copyFilesPlugin = () => ({
    //     name: 'copy-files',
    //     apply: 'build',
    //     async writeBundle() {
    //         const copyTasks = [
    //             {src: 'node_modules/@selectize/selectize/dist/js/selectize.min.js', dest: 'web/npm/'},
    //             {src: 'node_modules/bootstrap-toggle/css/bootstrap-toggle.min.css', dest: 'web/npm/'},
    //             {src: 'node_modules/bootstrap-toggle/js/bootstrap-toggle.min.js', dest: 'web/npm/'},
    //             {src: 'node_modules/clipboard/dist/clipboard.min.js', dest: 'web/npm/'},
    //             {src: 'node_modules/corejs-typeahead/dist/typeahead.bundle.min.js', dest: 'web/npm/'},
    //             {src: 'node_modules/isotope-layout/dist/isotope.pkgd.min.js', dest: 'web/npm/'},
    //             {src: 'node_modules/jquery/dist/jquery.min.js', dest: 'web/npm/'},
    //             {src: 'node_modules/moment/min/moment-with-locales.min.js', dest: 'web/npm/'},
    //             {src: 'node_modules/requirejs/require.js', dest: 'web/npm/'},
    //             {src: 'node_modules/sortablejs/Sortable.min.js', dest: 'web/npm/'},
    //             {src: 'node_modules/vue/dist/vue.global.prod.js', dest: 'web/npm/'},
    //             {src: 'node_modules/vuedraggable/dist/vuedraggable.umd.min.js', dest: 'web/npm/'},
    //             {src: 'node_modules/vue-draggable-plus/dist/vue-draggable-plus.iife.js', dest: 'web/npm/'},
    //         ];
    //
    //         for (const task of copyTasks) {
    //             try {
    //                 const srcPath = resolve(__dirname, task.src);
    //                 const destPath = resolve(__dirname, task.dest, task.src.split('/').pop());
    //
    //                 if(fs.existsSync(srcPath)) {
    //                     await fs.ensureDir(resolve(__dirname, task.dest));
    //                     await fs.copy(srcPath, destPath);
    //                     console.log(`✓ Copied ${task.src} to ${task.dest}`);
    //                 } else {
    //                     console.warn(`⚠ Source file not found: ${task.src}`);
    //                 }
    //             } catch (err) {
    //                 console.error(`✗ Error copying ${task.src}:`, err.message);
    //             }
    //         }
    //     },
    // });
const dist = resolve('web/js/build/');


/**
 * Vite configuration
 * Replaces gulpfile.js with modern ESM build system
 */
export default defineConfig({
    // plugins: [copyFilesPlugin()],

    resolve: {
        alias: {
            '@': resolve(__dirname, './web'),
        },
    },

    build: {
        outDir: dist,
        target: 'es2015',
        sourcemap: false,
        // minify: 'terser',
        lib: {
            // entry: glob.sync('web/typescript/**/*.ts'),  // Or single entry
            entry:
            Object.fromEntries(
                glob.sync('web/typescript/**/*.ts').map(file => [
                    path.relative('web/typescript', file.slice(0, -path.extname(file).length)).replace(/\\/g, '/'),
                    file
                ])
            ),

            // formats: ['es', 'umd']
        },
        treeshake: false ,
        rollupOptions: {

            // input: glob.sync('web/typescript/**/*.ts'),
            // input: Object.fromEntries(
            //     glob.sync('web/typescript/**/*.ts').map(file => [
            //         path.relative('web/typescript', file.slice(0, -path.extname(file).length)).replace(/\\/g, '/'),
            //         file
            //     ])
            // ),

            output: {
                // preserveEntrySignatures: 'strict',  // Keeps all entry exports
                //  preserveModules: true,
                // preserveModulesRoot: 'web/typescript',
                // Preserve directory structure
                entryFileNames: '[name].js',
                chunkFileNames: '[name].js',
                assetFileNames: '[name][extname]',

                // Generate source maps
                sourcemap: true,
            },
        },
    },

    /**
     * CSS/Sass processing
     * Automatically handles SCSS compilation with Sass
     */
    // css: {
    //     preprocessorOptions: {
    //         scss: {
    //             api: 'modern-compiler', // Use modern Sass API
    //             loadPaths: [resolve(__dirname, './web')],
    //             quietDeps: true,
    //             silenceDeprecations: ['color-functions', 'import', 'global-builtin'],
    //         },
    //     },
    //
    //     postcss: {
    //         plugins: [
    //             require('autoprefixer'),
    //         ],
    //     },
    //
    //     sourceMap: true,
    // },

    /**
     * Server configuration for watch mode
     */
    // server: {
    //     middlewareMode: true,
    //     watch: {
    //         usePolling: true, // Match gulp's usePolling setting
    //         interval: 100,
    //     },
    // },
});
