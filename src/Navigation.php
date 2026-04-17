<?php

declare(strict_types=1);

namespace Duon\Cms;

use Closure;
use Duon\Cms\Exception\RuntimeException;

final class Navigation implements NavGroup
{
	private readonly Section $root;

	/** @var array<string, CollectionRef> */
	private array $collections = [];

	public function __construct()
	{
		$this->root = new Section('_root', Closure::fromCallable([$this, 'register']));
	}

	public function section(string $label): Section
	{
		return $this->root->section($label);
	}

	public function collection(string $class): CollectionRef
	{
		return $this->root->collection($class);
	}

	/** @return list<NavigationItem> */
	public function children(): array
	{
		return $this->root->children();
	}

	/** @return list<NavigationItem> */
	public function items(): array
	{
		return $this->children();
	}

	/** @return array<string, CollectionRef> */
	public function refs(): array
	{
		return $this->collections;
	}

	public function ref(string $handle): CollectionRef
	{
		if (!isset($this->collections[$handle])) {
			throw new RuntimeException('Unknown collection handle: ' . $handle);
		}

		return $this->collections[$handle];
	}

	public function payload(): array
	{
		return $this->serialize($this->items());
	}

	private function register(CollectionRef $collection): void
	{
		$handle = $collection->handle();

		if (isset($this->collections[$handle])) {
			throw new RuntimeException('Duplicate collection handle: ' . $handle);
		}

		$this->collections[$handle] = $collection;
	}

	/**
	 * @param list<NavigationItem> $items
	 * @return list<array<string, mixed>>
	 */
	private function serialize(array $items): array
	{
		$result = [];

		foreach ($items as $item) {
			if ($item instanceof Section) {
				$result[] = [
					'type' => $item->type(),
					'name' => $item->name(),
					'meta' => $item->meta()->array(),
					'children' => $this->serialize($item->children()),
				];

				continue;
			}

			$result[] = [
				'type' => $item->type(),
				'slug' => $item->slug(),
				'name' => $item->name(),
				'meta' => $item->meta()->array(),
				'children' => [],
			];
		}

		return $result;
	}
}
