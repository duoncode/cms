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

	public static function from(\Duon\Cms\Config $config): self
	{
		return new self(
			$config->get('session.enabled'),
			$config->get('session.options'),
			$config->get('session.handler'),
		);
	}
}
