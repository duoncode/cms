<?php

declare(strict_types=1);

namespace Duon\Cms\Tests;

use Duon\Cms\Context;
use Duon\Cms\Finder\Finder;
use Duon\Cms\Tests\TestCase;

/**
 * Base class for integration tests that interact with the database.
 *
 * This class extends TestCase and enables transaction-based test isolation
 * by default, ensuring each test has a clean database state.
 *
 * @internal
 *
 * @coversNothing
 */
class IntegrationTestCase extends TestCase
{
	protected bool $useTransactions = true;

	protected function createContext(): Context
	{
		return new Context(
			$this->db(),
			$this->request(),
			$this->config(),
			$this->registry(),
			$this->factory(),
		);
	}

	protected function createFinder(): Finder
	{
		return new Finder($this->createContext());
	}
}
