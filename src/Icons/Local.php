<?php

declare(strict_types=1);

namespace Duon\Cms\Icons;

use Duon\Cms\Contract;

final class Local implements Contract\Icons
{
	/** @var list<string> */
	private array $paths;

	/** @param array<array-key, mixed> $paths */
	public function __construct(array $paths)
	{
		$this->paths = $this->normalizePaths($paths);
	}

	public function icon(
		string $id,
		?string $color = null,
		?string $class = null,
		?string $style = null,
	): string {
		$parts = $this->split($id);

		if ($parts === null) {
			return '';
		}

		foreach ($this->paths as $path) {
			$file = $this->file($path, $parts['prefix'], $parts['name']);

			if ($file === null) {
				continue;
			}

			$svg = file_get_contents($file);

			if (is_string($svg) && $this->isSvg($svg)) {
				return $svg;
			}
		}

		return '';
	}

	/**
	 * @return array{prefix: ?string, name: string}|null
	 */
	private function split(string $id): ?array
	{
		$id = trim($id);

		if (!preg_match(
			'/^(?:(?<prefix>[a-z0-9]+(?:[-_][a-z0-9]+)*):)?(?<name>[a-z0-9]+(?:[-_][a-z0-9]+)*)$/i',
			$id,
			$matches,
		)) {
			return null;
		}

		$name = strtolower((string) $matches['name']);

		if ($name === '') {
			return null;
		}

		$prefix = $matches['prefix'] ?? null;
		$prefix = is_string($prefix) && $prefix !== '' ? strtolower($prefix) : null;

		return ['prefix' => $prefix, 'name' => $name];
	}

	/**
	 * @param array<array-key, mixed> $paths
	 * @return list<string>
	 */
	private function normalizePaths(array $paths): array
	{
		$result = [];

		foreach ($paths as $path) {
			if (!is_string($path)) {
				continue;
			}

			$path = trim($path);

			if ($path === '') {
				continue;
			}

			$real = realpath($path);

			if ($real !== false && is_dir($real)) {
				$result[] = $real;
			}
		}

		return array_values(array_unique($result));
	}

	private function file(string $path, ?string $prefix, string $name): ?string
	{
		$file = $prefix === null
			? $path . DIRECTORY_SEPARATOR . $name . '.svg'
			: $path . DIRECTORY_SEPARATOR . $prefix . DIRECTORY_SEPARATOR . $name . '.svg';
		$resolved = realpath($file);

		if ($resolved === false || !is_file($resolved)) {
			return null;
		}

		$root = rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

		if (!str_starts_with($resolved, $root)) {
			return null;
		}

		return $resolved;
	}

	private function isSvg(string $svg): bool
	{
		return str_starts_with(ltrim($svg), '<svg');
	}
}
