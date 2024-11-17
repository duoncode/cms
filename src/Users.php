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

	protected function getUserOrNull(?array $data): ?User
	{
		if ($data) {
			return new User($data);
		}

		return null;
	}
}
