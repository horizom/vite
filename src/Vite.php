<?php

namespace Horizom\Vite;

class Vite
{
    protected array $configs = [
        'forceDev' => false,
        'host' => 'http://localhost:5173',
        'manifest' => '.vite/manifest.json',
        'outputDir' => '',
        'basePath' => '',
    ];

    protected bool $forceDev = false;
    protected string $host;
    protected string $basePath = '';

    public function __construct(array $configs = [])
    {
        $this->configs = array_merge($this->configs, $configs);
        $this->forceDev = (bool) $this->configs['forceDev'];
        $this->host = (string) trim($this->configs['host'], '/');
        $this->basePath = (string) $this->configs['basePath'];
    }

    /**
     * Prints all the html entries needed for Vite
     *
     * @param string $entry
     * @return string
     */
    public function render(string $entry): string
    {
        return "\n" . $this->cssTag($entry)
            . "\n" . $this->jsPreloadImports($entry)
            . "\n" . $this->jsTag($entry);
    }

    /**
     * Print javascript tag
     *
     * @param string $entry
     * @return string
     */
    protected function jsTag(string $entry): string
    {
        $url = $this->isDev($entry) ? $this->host . '/' . $entry : $this->assetUrl($entry);

        if (!$url) {
            return '';
        }

        if ($this->isDev($entry)) {
            return '<script type="module" src="' . $this->host . '/@vite/client"></script>' . "\n"
                . '<script type="module" src="' . $url . '"></script>';
        }

        return '<script type="module" src="' . $url . '"></script>';
    }

    protected function jsPreloadImports(string $entry): string
    {
        if ($this->isDev($entry)) {
            return '';
        }

        $res = '';

        foreach ($this->importsUrls($entry) as $url) {
            $res .= '<link rel="modulepreload" href="' . $url . '">';
        }

        return $res;
    }

    protected function cssTag(string $entry): string
    {
        // not needed on dev, it's inject by Vite
        if ($this->isDev($entry)) {
            return '';
        }

        $tags = '';

        foreach ($this->cssUrls($entry) as $url) {
            $tags .= '<link rel="stylesheet" href="' . $url . '">';
        }

        return $tags;
    }

    protected function assetUrl(string $entry): string
    {
        $manifest = $this->loadManifest();
        return isset($manifest[$entry]) ? $this->url('dist/' . $manifest[$entry]['file']) : '';
    }

    protected function cssUrls(string $entry): array
    {
        $urls = [];
        $manifest = $this->loadManifest();

        if (!empty($manifest[$entry]['css'])) {
            foreach ($manifest[$entry]['css'] as $file) {
                $urls[] = $this->url('dist/' . $file);
            }
        }

        return $urls;
    }

    protected function importsUrls(string $entry): array
    {
        $urls = [];
        $manifest = $this->loadManifest();

        if (!empty($manifest[$entry]['imports'])) {
            foreach ($manifest[$entry]['imports'] as $imports) {
                $urls[] = $this->url('dist/' . $manifest[$imports]['file']);
            }
        }

        return $urls;
    }

    protected function url(string $path): string
    {
        return $this->basePath . '/' . $path;
    }

    /**
     * Some dev/prod mechanism would exist in your project
     *
     * This method is very useful for the local server
     * if we try to access it, and by any means, didn't started Vite yet
     * it will fallback to load the production files from manifest
     * so you still navigate your site as you intended!
     *
     * @param string $entry
     * @return bool
     */
    protected function isDev(string $entry): bool
    {
        static $exists = null;

        if ($exists !== null) {
            return $exists;
        }

        $handle = curl_init($this->host . '/' . $entry);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_NOBODY, true);

        curl_exec($handle);
        $error = curl_errno($handle);
        curl_close($handle);

        return $exists = !$error;
    }

    protected function loadManifest(): array
    {
        $ds = DIRECTORY_SEPARATOR;
        $outDir = $this->configs['outputDir'];
        $file = trim($this->configs['manifest'], '/');
        $filename = str_replace(['/', '\\'], $ds, $outDir . '/' . $file);

        if (!file_exists($filename)) {
            return [];
        }

        return json_decode(file_get_contents($filename), true);
    }
}
