<?php

declare(strict_types=1);

namespace Conia\Core\View;

use Conia\Core\Context;
use Conia\Core\Exception\HttpNotFound;
use Conia\Core\Factory;
use Conia\Core\Finder\Finder;
use Conia\Core\Middleware\Permission;
use Conia\Http\Response;
use Conia\Registry\Registry;

class Page
{
    public function __construct(
        protected readonly Factory $factory,
        protected readonly Registry $registry,
    ) {
    }

    public function catchall(Context $context, Finder $find): Response
    {
        $path = $context->request->uri()->getPath();
        $page = $find->node->byPath($path);

        if (!$page) {
            $this->redirectIfExists($context, $path);

            throw new HttpNotFound();
        }

        return $page->response();
    }

    #[Permission('panel')]
    public function preview(Finder $find, string $slug): Response
    {
        $page = $find->node->byPath('/' . $slug);

        return $page->response();
    }

    protected function redirectIfExists(Context $context, string $path): void
    {
        $db = $context->db;
        $path = $db->paths->byPath(['path' => $path])->one();

        if ($path && !is_null($path['inactive'])) {
            $paths = $db->paths->activeByNode(['node' => $path['node']])->all();

            $pathsByLocale = array_combine(
                array_map(fn ($p) => $p['locale'], $paths),
                array_map(fn ($p) => $p['path'], $paths),
            );

            $locale = $context->request->get('locale');

            while ($locale) {
                $path = $pathsByLocale[$locale->id] ?? null;

                if ($path) {
                    header('Location: ' . $path, true, 301);
                    exit;
                }

                $locale = $locale->fallback();
            }
        }
    }
}
