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
        $this->config = $request->config();
    }

    public function settings(): array
    {
        return [
            // 'locales' => $this->config->get('locales.list'),
            // 'locale' => 'de',
            'debug' => $this->request->config()->debug(),
            'env' => $this->request->config()->env(),
            'csrfToken' => 'TOKEN' // TODO: real token
        ];
    }

    public function boot(): array
    {
        $config = $this->config;

        return [
            'panelPath' => $config->panelUrl(),
            'types' => $config->types,
        ];
    }

    public function type(string $name): array
    {
        $type = $this->config->types->get($name);

        return [
            'name' => $name,
            'label' => $type->label,
        ];
    }
}
