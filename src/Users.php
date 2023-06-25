<?php

declare(strict_types=1);

namespace Conia\Core;

use Conia\Quma\Database;

class Users
{
    public function __construct(protected Database $db)
    {
    }

    public function byLogin(string $login): ?array
    {
        return $this->db->users->get([
            'login' => $login,
        ])->one();
    }

    public function bySession(string $hash): ?array
    {
        return $this->db->users->get([
            'sessionhash' => $hash,
        ])->one();
    }

    public function byUid(string $uid): ?array
    {
        return $this->db->users->get([
            'uid' => $uid,
        ])->one();
    }

    public function byId(int $id): ?array
    {
        return $this->db->users->get([
            'usr' => $id,
        ])->one();
    }

    public function remember(string $hash, int $userId, string $expires): bool
    {
        return $this->db->users->remember([
            'hash' => $hash,
            'user' => $userId,
            'expires' => $expires,
        ])->run();
    }

    public function forget(string $hash): bool
    {
        return $this->db->users->forget([
            'hash' => $hash,
        ])->run();
    }
}
