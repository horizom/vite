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

## License

The Laravel Vite plugin is open-sourced software licensed under the [MIT license](LICENSE.md).
