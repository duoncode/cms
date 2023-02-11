<?php

declare(strict_types=1);

namespace Conia\Core\Value;

use Conia\Core\Asset;

class TranslatedImage extends Image
{
    public function textValue(string $key): string
    {
        return $this->translated($key);
    }

    protected function translated(string $key): string
    {
        $locale = $this->locale;

        while ($locale) {
            $value = $this->data['files'][0][$locale->id][$key] ?? null;

            if ($value) {
                return $value;
            }

            $locale = $locale->fallback();
        }

        return '';
    }

    protected function getImage(): Asset
    {
        $file = $this->translated('file');
        $image = $this->getAssets()->image($this->assetsPath() . $file);

        if ($this->width > 0 || $this->height > 0) {
            $image = $image->resize($this->width, $this->height, $this->crop);
        }

        return $image;
    }
}
