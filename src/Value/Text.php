<?php

declare(strict_types=1);

namespace Conia\Core\Value;

class Text extends Value
{
    protected string $value;

    public function __toString(): string
    {
        return htmlspecialchars($this->raw());
    }

    public function raw(): string
    {
        if (isset($this->value)) {
            return $this->value;
        }

        if ($this->translate) {
            $locale = $this->locale;

            while ($locale) {
                $value = $this->data['value'][$locale->id] ?? null;

                if ($value) {
                    $this->value = $value;

                    return $value;
                }

                $locale = $locale->fallback();
            }

            $this->value = '';

            return '';
        }

        $this->value = isset($this->data['value']) ? $this->data['value'] : '';

        return $this->value;
    }

    public function json(): mixed
    {
        return $this->raw();
    }

    public function isset(): bool
    {
        return $this->raw() ?? null ? true : false;
    }
}
