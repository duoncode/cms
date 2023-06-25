<?php

declare(strict_types=1);

namespace Conia\Core\View;

use Conia\Chuck\Factory;
use Conia\Chuck\Registry;
use Conia\Chuck\Request;
use Conia\Chuck\Response;
use Conia\Core\Collection;
use Conia\Core\Config;
use Conia\Core\Finder;
use Conia\Core\Middleware\Permission;

class Panel
{
    protected string $publicPath;

    public function __construct(
        protected readonly Request $request,
        protected readonly Config $config,
        protected readonly Registry $registry,
    ) {
        $this->publicPath = $config->get('path.public');
    }

    public function boot(): array
    {
        $locales = $this->config->locales();
        $localesList = array_map(
            function ($locale) {
                return [
                    'id' => $locale->id,
                    'title' => $locale->title,
                    'fallback' => $locale->fallback,
                ];
            },
            iterator_to_array($locales),
            [] // Add an empty array to remove the assoc array keys
            //    See: https://www.php.net/manual/en/function.array-map.php#refsect1-function.array-map-returnvalues
        );

        return [
            'locales' => $localesList,
            'locale' => $locales->getDefault()->id,
            'debug' => $this->config->debug(),
            'env' => $this->config->env(),
            'csrfToken' => 'TOKEN', // TODO: real token
            'logo' => $this->config->get('panel.logo', null),
        ];
    }

    public function index(Factory $factory): Response
    {
        return Response::fromFactory($factory)->file($this->getPanelIndex());
    }

    public function catchall(Factory $factory, string $slug): Response
    {
        $file = $this->publicPath . '/panel/' . $slug;

        if (file_exists($file)) {
            return Response::fromFactory($factory)->file($file);
        }

        return Response::fromFactory($factory)->file($this->getPanelIndex());
    }

    #[Permission('panel')]
    public function collections(): array
    {
        $tag = $this->registry->tag(Collection::class);
        $collections = [];

        foreach ($tag->entries() as $id) {
            $collection = $tag->get($id);
            $collections[] = [
                'slug' => $id,
                'name' => $collection->name(),
            ];
        }

        return $collections;
    }

    #[Permission('panel')]
    public function collection(string $collection): array
    {
        $obj = $this->registry->tag(Collection::class)->get($collection);

        return [
            'name' => $obj->name(),
            'slug' => $collection,
            'header' => $obj->header(),
            'nodes' => $obj->listing(),
        ];
    }

    #[Permission('panel')]
    public function node(Finder $find, string $uid): array
    {
        $node = $find->node->byUid($uid);

        return $node->response();
    }

    protected function getPanelIndex(): string
    {
        return $this->publicPath . $config->getPanelPath() . '/index.html';
    }
}
