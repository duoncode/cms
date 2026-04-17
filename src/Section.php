<?php

declare(strict_types=1);

namespace Duon\Cms;

use Closure;
use Duon\Cms\Exception\RuntimeException;
use Override;

final class Section extends NavigationItem implements NavGroup
{
	/** @var list<NavigationItem> */
	private array $children = [];

	private readonly ?Closure $onCollection;

	public function __construct(
		string $label,
		?Closure $onCollection = null,
	) {
		$label = trim($label);

		if ($label === '') {
			throw new RuntimeException('Section labels must not be empty');
		}

		parent::__construct(new NavMeta($label));
		$this->onCollection = $onCollection;
	}

	#[Override]
	public function type(): string
	{
		return 'section';
	}

	#[Override]
	public function slug(): ?string
	{
		return null;
	}

	/** @return list<NavigationItem> */
	#[Override]
	public function children(): array
	{
		$visible = [];

		foreach ($this->children as $item) {
			if ($item->isHidden()) {
				continue;
			}

			if ($item instanceof self && $item->children() === []) {
				continue;
			}

			$visible[] = $item;
		}

		return $this->sort($visible);
	}

	public function section(string $label): self
	{
		$section = new self($label, $this->onCollection);
		$this->children[] = $section;

		return $section;
	}

	/** @param class-string<Collection> $class */
	public function collection(string $class): Collection
	{
		if (!is_a($class, Collection::class, true)) {
			throw new RuntimeException('Collections must extend ' . Collection::class);
		}

		$collection = new $class(parent: $this);
		$this->children[] = $collection;

		if ($this->onCollection !== null) {
			($this->onCollection)($collection);
		}

		return $collection;
	}

	/**
	 * @param list<NavigationItem> $items
	 * @return list<NavigationItem>
	 */
	private function sort(array $items): array
	{
		$indexed = [];

		foreach ($items as $index => $item) {
			$indexed[] = [
				'index' => $index,
				'item' => $item,
			];
		}

		usort($indexed, static function (array $left, array $right): int {
			$cmp = $left['item']->sortOrder() <=> $right['item']->sortOrder();

			if ($cmp !== 0) {
				return $cmp;
			}

			return $left['index'] <=> $right['index'];
		});

		return array_map(
			static fn(array $item): NavigationItem => $item['item'],
			$indexed,
		);
	}
}
