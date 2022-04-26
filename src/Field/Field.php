<?php

declare(strict_types=1);

namespace Conia\Field;

use \Exception;


abstract class Field
{
    public function __construct(
        protected string $name,
        protected string|array $label,
        protected bool $multilang = false,
        protected bool $required = false,
        protected string|array $description = '',
        protected int $width = 100,
    ) {
        if (empty($name)) {
            throw new Exception('$name must not be emtpy');
        }

        if ($width > 100 || $width < 10) {
            throw new Exception('$width must be >= 10 and <= 100');
        }
    }
}
