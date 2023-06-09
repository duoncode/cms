<?php

declare(strict_types=1);

namespace Conia\Core\View;

use Conia\Core\Middleware\Permission;

class User
{
    #[Permission('edit-users')]
    public function list()
    {
    }

    #[Permission('edit-users')]
    public function me(string $uid)
    {
    }

    #[Permission('edit-users')]
    public function save(string $uid)
    {
    }

    #[Permission('edit-users')]
    public function create()
    {
    }
}
