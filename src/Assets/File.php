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

    public function path(bool $bust = false): string
    {
        $path = Path::inside($this->assets->assetsDir, $this->file);
        $path = implode('/', array_map('urlencode', explode('/', str_replace('\\', '/', $path))));

        if ($bust) {
            $path = $this->bust($path);
        }

        return substr($path, strlen($this->assets->publicDir));
    }

    public function url(bool $bust = true): string
    {
        return $this->request->origin() . $this->path($bust);
    }

    protected function bust(string $path): string
    {
        $buster = hash('xxh32', (string)filemtime($path));

        return $path . '?v=' . $buster;
    }
}
