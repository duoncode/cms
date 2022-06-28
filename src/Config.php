<?php

declare(strict_types=1);

namespace Conia;

use \Closure;
use \Exception;
use \PDO;
use \ValueError;
use Chuck\Config as BaseConfig;
use Chuck\Config\Connection;


class Config extends BaseConfig
{
    public readonly Locales $locales;

    protected string $panelUrl = 'panel';
    protected ?string $panelTheme = null;
    /** @var array<string, Type> */
    protected array $types = [];
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

        $this->scripts()->add($this->root . DIRECTORY_SEPARATOR . 'bin');
        $this->locales = new Locales();
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

    public function addType(Type $type): void
    {
        $name = $type->name;

        if (array_key_exists($name, $this->types)) {
            $class = $type::class;

            throw new Exception("Type '$name' already exists. Instance of '$class'");
        }

        $this->types[$name] = $type;
    }

    public function types(): array
    {
        return $this->types;
    }

    public function type(string $name): Type
    {
        return $this->types[$name];
    }

    public function setLocaleNegotiator(Closure $func): void
    {
        $this->locales->setNegotiator = $func;
    }

    public function addLocale(
        string $id,
        string $title,
        ?string $fallback = null,
        string|array|null $domain = null,
        ?string $urlPrefix = null,
    ) {
        $this->locales->add($id, $title, $fallback, $domain, $urlPrefix);
    }

    public function setDefaultLocale(string $locale): void
    {
        $this->locales->setDefault($locale);
    }
}
