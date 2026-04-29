<?php

declare(strict_types=1);

namespace Duon\Cms;

use Duon\Cms\Config\Defaults;
use Duon\Cms\Config\Env as Environment;
use Duon\Core\Exception\OutOfBoundsException;
use Duon\Core\Exception\ValueError;

class Config
{
	/** @var array<string, mixed> */
	protected array $settings = [];

	protected readonly Environment $environment;

	public function __construct(string $root, array $settings = [])
	{
		$root = $this->normalizeRoot($root);
		$this->environment = Environment::load($root);
		$this->environment->validate();
		$this->settings = array_merge(Defaults::values($root, $this->environment), $settings);
	}

	/** @param non-empty-string|list<non-empty-string> $variables */
	public function requireEnv(string|array $variables): self
	{
		$this->environment->require($variables);

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
}
