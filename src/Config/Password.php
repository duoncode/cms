<?php

declare(strict_types=1);

namespace Duon\Cms\Config;

final class Password
{
	public function __construct(
		private readonly \Duon\Cms\Config $config,
	) {}

	/** @var positive-float */
	public float $entropy {
		get => $this->config->get('password.entropy');
	}

	public int|string|null $algorithm {
		get => $this->config->get('password.algorithm');
	}
}
