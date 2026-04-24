<?php

declare(strict_types=1);

namespace Duon\Cms\Field\Schema;

use Duon\Cms\Field\Field;

class IconHandler extends Handler
{
	public function apply(object $meta, Field $field): void
	{
		// Icons only contribute metadata for panel payloads.
	}

	public function properties(object $meta, Field $field): array
	{
		$id = trim((string) $meta->id);

		if ($id === '') {
			return ['icon' => null];
		}

		return [
			'icon' => [
				'id' => $id,
				'color' => $this->normalize($meta->color),
				'class' => $this->normalize($meta->class),
				'style' => $this->normalize($meta->style),
			],
		];
	}

	private function normalize(?string $value): ?string
	{
		if ($value === null) {
			return null;
		}

		$value = trim($value);

		return $value === '' ? null : $value;
	}
}
