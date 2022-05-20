<?php

declare(strict_types=1);

#[Attribute]
class TypeMeta
{
    public function __construct(
        public readonly ?string $name = null,
        public readonly ?string $description = null,
        public readonly ?string $template = null,
        public readonly int $columns = 12,
    ) {
        $namePattern = '/^[a-zA-Z][a-zA-Z0-9_]{1,63}$/';
        $templatePattern = '/^[a-z][a-z0-9_]{1,64}$/';

        if (isset($name) && !preg_match($namePattern, $name)) {
            throw new ValueError("The value of \$name must adhere to this pattern: $namePattern");
        }

        if (isset($template) && !preg_match($templatePattern, $template)) {
            throw new ValueError("The value of \$template must adhere to this pattern: $templatePattern");
        }

        if ($columns < 12 || $columns > 25) {
            throw new ValueError('The value of $columns must be >= 12 and <= 25');
        }
    }
}
