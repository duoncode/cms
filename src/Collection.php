<?php

declare(strict_types=1);

namespace Conia\Core;

abstract class Collection
{
    public function __construct(
        public readonly Finder $find,
    ) {
    }

    abstract public function entries(): array;

    public function title(): string
    {
        return preg_replace('/(?<!^)[A-Z]/', ' $0', static::class);
    }
}
