<?php

declare(strict_types=1);

namespace Duon\Cms\Commands;

use Duon\Cli\Command;

class InitApp extends Command
{
	protected string $group = 'General';
	protected string $name = 'init-app';
	protected string $description = 'Initialize the Duon app';

	public function run(): int
	{
		return 0;
	}
}
