<?php

declare(strict_types=1);

namespace Conia\Core\View;

use Conia\Chuck\Request;
use Conia\Core\Config;
use Conia\Core\Middleware\Permission;

class Panel
{
    public function __construct(
        protected readonly Request $request,
        protected readonly Config $config,
    ) {
    }

    public function settings(): array
    {
        return [
            // 'locales' => $this->config->get('locales.list'),
            // 'locale' => 'de',
            'debug' => $this->config->debug(),
            'env' => $this->config->env(),
            'csrfToken' => 'TOKEN', // TODO: real token
        ];
    }

    #[Permission('panel')]
    public function boot(): array
    {
        $config = $this->config;

        return [
            'types' => $config->types,
        ];
    }

    #[Permission('panel')]
    public function type(string $name): array
    {
        $type = $this->config->types->get($name);

        return [
            'name' => $name,
            'label' => $type->label,
        ];
    }
}
