<?php

declare(strict_types=1);

namespace Duon\Cms\Tests\Setup;

class C
{
	public static function root(): string
	{
		return dirname(dirname(__DIR__));
	}
}
