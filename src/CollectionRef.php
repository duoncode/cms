<?php

declare(strict_types=1);

namespace Duon\Cms;

use Duon\Cms\Exception\RuntimeException;
use Override;

final class CollectionRef extends NavigationItem
{
	/** @param class-string<Collection> $class */
	public function __construct(
		private readonly NavGroup $parent,
		private readonly string $class,
	) {
		if (!is_a($class, Collection::class, true)) {
			throw new RuntimeException('Collections must extend ' . Collection::class);
		}

		parent::__construct($class::nav());
	}

	#[Override]
	public function type(): string
	{
		return 'collection';
	}

	#[Override]
	/** @return list<NavigationItem> */
	public function children(): array
	{
		return [];
	}

	#[Override]
	public function slug(): ?string
	{
		return $this->handle();
	}

	/** @return class-string<Collection> */
	public function class(): string
	{
		return $this->class;
	}

	public function handle(): string
	{
		return $this->class::handle();
	}

	public function section(string $label): Section
	{
		return $this->parent->section($label);
	}

	public function collection(string $class): self
	{
		return $this->parent->collection($class);
	}
}
