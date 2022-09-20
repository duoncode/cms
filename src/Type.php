<?php

declare(strict_types=1);

namespace Conia;

use \Generator;
use \RuntimeException;
use \ValueError;
use Conia\Field\Field;
use Conia\Value\Value;


abstract class Type extends Model
{
    static protected string $name = '';
    static protected string $template = '';
    static protected array $permissions = [];
    static protected int $columns = 12;

    protected array $list = [];
    protected array $fields = [];

    public final function __construct(
        protected readonly Request $request,
        protected readonly array $data,
    ) {
        $this->init();
    }

    abstract public function init(): void;
    abstract public function title(): string;

    public final function __get(string $name): Value
    {
        if (!array_key_exists($name, $this->fields)) {
            $type = $this::class;
            throw new RuntimeException("Type $type has no field '$name'.");
        }

        $field = $this->fields[$name];
        $content = $this->data['content'][$name] ?? [];

        return $field->value($this->request, $content);
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

    public static function className(): string
    {
        return basename(str_replace('\\', '/', static::class));
    }

    public static function name(): ?string
    {
        if (!empty(static::$name)) {
            return static::$name;
        }

        return static::className();
    }

    public function uid(): string
    {
        return $this->data['uid'];
    }

    public static function template(): ?string
    {
        if (!empty(static::$template)) {
            return static::$template;
        }

        return static::className();
    }

    protected function getJsonContent(): array
    {
        $result = [];

        foreach ($this->list as $field) {
            $result[$field] = $this->__get($field)->json();
        }

        return $result;
    }

    public function json(): array
    {
        $data = $this->data;

        unset($data['classname']);

        $content = [
            'content' => $this->getJsonContent(),
        ];

        return array_merge($data, $content);
    }
}
