<?php

declare(strict_types=1);

namespace Conia\Value;

use Conia\Locale;
use Conia\Request;


abstract class Value
{
    protected readonly Locale $locale;

    public function __construct(
        protected readonly Request $request,
        protected readonly array $data,
    ) {
        $this->locale = $request->locale();
    }

    abstract public function __toString(): string;
    abstract public function json(): mixed;
}
