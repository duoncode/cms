<?php

declare(strict_types=1);

namespace Duon\Cms\Config;

final readonly class Panel
{
	/**
	 * @param non-empty-string $path
	 * @param list<non-empty-string> $theme
	 * @param ?non-empty-string $logo
	 */
	public function __construct(
		public string $path,
		public array $theme,
		public ?string $logo,
	) {}

	public static function from(\Duon\Cms\Config $config): self
	{
		return new self(
			$config->get('path.panel'),
			self::strings($config->get('panel.theme')),
			$config->get('panel.logo'),
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
