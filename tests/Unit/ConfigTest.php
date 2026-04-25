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

		$this->clearEnvironment('CMS_DEBUG', 'CMS_ENV', 'CMS_REQUIRED', 'CMS_MISSING');
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
		$this->assertFalse($config->debug());
		$this->assertSame('', $config->env());
	}

	public function testSettingsCanOverrideAppNameDebugAndEnvironment(): void
	{
		$config = new Config(self::root(), [
			'app.name' => 'site-cms',
			'app.debug' => 'false',
			'app.env' => 'production',
		]);

		$this->assertSame('site-cms', $config->app());
		$this->assertFalse($config->debug());
		$this->assertSame('production', $config->env());
	}

	public function testSetValidatesAppName(): void
	{
		$config = new Config(self::root());

		$this->throws(ValueError::class, 'The app name must be a non-empty string');

		$config->set('app.name', 'not valid');
	}

	public function testConstructorValidatesAppName(): void
	{
		$this->throws(ValueError::class, 'The app name must be a non-empty string');

		new Config(self::root(), ['app.name' => 'not valid']);
	}

	public function testConstructorRequiresRoot(): void
	{
		$this->throws(ValueError::class, 'The root path must be a non-empty string.');

		new Config('');
	}

	public function testDotenvIsLoadedFromRoot(): void
	{
		$root = $this->rootWithEnv("CMS_DEBUG=true\nCMS_ENV=testing\nCMS_REQUIRED=present\n");
		$config = new Config($root);

		$this->assertTrue($config->debug());
		$this->assertSame('testing', $config->env());
		$this->assertSame('present', $_ENV['CMS_REQUIRED']);
	}

	public function testMissingDotenvFileIsIgnored(): void
	{
		$config = new Config($this->rootWithEnv());

		$this->assertFalse($config->debug());
	}

	public function testRequireEnvReturnsConfigWhenVariableExists(): void
	{
		$config = new Config($this->rootWithEnv("CMS_REQUIRED=present\n"));

		$this->assertSame($config, $config->requireEnv('CMS_REQUIRED'));
	}

	public function testRequireEnvFailsForMissingVariable(): void
	{
		$config = new Config($this->rootWithEnv());

		$this->throws(ValidationException::class);

		$config->requireEnv('CMS_MISSING');
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
