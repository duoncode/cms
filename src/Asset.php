<?php

declare(strict_types=1);

namespace Conia\Core;

use Conia\Chuck\Request;
use Conia\Core\Exception\RuntimeException;
use Conia\Sizer;

class Asset
{
    public function __construct(
        protected readonly Request $request,
        protected readonly Config $config,
        protected readonly Sizer\Image|Sizer\CachedImage $image,
    ) {
    }

    public function resize(int $width = 0, int $height = 0, bool $crop = false): Asset
    {
        if ($this->image instanceof Sizer\CachedImage) {
            throw new RuntimeException('Image is already resized');
        }

        return new self(
            $this->request,
            $this->config,
            $this->image->resize($width, $height, $crop)
        );
    }

    public function path(bool $bust = true): string
    {
        if ($this->image instanceof Sizer\CachedImage) {
            $dir = $this->config->get('path.cache');
        } else {
            $dir = $this->config->get('path.assets');
        }

        return $dir . '/' . $this->image->relative($bust);
    }

    public function url(bool $bust = true): string
    {
        return $this->request->origin() . $this->path($bust);
    }
}
