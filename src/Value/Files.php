<?php

declare(strict_types=1);

namespace Conia\Core\Value;

use Iterator;

class Files extends Value implements Iterator
{
    protected int $pointer = 0;

    public function __toString(): string
    {
        return 'Files: count(' . count($this->raw()) . ')';
    }

    public function rewind(): void
    {
        $this->pointer = 0;
    }

    public function current(): File
    {
        return new File($this->node, $this->field, $this->context, $this->pointer);
    }

    public function key(): int
    {
        return $this->pointer;
    }

    public function next(): void
    {
        $this->pointer++;
    }

    public function valid(): bool
    {
        return isset($this->data['files'][$this->pointer]);
    }

    public function raw(): array
    {
        $locale = $this->locale;

        while ($locale) {
            $value = $this->data[$this->locale->id] ?? null;

            if ($value) {
                return $value;
            }

            $locale = $this->locale->fallback();
        }

        return [];
    }

    public function json(): mixed
    {
        return $this->raw();
    }

    public function isset(): bool
    {
        return isset($this->data['files'][0]) ? true : false;
    }

    protected function len(): int
    {
        return count($this->data['files']);
    }
}
