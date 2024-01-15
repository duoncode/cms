<?php

declare(strict_types=1);

namespace Conia\Core;

use Conia\Quma\Connection;
use Conia\Quma\Database;
use Conia\Registry\Registry;
use Conia\Route\Route;
use PDO;

class Cms implements Plugin
{
    protected readonly Config $config;
    protected readonly Factory $factory;
    protected readonly Registry $registry;
    protected readonly Database $db;
    protected array $collections = [];
    protected array $nodes = [];

    public function __construct(protected readonly bool $sessionEnabled = false)
    {
    }

    public function load(App $app): void
    {
        $this->factory = $app->factory();
        $this->registry = $app->registry();

        (new Routes($this->config, $this->db, $this->factory, $this->sessionEnabled))->add($app);
    }

    protected function collect(): void
    {
        foreach ($this->collections as $name => $collection) {
            $this->registry
                ->tag(Collection::class)
                ->add($name, $collection);
        }

        foreach ($this->nodes as $name => $node) {
            $this->registry
                ->tag(Node::class)
                ->add($name, $node);
        }
    }

    public function section(string $name): void
    {
        $this->collections[$name] = new Section($name);
    }

    public function collection(string $class): void
    {
        $this->collections[$class::handle()] = $class;
    }

    public function node(string $class): void
    {
        $this->collections[$class::handle()] = $class;
    }

    public function database(
        string $dsn,
        string|array $sql = null,
        string|array $migrations = null,
        array $options = [],
        bool $print = false
    ): void {
        $root = dirname(__DIR__);
        $sql = array_merge(
            [$root . '/db/sql'],
            $sql ? (is_array($sql) ? $sql : [$sql]) : []
        );
        $migrations = array_merge(
            [$root . '/db/migrations'],
            $migrations ? (is_array($migrations) ? $migrations : [$migrations]) : []
        );

        $this->db = new Database(new Connection(
            $dsn,
            $sql,
            $migrations,
            fetchMode: PDO::FETCH_ASSOC,
            options: $options,
            print: $print,
        ));
        $this->registry->add(Database::class, $this->db);
    }

    /**
     * Catchall for page url paths.
     *
     * Should be the last one
     */
    public function catchallRoute(): Route
    {
        return Route::any(
            '/...slug',
            [Page::class, 'catchall'],
            'conia.catchall',
        );
    }
}
