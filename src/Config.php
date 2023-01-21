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
        string $env = ''
    ) {
        parent::__construct($app, $debug, $env);

        $this->set('session.options', []);

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

    public function setSecret(string $secret): void
    {
        $this->set('app.secret', $secret);
    }

    public function setPanelUrl(string $url): void
    {
        $this->panelUrl = $url;
    }

    public function panelUrl(): string
    {
        return $this->debugPanel ? '/panel' : '/' . $this->panelUrl;
    }

    public function setPanelTheme(string $url): void
    {
        $this->panelTheme = $url;
    }

    public function setLocaleNegotiator(Closure $func): void
    {
        $this->locales->setNegotiator($func);
    }

    public function addLocale(
        string $id,
        string $title,
        ?string $fallback = null,
        array|null $domains = null,
        ?string $urlPrefix = null,
    ) {
        $this->locales->add($id, $title, $fallback, $domains, $urlPrefix);
    }

    public function setDefaultLocale(string $locale): void
    {
        $this->locales->setDefault($locale);
    }
}
