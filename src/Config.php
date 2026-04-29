<?php

declare(strict_types=1);

namespace Duon\Cms;

use Dotenv\Dotenv;
use Duon\Core\Exception\OutOfBoundsException;
use Duon\Core\Exception\ValueError;

class Config
{
	/** @var array<string, mixed> */
	protected array $settings = [];

	protected readonly Dotenv $dotenv;

	public function __construct(string $root, array $settings = [])
	{
		$root = $this->normalizeRoot($root);
		$this->dotenv = Dotenv::createImmutable($root);
		$this->dotenv->safeLoad();
		$this->validateEnvironment();
		$this->settings = array_merge([
			'app.name' => env('APP_NAME', 'duoncms'),
			'app.debug' => $this->boolEnv('APP_DEBUG', false),
			'app.env' => env('APP_ENV', ''),
			'app.secret' => env('APP_SECRET', null),
			'path.root' => $root,
			'path.public' => $root . '/public',
			'path.prefix' => '',
			'path.assets' => '/assets',
			'path.cache' => '/cache',
			'path.views' => '/views',
			'path.panel' => '/cms',
			'path.api' => null,
			'panel.theme' => null,
			'panel.logo' => '/images/logo.png',
			'error.enabled' => true,
			'error.renderer' => null,
			'error.trusted' => [],
			'error.views' => null,
			'error.whoops' => true,
			'icons.local.paths' => [],
			'icons.iconify.base_url' => 'https://api.iconify.design',
			'icons.iconify.timeout' => 5,
			'icons.iconify.user_agent' => 'duon/cms',
			'db.dsn' => env('DATABASE_URL', null),
			'db.sql' => [],
			'db.migrations' => [],
			'db.print' => false,
			'db.options' => [],
			'session.enabled' => $this->boolEnv('SITE_SESSION_ENABLED', false),
			'session.options' => [
				'cookie_httponly' => true,
				'cookie_secure' => $this->boolEnv('SESSION_COOKIE_SECURE', true),
				'cookie_lifetime' => $this->intEnv('SESSION_COOKIE_LIFETIME', 0),
				'gc_maxlifetime' => $this->intEnv('SESSION_IDLE_TIMEOUT', 3600),
				'cache_expire' => 3600,
			],
			'session.handler' => null,
			'media.fileserver' => null,
			'upload.mimetypes.file' => [
				'application/pdf' => ['pdf'],
			],
			'upload.mimetypes.image' => [
				'image/gif' => ['gif'],
				'image/jpeg' => ['jpeg', 'jpg', 'jfif'],
				'image/png' => ['png'],
				'image/webp' => ['webp'],
				'image/svg+xml' => ['svg'],
			],
			'upload.mimetypes.video' => [
				'video/mp4' => ['mp4'],
				'video/ogg' => ['ogg'],
			],
			'upload.maxsize' => 10 * 1024 * 1024,
			'password.entropy' => Util\Password::DEFAULT_PASSWORD_ENTROPY,
			'password.algorithm' => null,
		], $settings);
	}

	/** @param non-empty-string|list<non-empty-string> $variables */
	public function requireEnv(string|array $variables): self
	{
		$this->dotenv->required($variables);

		return $this;
	}

	public function set(string $key, mixed $value): void
	{
		$this->settings[$key] = $value;
	}

	public function has(string $key): bool
	{
		return array_key_exists($key, $this->settings);
	}

	public function get(string $key, mixed $default = null): mixed
	{
		if (array_key_exists($key, $this->settings)) {
			return $this->settings[$key];
		}

		if (func_num_args() > 1) {
			return $default;
		}

		throw new OutOfBoundsException(
			"The configuration key '{$key}' does not exist",
		);
	}

	public function app(): string
	{
		return (string) $this->get('app.name');
	}

	public function debug(): bool
	{
		return filter_var($this->get('app.debug'), FILTER_VALIDATE_BOOL);
	}

	public function panelPath(): string
	{
		if ($this->env() === 'cms-development') {
			return '/cms';
		}

		return $this->settings['path.panel'];
	}

	public function apiPath(): ?string
	{
		return $this->get('path.api', null);
	}

	public function env(): string
	{
		return (string) $this->get('app.env');
	}

	protected function normalizeRoot(string $root): string
	{
		if ($root === '') {
			throw new ValueError('The root path must be a non-empty string.');
		}

		return rtrim($root, '/\\') ?: DIRECTORY_SEPARATOR;
	}

	public function printAll(): void
	{
		error_log(print_r($this->settings, true));
	}

	protected function validateEnvironment(): void
	{
		$this->dotenv->ifPresent([
			'APP_DEBUG',
			'SITE_SESSION_ENABLED',
			'SESSION_COOKIE_SECURE',
		])->isBoolean();

		$this->dotenv->ifPresent([
			'SESSION_COOKIE_LIFETIME',
			'SESSION_IDLE_TIMEOUT',
		])->isInteger();
	}

	protected function boolEnv(string $key, bool $default): bool
	{
		return filter_var(env($key, $default), FILTER_VALIDATE_BOOL);
	}

	protected function intEnv(string $key, int $default): int
	{
		return (int) env($key, $default);
	}
}
