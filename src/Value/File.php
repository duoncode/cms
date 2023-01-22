<?php

declare(strict_types=1);

namespace Conia\Core\Value;

use Conia\Core\Locale;

class File extends Value
{
    public function __toString(): string
    {
        return htmlspecialchars($this->file['file']);
    }

    public function title(): string
    {
        $locale = $this->request->get('locale');
        assert($locale instanceof Locale);

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
