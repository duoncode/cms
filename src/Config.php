<?php

declare(strict_types=1);

namespace Conia;

use Chuck\Config as BaseConfig;


class Config extends BaseConfig
{
    public function __construct(array $config)
    {
        $coniaConfig = [];

        parent::__construct(array_replace_recursive(
            $coniaConfig,
            $config,
        ));
    }
}
