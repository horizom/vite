# Horizom Vite Plugin

<a href="https://www.npmjs.com/package/horizom-vite-plugin"><img src="https://img.shields.io/npm/dt/horizomvite-plugin" alt="Total Downloads"></a>
<a href="https://www.npmjs.com/package/horizom-vite-plugin"><img src="https://img.shields.io/npm/v/horizom-vite-plugin" alt="Latest Stable Version"></a>
<a href="https://www.npmjs.com/package/horizom-vite-plugin"><img src="https://img.shields.io/npm/l/horizom-vite-plugin" alt="License"></a>

## Description

Vite is a modern frontend build tool that provides an extremely fast development environment and bundles your code for production.

This plugin configures Vite for use with a PHP backend server.

## Usage

```php

use Horizom\Vite\Vite;

// Your entry point
$entry = 'resources/js/app.js';

$config = [
    
    /**
     * The build directory (default: 'public/dist')
     */
    'outputDir' => 'public/dist',

    /**
     * Force development mode (default: false)
     */
    'forceDev' => false,

    /**
     * The host (default: 'http://localhost:5173')
     */
    'host' => 'http://localhost:5173',

    /**
     * The manifest file (default: '.vite/manifest.json')
     */
    'manifest' => '.vite/manifest.json',

    /**
     * The base path (default: '')
     */
    'basePath' => '',
];

$plugin = new Vite($config);

// Render the html tags
$plugin->render($entry);
```

And setup your vite config in `vite.config.js` file

```js
import { defineConfig, splitVendorChunkPlugin } from "vite";
import path from "node:path";
import vue from "@vitejs/plugin-vue";
import liveReload from "vite-plugin-live-reload";

export default defineConfig({
  plugins: [
    vue(),
    liveReload([
      __dirname + "/resources/views/**/*.php",
      __dirname + "/app/*.php",
    ]),
    splitVendorChunkPlugin(),
  ],
  publicDir: path.resolve(__dirname, "storage/assets"),
  base: process.env.APP_ENV === "development" ? "/" : "/dist/",
  build: {
    outDir: "./public/dist",
    emptyOutDir: true,
    manifest: true,
    rollupOptions: {
      input: path.resolve(__dirname, "resources/js/app.js"),
    },
  },
});
```

## License

The Laravel Vite plugin is open-sourced software licensed under the [MIT license](LICENSE.md).
