<?php

declare(strict_types=1);

namespace Duon\Cms;

use Duon\Container\Container;

use function Duon\Cms\Util\escape;

final class Icons implements Contract\Icons
{
	/** @var array<string, string> */
	private array $cache = [];

	public function __construct(
		private readonly Container $container,
		private readonly Config $config,
	) {}

	public function icon(
		string $id,
		?string $color = null,
		?string $class = null,
		?string $style = null,
	): string {
		$id = trim($id);

		if ($id === '') {
			return $this->failed('empty icon id');
		}

		$key = hash('xxh3', implode("\x1f", [
			$id,
			$this->clean($color) ?? '',
			$this->clean($class) ?? '',
			$this->clean($style) ?? '',
		]));

		if (array_key_exists($key, $this->cache)) {
			return $this->cache[$key];
		}

		$iconStyle = $this->mergeStyle($style, $color);

		foreach ($this->providers() as $provider) {
			if ($provider === $this) {
				continue;
			}

			$svg = $provider->icon($id);

			if ($svg === '') {
				continue;
			}

			return $this->cache[$key] = $this->injectAttributes($svg, $class, $iconStyle);
		}

		return $this->cache[$key] = $this->failed('icon not found: ' . $id);
	}

	/** @return iterable<IconsContract> */
	private function providers(): iterable
	{
		$tag = $this->container->tag(Contract\Icons::class);

		foreach ($tag->entries() as $id) {
			$provider = $tag->get($id);

			if ($provider instanceof Contract\Icons) {
				yield $provider;
			}
		}
	}

	private function failed(string $message): string
	{
		if (!$this->config->debug) {
			return '';
		}

		return sprintf('<!-- %s -->', escape($message));
	}

	private function mergeStyle(?string $style, ?string $color): ?string
	{
		$style = $this->clean($style);
		$color = $this->clean($color);

		if ($color === null) {
			return $style;
		}

		$colorStyle = 'color: ' . $color;

		if ($style === null) {
			return $colorStyle;
		}

		return rtrim($style, '; ') . '; ' . $colorStyle;
	}

	private function injectAttributes(string $svg, ?string $class, ?string $style): string
	{
		$class = $this->clean($class);
		$style = $this->clean($style);

		if ($class === null && $style === null) {
			return $svg;
		}

		if (!preg_match('/<svg\\b[^>]*>/i', $svg, $matches, PREG_OFFSET_CAPTURE)) {
			return $svg;
		}

		$tag = $matches[0][0];
		$offset = $matches[0][1];
		$length = strlen($tag);

		if ($class !== null) {
			$tag = $this->appendAttribute($tag, 'class', $class);
		}

		if ($style !== null) {
			$tag = $this->appendAttribute($tag, 'style', $style);
		}

		return substr_replace($svg, $tag, $offset, $length);
	}

	private function appendAttribute(string $tag, string $name, string $value): string
	{
		$pattern = sprintf('/\\s%s\\s*=\\s*(?:"([^"]*)"|\'([^\']*)\')/i', preg_quote($name, '/'));

		if (preg_match($pattern, $tag, $matches) === 1) {
			$current = $matches[1] !== '' ? $matches[1] : $matches[2];
			$merged = $name === 'style'
				? $this->joinStyles($current, $value)
				: trim($current . ' ' . $value);
			$replacement = sprintf(' %s="%s"', $name, escape($merged));

			return (string) preg_replace($pattern, $replacement, $tag, 1);
		}

		$injection = sprintf(' %s="%s"', $name, escape($value));
		$closer = str_ends_with($tag, '/>') ? '/>' : '>';
		$base = substr($tag, 0, -strlen($closer));

		return $base . $injection . $closer;
	}

	private function joinStyles(string $base, string $append): string
	{
		$base = trim($base);
		$append = trim($append);

		if ($base === '') {
			return $append;
		}

		if ($append === '') {
			return $base;
		}

		return rtrim($base, '; ') . '; ' . ltrim($append, '; ');
	}

	private function clean(?string $value): ?string
	{
		if ($value === null) {
			return null;
		}

		$value = trim($value);

		return $value === '' ? null : $value;
	}
}
