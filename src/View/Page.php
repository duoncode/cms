<?php

declare(strict_types=1);

namespace Conia\Cms\View;

use Conia\Cms\Context;
use Conia\Cms\Finder\Finder;
use Conia\Cms\Middleware\Permission;
use Conia\Core\Exception\HttpBadRequest;
use Conia\Core\Exception\HttpNotFound;
use Conia\Core\Factory;
use Conia\Core\Response;
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
        $prefix = $context->config->get('path.prefix', '');

        if ($prefix) {
            $path = preg_replace('/^' . preg_quote($prefix, '/') . '/', '', $path);
        }

        $page = $find->node->byPath($path === '' ? '/' : $path);

        if (!$page) {
            $this->redirectIfExists($context, $path);

            throw new HttpNotFound($context->request);
        }

        if ($context->request->get('isXhr', false)) {
            if ($context->request->method() === 'GET') {
                return $page->jsonResponse();
            }

            throw new HttpBadRequest();
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

        if ($path && !($path['inactive'] === null)) {
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
