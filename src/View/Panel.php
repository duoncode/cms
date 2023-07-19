<?php

declare(strict_types=1);

namespace Conia\Core\View;

use Conia\Chuck\Exception\HttpNotFound;
use Conia\Chuck\Factory;
use Conia\Chuck\Registry;
use Conia\Chuck\Request;
use Conia\Chuck\Response;
use Conia\Core\Collection;
use Conia\Core\Config;
use Conia\Core\Context;
use Conia\Core\Finder\Finder;
use Conia\Core\Middleware\Permission;
use Conia\Core\Node\Node;

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
            'locale' => $locales->getDefault()->id, // TODO: set the correct user locale
            'defaultLocale' => $locales->getDefault()->id,
            'debug' => $this->config->debug(),
            'env' => $this->config->env(),
            'csrfToken' => 'TOKEN', // TODO: real token
            'logo' => $this->config->get('panel.logo', null),
            'transliterate' => $this->config->get('slug.transliterate'),
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
        $blueprints = [];

        foreach ($obj->blueprints() as $blueprint) {
            $blueprints[] = ['slug' => $blueprint::handle(), 'name' => $blueprint::name()];
        }

        return [
            'name' => $obj->name(),
            'slug' => $collection,
            'header' => $obj->header(),
            'nodes' => $obj->listing(),
            'blueprints' => $blueprints,
        ];
    }

    #[Permission('panel')]
    public function blueprint(string $type, Context $context, Finder $find): array
    {
        $class = $this->registry->tag(Node::class)->entry($type)->definition();
        $obj = new $class($context, $find, []);

        return $obj->blueprint();
    }

    #[Permission('panel')]
    public function createNode(string $type, Context $context, Finder $find): array
    {
        $class = $this->registry->tag(Node::class)->entry($type)->definition();
        $obj = new $class($context, $find, $this->request->json());
        $obj->create();

        return ['success' => true];
    }

    #[Permission('panel')]
    public function node(Finder $find, string $uid): array
    {
        $node = $find->node->byUid($uid, published: null);

        if ($node) {
            return $node->response();
        }

        throw new HttpNotFound();
    }

    protected function getPanelIndex(): string
    {
        return $this->publicPath . $this->config->getPanelPath() . '/index.html';
    }
}
