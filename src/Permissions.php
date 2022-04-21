<?php

declare(strict_types=1);

namespace Conia;

use Conia\Config;


class Permissions
{
    protected array $permissions = [
        'superuser' => [
            'superuser',
            'admin',
            'editor',
            'panel',
            'edit-settings',
            'edit-users',
            'edit-pages',
            'authenticated',
        ],
        'admin' => [
            'admin',
            'editor',
            'panel',
            'edit-users',
            'edit-pages',
            'authenticated',
        ],
        'editor' => [
            'editor',
            'panel',
            'edit-pages',
            'authenticated',
        ],
    ];

    public function __construct(protected Config $config)
    {
    }

    public function add(string $role, string $permission)
    {
        $this->permissions[$role][] = $permission;
    }

    public function has(string $role, string $permission): bool
    {
        return in_array($permission, $this->permissions[$role] ?? []);
    }

    public function get(string $role): array
    {
        return $this->permissions[$role] ?? [];
    }
}
