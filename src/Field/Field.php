<?php

declare(strict_types=1);

namespace Conia\Field;

use \Exception;


abstract class Field
{
    public readonly string $type;

    public function __construct(
        protected string|array $label,
        protected bool $required = false,
        protected bool $multilang = false,
        protected string|array $description = '',
        protected int $width = 100,
    ) {
        if ($width > 100 || $width < 10) {
            throw new Exception('$width must be >= 10 and <= 100');
        }

        $this->type = basename(str_replace('\\', '/', strtolower($this::class)));
    }

    public function validate(): bool
    {
        return true;
    }
}
