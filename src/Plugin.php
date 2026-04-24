<?php

declare(strict_types=1);

namespace Duon\Cms;

use Duon\Cms\Boiler\Renderer as BoilerRenderer;
use Duon\Cms\Contract\Icons as IconsContract;
use Duon\Cms\Exception\RuntimeException;
use Duon\Cms\Icons\Iconify;
use Duon\Cms\Icons\Local;
use Duon\Cms\Node\Types;
use Duon\Container\Container;
use Duon\Container\Entry;
use Duon\Core\App;
use Duon\Core\Factory;
use Duon\Core\Plugin as CorePlugin;
use Duon\Quma\Connection;
use Duon\Quma\Database;
use Duon\Router\Route;
use PDO;

class Plugin implements CorePlugin
{
	public const string NODE_TAG = 'duon.cms.node';

	protected readonly Config $config;
	protected readonly Factory $factory;
	protected readonly Container $container;
	protected readonly Database $db;
	protected readonly Connection $connection;
	protected readonly Routes $routes;
	protected readonly Types $types;

	/** @property array<Entry> */
	protected array $renderers = [];

	protected readonly Navigation $navigation;
	protected array $nodes = [];

	/** @var list<class-string<IconsContract>|IconsContract> */
	protected array $iconProviders = [];

	public function __construct(
		protected readonly bool $sessionEnabled = false,
		?Types $types = null,
	) {
		$this->types = $types ?? new Types();
		$this->navigation = new Navigation();
		$this->iconProviders = [
			Local::class,
			Iconify::class,
		];
	}

	public function load(App $app): void
	{
		$this->factory = $app->factory();
		$this->container = $app->container();
		$this->config = $app->config();

		$this->addPanelRenderer();

		$this->collect();
		$this->database();

		$this->container->add($this->container::class, $this->container);
		$this->container->add(Connection::class, $this->connection);
		$this->container->add(Database::class, $this->db);
		$this->container->add(Factory::class, $this->factory);
		$this->container->add(Types::class, $this->types);
		$this->container->add(IconsContract::class, Icons::class);

		$this->routes = new Routes($app->config(), $this->db, $this->factory, $this->sessionEnabled);
		$this->routes->add($app);
	}

	protected function collect(): void
	{
		$this->container->add(Navigation::class, $this->navigation)->value();

		foreach ($this->navigation->refs() as $name => $collection) {
			$this->container
				->tag(Collection::class)
				->add($name, $collection::class);
		}

		foreach ($this->nodes as $name => $node) {
			$this->container
				->tag(self::NODE_TAG)
				->add($name, $node);
		}

		foreach ($this->renderers as $entry) {
			$this->container
				->tag(Renderer::class)
				->addEntry($entry);
		}

		foreach ($this->iconProviders as $index => $provider) {
			$providerName = is_string($provider) ? $provider : $provider::class;
			$this->container
				->tag(IconsContract::class)
				->add(
					sprintf('icons.%d.%s', $index, str_replace('\\\\', '.', $providerName)),
					$provider,
				);
		}
	}

	public function section(string $name): Section
	{
		return $this->navigation->section($name);
	}

	/** @param class-string<Collection> $class */
	public function collection(string $class): Collection
	{
		return $this->navigation->collection($class);
	}

	/**
	 * @param class-string<IconsContract>|IconsContract $icons
	 */
	public function icons(string|IconsContract $icons, bool $replace = false): void
	{
		if (is_string($icons) && !is_a($icons, IconsContract::class, true)) {
			throw new RuntimeException('Icons providers must implement ' . IconsContract::class);
		}

		if ($replace) {
			$this->iconProviders = [];
		}

		array_unshift($this->iconProviders, $icons);
	}

	public function navigation(): Navigation
	{
		return $this->navigation;
	}

	public function meta(): Types
	{
		return $this->types;
	}

	public function node(string $class): void
	{
		$handle = (string) $this->types->get($class, 'handle');

		if (isset($this->nodes[$handle])) {
			throw new RuntimeException('Duplicate node handle: ' . $handle);
		}

		$this->nodes[$handle] = $class;
	}

	protected function database(): void
	{
		if (!$this->config) {
			throw new RuntimeException('No config given');
		}

		$root = dirname(__DIR__);
		$sqlConfig = $this->config->get('db.sql', []);
		$sqlPaths = [];

		if ($sqlConfig) {
			$sqlPaths = is_array($sqlConfig) ? $sqlConfig : [$sqlConfig];
		}

		$sql = array_merge(
			[$root . '/db/sql'],
			$sqlPaths,
		);
		$migrations = $this->config->get('db.migrations', []);
		$migrationPaths = [];

		if ($migrations) {
			$migrationPaths = is_array($migrations) ? $migrations : [$migrations];
		}

		$namespacedMigrations = [];
		$namespacedMigrations['install'] = [$root . '/db/migrations/install'];
		$namespacedMigrations['default'] = array_merge(
			$migrationPaths,
			[$root . '/db/migrations/update'],
		);

		$this->connection = new Connection(
			$this->config->get('db.dsn'),
			$sql,
			$namespacedMigrations,
			fetchMode: PDO::FETCH_ASSOC,
			options: $this->config->get('db.options'),
			print: $this->config->get('db.print'),
		);
		$this->db = new Database($this->connection);
	}

	/**
	 * Catchall for page url paths.
	 *
	 * Should be the last one
	 */
	public function catchallRoute(): Route
	{
		return $this->routes->catchallRoute();
	}

	public function renderer(string $id, string $class): Entry
	{
		if (is_a($class, Renderer::class, true)) {
			$entry = new Entry($id, $class);
			$this->renderers[] = $entry;

			return $entry;
		}

		throw new RuntimeException('Renderers must imlement the `Duon\\Cms\\Renderer` interface');
	}

	protected function synchronizeNodes(): void
	{
		if (!$this->db->sys->isInitialized()->one()['value']) {
			return;
		}

		$types = array_map(
			static fn($record) => $record['handle'],
			$this->db
				->nodes
				->types()
				->all(),
		);

		foreach ($this->nodes as $handle => $class) {
			if (in_array($handle, $types, true)) {
				continue;
			}

			$this->db->nodes->addType([
				'handle' => $handle,
			])->run();
		}
	}

	protected function addPanelRenderer(): void
	{
		$root = dirname(__DIR__);
		$this->renderer('panel', BoilerRenderer::class)->args(
			dirs: "{$root}/panel/views",
			autoescape: true,
		);
	}
}
