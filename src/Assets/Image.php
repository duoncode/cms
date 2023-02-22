<?php

declare(strict_types=1);

namespace Conia\Core\Assets;

use Conia\Chuck\Request;
use Conia\Core\Exception\RuntimeException;
use Conia\Sizer;

class Image
{
    public function __construct(
        protected readonly Request $request,
        protected readonly Assets $assets,
        protected readonly Sizer\Image|Sizer\CachedImage $image,
    ) {
    }

    public function resize(int $width = 0, int $height = 0, bool $crop = false): static
    {
        if ($this->image instanceof Sizer\CachedImage) {
            throw new RuntimeException('Image is already resized');
        }

        return new self(
            $this->request,
            $this->assets,
            $this->image->resize($width, $height, $crop)
        );
    }

    public function path(bool $bust = false): string
    {
        if ($this->image instanceof Sizer\CachedImage) {
            $dir = $this->assets->cacheDir;
        } else {
            $dir = $this->assets->assetsDir;
        }

        return substr($dir . '/' . $this->image->urlPath($bust), strlen($this->assets->publicDir));
    }

    public function url(bool $bust = false): string
    {
        return $this->request->origin() . $this->path($bust);
    }
}
