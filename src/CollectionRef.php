<?php

declare(strict_types=1);

namespace Duon\Cms;

use Duon\Cms\Exception\RuntimeException;

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

	public function type(): string
	{
		return 'collection';
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

	public function slug(): ?string
	{
		return $this->handle();
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
