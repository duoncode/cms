<?php

declare(strict_types=1);

namespace Duon\Cms;

use Duon\Cms\Config;
use Duon\Cms\Util\Time;
use Psr\Http\Message\ServerRequestInterface as Request;
use RuntimeException;

class Auth
{
	public function __construct(
		protected Request $request,
		protected Users $users,
		protected Config $config,
		protected ?Session $session = null,
	) {}

	public function logout(): void
	{
		if (!$this->session) {
			return;
		}

		$session = $this->session;
		$hash = $this->getSessionTokenHash();

		if ($hash) {
			$this->users->forget($hash);
			$session->forgetRemembered();
		}

		$session->forget();
	}

	public function authenticate(
		string $login,
		string $password,
		bool $remember,
		bool $initSession,
	): User|false {
		$user = $this->users->byLogin($login);

		if (!$user) {
			return false;
		}

		if (password_verify($password, $user->pwhash)) {
			if ($initSession) {
				$this->login($user->id, $remember);
			}

			return $user;
		}

		return false;
	}

	public function authenticateByOneTimeToken(
		string $token,
		bool $initSession,
	): User|false {
		$user = $this->users->byOneTimeToken($token);

		if (!$user) {
			return false;
		}

		if ($initSession) {
			$this->login($user->id, false);
		}

		return $user;
	}

	public function getOneTimeToken(
		string $token,
	): string|false {
		$user = $this->users->byAuthToken($token);

		if (!$user) {
			return false;
		}

		return $this->users->createOneTimeToken($user->id);
	}

	public function invalidateOneTimeToken(
		string $token,
	): void {
		$this->users->removeOneTimeToken($token);
	}

	public function user(): ?User
	{
		if (!$this->session) {
			return $this->userFromToken();
		}

		// Verify if user is logged in via cookie session
		$userId = $this->session->authenticatedUserId();

		if ($userId) {
			return $this->users->byId($userId);
		}

		$hash = $this->getSessionTokenHash();

		if ($hash) {
			$user = $this->users->bySession($hash);

			if ($user && !(strtotime($user->expires) < time())) {
				$this->login($user->id, false);

				return $user;
			}
		}

		return null;
	}

	protected function userFromToken(): ?User
	{
		$authToken = self::getAuthToken($this->request);

		if ($authToken) {
			return $this->users->byAuthToken($authToken);
		}

		return null;
	}

	public function permissions(): array
	{
		$user = $this->user();

		if ($user === null) {
			return [];
		}

		return $user->permissions();
	}

	public function getAuthToken(): string
	{
		$authToken = '';
		$bearer = $this->request->getHeaderLine('Authentication');

		if (preg_match('/Bearer\s(\S+)/', $bearer, $matches)) {
			$authToken = $matches[1];
		}

		return $authToken;
	}

	protected function remember(int $userId): RememberDetails
	{
		$token = new Token($this->config->get('app.secret'));
		$expires = time() + $this->config->get('session.options', [])['cache_expire'];

		$remembered = $this->users->remember(
			$token->hash(),
			$userId,
			Time::toIsoDateTime($expires),
		);

		if ($remembered) {
			return new RememberDetails($token, $expires);
		}

		throw new RuntimeException('Could not remember user');
	}

	protected function login(int $userId, bool $remember): void
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
					$details->expires,
				);
			}
		} else {
			// Remove the user entry from loginsessions table as the user
			// has not checked "remember me". In that case the session is
			// only valid as long as the browser is not closed.
			$token = $session->getAuthToken();

			if ($token !== null) {
				$this->users->forget($token);
			}
		}
	}

	protected function getSessionTokenHash(): ?string
	{
		$token = $this->session->getAuthToken();

		if ($token) {
			return (new Token($this->config->get('app.secret'), $token))->hash();
		}

		return null;
	}
}
