<?php

declare(strict_types=1);

namespace Duon\Cms\Node\Meta;

use Attribute;

#[Attribute]
class Permission
{
	/**
	 * @param string|array<string,string> $value
	 */
	public function __construct(public readonly array|string $value) {}
}
