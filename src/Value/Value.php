<?php

declare(strict_types=1);

namespace Conia\Value;

use \ReflectionClass;
use Conia\Locale;
use Conia\Request;


abstract class Value
{
    public readonly string $fieldType;
    public readonly string $valueType;
    protected readonly Locale $locale;

    public function __construct(
        protected readonly Request $request,
        protected readonly array $data,
    ) {
        $this->locale = $request->locale();
        $this->fieldType = $data['type'];
        $this->valueType = (new ReflectionClass($this))->getShortName();
    }

    abstract public function __toString(): string;
    abstract public function json(): mixed;
}
