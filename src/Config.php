<?php

declare(strict_types=1);

namespace Conia\Core;

use Closure;
use Conia\Chuck\Config as BaseConfig;
use Conia\Core\Locales;

class Config extends BaseConfig
{
    public readonly Locales $locales;
    public readonly Types $types;
    protected string $panelPath = 'panel';
    protected ?string $panelTheme = null;
    protected Closure $languageNegotiator;
    private bool $debugPanel = false;

    public function __construct(
        public readonly string $root,
        string $app = 'conia',
        bool $debug = false,
        string $env = '',
        array $settings = [],
    ) {
        $settings = array_merge([
            'path.public' => "{$root}/public",
            'path.assets' => '/assets',
            'path.cache' => '/cache',
            'session.options' => [],
            // 'password.algorithm' => PASSWORD_* PHP constant
            // 'password.entropy' => float
        ], $settings);

        parent::__construct($app, $debug, $env, $settings);

        $this->locales = new Locales();
        $this->types = new Types();
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
}
