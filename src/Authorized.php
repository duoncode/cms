<?php

declare(strict_types=1);

#[Attribute]
class Authorized
{
    protected array $permissions;

    public function __construct(string ...$permissions)
    {
        $this->permissions = $permissions;
    }

    public function get(): array
    {
        return $this->permissions;
    }
}
