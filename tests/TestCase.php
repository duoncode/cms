<?php

declare(strict_types=1);

namespace Duon\Cms\Tests;

use Duon\Cms\Config;
use Duon\Cms\Locales;
use Duon\Cms\Tests\Setup\C;
use Duon\Core\Factory;
use Duon\Core\Factory\Laminas;
use Duon\Core\Request;
use Duon\Quma\Connection;
use Duon\Quma\Database;
use Duon\Registry\Registry;
use PDO;
use PHPUnit\Framework\TestCase as BaseTestCase;
use Psr\Http\Message\ServerRequestInterface as PsrServerRequest;
use RuntimeException;
use ValueError;

/**
 * @internal
 *
 * @coversNothing
 */
class TestCase extends BaseTestCase
{
	protected static bool $dbInitialized = false;
	protected static ?Connection $sharedConnection = null;
	protected ?Database $testDb = null;
	protected bool $useTransactions = false;

	public static function setUpBeforeClass(): void
	{
		parent::setUpBeforeClass();

		if (!self::$dbInitialized) {
			self::initializeTestDatabase();
			self::$dbInitialized = true;
		}
	}

	protected static function initializeTestDatabase(): void
	{
		// Create shared connection for migration check
		self::$sharedConnection = new Connection(
			'pgsql:host=localhost;dbname=duoncms;user=duoncms;password=duoncms',
			self::root() . '/db/sql',
			self::root() . '/db/migrations',
			fetchMode: PDO::FETCH_ASSOC,
			print: false,
		);

		$db = new Database(self::$sharedConnection);

		// Check if migrations table exists
		$tableExists = $db->execute(
			"SELECT EXISTS (
				SELECT FROM information_schema.tables
				WHERE table_schema = 'public'
				AND table_name = 'migrations'
			) as exists",
		)->one()['exists'] ?? false;

		if (!$tableExists) {
			echo "\n⚠ Test database not initialized. Run: ./run recreate-db && ./run migrate --apply\n\n";

			throw new RuntimeException(
				'Test database not initialized. Run: ./run recreate-db && ./run migrate --apply',
			);
		}

		// Check if cms schema exists (indicates migrations have been run)
		$schemaExists = $db->execute(
			"SELECT EXISTS (
				SELECT FROM information_schema.schemata
				WHERE schema_name = 'cms'
			) as exists",
		)->one()['exists'] ?? false;

		if (!$schemaExists) {
			echo "\n⚠ Migrations not applied. Run: ./run migrate --apply\n\n";

			throw new RuntimeException(
				'Migrations not applied to test database. Run: ./run migrate --apply',
			);
		}
	}

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

		// Begin transaction if this test uses them
		if ($this->useTransactions) {
			$this->testDb = new Database($this->conn());
			$this->testDb->begin();
		}
	}

	protected static function root(): string
	{
		return dirname(__DIR__);
	}

	public function throws(string $exception, ?string $message = null): void
	{
		$this->expectException($exception);

		if ($message) {
			$this->expectExceptionMessage($message);
		}
	}

	protected function tearDown(): void
	{
		// Rollback transaction if this test used them
		if ($this->useTransactions && $this->testDb !== null) {
			$this->testDb->rollback();
			$this->testDb = null;
		}

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
			'pgsql:host=localhost;dbname=duoncms;user=duoncms;password=duoncms',
			self::root() . '/db/sql',
			self::root() . '/db/migrations',
			fetchMode: PDO::FETCH_ASSOC,
			print: false,
		);
	}

	public function db(): Database
	{
		// If using transactions, return the same database instance
		if ($this->useTransactions && $this->testDb !== null) {
			return $this->testDb;
		}

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
		$registry = new Registry();

		// Register test Node classes for fixture types
		$registry->tag(\Duon\Cms\Node\Node::class)
			->add('test-page', \Duon\Cms\Tests\Integration\Fixtures\Node\TestPage::class);
		$registry->tag(\Duon\Cms\Node\Node::class)
			->add('test-article', \Duon\Cms\Tests\Integration\Fixtures\Node\TestArticle::class);
		$registry->tag(\Duon\Cms\Node\Node::class)
			->add('test-home', \Duon\Cms\Tests\Integration\Fixtures\Node\TestHome::class);
		$registry->tag(\Duon\Cms\Node\Node::class)
			->add('test-block', \Duon\Cms\Tests\Integration\Fixtures\Node\TestBlock::class);
		$registry->tag(\Duon\Cms\Node\Node::class)
			->add('test-widget', \Duon\Cms\Tests\Integration\Fixtures\Node\TestWidget::class);
		$registry->tag(\Duon\Cms\Node\Node::class)
			->add('test-document', \Duon\Cms\Tests\Integration\Fixtures\Node\TestDocument::class);
		$registry->tag(\Duon\Cms\Node\Node::class)
			->add('test-media-document', \Duon\Cms\Tests\Integration\Fixtures\Node\TestMediaDocument::class);

		// Register dynamically created test types (reuse TestPage for all page types)
		$registry->tag(\Duon\Cms\Node\Node::class)
			->add('ordered-test-page', \Duon\Cms\Tests\Integration\Fixtures\Node\TestPage::class);
		$registry->tag(\Duon\Cms\Node\Node::class)
			->add('limit-test-page', \Duon\Cms\Tests\Integration\Fixtures\Node\TestPage::class);
		$registry->tag(\Duon\Cms\Node\Node::class)
			->add('hidden-test-page', \Duon\Cms\Tests\Integration\Fixtures\Node\TestPage::class);

		return $registry;
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

	/**
	 * Load SQL fixture files into the test database.
	 *
	 * @param string ...$fixtures Fixture names (without .sql extension)
	 */
	protected function loadFixtures(string ...$fixtures): void
	{
		$db = $this->db();

		foreach ($fixtures as $fixture) {
			$path = self::root() . "/tests/Integration/Fixtures/data/{$fixture}.sql";

			if (!file_exists($path)) {
				throw new RuntimeException("Fixture file not found: {$path}");
			}

			$sql = file_get_contents($path);
			$db->execute($sql)->run();
		}
	}

	/**
	 * Create a test content type.
	 *
	 * @return int The type ID
	 */
	protected function createTestType(string $handle, string $kind = 'page'): int
	{
		$sql = "INSERT INTO cms.types (handle, kind)
				VALUES (:handle, :kind)
				RETURNING type";

		return $this->db()->execute($sql, [
			'handle' => $handle,
			'kind' => $kind,
		])->one()['type'];
	}

	/**
	 * Create a test node.
	 *
	 * @param array $data Node data (uid, type, content, etc.)
	 * @return int The node ID
	 */
	protected function createTestNode(array $data): int
	{
		$defaults = [
			'uid' => uniqid('test-'),
			'parent' => null,
			'published' => true,
			'hidden' => false,
			'locked' => false,
			'creator' => 1, // System user
			'editor' => 1,
			'created' => 'now()',
			'changed' => 'now()',
			'content' => '{}',
		];

		$data = array_merge($defaults, $data);

		// Convert content array to JSON if needed
		if (is_array($data['content'])) {
			$data['content'] = json_encode($data['content']);
		}

		$sql = "INSERT INTO cms.nodes (uid, parent, published, hidden, locked, type, creator, editor, created, changed, content)
				VALUES (:uid, :parent, :published, :hidden, :locked, :type, :creator, :editor, :created, :changed, :content::jsonb)
				RETURNING node";

		return $this->db()->execute($sql, $data)->one()['node'];
	}

	/**
	 * Create a test user.
	 *
	 * @return int The user ID
	 */
	protected function createTestUser(array $data): int
	{
		$defaults = [
			'uid' => uniqid('user-'),
			'email' => 'test@example.com',
			'full_name' => 'Test User',
			'display_name' => 'Test',
			'pwhash' => password_hash('password', PASSWORD_ARGON2ID),
			'role' => 4, // Editor role
		];

		$data = array_merge($defaults, $data);

		$sql = "INSERT INTO cms.users (uid, email, full_name, display_name, pwhash, role)
				VALUES (:uid, :email, :full_name, :display_name, :pwhash, :role)
				RETURNING \"user\"";

		return $this->db()->execute($sql, $data)->one()['user'];
	}
}
