<?php

declare(strict_types=1);

namespace Conia\Core;

use Conia\Session\Session as BaseSession;
use SessionHandlerInterface;

class Session extends BaseSession
{
    protected string $authCookie;

    public function __construct(
        protected readonly string $name = '',
        protected readonly array $options = [],
        protected readonly ?SessionHandlerInterface $handler = null,
    ) {
        parent::__construct($name, $options, $handler);

        $this->authCookie = $name ? $name . '_auth' : 'conia_auth';
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
