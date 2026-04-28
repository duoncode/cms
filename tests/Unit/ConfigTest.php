<?php

declare(strict_types=1);

namespace Duon\Cms\Tests\Unit;

use Dotenv\Exception\ValidationException;
use Duon\Cms\Config;
use Duon\Cms\Tests\TestCase;
use Duon\Core\Exception\ValueError;

/**
 * @internal
 *
 * @coversNothing
 */
final class ConfigTest extends TestCase
{
	/** @var array<string, array{env: bool, envValue: mixed, server: bool, serverValue: mixed}> */
	private array $environment = [];

	/** @var list<string> */
	private array $roots = [];

	protected function setUp(): void
	{
		parent::setUp();

		$this->clearEnvironment(
			'APP_DEBUG',
			'APP_ENV',
			'APP_MISSING',
			'APP_NAME',
			'APP_REQUIRED',
			'APP_SECRET',
			'CMS_DSN',
			'DATABASE_URL',
			'SITE_SESSION_ENABLED',
			'SESSION_COOKIE_LIFETIME',
			'SESSION_COOKIE_SECURE',
			'SESSION_IDLE_TIMEOUT',
		);
	}

	protected function tearDown(): void
	{
		foreach ($this->roots as $root) {
			if (is_file($root . '/.env')) {
				unlink($root . '/.env');
			}

			if (is_dir($root)) {
				rmdir($root);
			}
		}

		foreach ($this->environment as $key => $value) {
			if ($value['env']) {
				$_ENV[$key] = $value['envValue'];
			} else {
				unset($_ENV[$key]);
			}

			if ($value['server']) {
				$_SERVER[$key] = $value['serverValue'];
			} else {
				unset($_SERVER[$key]);
			}
		}

		parent::tearDown();
	}

	public function testDefaultsUseRootAndDefaultAppName(): void
	{
		$config = new Config(self::root());

		$this->assertSame('duoncms', $config->app());
		$this->assertSame('duoncms', $config->get('app.name'));
		$this->assertSame(self::root(), $config->get('path.root'));
		$this->assertSame(self::root() . '/public', $config->get('path.public'));
		$this->assertNull($config->get('app.secret'));
		$this->assertFalse($config->get('session.enabled'));
		$this->assertSame(0, $config->get('session.options')['cookie_lifetime']);
		$this->assertTrue($config->get('session.options')['cookie_secure']);
		$this->assertSame(3600, $config->get('session.options')['gc_maxlifetime']);
		$this->assertNull($config->get('db.dsn'));
		$this->assertFalse($config->debug());
		$this->assertSame('', $config->env());
	}

	public function testSettingsCanOverrideAppNameDebugAndEnvironment(): void
	{
		$config = new Config(self::root(), [
			'app.name' => 'site-cms',
			'app.debug' => 'false',
			'app.env' => 'production',
			'app.secret' => 'configured-secret',
			'session.enabled' => true,
		]);

		$this->assertSame('site-cms', $config->app());
		$this->assertFalse($config->debug());
		$this->assertSame('production', $config->env());
		$this->assertSame('configured-secret', $config->get('app.secret'));
		$this->assertTrue($config->get('session.enabled'));
	}

	public function testConstructorRequiresRoot(): void
	{
		$this->throws(ValueError::class, 'The root path must be a non-empty string.');

		new Config('');
	}

	public function testDotenvIsLoadedFromRoot(): void
	{
		$root = $this->rootWithEnv(
			"APP_NAME=test-cms\nAPP_DEBUG=true\nAPP_ENV=testing\nAPP_REQUIRED=present\nAPP_SECRET=test-secret\nSITE_SESSION_ENABLED=true\n",
		);
		$config = new Config($root);

		$this->assertSame('test-cms', $config->app());
		$this->assertTrue($config->debug());
		$this->assertSame('testing', $config->env());
		$this->assertSame('test-secret', $config->get('app.secret'));
		$this->assertTrue($config->get('session.enabled'));
		$this->assertSame('present', $_ENV['APP_REQUIRED']);
	}

	public function testSessionOptionsCanBeChangedFromEnvironment(): void
	{
		$config = new Config($this->rootWithEnv(
			"SESSION_COOKIE_SECURE=false\nSESSION_COOKIE_LIFETIME=86400\nSESSION_IDLE_TIMEOUT=7200\n",
		));

		$this->assertFalse($config->get('session.options')['cookie_secure']);
		$this->assertSame(86400, $config->get('session.options')['cookie_lifetime']);
		$this->assertSame(7200, $config->get('session.options')['gc_maxlifetime']);
	}

	public function testDatabaseDsnUsesEnvironmentVariable(): void
	{
		$config = new Config($this->rootWithEnv("DATABASE_URL=pgsql:dbname=cms\n"));

		$this->assertSame('pgsql:dbname=cms', $config->get('db.dsn'));
	}

	public function testDatabaseDsnDoesNotFallBackToLegacyEnvironmentVariable(): void
	{
		$config = new Config($this->rootWithEnv("CMS_DSN=pgsql:dbname=legacy\n"));

		$this->assertNull($config->get('db.dsn'));
	}

	public function testMissingDotenvFileIsIgnored(): void
	{
		$config = new Config($this->rootWithEnv());

		$this->assertFalse($config->debug());
	}

	public function testRequireEnvReturnsConfigWhenVariableExists(): void
	{
		$config = new Config($this->rootWithEnv("APP_REQUIRED=present\n"));

		$this->assertSame($config, $config->requireEnv('APP_REQUIRED'));
	}

	public function testRequireEnvFailsForMissingVariable(): void
	{
		$config = new Config($this->rootWithEnv());

		$this->throws(ValidationException::class);

		$config->requireEnv('APP_MISSING');
	}

	private function clearEnvironment(string ...$keys): void
	{
		foreach ($keys as $key) {
			$this->environment[$key] = [
				'env' => array_key_exists($key, $_ENV),
				'envValue' => $_ENV[$key] ?? null,
				'server' => array_key_exists($key, $_SERVER),
				'serverValue' => $_SERVER[$key] ?? null,
			];

			unset($_ENV[$key], $_SERVER[$key]);
		}
	}

	private function rootWithEnv(string $contents = ''): string
	{
		$root = sys_get_temp_dir() . '/duon-cms-config-' . bin2hex(random_bytes(4));
		mkdir($root);
		$this->roots[] = $root;

		if ($contents !== '') {
			file_put_contents($root . '/.env', $contents);
		}

		return $root;
	}
}
