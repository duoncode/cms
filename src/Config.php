<?php

declare(strict_types=1);

namespace Conia;

use Closure;
use PDO;
use ValueError;
use Conia\Chuck\Config as BaseConfig;
use Conia\I18n\Locales;
use Conia\I18n\Locale;
use Conia\Puma\Connection;
use Conia\Boiler\Engine;


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

    protected function fromBrowser(): string|false
    {
        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            preg_match_all(
                '/([a-z]{1,8}(-[a-z]{1,8})?)\s*(;\s*q\s*=\s*(1|0\.[0-9]+))?/i',
                $_SERVER['HTTP_ACCEPT_LANGUAGE'],
                $matches
            );

            if (count($matches[1])) {
                $langs = array_combine($matches[1], $matches[4]);

                foreach ($langs as $lang => $val) {
                    if ($val === '') {
                        $langs[$lang] = 1;
                    }
                }

                arsort($langs, SORT_NUMERIC);

                foreach ($langs as $lang => $val) {
                    if ($this->exists($lang)) return $lang;

                    $lang = str_replace('-', '_', $lang);
                    if ($this->exists($lang))  return $lang;

                    $lang = strtok($lang, '_');
                    if ($this->exists($lang))  return $lang;
                }
            }
        }

        return false;
    }

    public function fromRequest(Request $request): Locale
    {
        // By domain
        $host = strtolower(explode(':', $request->host())[0]);
        foreach ($this->locales as $locale) {
            foreach ($locale->domains as $domain) {
                if ($host === $domain) {
                    return $locale;
                }
            }
        }

        // From URL path prefix. e. g. http://example.com/en_EN/path/to/page
        $prefix = explode('/', trim(parse_url($request->url())['path'], '/'))[0];
        foreach ($this->locales as $locale) {
            if ($prefix === $locale->urlPrefix) {
                return $locale;
            }
        }

        // From session
        $locale = $request->session()->get('locale', false);
        if ($locale && $this->exists($locale)) {
            return $this->locales[$locale];
        }

        // From the locales the browser says the user accepts
        $locale =  $this->fromBrowser();
        if ($locale && $this->exists($locale)) {
            return $this->locales[$locale];
        }

        // default locale from config file
        if ($this->default !== null) {
            return $this->locales[$this->default];
        }

        return null;
    }

    public function debugPanel(bool $debug = true): bool
    {
        if (func_num_args() > 0) {
            $this->debugPanel = $debug;
        }

        return $this->debugPanel;
    }

    public function setDsn(string $dsn, bool $print = false): void
    {

        $this->addConnection(new Connection(
            $dsn,
            $this->root . DIRECTORY_SEPARATOR . 'db' . DIRECTORY_SEPARATOR . 'sql',
            $this->root . DIRECTORY_SEPARATOR . 'db' . DIRECTORY_SEPARATOR . 'migrations',
            fetchMode: PDO::FETCH_ASSOC,
            print: $print,
        ));
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
        $this->locales->setNegotiator = $func;
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

    public function addConnection(Connection $conn, string $name = self::DEFAULT): void
    {
        if (!isset($this->connections[$name])) {
            $this->connections[$name] = $conn;
        } else {
            throw new ValueError("A connection with the name '$name' already exists");
        }
    }

    public function connection(string $name = self::DEFAULT): Connection
    {
        return $this->connections[$name];
    }
}
