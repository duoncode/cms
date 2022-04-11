<?php

declare(strict_types=1);

namespace Conia;

use Chuck\ConfigInterface;
use Chuck\RequestInterface;
use Chuck\Database\DatabaseInterface;


class Auth
{
    protected ?ConfigInterface $config;

    public function __construct(
        protected DatabaseInterface $db,
        protected RequestInterface $request
    ) {
        if ($request) {
            $this->config = $request->getConfig();
        }
    }

    protected function login(int $userId, bool $remember): void
    {
        $session = $this->request->session();
        // regenerate the session id before setting the user id
        // to mitigate session fixation attack.
        $session->regenerate();
        $session->setUser($userId);

        if ($remember) {
            $details = self::remember($userId);

            if ($details) {
                $session->remember(
                    $details->token,
                    $details->expires
                );
            };
        }
    }

    public function logout(): void
    {
        $session = $this->request->session;
        $session->forget();

        $hash = self::getHash();
        if ($hash) {
            Users::forgetLogin($hash);
            $session->forgetRemembered();
        }
    }

    public function authenticate(
        array $features,
        string $login,
        string $password,
        bool $remember,
        bool $initSession,
    ): ?int {
        $users = new Users($this->db, $this->request);
        $user = $users->byLogin($login);

        if (!$user) {
            return null;
        }

        if (password_verify($password, $user['pwhash'])) {
            $userId = self::decode($user['usr']);
            if ($initSession) {
                self::login($userId, $remember);
            }
            Users::setLastLogin($userId);

            return $userId;
        } else {
            return null;
        }
    }

    public function permissions(): array
    {
        $user = self::user();

        if ($user === null) {
            return [];
        }

        return PERMISSIONS[$user['role']];
    }

    protected function remember(int $userId): ?RememberDetails
    {
        $db = $this->db;
        $token = new Token($this->config->get('secret'));
        $expires = time() + $this->config->get('session')['expire'];

        $remembered = ($db->users->remember)([
            'hash' => $token->hash(),
            'user' => $userId,
            'expires' => self::toIsoDateTime($expires),
        ])->run();

        if ($remembered) {
            return new RememberDetails($token, $expires);
        };
    }

    public function addSuperuser(array $params): array
    {
        $db = $this->db;
        try {
            $db->begin();
            ($db->users->addSuperuser)($params)->run();
            $db->commit();
            return ['success' => true];
        } catch (\PDOException $e) {
            $db->rollback();
            throw $e;
        }
    }
}
