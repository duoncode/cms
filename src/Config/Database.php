<?php

declare(strict_types=1);

namespace Duon\Cms\Config;

final readonly class Database
{
	/**
	 * @param ?non-empty-string $dsn
	 * @param list<non-empty-string> $sql
	 * @param list<non-empty-string> $migrations
	 * @param array<string, mixed> $options
	 */
	public function __construct(
		public ?string $dsn,
		public array $sql,
		public array $migrations,
		public bool $print,
		public array $options,
	) {}

	public static function from(\Duon\Cms\Config $config): self
	{
		return new self(
			$config->get('db.dsn'),
			self::strings($config->get('db.sql')),
			self::strings($config->get('db.migrations')),
			$config->get('db.print'),
			$config->get('db.options'),
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
