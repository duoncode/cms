<?php

declare(strict_types=1);

namespace Conia\Core\Value;

use Conia\Core\Locale;

class File
{
    public function __construct(
        protected readonly array $file,
        protected readonly Locale $locale
    ) {
    }

    public function __toString(): string
    {
        return htmlspecialchars($this->file['file']);
    }

    public function title(): string
    {
        $locale = $this->locale;

        while ($locale) {
            $value = $this->file[$this->locale->id];

            if ($value) {
                return $value;
            }

            $locale = $this->locale->fallback();
        }

        return '';
    }

    public function json(): mixed
    {
        return [];
    }
}
