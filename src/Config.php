<?php

declare(strict_types=1);

namespace Conia;

use Chuck\Config as BaseConfig;


class Config extends BaseConfig
{
    protected array $layouts = [];

    public function __construct(array $config)
    {
        $coniaConfig = [
            'panel.path' => 'panel',
            'panel.theme' => null,
            'sql.conia' => dirname(__DIR__) . DIRECTORY_SEPARATOR . 'db' . DIRECTORY_SEPARATOR . 'sql',
            'session.expires' => 60 * 60 * 24,
            'session.authcookie' => $config['app'] . '_auth',
        ];

        parent::__construct(array_replace_recursive(
            $coniaConfig,
            $config,
        ));
    }

    public function addLayouts(array $layouts): void
    {
        $this->layouts = array_merge($this->layouts, $layouts);
    }

    public function layouts(): array
    {
        return $this->layouts;
    }
}
