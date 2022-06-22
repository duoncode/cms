<?php

declare(strict_types=1);

namespace Conia;

use Conia\Request;
use Chuck\Database\DatabaseInterface;


class Users
{
    protected DatabaseInterface $db;

    public function __construct(protected Request $request)
    {
        $this->db = $request->db('conia');
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

    public function byId(string $uid): ?array
    {
        return $this->db->users->get([
            'uid' => $uid,
        ])->one();
    }

    public function remember(string $hash, string $userId, string $expires): bool
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
