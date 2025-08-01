<?php

declare(strict_types=1);

namespace Duon\Cms\Tests\Setup;

use Duon\Cms\Config;
use Duon\Cms\Locales;
use Duon\Core\Factory;
use Duon\Core\Factory\Laminas;
use Duon\Core\Request;
use Duon\Quma\Connection;
use Duon\Quma\Database;
use Duon\Registry\Registry;
use PDO;
use PHPUnit\Framework\TestCase as BaseTestCase;
use Psr\Http\Message\ServerRequestInterface as PsrServerRequest;
use ValueError;

/**
 * @internal
 *
 * @coversNothing
 */
class TestCase extends BaseTestCase
{
	protected function setUp(): void
	{
		parent::setUp();

		$_SERVER['HTTP_ACCEPT'] = 'text/html,application/xhtml+xml,text/plain';
		$_SERVER['HTTP_ACCEPT_ENCODING'] = 'gzip, deflate, br';
		$_SERVER['HTTP_ACCEPT_LANGUAGE'] = 'en-US,de;q=0.7,en;q=0.3';
		$_SERVER['HTTP_HOST'] = 'www.example.com';
		$_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:108.0) ' .
			'Gecko/20100101 Firefox/108.0';
		$_SERVER['REQUEST_METHOD'] = 'GET';
		$_SERVER['REQUEST_URI'] = '/';
		$_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
	}

	public function throws(string $exception, string $message = null): void
	{
		$this->expectException($exception);

		if ($message) {
			$this->expectExceptionMessage($message);
		}
	}

	protected function tearDown(): void
	{
		unset(
			$_SERVER['CONTENT_TYPE'],
			$_SERVER['HTTPS'],
			$_SERVER['HTTP_ACCEPT'],
			$_SERVER['HTTP_ACCEPT_ENCODING'],
			$_SERVER['HTTP_ACCEPT_LANGUAGE'],
			$_SERVER['HTTP_HOST'],
			$_SERVER['HTTP_USER_AGENT'],
			$_SERVER['HTTP_X_FORWARDED_PROTO'],
			$_SERVER['QUERY_STRING'],
			$_SERVER['REQUEST_METHOD'],
			$_SERVER['REQUEST_SCHEME'],
			$_SERVER['REQUEST_URI'],
			$_SERVER['SERVER_PROTOCOL'],
			$_SERVER['argv'],
		);

		global $_GET;
		$_GET = [];
		global $_POST;
		$_POST = [];
		global $_FILES;
		$_FILES = [];
		global $_COOKIE;
		$_COOKIE = [];
	}

	public function setMethod(string $method): void
	{
		$_SERVER['REQUEST_METHOD'] = strtoupper($method);
	}

	public function setContentType(string $contentType): void
	{
		$_SERVER['HTTP_CONTENT_TYPE'] = $contentType;
	}

	public function setRequestUri(string $url): void
	{
		if (substr($url, 0, 1) === '/') {
			$_SERVER['REQUEST_URI'] = $url;
		} else {
			$_SERVER['REQUEST_URI'] = "/{$url}";
		}
	}

	public function setQueryString(string $qs): void
	{
		$_SERVER['QUERY_STRING'] = $qs;
	}

	public function config(array $settings = [], bool $debug = false): Config
	{
		$config = new Config('duon', debug: $debug, settings: $settings);

		return $config;
	}

	public function conn(): Connection
	{
		return new Connection(
			'pgsql:host=localhost;dbname=duon_cms_test_db;user=duon_cms_test_user;password=duon_cms_test_password',
			C::root() . '/db/sql',
			C::root() . '/db/migrations',
			fetchMode: PDO::FETCH_ASSOC,
			print: false,
		);
	}

	public function db(): Database
	{
		return new Database($this->conn());
	}

	public function request(
		?string $method = null,
		?string $url = null,
	): Request {
		if ($method) {
			$this->setMethod($method);
		}

		if ($url) {
			$this->setRequestUri($url);
		}

		return new Request($this->psrRequest());
	}

	public function registry(): Registry
	{
		return new Registry();
	}

	public function set(string $method, array $values): void
	{
		global $_GET;
		global $_POST;
		global $_COOKIE;

		foreach ($values as $key => $value) {
			if (strtoupper($method) === 'GET') {
				$_GET[$key] = $value;

				continue;
			}

			if (strtoupper($method) === 'POST') {
				$_POST[$key] = $value;

				continue;
			}

			if (strtoupper($method) === 'COOKIE') {
				$_COOKIE[$key] = $value;
			} else {
				throw new ValueError("Invalid method '{$method}'");
			}
		}
	}

	public function psrRequest(string $localeId = 'en'): PsrServerRequest
	{
		$request = $this->factory()->serverRequest();
		$locales = new Locales();
		$locales->add(
			'en',
			title: 'English',
			domains: ['www.example.com'],
		);
		$locales->add(
			'de',
			title: 'Deutsch',
			domains: ['www.example.de'],
			fallback: 'en',
		);
		$locales->add(
			'it',
			domains: ['www.example.it'],
			title: 'Italiano',
			fallback: 'en',
		);

		return $request
			->withAttribute('locales', $locales)
			->withAttribute('locale', $locales->get($localeId));
	}

	public function factory(): Factory
	{
		return new Laminas();
	}

	public function fullTrim(string $text): string
	{
		return trim(
			preg_replace(
				'/> </',
				'><',
				preg_replace(
					'/\s+/',
					' ',
					preg_replace('/\n/', '', $text),
				),
			),
		);
	}
}
