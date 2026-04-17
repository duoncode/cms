<?php

declare(strict_types=1);

namespace Duon\Cms;

use Duon\Cms\Exception\RuntimeException;

abstract class NavigationItem
{
	public function __construct(
		protected readonly NavMeta $meta,
	) {}

	abstract public function type(): string;

	public function name(): string
	{
		return $this->meta->label;
	}

	public function meta(): NavMeta
	{
		return $this->meta;
	}

	public function label(string $label): static
	{
		$label = trim($label);

		if ($label === '') {
			throw new RuntimeException('Navigation labels must not be empty');
		}

		$this->meta->label = $label;

		return $this;
	}

	public function icon(?string $icon): static
	{
		$this->meta->icon = $this->stringOrNull($icon);

		return $this;
	}

	public function badge(?string $badge): static
	{
		$this->meta->badge = $this->stringOrNull($badge);

		return $this;
	}

	public function permission(?string $permission): static
	{
		$this->meta->permission = $this->stringOrNull($permission);

		return $this;
	}

	public function hidden(bool $hidden = true): static
	{
		$this->meta->hidden = $hidden;

		return $this;
	}

	public function order(int $order): static
	{
		$this->meta->order = $order;

		return $this;
	}

	private function stringOrNull(?string $value): ?string
	{
		if ($value === null) {
			return null;
		}

		$value = trim($value);

		return $value === '' ? null : $value;
	}
}
