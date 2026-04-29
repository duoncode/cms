<?php

declare(strict_types=1);

namespace Duon\Cms\Config;

final readonly class Password
{
	/** @param positive-float $entropy */
	public function __construct(
		public float $entropy,
		public int|string|null $algorithm,
	) {}

	public static function from(\Duon\Cms\Config $config): self
	{
		return new self(
			$config->get('password.entropy'),
			$config->get('password.algorithm'),
		);
	}
}
