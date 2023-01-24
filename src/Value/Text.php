<?php

declare(strict_types=1);

namespace Conia\Core\Value;

class Text extends Value
{
    public function __toString(): string
    {
        return htmlspecialchars($this->raw());
    }

    public function raw(): string
    {
        if ($this->multilang) {
            $locale = $this->locale;

            while ($locale) {
                $value = $this->data['value'][$locale->id] ?? null;

                if ($value) {
                    return $value;
                }

                $locale = $locale->fallback();
            }

            return '';
        }

        return $this->data['value'] ?? '';
    }

    public function json(): mixed
    {
        return $this->raw();
    }
}
