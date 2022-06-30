<?php

declare(strict_types=1);

namespace Conia;

use \Generator;
use \ValueError;
use Conia\Field;


abstract class Type
{
    static protected string $name = '';
    static protected string $template = '';
    static protected array $permissions = [];
    static protected int $columns = 12;

    protected array $list = [];
    protected array $fields = [];

    public final function __construct(
        protected readonly array $data,
        protected readonly string $locale,
    ) {
    }

    abstract public function init(): void;
    abstract public function title(): string;

    public final function __get(string $name): Field
    {
        return $this->fields[$name]->value($this->data[$name]);
    }

    public final function __set(string $name, Field $field): void
    {
        $this->list[] = $name;
        $this->fields[$name] = $field;
    }

    public function form(): Generator
    {
        foreach ($this->list as $field) {
            yield $this->fields[$field];
        }
    }

    public static function columns(): int
    {
        if (static::$columns < 12 || static::$columns > 25) {
            throw new ValueError('The value of $columns must be >= 12 and <= 25');
        }

        return static::$columns;
    }

    public static function name(): ?string
    {
        if (!empty(static::$name)) {
            return static::$name;
        }

        return static::className();
    }

    public static function template(): ?string
    {
        if (!empty(static::$template)) {
            return static::$template;
        }

        return static::className();
    }

    public static function className(): string
    {
        return basename(str_replace('\\', '/', static::class));
    }

    public function json(): array
    {
        return $this->data;
    }
}
