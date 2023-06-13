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

    #[Permission('panel')]
    public function boot(): array
    {
        return [
            // 'locales' => $this->config->get('locales.list'),
            // 'locale' => 'de',
            'panelPath' => $this->config->getPanelPath(),
            'types' => [['name' => 'Type 1'], ['name' => 'Type 2']],
            'sections' => [['name' => 'Section 1'], ['name' => 'Section 2']],
            'debug' => $this->config->debug(),
            'env' => $this->config->env(),
            'csrfToken' => 'TOKEN', // TODO: real token
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
