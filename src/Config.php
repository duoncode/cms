<?php

declare(strict_types=1);

namespace Conia\Cms;

use Conia\Core\AddsConfigInterface;
use Conia\Core\ConfigInterface;
use Conia\Core\Exception\ValueError;

class Config implements ConfigInterface
{
    use AddsConfigInterface;

    public function __construct(
        public readonly string $app = 'conia',
        public readonly bool $debug = false,
        public readonly string $env = '',
        array $settings = [],
    ) {
        $this->settings = $settings;
        $this->validateApp($app);
    }

    public function app(): string
    {
        return $this->app;
    }

    public function debug(): bool
    {
        return $this->debug;
    }

    public function env(): string
    {
        return $this->env;
    }

    protected function validateApp(string $app): void
    {
        if (!preg_match('/^[a-zA-Z0-9_$-]{1,64}$/', $app)) {
            throw new ValueError(
                'The app name must be a nonempty string which consist only of lower case ' .
                    'letters and numbers. Its length must not be longer than 32 characters.'
            );
        }
    }
}
