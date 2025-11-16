<?php

declare(strict_types=1);

namespace Duon\Cms\Tests\Setup;

use Duon\Cms\Context;
use Duon\Cms\Finder\Finder;

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

	/**
	 * Create a Context instance for testing.
	 *
	 * @param string $localeId Locale ID (default: 'en')
	 */
	protected function createContext(string $localeId = 'en'): Context
	{
		return new Context(
			$this->db(),
			$this->request(),
			$this->config(),
			$this->registry(),
			$this->factory(),
		);
	}

	/**
	 * Create a Finder instance for testing queries.
	 *
	 * @param string $localeId Locale ID (default: 'en')
	 */
	protected function createFinder(string $localeId = 'en'): Finder
	{
		return new Finder($this->createContext($localeId));
	}
}
