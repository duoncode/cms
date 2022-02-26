<?php

declare(strict_types=1);

class InitApp extends Chuck\Cli\Command
{
    public static string $group = 'General';
    public static string $title = 'Initialize the Conia app';

    public function run(Chuck\ConfigInterface $config, string ...$args): void
    {
    }
}

return new InitApp();
