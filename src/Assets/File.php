<?php

declare(strict_types=1);

namespace Conia\Core\Assets;

use Conia\Chuck\Request;
use Conia\Core\Util\Path;

class File
{
    public function __construct(
        protected readonly Request $request,
        protected readonly Assets $assets,
        protected readonly string $file,
    ) {
    }

    public function path(): string
    {
        return Path::inside($this->assets->assetsDir, $this->file);
    }

    public function publicPath(bool $bust = false): string
    {
        $path = implode('/', array_map('urlencode', explode('/', str_replace('\\', '/', $this->path()))));

        if ($bust) {
            $path = $this->bust($path);
        }

        return substr($path, strlen($this->assets->publicDir));
    }

    public function url(bool $bust = true): string
    {
        return $this->request->origin() . $this->publicPath($bust);
    }

    protected function bust(string $path): string
    {
        $buster = hash('xxh32', (string)filemtime($path));

        return $path . '?v=' . $buster;
    }
}
