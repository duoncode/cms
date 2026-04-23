<?php

declare(strict_types=1);

namespace Duon\Cms\Node\Schema;

class IconHandler extends Handler
{
	public function resolve(object $meta, string $nodeClass): array
	{
		$id = trim((string) $meta->id);

		if ($id === '') {
			return ['icon' => null];
		}

		return ['icon' => [
			'id' => $id,
			'color' => $this->normalize($meta->color),
			'class' => $this->normalize($meta->class),
			'style' => $this->normalize($meta->style),
		]];
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
