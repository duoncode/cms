<?php

declare(strict_types=1);

namespace Conia;

use Chuck\Session as BaseSession;


class Session extends BaseSession
{
    public function setUser(string|int $userId): void
    {
        $_SESSION['user_id'] = $userId;
    }

    public function authenticatedUserId(): mixed
    {
        return $_SESSION['user_id'] ?? null;
    }

    public function remember(Token $token, int $expire): void
    {
        setcookie(
            $this->config->get('appname') . '_auth',
            $token->get(),
            $expire,
            '/'
        );
    }

    public function forgetRemembered(): void
    {
        setcookie(
            $this->name . '_auth',
            '',
            time() - 60 * 60 * 24
        );
    }

    public function getAuthToken(): ?string
    {
        return $_COOKIE[$this->name . '_auth'] ?? null;
    }
}
