<?php

declare(strict_types=1);

namespace Conia\Core\View;

use Conia\Chuck\Factory;
use Conia\Chuck\Request;
use Conia\Chuck\Response;
use Conia\Core\Config;
use Conia\Core\Middleware\Permission;

class Panel
{
    protected string $publicPath;
    protected string $panelIndex;

    public function __construct(
        protected readonly Request $request,
        protected readonly Config $config,
    ) {
        $this->publicPath = $config->get('path.public');
        $this->panelIndex = $this->publicPath . '/panel/index.html';
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
        return [
            'name' => $name,
            'label' => 'hans',
        ];
    }

    public function index(Factory $factory): Response
    {
        return Response::fromFactory($factory)->file($this->panelIndex);
    }

    public function catchall(Factory $factory, string $slug): Response
    {
        $file = $this->publicPath . '/panel/' . $slug;

        if (file_exists($file)) {
            return Response::fromFactory($factory)->file($file);
        }

        return Response::fromFactory($factory)->file($this->panelIndex);
    }
}
