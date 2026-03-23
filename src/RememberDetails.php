<?php

declare(strict_types=1);

namespace Duon\Cms;

class RememberDetails
{
	public function __construct(
		#[\SensitiveParameter]
		public readonly Token $token,
		public readonly int $expires,
	) {}
}
