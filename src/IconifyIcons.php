<?php

declare(strict_types=1);

namespace Duon\Cms;

use Closure;
use Duon\Cms\Contract\Icons;

use function Duon\Cms\Util\escape;

final class IconifyIcons implements Icons
{
	/** @var Closure(string, int, string): ?string */
	private readonly Closure $fetch;

	public function __construct(
		private readonly Config $config,
		?callable $fetch = null,
	) {
		$this->fetch = $fetch === null
			? Closure::fromCallable([$this, 'request'])
			: Closure::fromCallable($fetch);
	}

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

		$parts = $this->splitId($id);

		if ($parts === null) {
			return $this->failed('invalid icon id: ' . $id);
		}

		$svg = $this->loadSvg($id, $parts['prefix'], $parts['name']);

		if ($svg === null) {
			return $this->failed('icon not found: ' . $id);
		}

		return $this->injectAttributes($svg, $class, $this->mergeStyle($style, $color));
	}

	/**
	 * @return array{prefix: string, name: string}|null
	 */
	private function splitId(string $id): ?array
	{
		if (!preg_match('/^(?<prefix>[a-z0-9]+(?:[-_][a-z0-9]+)*):(?<name>[a-z0-9]+(?:[-_][a-z0-9]+)*)$/i', $id, $matches)) {
			return null;
		}

		$prefix = strtolower((string) $matches['prefix']);
		$name = strtolower((string) $matches['name']);

		if ($prefix === '' || $name === '') {
			return null;
		}

		return ['prefix' => $prefix, 'name' => $name];
	}

	private function loadSvg(string $id, string $prefix, string $name): ?string
	{
		$file = $this->cacheFile($id);

		if ($file !== null && is_file($file)) {
			$cached = file_get_contents($file);

			if (is_string($cached) && $this->isSvg($cached)) {
				return $cached;
			}
		}

		$url = $this->iconUrl($prefix, $name);
		$timeout = max((int) $this->config->get('icons.timeout', 5), 1);
		$userAgent = trim((string) $this->config->get('icons.user_agent', 'duon/cms'));
		$userAgent = $userAgent === '' ? 'duon/cms' : $userAgent;
		$svg = ($this->fetch)($url, $timeout, $userAgent);

		if (!is_string($svg) || !$this->isSvg($svg)) {
			return null;
		}

		if ($file !== null) {
			$this->store($file, $svg);
		}

		return $svg;
	}

	private function iconUrl(string $prefix, string $name): string
	{
		$base = trim((string) $this->config->get('icons.base_url', 'https://api.iconify.design'));
		$base = $base === '' ? 'https://api.iconify.design' : rtrim($base, '/');

		return sprintf('%s/%s/%s.svg', $base, rawurlencode($prefix), rawurlencode($name));
	}

	private function request(string $url, int $timeout, string $userAgent): ?string
	{
		$handle = curl_init($url);

		if ($handle === false) {
			return null;
		}

		curl_setopt_array($handle, [
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_CONNECTTIMEOUT => $timeout,
			CURLOPT_TIMEOUT => $timeout,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_USERAGENT => $userAgent,
			CURLOPT_HTTPHEADER => ['Accept: image/svg+xml,text/plain;q=0.9,*/*;q=0.1'],
		]);
		$body = curl_exec($handle);
		$status = (int) curl_getinfo($handle, CURLINFO_RESPONSE_CODE);
		$error = curl_errno($handle);
		curl_close($handle);

		if (!is_string($body) || $error !== 0 || $status < 200 || $status >= 300) {
			return null;
		}

		return $body;
	}

	private function cacheFile(string $id): ?string
	{
		$dir = $this->cacheDir();

		if ($dir === null) {
			return null;
		}

		return $dir . DIRECTORY_SEPARATOR . hash('xxh3', $id) . '.svg';
	}

	private function cacheDir(): ?string
	{
		$publicDir = realpath((string) $this->config->get('path.public'));

		if ($publicDir === false) {
			return null;
		}

		$cacheDir = trim((string) $this->config->get('path.cache', '/cache'));

		if ($cacheDir === '' || str_contains(str_replace('\\', '/', $cacheDir), '..')) {
			return null;
		}

		$target = rtrim($publicDir, '\\/')
			. DIRECTORY_SEPARATOR
			. ltrim($cacheDir, '\\/')
			. DIRECTORY_SEPARATOR
			. 'icons';

		if (!is_dir($target) && !mkdir($target, 0o755, true) && !is_dir($target)) {
			return null;
		}

		$resolved = realpath($target);

		if ($resolved === false || strncmp($resolved, $publicDir, strlen($publicDir)) !== 0) {
			return null;
		}

		return $resolved;
	}

	private function store(string $file, string $svg): void
	{
		$temp = $file . '.tmp.' . bin2hex(random_bytes(6));

		if (file_put_contents($temp, $svg, LOCK_EX) === false) {
			@unlink($temp);
			return;
		}

		if (!@rename($temp, $file)) {
			@unlink($temp);
		}
	}

	private function isSvg(string $svg): bool
	{
		return str_starts_with(ltrim($svg), '<svg');
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
