<?php

declare(strict_types=1);

namespace Conia;


abstract class Value
{
    final public function __construct(
        public readonly array $data,
        protected readonly Locale $locale
    ) {
    }

    abstract public function __toString(): string;
    abstract public function json(): mixed;
}
