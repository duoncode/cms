<?php

declare(strict_types=1);

namespace Conia\Core;

use Conia\Chuck\Request;
use Conia\Core\Config;
use Conia\Core\Permissions;
use Conia\Core\Util\Time;
use RuntimeException;

class Auth
{
    public function __construct(
        protected Request $request,
        protected Users $users,
        protected Config $config,
        protected Session $session,
    ) {
    }

    public function logout(): void
    {
        $session = $this->session;
        $session->forget();
        $hash = $this->getTokenHash();

        if ($hash) {
            $this->users->forget($hash);
            $session->forgetRemembered();
        }
    }

    public function authenticate(
        string $login,
        string $password,
        bool $remember,
        bool $initSession,
    ): array|false {
        $user = $this->users->byLogin($login);

        if (!$user) {
            return false;
        }

        if (password_verify($password, $user['pwhash'])) {
            if ($initSession) {
                $this->login($user['uid'], $remember);
            }

            unset($user['pwhash']);

            return $user;
        }

        return false;
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
            $user = $this->users->byId($userId);

            return $user;
        }

        $hash = $this->getTokenHash();

        if ($hash) {
            $user = $this->users->bySession($hash);

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
        $permissions = new Permissions();
        $user = $this->user();

        if ($user === null) {
            return [];
        }

        return $permissions->get($user['role']);
    }

    protected function remember(string $userUid): RememberDetails
    {
        $token = new Token($this->config->get('app.secret'));
        $expires = time() + ($this->config->get('session.options', [])['cache_expire'] ?? 180);

        $remembered = $this->users->remember(
            $token->hash(),
            $userUid,
            Time::toIsoDateTime($expires),
        );

        if ($remembered) {
            return new RememberDetails($token, $expires);
        }

        throw new RuntimeException('Could not remember user');
    }

    protected function login(string $userUid, bool $remember): void
    {
        $session = $this->session;

        // Regenerate the session id before setting the user id
        // to mitigate session fixation attack.
        $session->regenerate();
        $session->setUser($userUid);

        if ($remember) {
            $details = $this->remember($userUid);

            if ($details) {
                $session->remember(
                    $details->token,
                    $details->expires
                );
            }
        } else {
            // Remove the user entry from loginsessions table as the user
            // has not checked "remember me". In that case the session is
            // only valid as long as the browser is not closed.
            $token = $session->getAuthToken();

            if ($token !== null) {
                $this->users->forget($userUid);
            }
        }
    }

    protected function getTokenHash(): ?string
    {
        $token = $this->session->getAuthToken();

        if ($token) {
            return (new Token($this->config->get('secret'), $token))->hash();
        }

        return null;
    }
}
