<?php

declare(strict_types=1);

namespace Conia\Panel;

class Panel
{
    public function section(string $name): void
    {
        $this->registry
            ->tag(Collection::class)
            ->add($name, new Section($name));
    }

    public function collection(string $class): void
    {
        $this->registry
            ->tag(Collection::class)
            ->add($class::handle(), $class);
    }

    public function node(string $class): void
    {
        $this->registry
            ->tag(Node::class)
            ->add($class::handle(), $class);
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
}
