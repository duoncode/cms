<?php

declare(strict_types=1);

namespace Conia\View;

use Conia\Request;
use Conia\Config;


class System
{
    protected Config $config;

    public function __construct(protected Request $request)
    {
        $this->config = $request->getConfig();
    }

    public function settings(): array
    {
        $config = $this->config;

        error_log('hinna');

        return [
            'panelPath' => $config->get('panel.path'),
            'locales' => $config->get('locales.list'),
            'locale' => 'de',
        ];
    }
}
