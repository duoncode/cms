<?php

declare(strict_types=1);

namespace Conia;

use \Generator;
use \ValueError;
use Conia\Data;


abstract class Type
{
    protected static $name = null;

    public function __construct(
        public readonly string|array $label,
        public readonly array $permissions = [],
        public readonly int $columns = 12,
    ) {
        if ($columns < 12 || $columns > 25) {
            throw new ValueError('The value of $columns must be >= 12 and <= 25');
        }
    }

    public static function fromUid(string $uid): self
    {
    }

    public static function fromSlug(string $uid): self
    {
    }

    public function name(): string
    {
        return static::$name ?: basename(str_replace('\\', '/', strtolower($this::class)));
    }

    public function template(): string
    {
        return $this->name() . '.php';
    }

    public function get(): Generator
    {
        foreach ($this as $field => $object) {
            if ($object instanceof Data) {
                (yield $field => $object->meta());
            }
        }
    }

    abstract public function init(): void;
    abstract public function title(): string;

    public function structure(): array
    {
        $result = [];

        foreach ($this->get() as $key => $value) {
            $result[] = array_merge(['name' => $key], $value);
        }

        return $result;
    }

    public function renameField(string $current, string $new): void
    {
    }
}
