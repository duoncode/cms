<?php

declare(strict_types=1);

namespace FiveOrbs\Cms\Tests;

use FiveOrbs\Cms\Config;
use FiveOrbs\Cms\Tests\Setup\TestCase;
use FiveOrbs\Cms\Util\Password;

final class PasswordTest extends TestCase
{
	public function testPasswordStrength(): void
	{
		$pw = new Password();

		$this->assertSame(false, $pw->strongEnough('1'));
		$this->assertSame(false, $pw->strongEnough('abcdef'));
		$this->assertSame(true, $pw->strongEnough('evil-chuck-666'));
	}

	public function testPasswordHashDefaultArgon2(): void
	{
		$pw = new Password();

		$this->assertSame(true, str_starts_with($pw->hash('evil-chuck-666'), '$argon2id$v'));
	}

	public function testPasswordVerify(): void
	{
		$pw = new Password();
		$hash = $pw->hash('evil-chuck-666');

		$this->assertSame(true, $pw->valid('evil-chuck-666', $hash));
		$this->assertSame(false, $pw->valid('evil-chuck-660', $hash));
	}

	public function testPasswordInitFromConfig(): void
	{
		$config = new Config('fiveorbs');
		$hasArgon = Password::hasArgon2();

		if ($hasArgon) {
			$pw = Password::fromConfig($config);
			$this->assertSame(true, str_starts_with($pw->hash('evil-chuck-666'), '$argon2id$v'));
		}

		$config->set('password.algorithm', PASSWORD_BCRYPT);

		$pw = Password::fromConfig($this->config([
			'password.algorithm' => PASSWORD_BCRYPT,
		]));
		$this->assertSame(true, str_starts_with($pw->hash('evil-chuck-666'), '$2y$'));
	}
}
