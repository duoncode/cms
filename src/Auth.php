<?php

declare(strict_types=1);

namespace Conia;

use \RuntimeException;
use Conia\Config;
use Conia\Request;
use Conia\Permissions;
use Conia\Util\Time;


class RememberDetails
{
    public function __construct(Token $token, int $expires)
    {
        $this->token = $token;
        $this->expires = $expires;
    }
}


class Auth
{
    protected Config $config;
    protected Session $session;

    public function __construct(
        protected Request $request,
    ) {
        $this->config = $request->config();
        $this->session = $request->session();
    }

    protected function remember(string $userId): RememberDetails
    {
        $token = new Token($this->config->get('secret'));
        $expires = time() + $this->config->get('session.expires');

        $remembered = Users::remember(
            $token->hash(),
            $userId,
            Time::toIsoDateTime($expires),
        );

        if ($remembered) {
            return new RememberDetails($token, $expires);
        } else {
            throw new RuntimeException('Could not remember user');
        }
    }

    protected function login(string $userId, bool $remember): void
    {
        $session = $this->session;

        // Regenerate the session id before setting the user id
        // to mitigate session fixation attack.
        $session->regenerate();
        $session->setUser($userId);

        if ($remember) {
            $details = $this->remember($userId);

            if ($details) {
                $session->remember(
                    $details->token,
                    $details->expires
                );
            };
        } else {
            // Remove the user entry from loginsessions table as the user
            // has not checked "remember me". In that case the session is
            // only valid as long as the browser is not closed.
            $token = $session->getAuthToken();

            if ($token !== null) {
                Users::forget($userId);
            }
        }
    }

    public function logout(): void
    {
        $session = $this->session;
        $session->forget();
        $hash = $this->getTokenHash();

        if ($hash) {
            Users::forget($hash);
            $session->forgetRemembered();
        }
    }

    public function authenticate(
        string $login,
        string $password,
        bool $remember,
        bool $initSession,
    ): array|false {
        $user = Users::byLogin($login);

        if (!$user) {
            return false;
        }

        if (password_verify($password, $user['pwhash'])) {
            if ($initSession) {
                $this->login($user['uid'], $remember);
            }

            unset($user['pwhash']);

            return $user;
        } else {
            return false;
        }
    }


    protected function getTokenHash(): ?string
    {
        $token = $this->session->getAuthToken();

        if ($token) {
            $hash = (new Token($this->config->get('secret'), $token))->hash();
            return $hash;
        }

        return null;
    }

    public function user(): ?array
    {
        static $user = false;

        if ($user !== false) {
            return $user;
        }

        // verify if user is logged in via cookie session
        $userId = $this->session->authenticatedUserId();

        if ($userId) {
            $user = Users::byId($userId);
            return $user;
        }

        $hash = $this->getTokenHash();

        if ($hash) {
            $user = Users::bySession($hash);

            if ($user && !(strtotime($user['expires']) < time())) {
                $this->login($user['uid'], false);
                return $user;
            }
        }

        $user = null; // set static var
        return $user;
    }

    public function permissions(): array
    {
        $permissions = new Permissions($this->config);
        $user = $this->user();

        if ($user === null) {
            return [];
        }

        return $permissions->get($user['role']);
    }
}
