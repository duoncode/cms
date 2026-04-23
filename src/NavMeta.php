<?php

declare(strict_types=1);

namespace Duon\Cms;

final class NavMeta
{
	/**
	 * @param array{id: string, color: ?string, class: ?string, style: ?string}|null $icon
	 */
	public function __construct(
		public string $label,
		public ?array $icon = null,
		public ?string $badge = null,
		public ?string $permission = null,
		public bool $hidden = false,
		public int $order = 0,
	) {}

	public function array(): array
	{
		return [
			'label' => $this->label,
			'icon' => $this->icon['id'] ?? null,
			'iconMeta' => $this->icon,
			'badge' => $this->badge,
			'permission' => $this->permission,
			'hidden' => $this->hidden,
			'order' => $this->order,
		];
	}
}
