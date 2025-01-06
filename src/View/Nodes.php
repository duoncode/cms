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
		$asDict = $this->request->param('asdict', 'false') === 'true' ? true : false;
		$query = $this->request->param('query', null);
		$uid = $this->request->param('uid', null);
		$order = $this->request->param('order', 'changed');
		$fields = explode(',', $this->request->param('fields', ''));

		if ($query) {
			$nodes = $find->nodes($query)->order($order);
		} elseif ($uid) {
			$uids = array_map(fn(string $uid) => trim($uid), explode(',', $uid));

			error_log(print_r($uids, true));

			if(count($uids) > 1) {
				$quoted = implode(', ', array_map(fn($uid) => "'{$uid}'", $uids));
				$query = "uid @ [{$quoted}]";
				error_log($query);
			} else {
				$query = "uid = '{$uid}'";
			}

			$nodes = $find->nodes($query)->order($order);
		} else {
			throw new HttpBadRequest($this->request);
		}

		$result = [];

		foreach ($nodes as $node) {
			$uid = $node->meta('uid');
			$n = [
				'uid' => $uid,
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
				$n[$field] = $node->getValue(trim($field))->unwrap();
			}

			if ($asDict) {
				$result[$uid] = $n;
			} else {
				$result[] = $n;
			}
		}

		return (new Response($factory->response()))->json($result);
	}
}
