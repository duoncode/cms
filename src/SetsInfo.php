<?php

declare(strict_types=1);

namespace Conia;

use \ValueError;


trait SetsInfo
{
    public readonly string $label;
    public readonly ?string $description;

    protected function setInfo(string $label, ?string $description)
    {
        $label = $this->sanitize($label);

        if (!$label) {
            throw new ValueError('Label must not be empty');
        }

        $this->label = $label;
        $this->description = $this->sanitize($description);
    }

    protected function sanitize(?string $value): ?string
    {
        if (isset($value)) {
            return htmlspecialchars(trim($value)) ?: null;
        }

        return null;
    }
}
