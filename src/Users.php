<?php

declare(strict_types=1);

namespace FiveOrbs\Cms;

use FiveOrbs\Quma\Database;

class Users
{
	public function __construct(protected Database $db) {}

	public function byLogin(string $login): ?User
	{
		return $this->getUserOrNull($this->db->users->get([
			'login' => $login,
		])->one());
	}

	public function byAuthToken(string $token): ?User
	{
		return $this->getUserOrNull($this->db->users->get([
			'token' => $token,
		])->one());
	}

	public function byOneTimeToken(string $token): ?User
	{
		$hashedToken = hash('sha256', $token);

		$user = $this->getUserOrNull($this->db->users->get([
			'onetimetoken' => $hashedToken,
		])->one());

		if ($user) {
			// $this->db->users->removeOneTimeToken([
			// 	'usr' => $user->id,
			// 	'token' => $hashedToken,
			// ])->run();
		}

		return $user;
	}

	public function bySession(string $hash): ?User
	{
		return $this->getUserOrNull($this->db->users->get([
			'sessionhash' => $hash,
		])->one());
	}

	public function byUid(string $uid): ?User
	{
		return $this->getUserOrNull($this->db->users->get([
			'uid' => $uid,
		])->one());
	}

	public function byId(int $id): ?User
	{
		return $this->getUserOrNull($this->db->users->get([
			'usr' => $id,
		])->one());
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

	public function createOneTimeToken(int $userId): string
	{
		$token = bin2hex(random_bytes(32));

		$this->db->users->saveOneTimeToken([
			'token' => hash('sha256', $token),
			'usr' => $userId,
		])->run();

		return $token;
	}

	protected function getUserOrNull(?array $data): ?User
	{
		if ($data) {
			return new User($data);
		}

		return null;
	}
}
