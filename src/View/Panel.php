<?php

declare(strict_types=1);

namespace Conia\View;

use Conia\Request;
use Conia\Config;


class Panel
{
    protected Config $config;

    public function __construct(protected Request $request)
    {
        $this->config = $request->getConfig();
    }

    public function settings(): array
    {
        return [
            'panelPath' => $this->config->get('panel.path'),
            'locales' => $this->config->get('locales.list'),
            'locale' => 'de',
            'debug' => $this->request->debug,
            'env' => $this->request->env,
            'csrfToken' => 'TOKEN' // TODO: real token
        ];
    }

    public function boot(): array
    {
        $config = $this->config;

        return [
            'types' => $config->types(),
        ];
    }
}
