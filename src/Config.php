<?php

declare(strict_types=1);

namespace Duon\Cms;

use Dotenv\Dotenv;
use Duon\Core\Exception\OutOfBoundsException;
use Duon\Core\Exception\ValueError;

use function Duon\Core\env;

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
		$this->settings = array_merge([
			'app.name' => 'duoncms',
			'app.debug' => env('CMS_DEBUG', false),
			'app.env' => env('CMS_ENV', ''),
			'app.secret' => env('CMS_SECRET', null),
			'path.root' => $root,
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
			'db.dsn' => env('CMS_DB_DSN', env('CMS_DSN', null)),
			'db.sql' => [],
			'db.migrations' => [],
			'db.print' => false,
			'db.options' => [],
			'session.options' => [
				'cookie_httponly' => true,
				'cookie_lifetime' => 0,
				'gc_maxlifetime' => 3600,
			],
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
			'slug.transliterate' => [
				'À' => 'A',
				'Á' => 'A',
				'Â' => 'A',
				'Ã' => 'A',
				'Ä' => 'Ae',
				'Å' => 'Aa',
				'Ā' => 'A',
				'Ă' => 'A',
				'Ą' => 'A',
				'à' => 'a',
				'á' => 'a',
				'â' => 'a',
				'ã' => 'a',
				'ä' => 'ae',
				'å' => 'aa',
				'ā' => 'a',
				'ă' => 'a',
				'ą' => 'a',
				'Æ' => 'AE',
				'æ' => 'ae',
				'Ç' => 'C',
				'Ć' => 'C',
				'Ĉ' => 'C',
				'Ċ' => 'C',
				'Č' => 'C',
				'ç' => 'c',
				'ć' => 'c',
				'ĉ' => 'c',
				'ċ' => 'c',
				'č' => 'c',
				'Ð' => 'D',
				'Ď' => 'D',
				'Đ' => 'D',
				'ð' => 'd',
				'ď' => 'd',
				'đ' => 'd',
				'È' => 'E',
				'É' => 'E',
				'Ê' => 'E',
				'Ë' => 'E',
				'Ē' => 'E',
				'Ĕ' => 'E',
				'Ė' => 'E',
				'Ę' => 'E',
				'Ě' => 'E',
				'è' => 'e',
				'é' => 'e',
				'ê' => 'e',
				'ë' => 'e',
				'ē' => 'e',
				'ĕ' => 'e',
				'ė' => 'e',
				'ę' => 'e',
				'ě' => 'e',
				'Ĝ' => 'G',
				'Ğ' => 'G',
				'Ġ' => 'G',
				'Ģ' => 'G',
				'ĝ' => 'g',
				'ğ' => 'g',
				'ġ' => 'g',
				'ģ' => 'g',
				'Ĥ' => 'H',
				'Ħ' => 'H',
				'ĥ' => 'h',
				'ħ' => 'h',
				'Ì' => 'I',
				'Í' => 'I',
				'Î' => 'I',
				'Ï' => 'I',
				'Ĩ' => 'I',
				'Ī' => 'I',
				'Ĭ' => 'I',
				'Į' => 'I',
				'İ' => 'I',
				'ì' => 'i',
				'í' => 'i',
				'î' => 'i',
				'ï' => 'i',
				'ĩ' => 'i',
				'ī' => 'i',
				'ĭ' => 'i',
				'į' => 'i',
				'ı' => 'i',
				'Ĵ' => 'J',
				'ĵ' => 'j',
				'Ķ' => 'K',
				'ķ' => 'k',
				'ĸ' => 'k',
				'Ĺ' => 'L',
				'Ļ' => 'L',
				'Ľ' => 'L',
				'Ŀ' => 'L',
				'Ł' => 'L',
				'ĺ' => 'l',
				'ļ' => 'l',
				'ľ' => 'l',
				'ŀ' => 'l',
				'ł' => 'l',
				'Ñ' => 'N',
				'Ń' => 'N',
				'Ņ' => 'N',
				'Ň' => 'N',
				'Ŋ' => 'N',
				'ñ' => 'n',
				'ń' => 'n',
				'ņ' => 'n',
				'ň' => 'n',
				'ŉ' => 'n',
				'ŋ' => 'n',
				'Ò' => 'O',
				'Ó' => 'O',
				'Ô' => 'O',
				'Õ' => 'O',
				'Ö' => 'Oe',
				'Ø' => 'Oe',
				'Ō' => 'O',
				'Ŏ' => 'O',
				'Ő' => 'O',
				'ò' => 'o',
				'ó' => 'o',
				'ô' => 'o',
				'õ' => 'o',
				'ö' => 'oe',
				'ø' => 'oe',
				'ō' => 'o',
				'ŏ' => 'o',
				'ő' => 'o',
				'Œ' => 'OE',
				'œ' => 'oe',
				'Ŕ' => 'R',
				'Ŗ' => 'R',
				'Ř' => 'R',
				'ŕ' => 'r',
				'ŗ' => 'r',
				'ř' => 'r',
				'Ś' => 'S',
				'Ŝ' => 'S',
				'Ş' => 'S',
				'Š' => 'S',
				'ś' => 's',
				'ŝ' => 's',
				'ş' => 's',
				'š' => 's',
				'Ţ' => 'T',
				'Ť' => 'T',
				'Ŧ' => 'T',
				'ţ' => 't',
				'ť' => 't',
				'ŧ' => 't',
				'Ù' => 'U',
				'Ú' => 'U',
				'Û' => 'U',
				'Ü' => 'Ue',
				'Ũ' => 'U',
				'Ū' => 'U',
				'Ŭ' => 'U',
				'Ů' => 'U',
				'Ű' => 'U',
				'Ų' => 'U',
				'ù' => 'u',
				'ú' => 'u',
				'û' => 'u',
				'ü' => 'ue',
				'ũ' => 'u',
				'ū' => 'u',
				'ŭ' => 'u',
				'ů' => 'u',
				'ű' => 'u',
				'ų' => 'u',
				'Ŵ' => 'W',
				'ŵ' => 'w',
				'Ý' => 'Y',
				'Ŷ' => 'Y',
				'Ÿ' => 'Y',
				'ý' => 'y',
				'ŷ' => 'y',
				'ÿ' => 'y',
				'Ź' => 'Z',
				'Ż' => 'Z',
				'Ž' => 'Z',
				'ź' => 'z',
				'ż' => 'z',
				'ž' => 'z',
				'Þ' => 'Th',
				'þ' => 'th',
				'ß' => 'ss',
				'ẞ' => 'SS',
			],
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
}
