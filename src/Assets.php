<?php

declare(strict_types=1);

namespace Conia\Core;

class Assets extends \Conia\Sizer\Assets
{
    public static function fromConfig(Config $config): \Conia\Sizer\Assets
    {
        $public = $config->get('path.public');

        return new \Conia\Sizer\Assets(
            $public . $config->get('path.assets'),
            $public . $config->get('path.cache'),
        );
    }
}
