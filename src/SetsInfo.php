<?php

declare(strict_types=1);

namespace Conia;

use \ValueError;


trait SetsInfo
{
    public readonly string $name;
    public readonly string $label;
    public readonly ?string $description;

    protected function setInfo(?string $label, ?string $name, ?string $description)
    {
        if ($name) {
            $namePattern = '/^[a-zA-Z][a-zA-Z0-9_-]{1,63}$/';
            if (!preg_match($namePattern, $name)) {
                throw new ValueError("The value of \$name must adhere to this pattern: $namePattern");
            }
            $this->name = $name;
        } else {
            $this->name = $this->getNameFromClass();
        }

        $this->label = $this->sanitize($label) ?: $this->getNameFromClass();
        $this->description = $this->sanitize($description);
    }

    protected function sanitize(?string $value): ?string
    {
        if (isset($value)) {
            return htmlspecialchars(trim($value)) ?: null;
        }

        return null;
    }

    protected function getNameFromClass(): string
    {
        return basename(str_replace('\\', '/', $this::class));
    }
}
