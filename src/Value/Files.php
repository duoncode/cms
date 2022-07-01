<?php

declare(strict_types=1);

namespace Conia\Value;

use Conia\Locale;


class Files extends Value
{
    public function raw(): array
    {
        $locale = $this->locale;

        while ($locale) {
            $value = $this->data[$this->locale->id] ?? null;

            if ($value) return $value;

            $locale = $this->locale->fallback();
        }

        return [];
    }

    public function __toString(): string
    {
        return 'Files: count(' . count($this->raw()) . ')';
    }

    public function json(): mixed
    {
        return $this->raw();
    }

    protected function getFile(array $file, Locale $locale): File
    {
        return new File($file, $locale);
    }

    public function all(): array
    {
        return array_map(function (array $file) {
            return $this->getFile($file, $this->locale);
        }, $this->raw());
    }
}
