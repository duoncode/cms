<?php

declare(strict_types=1);

namespace Duon\Cms\Config;

final readonly class Icons
{
	/** @param list<non-empty-string> $localPaths */
	public function __construct(
		public array $localPaths,
		public Iconify $iconify,
	) {}

	public static function from(\Duon\Cms\Config $config): self
	{
		return new self(
			self::strings($config->get('icons.local.paths')),
			Iconify::from($config),
		);
	}

	/** @return list<non-empty-string> */
	private static function strings(mixed $value): array
	{
		if ($value === null) {
			return [];
		}

		if (is_string($value)) {
			$value = trim($value);

			return $value === '' ? [] : [$value];
		}

		return array_values($value);
	}
}
