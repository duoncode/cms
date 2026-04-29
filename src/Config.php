<?php

declare(strict_types=1);

namespace Duon\Cms;

use Duon\Core\Exception\OutOfBoundsException;
use Duon\Core\Exception\ValueError;

/**
 * @psalm-import-type BuiltinConfig from \Duon\Cms\Config\Types
 * @psalm-import-type BuiltinConfigInput from \Duon\Cms\Config\Types
 */
class Config
{
	/** @var BuiltinConfig&array<string, mixed> */
	private readonly array $settings;

	private readonly Config\Env $environment;

	private ?Config\App $appConfig = null;
	private ?Config\Path $pathConfig = null;
	private ?Config\Panel $panelConfig = null;
	private ?Config\Error $errorConfig = null;
	private ?Config\Icons $iconsConfig = null;
	private ?Config\Database $dbConfig = null;
	private ?Config\Session $sessionConfig = null;
	private ?Config\Media $mediaConfig = null;
	private ?Config\Upload $uploadConfig = null;
	private ?Config\Password $passwordConfig = null;

	public Config\App $app {
		get => $this->appConfig ??= $this->appConfig();
	}

	public Config\Path $path {
		get => $this->pathConfig ??= $this->pathConfig();
	}

	public Config\Panel $panel {
		get => $this->panelConfig ??= $this->panelConfig();
	}

	public Config\Error $error {
		get => $this->errorConfig ??= $this->errorConfig();
	}

	public Config\Icons $icons {
		get => $this->iconsConfig ??= $this->iconsConfig();
	}

	public Config\Database $db {
		get => $this->dbConfig ??= $this->dbConfig();
	}

	public Config\Session $session {
		get => $this->sessionConfig ??= $this->sessionConfig();
	}

	public Config\Media $media {
		get => $this->mediaConfig ??= $this->mediaConfig();
	}

	public Config\Upload $upload {
		get => $this->uploadConfig ??= $this->uploadConfig();
	}

	public Config\Password $password {
		get => $this->passwordConfig ??= $this->passwordConfig();
	}

	/** @param BuiltinConfigInput&array<string, mixed> $settings */
	public function __construct(string $root, array $settings = [])
	{
		$root = $this->normalizeRoot($root);
		$this->environment = Config\Env::load($root);
		$this->environment->validate();
		$this->settings = Config\Normalize::settings(
			Config\Defaults::values($root, $this->environment),
			$settings,
		);
	}

	/** @param non-empty-string|list<non-empty-string> $variables */
	public function requireEnv(string|array $variables): self
	{
		$this->environment->require($variables);

		return $this;
	}

	/**
	 * @template TKey of string
	 * @param TKey $key
	 * @param (TKey is key-of<BuiltinConfig> ? BuiltinConfig[TKey] : mixed) $value
	 */
	public function with(string $key, mixed $value): self
	{
		$settings = $this->settings;
		$settings[$key] = Config\Normalize::setValue($settings, $key, $value);

		return new self($this->path->root, $settings);
	}

	public function has(string $key): bool
	{
		return array_key_exists($key, $this->settings);
	}

	/**
	 * @template TKey of string
	 * @param TKey $key
	 * @return (TKey is key-of<BuiltinConfig> ? BuiltinConfig[TKey] : mixed)
	 */
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

	public function debug(): bool
	{
		return $this->app->debug;
	}

	public function env(): string
	{
		return $this->app->env;
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

	private function appConfig(): Config\App
	{
		return new Config\App(
			$this->get('app.name'),
			$this->get('app.debug'),
			$this->get('app.env'),
			$this->get('app.secret'),
		);
	}

	private function pathConfig(): Config\Path
	{
		return new Config\Path(
			$this->get('path.root'),
			$this->get('path.public'),
			$this->get('path.prefix'),
			$this->get('path.assets'),
			$this->get('path.cache'),
			$this->get('path.views'),
			$this->get('path.panel'),
			$this->get('path.api'),
		);
	}

	private function panelConfig(): Config\Panel
	{
		return new Config\Panel(
			$this->get('path.panel'),
			$this->get('panel.theme'),
			$this->get('panel.logo'),
		);
	}

	private function errorConfig(): Config\Error
	{
		return new Config\Error(
			$this->get('error.enabled'),
			$this->get('error.renderer'),
			$this->get('error.trusted'),
			$this->get('error.views'),
			$this->get('error.whoops'),
		);
	}

	private function iconsConfig(): Config\Icons
	{
		return new Config\Icons(
			$this->get('icons.local.paths'),
			new Config\Iconify(
				$this->get('icons.iconify.base_url'),
				$this->get('icons.iconify.timeout'),
				$this->get('icons.iconify.user_agent'),
			),
		);
	}

	private function dbConfig(): Config\Database
	{
		return new Config\Database(
			$this->get('db.dsn'),
			$this->get('db.sql'),
			$this->get('db.migrations'),
			$this->get('db.print'),
			$this->get('db.options'),
		);
	}

	private function sessionConfig(): Config\Session
	{
		return new Config\Session(
			$this->get('session.enabled'),
			$this->get('session.options'),
			$this->get('session.handler'),
		);
	}

	private function mediaConfig(): Config\Media
	{
		return new Config\Media(
			$this->get('media.fileserver'),
		);
	}

	private function uploadConfig(): Config\Upload
	{
		return new Config\Upload(
			$this->get('upload.mimetypes.file'),
			$this->get('upload.mimetypes.image'),
			$this->get('upload.mimetypes.video'),
			$this->get('upload.maxsize'),
		);
	}

	private function passwordConfig(): Config\Password
	{
		return new Config\Password(
			$this->get('password.entropy'),
			$this->get('password.algorithm'),
		);
	}
}
