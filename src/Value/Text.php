<?php

declare(strict_types=1);

namespace Conia\Value;


class Text extends Value
{
    public function raw(): string
    {
        $locale = $this->locale;

        while ($locale) {
            $value = $this->data[$this->locale->id];

            if ($value) return $value;

            $locale = $this->locale->fallback();
        }

        return '';
    }

    public function __toString(): string
    {
        return htmlspecialchars($this->data[$this->locale]);
    }

    public function json(): mixed
    {
        return $this->raw();
    }
}
