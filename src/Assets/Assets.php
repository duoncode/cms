<?php

declare(strict_types=1);

namespace Conia\Core\Assets;

use Conia\Chuck\Request;
use Conia\Core\Config;
use Conia\Core\Util\Path;

class Assets
{
    public readonly string $publicDir;
    public readonly string $assetsDir;
    public readonly string $cacheDir;
    protected \Conia\Sizer\Assets $sizerAssets;

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
        if (!isset($this->sizerAssets)) {
            $this->sizerAssets = new \Conia\Sizer\Assets(
                $this->assetsDir,
                $this->cacheDir,
            );
        }

        return new Image($this->request, $this, $path);
    }

    public function file(string $path): File
    {
        return new File($this->request, $this, $path);
    }
}
