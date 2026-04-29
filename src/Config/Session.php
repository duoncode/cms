<?php

declare(strict_types=1);

namespace Duon\Cms\Config;

use SessionHandlerInterface;

/** @psalm-import-type SessionOptions from Types */
final readonly class Session
{
	/** @param SessionOptions $options */
	public function __construct(
		public bool $enabled,
		public array $options,
		public ?SessionHandlerInterface $handler,
	) {}
}
