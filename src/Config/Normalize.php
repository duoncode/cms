<?php

declare(strict_types=1);

namespace Duon\Cms\Config;

use Duon\Core\Exception\ValueError;
use SessionHandlerInterface;

final class Normalize
{
	private const array BOOLEAN_KEYS = [
		'app.debug' => true,
		'error.enabled' => true,
		'error.whoops' => true,
		'db.print' => true,
		'session.enabled' => true,
	];

	private const array LIST_KEYS = [
		'panel.theme' => true,
		'icons.local.paths' => true,
		'db.sql' => true,
		'db.migrations' => true,
	];

	/**
	 * @param array<string, mixed> $defaults
	 * @param array<string, mixed> $settings
	 * @return array<string, mixed>
	 */
	public static function settings(array $defaults, array $settings): array
	{
		return self::all(self::mergeArray($defaults, $settings));
	}

	/** @param array<string, mixed> $settings */
	public static function setValue(array $settings, string $key, mixed $value): mixed
	{
		$current = $settings[$key] ?? null;

		if (is_array($current) && is_array($value) && self::canMerge($current, $value)) {
			$value = self::mergeArray($current, $value);
		}

		return self::value($key, $value);
	}

	/** @param array<string, mixed> $settings */
	private static function all(array $settings): array
	{
		foreach (array_keys(self::BOOLEAN_KEYS) as $key) {
			if (!array_key_exists($key, $settings)) {
				continue;
			}

			$settings[$key] = self::bool($settings[$key], $key);
		}

		foreach (array_keys(self::LIST_KEYS) as $key) {
			if (!array_key_exists($key, $settings)) {
				continue;
			}

			$settings[$key] = self::stringList($settings[$key], $key);
		}

		foreach ([
			'app.name',
			'path.root',
			'path.public',
			'path.assets',
			'path.cache',
			'path.views',
			'path.panel',
			'icons.iconify.base_url',
			'icons.iconify.user_agent',
		] as $key) {
			if (!array_key_exists($key, $settings)) {
				continue;
			}

			$settings[$key] = self::nonEmptyString($settings[$key], $key);
		}

		foreach ([
			'app.secret',
			'path.api',
			'panel.logo',
			'db.dsn',
		] as $key) {
			if (!array_key_exists($key, $settings)) {
				continue;
			}

			$settings[$key] = self::nullableNonEmptyString($settings[$key], $key);
		}

		if (array_key_exists('app.env', $settings)) {
			$settings['app.env'] = self::string($settings['app.env'], 'app.env');
		}

		if (array_key_exists('path.prefix', $settings)) {
			$settings['path.prefix'] = self::string($settings['path.prefix'], 'path.prefix');
		}

		if (array_key_exists('error.views', $settings)) {
			$settings['error.views'] = self::nullableStringOrList($settings['error.views'], 'error.views');
		}

		if (array_key_exists('icons.iconify.timeout', $settings)) {
			$settings['icons.iconify.timeout'] = self::positiveInt(
				$settings['icons.iconify.timeout'],
				'icons.iconify.timeout',
			);
		}

		if (array_key_exists('session.options', $settings)) {
			$settings['session.options'] = self::sessionOptions($settings['session.options']);
		}

		if (array_key_exists('session.handler', $settings)) {
			$settings['session.handler'] = self::sessionHandler($settings['session.handler']);
		}

		foreach ([
			'upload.mimetypes.file',
			'upload.mimetypes.image',
			'upload.mimetypes.video',
		] as $key) {
			if (!array_key_exists($key, $settings)) {
				continue;
			}

			$settings[$key] = self::mimeMap($settings[$key], $key);
		}

		if (array_key_exists('upload.maxsize', $settings)) {
			$settings['upload.maxsize'] = self::positiveInt($settings['upload.maxsize'], 'upload.maxsize');
		}

		if (array_key_exists('password.entropy', $settings)) {
			$settings['password.entropy'] = self::positiveFloat(
				$settings['password.entropy'],
				'password.entropy',
			);
		}

		if (array_key_exists('media.fileserver', $settings)) {
			$settings['media.fileserver'] = self::fileServer($settings['media.fileserver']);
		}

		return $settings;
	}

	private static function value(string $key, mixed $value): mixed
	{
		return self::all([$key => $value])[$key];
	}

	/**
	 * @param array<array-key, mixed> $base
	 * @param array<array-key, mixed> $override
	 * @return array<array-key, mixed>
	 */
	private static function mergeArray(array $base, array $override): array
	{
		foreach ($override as $key => $value) {
			$current = $base[$key] ?? null;

			if (is_array($current) && is_array($value) && self::canMerge($current, $value)) {
				$base[$key] = self::mergeArray($current, $value);
			} else {
				$base[$key] = $value;
			}
		}

		return $base;
	}

	/**
	 * @param array<array-key, mixed> $current
	 * @param array<array-key, mixed> $value
	 */
	private static function canMerge(array $current, array $value): bool
	{
		return !array_is_list($current) && !array_is_list($value);
	}

	private static function bool(mixed $value, string $key): bool
	{
		if (is_bool($value)) {
			return $value;
		}

		$result = filter_var($value, FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE);

		if ($result === null) {
			throw new ValueError("The configuration key '{$key}' must be boolean.");
		}

		return $result;
	}

	private static function string(mixed $value, string $key): string
	{
		if (is_scalar($value) || $value === null) {
			return (string) $value;
		}

		throw new ValueError("The configuration key '{$key}' must be a string.");
	}

	private static function nonEmptyString(mixed $value, string $key): string
	{
		$value = trim(self::string($value, $key));

		if ($value === '') {
			throw new ValueError("The configuration key '{$key}' must be a non-empty string.");
		}

		return $value;
	}

	private static function nullableNonEmptyString(mixed $value, string $key): ?string
	{
		if ($value === null) {
			return null;
		}

		$value = trim(self::string($value, $key));

		return $value === '' ? null : $value;
	}

	/** @return list<string> */
	private static function stringList(mixed $value, string $key): array
	{
		if ($value === null) {
			return [];
		}

		if (is_string($value)) {
			$value = trim($value);

			return $value === '' ? [] : [$value];
		}

		if (!is_array($value)) {
			throw new ValueError("The configuration key '{$key}' must be a string list.");
		}

		$list = [];

		foreach ($value as $item) {
			$item = self::nonEmptyString($item, $key);
			$list[] = $item;
		}

		return $list;
	}

	private static function nullableStringOrList(mixed $value, string $key): string|array|null
	{
		if ($value === null) {
			return null;
		}

		if (is_string($value)) {
			return self::nullableNonEmptyString($value, $key);
		}

		$list = self::stringList($value, $key);

		return $list === [] ? null : $list;
	}

	private static function int(mixed $value, string $key): int
	{
		if (is_int($value)) {
			return $value;
		}

		if (is_string($value) && preg_match('/^-?[0-9]+$/', $value)) {
			return (int) $value;
		}

		throw new ValueError("The configuration key '{$key}' must be an integer.");
	}

	private static function positiveInt(mixed $value, string $key): int
	{
		$value = self::int($value, $key);

		if ($value < 1) {
			throw new ValueError("The configuration key '{$key}' must be a positive integer.");
		}

		return $value;
	}

	private static function nonNegativeInt(mixed $value, string $key): int
	{
		$value = self::int($value, $key);

		if ($value < 0) {
			throw new ValueError("The configuration key '{$key}' must be zero or a positive integer.");
		}

		return $value;
	}

	private static function positiveFloat(mixed $value, string $key): float
	{
		if (!is_int($value) && !is_float($value) && !is_string($value)) {
			throw new ValueError("The configuration key '{$key}' must be a positive number.");
		}

		if (!is_numeric($value)) {
			throw new ValueError("The configuration key '{$key}' must be a positive number.");
		}

		$value = (float) $value;

		if ($value <= 0.0) {
			throw new ValueError("The configuration key '{$key}' must be a positive number.");
		}

		return $value;
	}

	/** @return array<string, mixed> */
	private static function sessionOptions(mixed $value): array
	{
		if (!is_array($value)) {
			throw new ValueError("The configuration key 'session.options' must be an array.");
		}

		$value['cookie_httponly'] = self::bool(
			$value['cookie_httponly'] ?? true,
			'session.options.cookie_httponly',
		);
		$value['cookie_secure'] = self::bool(
			$value['cookie_secure'] ?? true,
			'session.options.cookie_secure',
		);
		$value['cookie_lifetime'] = self::nonNegativeInt(
			$value['cookie_lifetime'] ?? 0,
			'session.options.cookie_lifetime',
		);
		$value['gc_maxlifetime'] = self::positiveInt(
			$value['gc_maxlifetime'] ?? 3600,
			'session.options.gc_maxlifetime',
		);
		$value['cache_expire'] = self::positiveInt(
			$value['cache_expire'] ?? 3600,
			'session.options.cache_expire',
		);

		return $value;
	}

	private static function sessionHandler(mixed $value): ?SessionHandlerInterface
	{
		if ($value === null || $value instanceof SessionHandlerInterface) {
			return $value;
		}

		throw new ValueError(
			"The configuration key 'session.handler' must be a session handler or null.",
		);
	}

	/** @return array<string, non-empty-list<string>> */
	private static function mimeMap(mixed $value, string $key): array
	{
		if (!is_array($value)) {
			throw new ValueError("The configuration key '{$key}' must be a MIME type map.");
		}

		$map = [];

		foreach ($value as $mime => $extensions) {
			if (!is_string($mime) || trim($mime) === '') {
				throw new ValueError("The configuration key '{$key}' must use non-empty MIME type strings.");
			}

			$list = self::stringList($extensions, $key . '.' . $mime);

			if ($list === []) {
				throw new ValueError(
					"The configuration key '{$key}.{$mime}' must contain at least one extension.",
				);
			}

			$map[$mime] = $list;
		}

		return $map;
	}

	private static function fileServer(mixed $value): ?string
	{
		if ($value === null || $value === 'apache' || $value === 'nginx') {
			return $value;
		}

		throw new ValueError(
			"The configuration key 'media.fileserver' must be 'apache', 'nginx', or null.",
		);
	}
}
