<?php

declare(strict_types=1);

namespace Conia\Core;

use Closure;
use Conia\Chuck\Exception\OutOfBoundsException;
use Conia\Chuck\Exception\ValueError;
use Conia\Core\Locales;

class Config
{
    public readonly Locales $locales;
    protected string $panelPath = '/panel';
    protected ?string $panelTheme = null;
    protected Closure $languageNegotiator;
    private bool $debugPanel = false;
    private array $settings;

    public function __construct(
        public readonly string $root,
        public readonly string $app = 'conia',
        public readonly bool $debug = false,
        public readonly string $env = '',
        array $settings = [],
    ) {
        $this->settings = array_merge([
            'path.public' => "{$root}/public",
            'path.assets' => '/assets',
            'path.cache' => '/cache',
            'session.options' => [
                'gc_maxlifetime' => 3600,
                'cookie_lifetime' => 0,
            ],
            'slug.transliterate' => null,
            'media.fileserver' => null,
            'upload.mimetypes' => [
                'application/pdf' => ['pdf'],
                'image/gif' => ['gif'],
                'image/jpeg' => ['jpeg', 'jpg', 'jfif'],
                'image/png' => ['png'],
                'image/webp' => ['webp'],
            ],
            'upload.maxsize' => 10 * 1024 * 1024,
            // 'password.algorithm' => PASSWORD_* PHP constant
            // 'password.entropy' => float
        ], $settings);

        $this->validateApp($app);
        $this->locales = new Locales();
    }

    public function set(string $key, mixed $value): void
    {
        $this->settings[$key] = $value;
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->settings);
    }

    public function get(string $key, mixed $default = null): mixed
    {
        if (isset($this->settings[$key])) {
            return $this->settings[$key];
        }

        if (func_num_args() > 1) {
            return $default;
        }

        throw new OutOfBoundsException(
            "The configuration key '{$key}' does not exist"
        );
    }

    public function app(): string
    {
        return $this->app;
    }

    public function debug(): bool
    {
        return $this->debug;
    }

    public function env(): string
    {
        return $this->env;
    }

    public function debugPanel(bool $debug = true): bool
    {
        if (func_num_args() > 0) {
            $this->debugPanel = $debug;
        }

        return $this->debugPanel;
    }

    public function secret(string $secret): void
    {
        $this->set('app.secret', $secret);
    }

    public function publicPath(string $path): void
    {
        $this->set('path.public', $path);
    }

    public function assets(string $assets, string $cache): void
    {
        $this->set('path.assets', '/' . ltrim($assets, '/'));
        $this->set('path.cache', '/' . ltrim($cache, '/'));
    }

    public function panelPath(string $path): void
    {
        $this->panelPath = $path;
    }

    public function getPanelPath(): string
    {
        return $this->debugPanel ? '/panel' : '/' . $this->panelPath;
    }

    public function panelTheme(string $url): void
    {
        $this->panelTheme = $url;
    }

    public function localeNegotiator(Closure $func): void
    {
        $this->locales->setNegotiator($func);
    }

    public function locale(
        string $id,
        string $title,
        ?string $fallback = null,
        ?string $pgDict = null,
        array|null $domains = null,
        ?string $urlPrefix = null,
    ) {
        $this->locales->add($id, $title, $fallback, $pgDict, $domains, $urlPrefix);
    }

    public function defaultLocale(string $locale): void
    {
        $this->locales->setDefault($locale);
    }

    public function locales(): Locales
    {
        return $this->locales;
    }

    protected function validateApp(string $app): void
    {
        if (!preg_match('/^[a-zA-Z0-9_$-]{1,64}$/', $app)) {
            throw new ValueError(
                'The app name must be a nonempty string which consist only of lower case ' .
                    'letters and numbers. Its length must not be longer than 32 characters.'
            );
        }
    }
}
