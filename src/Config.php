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

    protected string $panelUrl = 'panel';
    protected ?string $panelTheme = null;
    protected Closure $languageNegotiator;
    private bool $debugPanel = false;

    public function __construct(
        string $app,
        bool $debug = false,
        string $env = '',
        array $settings = [],
    ) {
        $settings = array_merge([
            'session.options' => [],
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

    public function assets(string $path, string $cache): void
    {
        $this->set('assets.path', $path);
        $this->set('assets.cache', $cache);
    }

    public function panelUrl(string $url): void
    {
        $this->panelUrl = $url;
    }

    public function getPanelUrl(): string
    {
        return $this->debugPanel ? '/panel' : '/' . $this->panelUrl;
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
        array|null $domains = null,
        ?string $urlPrefix = null,
    ) {
        $this->locales->add($id, $title, $fallback, $domains, $urlPrefix);
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
