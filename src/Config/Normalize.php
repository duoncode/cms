<?php

declare(strict_types=1);

namespace Duon\Cms\Config;

use Duon\Core\Exception\ValueError;

final class Normalize
{
	private const array BOOL_KEYS = [
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
		return self::all(self::merge($defaults, $settings));
	}

	/** @param array<string, mixed> $settings */
	public static function setValue(array $settings, string $key, mixed $value): mixed
	{
		$current = $settings[$key] ?? null;

		if (is_array($current) && is_array($value) && self::canMerge($current, $value)) {
			$value = self::merge($current, $value);
		}

		return self::value($key, $value);
	}

	/** @param array<string, mixed> $settings */
	private static function all(array $settings): array
	{
		foreach ($settings as $key => $value) {
			$settings[$key] = self::value((string) $key, $value);
		}

		return $settings;
	}

	private static function value(string $key, mixed $value): mixed
	{
		if (isset(self::BOOL_KEYS[$key])) {
			return self::bool($key, $value);
		}

		if (isset(self::LIST_KEYS[$key])) {
			return self::stringList($key, $value);
		}

		return match ($key) {
			'icons.iconify.timeout', 'upload.maxsize' => self::positiveInt($key, $value),
			'password.entropy' => self::positiveFloat($key, $value),
			'session.options' => self::sessionOptions($value),
			default => $value,
		};
	}

	/**
	 * @param array<array-key, mixed> $base
	 * @param array<array-key, mixed> $override
	 * @return array<array-key, mixed>
	 */
	private static function merge(array $base, array $override): array
	{
		foreach ($override as $key => $value) {
			$current = $base[$key] ?? null;

			if (is_array($current) && is_array($value) && self::canMerge($current, $value)) {
				$base[$key] = self::merge($current, $value);
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

	private static function bool(string $key, mixed $value): bool
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

	/** @return list<string> */
	private static function stringList(string $key, mixed $value): array
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
			if (!is_string($item)) {
				throw new ValueError("The configuration key '{$key}' must be a string list.");
			}

			$item = trim($item);

			if ($item !== '') {
				$list[] = $item;
			}
		}

		return $list;
	}

	private static function positiveInt(string $key, mixed $value): int
	{
		if (is_string($value) && preg_match('/^[0-9]+$/', $value)) {
			$value = (int) $value;
		}

		if (!is_int($value) || $value < 1) {
			throw new ValueError("The configuration key '{$key}' must be a positive integer.");
		}

		return $value;
	}

	private static function nonNegativeInt(string $key, mixed $value): int
	{
		if (is_string($value) && preg_match('/^[0-9]+$/', $value)) {
			$value = (int) $value;
		}

		if (!is_int($value) || $value < 0) {
			throw new ValueError("The configuration key '{$key}' must be zero or a positive integer.");
		}

		return $value;
	}

	private static function positiveFloat(string $key, mixed $value): float
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
			'session.options.cookie_httponly',
			$value['cookie_httponly'] ?? true,
		);
		$value['cookie_secure'] = self::bool(
			'session.options.cookie_secure',
			$value['cookie_secure'] ?? true,
		);
		$value['cookie_lifetime'] = self::nonNegativeInt(
			'session.options.cookie_lifetime',
			$value['cookie_lifetime'] ?? 0,
		);
		$value['gc_maxlifetime'] = self::positiveInt(
			'session.options.gc_maxlifetime',
			$value['gc_maxlifetime'] ?? 3600,
		);
		$value['cache_expire'] = self::positiveInt(
			'session.options.cache_expire',
			$value['cache_expire'] ?? 3600,
		);

		return $value;
	}
}
