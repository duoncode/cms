<?php

declare(strict_types=1);

namespace Conia;

use Chuck\RequestInterface;
use Chuck\Database\DatabaseInterface;

class Users
{
    public function __construct(
        protected DatabaseInterface $db,
        protected RequestInterface $request
    ) {
    }

    public function byLogin(string $login): ?array
    {
        return ($this->db->users->byLogin)([
            'login' => $login,
        ])->one();
    }
}
