<?php

declare(strict_types=1);

namespace Conia\Core;

use Conia\Chuck\Session as BaseSession;

class Session extends BaseSession
{
    protected string $authCookie;

    public function __construct(
        protected string $name,
        ?string $authCookie = null,
        protected string $flashMessagesKey = 'flash_messages',
        protected string $rememberedUriKey = 'remembered_uri',
    ) {
        $this->authCookie = $authCookie ?: $name . '_auth';
    }

    public function setUser(string $userId): void
    {
        $_SESSION['user_id'] = $userId;
    }

    public function authenticatedUserId(): ?string
    {
        return $_SESSION['user_id'] ?? null;
    }

    public function remember(Token $token, int $expires): void
    {
        setcookie(
            $this->authCookie,
            $token->get(),
            $expires,
            '/'
        );
    }

    public function forgetRemembered(): void
    {
        setcookie(
            $this->authCookie,
            '',
            time() - 60 * 60 * 24
        );
    }

    public function getAuthToken(): ?string
    {
        return $_COOKIE[$this->authCookie] ?? null;
    }
}
