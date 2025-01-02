<?php

declare(strict_types=1);

namespace FiveOrbs\Cms\Tests;

use FiveOrbs\Cms\Tests\Setup\TestCase;
use FiveOrbs\Cms\Util\Strings;

final class UtilStringTest extends TestCase
{
	public function testStringEntropy(): void
	{
		$lower = Strings::entropy('spirit crusher');
		$upper = Strings::entropy('SPIRIT CRUSHER');
		$mixed = Strings::entropy('Spirit Crusher');

		$this->assertSame($upper, $lower);
		$this->assertLessThan($mixed, $lower);
		$this->assertGreaterThan(100, Strings::entropy('Correct Horse Battery Staple'));
		$this->assertGreaterThan(40, Strings::entropy('evil-chuck-666'));
		$this->assertLessThan(15, Strings::entropy('acegik'));
		$this->assertLessThan(10, Strings::entropy('12345'));
		$this->assertSame(0.0, Strings::entropy('1'));
	}
}
