<?php

declare(strict_types=1);

namespace Duon\Cms\View;

use Duon\Cms\Config;
use Duon\Cms\Finder\Finder;
use Duon\Cms\Locales;
use Duon\Cms\Middleware\Permission;
use Duon\Core\Exception\HttpBadRequest;
use Duon\Core\Factory;
use Duon\Core\Request;
use Duon\Core\Response;
use Duon\Registry\Registry;

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
		if ($this->request->method() === 'GET') {
			$query = new GetQuery($this->request);
		} else {
			$query = new PostQuery($this->request);
		}

		if ($query->query) {
			$nodes = $find->nodes($query->query);
		} elseif (count($query->uids) > 0) {
			if (count($query->uids) > 1) {
				$quoted = implode(',', array_map(fn($uid) => "'{$uid}'", $query->uids));
				$queryString = "uid @ [{$quoted}]";
			} else {
				$queryString = "uid = '{$query->uids[0]}'";
			}

			$nodes = $find->nodes($queryString);
		} else {
			throw new HttpBadRequest($this->request);
		}

		$result = [];

		foreach (
			$nodes->published($query->published)
				->hidden($query->hidden)
				->order($query->order)
				->deleted($query->deleted) as $node
		) {
			$uid = $node->meta('uid');
			$n = [
				'uid' => $uid,
				'title' => $node->title(),
				'handle' => $node->meta('handle'),
				'published' => $node->meta('published'),
				'hidden' => $node->meta('hidden'),
				'locked' => $node->meta('locked'),
				'created' => $node->meta('created'),
				'changed' => $node->meta('changed'),
				'deleted' => $node->meta('deleted'),
				'paths' => $node->meta('paths'),
			];

			foreach ($query->fields as $field) {
				if ($field) {
					$n[$field] = $node->getValue(trim($field))->unwrap();
				}
			}

			if ($query->content) {
				$n['content'] = $node->content();
			}

			if ($query->map) {
				$result[$uid] = $n;
			} else {
				$result[] = $n;
			}
		}

		return (new Response($factory->response()))->json($result);
	}
}
