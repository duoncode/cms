<?php

declare(strict_types=1);

namespace Duon\Cms;

interface NavigationItem
{
	public NavMeta $meta { get; }

	public function type(): string;

	public function slug(): ?string;

	/** @return list<NavigationItem> */
	public function children(): array;
}
