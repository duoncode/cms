<?php

declare(strict_types=1);

namespace Conia\Core;

use Conia\Chuck\Request;

class Assets
{
    protected \Conia\Sizer\Assets $assets;

    public function __construct(
        protected readonly Request $request,
        protected readonly Config $config
    ) {
        $public = $config->get('path.public');

        $this->assets = new \Conia\Sizer\Assets(
            $public . $config->get('path.assets'),
            $public . $config->get('path.cache'),
        );
    }

    public function image(string $path): Asset
    {
        return new Asset($this->request, $this->config, $this->assets->image($path));
    }
}
