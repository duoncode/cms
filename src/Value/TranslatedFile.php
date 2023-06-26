<?php

declare(strict_types=1);

namespace Conia\Core\Value;

use Conia\Core\Assets;

class TranslatedFile extends File
{
    protected function textValue(string $key, int $index): string
    {
        return $this->translated($key, $index);
    }

    protected function translated(string $key, int $index): string
    {
        $locale = $this->locale;

        while ($locale) {
            $value = $this->data['files'][$index][$locale->id][$key] ?? null;

            if ($value) {
                return $value;
            }

            $locale = $locale->fallback();
        }

        return '';
    }

    protected function getFile(int $index): Assets\File
    {
        $file = $this->translated('file', $index);

        return $this->getAssets()->file($this->assetsPath() . $file);
    }
}
