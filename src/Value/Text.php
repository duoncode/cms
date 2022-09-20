<?php

declare(strict_types=1);

namespace Conia\Value;


class Text extends Value
{
    public function raw(): string
    {
        $locale = $this->locale;

        while ($locale) {
            $value = $this->data[$locale->id] ?? null;

            if ($value) return $value;

            $locale = $locale->fallback();
        }

        return '';
    }

    public function __toString(): string
    {
        return htmlspecialchars($this->raw());
    }

    public function json(): mixed
    {
        return $this->raw();
    }
}
