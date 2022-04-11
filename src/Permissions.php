<?php

declare(strict_types=1);

namespace Conia;


class Permissions
{
    protected array $permissions = [
        'superuser' => [
            'superuser',
            'admin',
            'editor',
            'backend',
            'edit-settings',
            'edit-users',
            'edit-pages',
            'authenticated',
        ],
        'admin' => [
            'admin',
            'editor',
            'backend',
            'edit-users',
            'edit-pages',
            'authenticated',
        ],
        'editor' => [
            'editor',
            'backend',
            'edit-pages',
            'authenticated',
        ],
    ];

    public function addPermission(string $role, string $permission)
    {
        $this->permissions[$role][] = $permission;
    }

    public function hasPermission(string $role, string $permission)
    {
        return in_array($permission, $this->permissions[$role] ?? []);
    }
}
