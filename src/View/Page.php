<?php

declare(strict_types=1);

namespace Duon\Cms\View;

use Duon\Cms\Context;
use Duon\Cms\Exception\RuntimeException;
use Duon\Cms\Finder\Finder;
use Duon\Cms\Middleware\Permission;
use Duon\Cms\Util\Path;
use Duon\Core\Exception\HttpBadRequest;
use Duon\Core\Exception\HttpNotFound;
use Duon\Core\Factory;
use Duon\Core\Response;
use Duon\Registry\Registry;

class Page
{
	public function __construct(
		protected readonly Factory $factory,
		protected readonly Registry $registry,
	) {}

	public function catchall(Context $context, Finder $find): Response
	{
		$request = $context->request;
		$config = $context->config;
		$path = $request->uri()->getPath();
		$prefix = $config->get('path.prefix', '');

		if ($prefix) {
			$path = preg_replace('/^' . preg_quote($prefix, '/') . '/', '', $path);
		}

		$page = $find->node->byPath($path === '' ? '/' : $path);

		if (!$page) {
			try {
				$path = Path::inside($config->get('path.public'), $path);

				return Response::create($this->factory)->file($path);
			} catch (RuntimeException $e) {
				$this->redirectIfExists($context, $path);

				throw new HttpNotFound($request, previous: $e);
			}
		}

		if ($request->get('isXhr', false)) {
			if ($request->method() === 'GET') {
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
				array_map(fn($p) => $p['locale'], $paths),
				array_map(fn($p) => $p['path'], $paths),
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
