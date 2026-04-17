<?php

declare(strict_types=1);

namespace Duon\Cms;

final class NavMeta
{
	public function __construct(
		public string $label,
		public ?string $icon = null,
		public ?string $badge = null,
		public ?string $permission = null,
		public bool $hidden = false,
		public int $order = 0,
	) {}

	public function array(): array
	{
		return [
			'label' => $this->label,
			'icon' => $this->icon,
			'badge' => $this->badge,
			'permission' => $this->permission,
			'hidden' => $this->hidden,
			'order' => $this->order,
		];
	}
}
