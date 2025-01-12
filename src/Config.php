<?php

declare(strict_types=1);

namespace FiveOrbs\Cms;

use FiveOrbs\Core\AddsConfigInterface;
use FiveOrbs\Core\ConfigInterface;
use FiveOrbs\Core\Exception\ValueError;

class Config implements ConfigInterface
{
	use AddsConfigInterface;

	public function __construct(
		public readonly string $app = 'cms',
		public readonly bool $debug = false,
		public readonly string $env = '',
		array $settings = [],
	) {
		$this->settings = array_merge([
			'path.prefix' => '',
			'path.assets' => '/assets',
			'path.cache' => '/cache',
			'path.panel' => '/cms',
			'path.api' => null,

			'panel.theme' => null,
			'panel.logo' => '/images/logo.png',
			'panel.debug' => $debug,
			'panel.color-success' => '',
			'panel.color-danger' => '',
			'panel.color-info' => '',

			'session.options' => [
				'cookie_httponly' => true,
				'cookie_lifetime' => 0,
				'gc_maxlifetime' => 3600,
			],
			'slug.transliterate' => null,
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
			// 'password.algorithm' => PASSWORD_* PHP constant
			// 'password.entropy' => float
		], $settings);
		$this->validateApp($app);
	}

	public function app(): string
	{
		return $this->app;
	}

	public function debug(): bool
	{
		return $this->debug;
	}

	public function panelPath(): string
	{
		if ($this->env === 'development') {
			return '/cms';
		}

		return $this->settings['path.panel'];
	}

	public function apiPath(): string
	{
		if ($this->env === 'development') {
			return '/cms/api';
		}

		return $this->get('path.api', $this->panelPath() . '/api');
	}

	public function env(): string
	{
		return $this->env;
	}

	protected function validateApp(string $app): void
	{
		if (!preg_match('/^[a-zA-Z0-9_$-]{1,64}$/', $app)) {
			throw new ValueError(
				'The app name must be a nonempty string which consist only of lower case ' .
					'letters and numbers. Its length must not be longer than 32 characters.',
			);
		}
	}

	public function printAll(): void
	{
		error_log(print_r($this->settings, true));
	}
}
