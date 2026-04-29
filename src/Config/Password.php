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
}
