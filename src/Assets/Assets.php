<?php

declare(strict_types=1);

namespace Conia\Cms\Assets;

use Conia\Chuck\Request;
use Conia\Cms\Config;
use Conia\Cms\Util\Path;

class Assets
{
    public readonly string $publicDir;
    public readonly string $assetsDir;
    public readonly string $cacheDir;

    public function __construct(
        protected readonly Request $request,
        protected readonly Config $config
    ) {
        $this->publicDir = rtrim(realpath($config->get('path.public')), '\\/');

        $this->assetsDir = Path::inside($this->publicDir, $config->get('path.assets'));
        $this->cacheDir = Path::inside($this->publicDir, $config->get('path.cache'));
    }

    public function image(string $path): Image
    {
        return new Image($this->request, $this, $path);
    }

    public function file(string $path): File
    {
        return new File($this->request, $this, $path);
    }
}
