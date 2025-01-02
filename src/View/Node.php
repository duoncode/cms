<?php

declare(strict_types=1);

namespace FiveOrbs\Cms\View;

use FiveOrbs\Cms\Collection;
use FiveOrbs\Cms\Config;
use FiveOrbs\Cms\Middleware\Permission;
use FiveOrbs\Core\Factory;
use FiveOrbs\Core\Request;
use FiveOrbs\Core\Response;
use FiveOrbs\Registry\Registry;

class Node
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
			'panelPath' => $this->config->get('panel.prefix'),
			'debug' => $this->config->debug(),
			'env' => $this->config->env(),
			'csrfToken' => 'TOKEN', // TODO: real token
			'collections' => $collections,
		];
	}

	public function index(Factory $factory): Response
	{
		return Response::create($factory)->file($this->panelIndex);
	}

	public function catchall(Factory $factory, string $slug): Response
	{
		$file = $this->publicPath . '/panel/' . $slug;

		if (file_exists($file)) {
			return Response::create($factory)->file($file);
		}

		return Response::create($factory)->file($this->panelIndex);
	}

	#[Permission('panel')]
	public function collection(string $collection): array
	{
		$obj = $this->registry->tag(Collection::class)->get($collection);

		return [
			'title' => $obj->title(),
			'slug' => $collection,
			'nodes' => $obj->listing(),
		];
	}

	#[Permission('panel')]
	public function nodes(): array
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
			'panelPath' => $this->config->get('panel.prefix'),
			'debug' => $this->config->debug(),
			'env' => $this->config->env(),
			'csrfToken' => 'TOKEN', // TODO: real token
			'collections' => $collections,
		];
	}
}
