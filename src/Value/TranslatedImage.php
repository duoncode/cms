<?php

declare(strict_types=1);

namespace Conia\Core\Value;

use Conia\Core\Assets;

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

    protected function getImage(int $index): Assets\Image
    {
        $file = $this->translated('file', $index);
        $image = $this->getAssets()->image($this->assetsPath() . $file);

        if ($this->size) {
            $image = $image->resize($this->size, $this->resizeMode, $this->enlarge, $this->quality);
        }

        return $image;
    }
}
