<?php

declare(strict_types=1);

namespace FiveOrbs\Cms\View;

use FiveOrbs\Cms\Config;
use FiveOrbs\Cms\Finder\Finder;
use FiveOrbs\Cms\Locales;
use FiveOrbs\Cms\Middleware\Permission;
use FiveOrbs\Core\Exception\HttpBadRequest;
use FiveOrbs\Core\Factory;
use FiveOrbs\Core\Request;
use FiveOrbs\Core\Response;
use FiveOrbs\Registry\Registry;

class Nodes
{
	public function __construct(
		protected readonly Request $request,
		protected readonly Config $config,
		protected readonly Registry $registry,
		protected readonly Locales $locales,
	) {}

	#[Permission('panel')]
	public function get(Finder $find, Factory $factory): Response
	{
		$query = $this->request->param('query', null);
		$fields = explode(',', $this->request->param('fields', ''));

		if ($query === null) {
			throw new HttpBadRequest($this->request);
		}

		$nodes = $find->nodes($query);
		$result = [];

		foreach ($nodes as $node) {
			$n = [
				'uid' => $node->meta('uid'),
				'title' => $node->title(),
				'published' => $node->meta('published'),
				'hidden' => $node->meta('hidden'),
				'locked' => $node->meta('locked'),
				'created' => $node->meta('created'),
				'changed' => $node->meta('changed'),
				'deleted' => $node->meta('deleted'),
				'paths' => $node->meta('paths'),
			];

			foreach ($fields as $field) {
				$n[$field] = $node->getValue($field)->unwrap();
			}

			$result[] = $n;
		}

		return (new Response($factory->response()))->json($result);
	}
}
