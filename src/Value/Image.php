<?php

declare(strict_types=1);

namespace Conia\Core\Value;

use Conia\Core\Asset;
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

    public function url(bool $bust = true): string
    {
        if ($url = filter_var($this->getImage()->url($bust), FILTER_VALIDATE_URL)) {
            return $url;
        }

        throw new RuntimeException('Invalid image url');
    }

    public function path(bool $bust = true): string
    {
        return filter_var($this->getImage()->path($bust), FILTER_SANITIZE_URL);
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
        return $this->textValue('link');
    }

    public function title(): string
    {
        return $this->textValue('title');
    }

    public function alt(): string
    {
        return $this->textValue('alt');
    }

    protected function textValue(string $key): string
    {
        $locale = $this->locale;

        while ($locale) {
            $value = $this->data['files'][$this->index][$key][$locale->id] ?? null;

            if ($value) {
                return $value;
            }

            $locale = $locale->fallback();
        }

        return '';
    }

    protected function getImage(): Asset
    {
        $image = $this->getAssets()->image($this->assetsPath() . $this->data['files'][$this->index]['file']);

        if ($this->width > 0 || $this->height > 0) {
            $image = $image->resize($this->width, $this->height, $this->crop);
        }

        return $image;
    }
}
