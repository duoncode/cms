<?php

declare(strict_types=1);

namespace Conia\Core\Value;

use Conia\Core\Assets;
use Conia\Core\Exception\RuntimeException;
use Gumlet\ImageResize;

class Image extends File
{
    protected ?Assets\Size $size = null;
    protected ?Assets\ResizeMode $resizeMode = null;
    protected bool $enlarge = false;
    protected bool $lazy = false;
    protected ?int $quality = null;

    public function __toString(): string
    {
        return $this->tag(true);
    }

    public function tag(bool $bust = true, string $class = null): string
    {
        return sprintf(
            '<img %ssrc="%s" alt="%s" data-path-original="%s">',
            $class ? sprintf('class="%s" ', escape($class)) : '',
            $this->url($bust),
            escape($this->alt() ?: strip_tags($this->title())),
            $this->path(),
        );
    }

    public function url(bool $bust = false): string
    {
        return $this->getImage($this->index)->url($bust);
    }

    public function path(bool $bust = false): string
    {
        return $this->getImage($this->index)->path($bust);
    }

    public function lazy($lazy = true): static
    {
        $new = clone $this;
        $new->lazy = $lazy;

        return $new;
    }

    public function width(int $width, bool $enlarge = false): static
    {
        $new = clone $this;
        $new->size = new Assets\Size($width);
        $new->resizeMode = Assets\ResizeMode::Width;
        $new->enlarge = $enlarge;

        return $new;
    }

    public function height(int $height, bool $enlarge = false): static
    {
        $new = clone $this;
        $new->size = new Assets\Size($height);
        $new->resizeMode = Assets\ResizeMode::Height;
        $new->enlarge = $enlarge;

        return $new;
    }

    public function longSide(int $size, bool $enlarge = false): static
    {
        $new = clone $this;
        $new->size = new Assets\Size($size);
        $new->resizeMode = Assets\ResizeMode::LongSide;
        $new->enlarge = $enlarge;

        return $new;
    }

    public function shortSide(int $size, bool $enlarge = false): static
    {
        $new = clone $this;
        $new->size = new Assets\Size($size);
        $new->resizeMode = Assets\ResizeMode::ShortSide;
        $new->enlarge = $enlarge;

        return $new;
    }

    public function fit(int $width, int $height, bool $enlarge = false): static
    {
        $new = clone $this;
        $new->size = new Assets\Size($width, $height);
        $new->resizeMode = Assets\ResizeMode::Fit;
        $new->enlarge = $enlarge;

        return $new;
    }

    public function crop(int $width, int $height, string $position = 'center'): static
    {
        $position = match ($position) {
            'top' => ImageResize::CROPTOP,
            'centre' => ImageResize::CROPCENTRE,
            'center' => ImageResize::CROPCENTER,
            'bottom' => ImageResize::CROPBOTTOM,
            'left' => ImageResize::CROPLEFT,
            'right' => ImageResize::CROPRIGHT,
            'topcenter' => ImageResize::CROPTOPCENTER,
            default => throw new RuntimeException('Crop position not supported: ' . $position),
        };

        $new = clone $this;
        $new->size = new Assets\Size($width, $height, $position);
        $new->resizeMode = Assets\ResizeMode::Crop;

        return $new;
    }

    public function freecrop(int $width, int $height, int|false $x = false, int|false $y = false): static
    {
        $new = clone $this;
        $new->size = new Assets\Size($width, $height, ['x' => $x, 'y' => $y]);
        $new->resizeMode = Assets\ResizeMode::FreeCrop;

        return $new;
    }

    public function quality(int $quality): static
    {
        $new = clone $this;
        $new->quality = $quality;

        return $new;
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

        if ($this->size) {
            $image = $image->resize(
                $this->size,
                $this->resizeMode,
                $this->enlarge,
                $this->lazy,
                $this->quality,
            );
        }

        return $image;
    }
}
