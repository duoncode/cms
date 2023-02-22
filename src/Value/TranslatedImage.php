<?php

declare(strict_types=1);

namespace Conia\Core\Value;

class TranslatedImage extends Image
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

    protected function getImage(int $index): \Conia\Core\Image
    {
        $file = $this->translated('file', $index);
        $image = $this->getAssets()->image($this->assetsPath() . $file);

        if ($this->width > 0 || $this->height > 0) {
            $image = $image->resize($this->width, $this->height, $this->crop);
        }

        return $image;
    }
}
