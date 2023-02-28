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
    protected ?int $quality = null;

    public function __toString(): string
    {
        return $this->tag(true);
    }

    public function tag(bool $bust = true, string $class = null): string
    {
        return sprintf(
            '<img %ssrc="%s" alt="%s" data-path-original="%s">',
            $class ? sprintf('class="%s" ', htmlspecialchars($class, ENT_QUOTES, 'UTF-8')) : '',
            $this->url($bust),
            htmlspecialchars(
                $this->alt() ?: strip_tags($this->title()),
                ENT_QUOTES,
                'UTF-8'
            ),
            $this->path(),
        );
    }

    public function url(bool $bust = false): string
    {
        if ($url = filter_var($this->getImage($this->index)->url($bust), FILTER_VALIDATE_URL)) {
            return $url;
        }

        throw new RuntimeException('Invalid image url');
    }

    public function path(bool $bust = false): string
    {
        return filter_var($this->getImage($this->index)->path($bust), FILTER_SANITIZE_URL);
    }

    public function width(int $width, bool $enlarge = false): static
    {
        $this->size = new Assets\Size($width);
        $this->resizeMode = Assets\ResizeMode::Width;
        $this->enlarge = $enlarge;

        return $this;
    }

    public function height(int $height, bool $enlarge = false): static
    {
        $this->size = new Assets\Size($height);
        $this->resizeMode = Assets\ResizeMode::Height;
        $this->enlarge = $enlarge;

        return $this;
    }

    public function longSide(int $size, bool $enlarge = false): static
    {
        $this->size = new Assets\Size($size);
        $this->resizeMode = Assets\ResizeMode::LongSide;
        $this->enlarge = $enlarge;

        return $this;
    }

    public function shortSide(int $size, bool $enlarge = false): static
    {
        $this->size = new Assets\Size($size);
        $this->resizeMode = Assets\ResizeMode::ShortSide;
        $this->enlarge = $enlarge;

        return $this;
    }

    public function fit(int $width, int $height, bool $enlarge = false): static
    {
        $this->size = new Assets\Size($width, $height);
        $this->resizeMode = Assets\ResizeMode::Fit;
        $this->enlarge = $enlarge;

        return $this;
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

        $this->size = new Assets\Size($width, $height, $position);
        $this->resizeMode = Assets\ResizeMode::Crop;

        return $this;
    }

    public function freecrop(int $width, int $height, int|false $x = false, int|false $y = false): static
    {
        $this->size = new Assets\Size($width, $height, ['x' => $x, 'y' => $y]);
        $this->resizeMode = Assets\ResizeMode::FreeCrop;

        return $this;
    }

    public function quality(int $quality): static
    {
        $this->quality = $quality;

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

        if ($this->size) {
            $image = $image->resize($this->size, $this->resizeMode, $this->enlarge, $this->quality);
        }

        return $image;
    }
}
