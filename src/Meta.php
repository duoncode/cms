<?php

declare(strict_types=1);

trait Meta
{
    public readonly ?string $description;

    protected function validate(): void
    {
        $namePattern = '/^[a-zA-Z][a-zA-Z0-9_]{1,63}$/';

        if (isset($this->name) && !preg_match($namePattern, $this->name)) {
            throw new ValueError("The value of \$name must adhere to this pattern: $namePattern");
        }

        $templatePattern = '/^[a-z][a-z0-9_]{1,64}$/';

        if (isset($this->template) && !preg_match($templatePattern, $this->template)) {
            throw new ValueError("The value of \$template must adhere to this pattern: $templatePattern");
        }

        if ($this->columns < 12 || $this->columns > 25) {
            throw new ValueError('The value of $columns must be >= 12 and <= 25');
        }
    }

    protected function sanitize(?string $value): ?string
    {
        if (isset($value)) {
            return htmlspecialchars(trim($value)) ?: null;
        }

        return null;
    }
}
