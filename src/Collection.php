<?php

declare(strict_types=1);

namespace Conia\Core;

use Conia\Core\Finder\Nodes;

abstract class Collection
{
    protected static string $name = '';
    protected static string $slug = '';

    public function __construct(
        public readonly Finder $find,
    ) {
    }

    abstract public function entries(): Nodes;
    abstract public function blueprints(): array;

    public function name(): string
    {
        return static::$name ?: preg_replace('/(?<!^)[A-Z]/', ' $0', static::class);
    }

    /**
     * Returns an array of columns with column definitions.
     *
     * Each column array must have the fields `title` and `field`
     */
    public function columns(): array
    {
        return [
            Column::new('Titel', 'title')->bold(true),
            Column::new('Seitentyp', 'meta.name'),
            Column::new('Editor', 'meta.editor'),
            Column::new('Bearbeitet', 'meta.changed')->date(true),
            Column::new('Erstellt', 'meta.created')->date(true),
        ];
    }

    public function header(): array
    {
        return array_map(function (Column $column) {
            return $column->title;
        }, $this->columns());
    }

    public function listing(): array
    {
        return array_map(function ($node) {
            return [
                'uid' => $node->meta('uid'),
                'columns' => array_map(
                    function (Column $column) use ($node) {
                        return $column->get($node);
                    },
                    $this->columns()
                ),
            ];
        }, iterator_to_array($this->entries()));
    }

    public static function slug(): string
    {
        return static::$slug ?: strtolower(basename(str_replace('\\', '/', static::class)));
    }
}
