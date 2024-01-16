<?php

declare(strict_types=1);

namespace Conia\Cms\Commands;

use Conia\Cli\Command;

class InitApp extends Command
{
    protected string $group = 'General';
    protected string $name = 'init-app';
    protected string $description = 'Initialize the Conia app';

    public function run(): int
    {
        return 0;
    }
}
