<?php

declare(strict_types=1);

namespace Conia;

use Conia\Request;
use Chuck\Database\DatabaseInterface;


class Users extends Model
{
    protected DatabaseInterface $db;

    public static function byLogin(string $login): ?array
    {
        return self::db()->users->get([
            'login' => $login,
        ])->one();
    }

    public static function bySession(string $hash): ?array
    {
        return self::db()->users->get([
            'sessionhash' => $hash,
        ])->one();
    }

    public static function byId(string $uid): ?array
    {
        return self::db()->users->get([
            'uid' => $uid,
        ])->one();
    }

    public static function remember(string $hash, string $userId, string $expires): bool
    {
        return self::db()->users->remember([
            'hash' => $hash,
            'user' => $userId,
            'expires' => $expires,
        ])->run();
    }

    public static function forget(string $hash): bool
    {
        return self::db()->users->forget([
            'hash' => $hash,
        ])->run();
    }
}
