<?php

declare(strict_types=1);

namespace Conia\Core;

use Closure;
use Conia\Boiler\Engine;
use Conia\Chuck\Config as BaseConfig;
use Conia\Core\Locales;
use ValueError;

class Config extends BaseConfig
{
    public readonly Locales $locales;
    public readonly Types $types;

    protected string $panelUrl = 'panel';
    protected ?string $panelTheme = null;
    protected Closure $languageNegotiator;
    private readonly string $root;
    private bool $debugPanel = false;

    public function __construct(
        string $app,
        bool $debug = false,
        string $env = ''
    ) {
        parent::__construct($app, $debug, $env);

        $this->root = dirname(__DIR__);

        $this->set('session.expires', 60 * 60 * 24);
        $this->set('session.authcookie', $app . '_auth');

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
        $this->set('secret', $secret);
    }

    public function setPanelUrl(string $url): void
    {
        if (preg_match('/^[A-Za-z0-9]{1,32}$/', $url)) {
            $this->panelUrl = $url;
        } else {
            throw new ValueError(
                'The panel url prefix be a nonempty string which consist only of letters' .
                    ' and numbers. Its length must not be longer than 32 characters.'
            );
        }
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

    public function templateEngine(array $defaults = []): Engine
    {
        return new Engine([$this->root . DIRECTORY_SEPARATOR . 'views'], array_merge([
            'config' => $this,
        ], $defaults));
    }
}
