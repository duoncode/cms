<?php

declare(strict_types=1);

namespace FiveOrbs\Cms\Commands;

use FiveOrbs\Cli\Command;

class InitApp extends Command
{
	protected string $group = 'General';
	protected string $name = 'init-app';
	protected string $description = 'Initialize the FiveOrbs app';

	public function run(): int
	{
		return 0;
	}
}
