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
}
