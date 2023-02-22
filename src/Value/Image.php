<?php

declare(strict_types=1);

namespace Conia\Core\Value;

use Conia\Core\Assets;
use Conia\Core\Exception\RuntimeException;

class Image extends File
{
    protected int $width = 0;
    protected int $height = 0;
    protected bool $crop = false;

    public function __toString(): string
    {
        return $this->tag(true);
    }

    public function tag(bool $bust = true, string $class = null): string
    {
        return sprintf(
            '<img %ssrc="%s" alt="%s">',
            $class ? sprintf('class="%s" ', htmlspecialchars($class, ENT_QUOTES, 'UTF-8')) : '',
            $this->url($bust),
            htmlspecialchars(
                $this->alt() ?: strip_tags($this->title()),
                ENT_QUOTES,
                'UTF-8'
            )
        );
    }

    public function url(bool $bust = false): string
    {
        error_log($this->getImage($this->index)->url($bust));
        if ($url = filter_var($this->getImage($this->index)->url($bust), FILTER_VALIDATE_URL)) {
            return $url;
        }

        throw new RuntimeException('Invalid image url');
    }

    public function path(bool $bust = false): string
    {
        return filter_var($this->getImage($this->index)->path($bust), FILTER_SANITIZE_URL);
    }

    public function resize(int $width = 0, int $height = 0, bool $crop = false): self
    {
        $this->width = $width;
        $this->height = $height;
        $this->crop = $crop;

        return $this;
    }

    public function link(): string
    {
        return $this->textValue('link', $this->index);
    }

    public function title(): string
    {
        return $this->textValue('title', $this->index);
    }

    public function alt(): string
    {
        return $this->textValue('alt', $this->index);
    }

    protected function textValue(string $key, int $index): string
    {
        if ($this->translate) {
            return $this->translated($key, $index);
        }

        return $this->data['files'][$this->index][$key][$this->defaultLocale->id] ?? '';
    }

    protected function translated(string $key, int $index): string
    {
        $locale = $this->locale;

        while ($locale) {
            $value = $this->data['files'][$index][$key][$locale->id] ?? null;

            if ($value) {
                return $value;
            }

            $locale = $locale->fallback();
        }

        return '';
    }

    protected function getImage(int $index): Assets\Image
    {
        $image = $this->getAssets()->image($this->assetsPath() . $this->data['files'][$index]['file']);

        if ($this->width > 0 || $this->height > 0) {
            $image = $image->resize($this->width, $this->height, $this->crop);
        }

        return $image;
    }
}
