<?php

declare(strict_types=1);

namespace Conia\Core\View;

use Conia\Chuck\Factory;
use Conia\Chuck\Registry;
use Conia\Chuck\Request;
use Conia\Chuck\Response;
use Conia\Core\Collection;
use Conia\Core\Config;
use Conia\Core\Middleware\Permission;

class Panel
{
    protected string $publicPath;
    protected string $panelIndex;

    public function __construct(
        protected readonly Request $request,
        protected readonly Config $config,
        protected readonly Registry $registry,
    ) {
        $this->publicPath = $config->get('path.public');
        $this->panelIndex = $this->publicPath . '/panel/index.html';
    }

    #[Permission('panel')]
    public function boot(): array
    {
        $tag = $this->registry->tag(Collection::class);
        $collections = [];

        foreach ($tag->entries() as $id) {
            $collection = $tag->get($id);
            $collections[] = [
                'slug' => $id,
                'title' => $collection->title(),
            ];
        }

        return [
            // 'locales' => $this->config->get('locales.list'),
            // 'locale' => 'de',
            'panelPath' => $this->config->getPanelPath(),
            'debug' => $this->config->debug(),
            'env' => $this->config->env(),
            'csrfToken' => 'TOKEN', // TODO: real token
            'collections' => $collections,
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

    #[Permission('panel')]
    public function collection(string $collection): array
    {
        error_log(print_r($this->registry->tag(Collection::class)->entries(), true));

        return [];
    }
}
