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
		$map = $this->tristateValue($this->request->param('map', 'false'));
		$query = $this->request->param('query', null);
		$published = $this->tristateValue($this->request->param('published', null));
		$hidden = $this->tristateValue($this->request->param('hidden', 'false'));
		$deleted = $this->tristateValue($this->request->param('deleted', 'false'));
		$content = $this->tristateValue($this->request->param('content', 'false'));
		$uid = $this->request->param('uid', null);
		$order = $this->request->param('order', 'changed');
		$fields = explode(',', $this->request->param('fields', ''));

		if ($query) {
			$nodes = $find->nodes($query);
		} elseif ($uid) {
			$uids = array_map(fn(string $uid) => trim($uid), explode(',', $uid));

			if(count($uids) > 1) {
				$quoted = implode(', ', array_map(fn($uid) => "'{$uid}'", $uids));
				$query = "uid @ [{$quoted}]";
			} else {
				$query = "uid = '{$uid}'";
			}

			$nodes = $find->nodes($query);
		} else {
			throw new HttpBadRequest($this->request);
		}

		$result = [];

		foreach ($nodes->published($published)->hidden($hidden)->order($order)->deleted($deleted) as $node) {
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

			foreach ($fields as $field) {
				if ($field) {
					$n[$field] = $node->getValue(trim($field))->unwrap();
				}
			}

			if ($content) {
				$n['content'] = $node->content();
			}

			if ($map) {
				$result[$uid] = $n;
			} else {
				$result[] = $n;
			}
		}

		return (new Response($factory->response()))->json($result);
	}

	private function tristateValue(string|null $value): bool|null
	{
		if ($value === 'true') {
			return true;
		}

		if ($value === 'false') {
			return false;
		}

		return null;
	}
}
