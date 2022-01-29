<?php

declare(strict_types=1);

namespace Conia;

use Chuck\Config as BaseConfig;
use Conia\Model\Auth;

class Config extends BaseConfig
{
    public function __construct(array $config)
    {
        $cmsConfig = [
            'di' => [
                'Auth' => Auth::class,
            ],
        ];

        parent::construct(array_replace_recursive(
            $cmsConfig,
            $config,
        ));
    }
}
