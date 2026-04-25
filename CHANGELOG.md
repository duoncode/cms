# Changelog

## [Unreleased](https://github.com/duoncode/boiler/compare/0.1.0-beta.2...HEAD)

### Breaking changes

This release removes the `Node` / `Page` / `Block` / `Document` inheritance hierarchy and dedicated node kind modeling. Content types are now plain PHP classes with metadata attributes, and behavior is derived from route/render conventions.

- **Removed** abstract base classes `Node`, `Page`, `Block`, `Document`.
- **Removed** the `RendersTemplate` trait.
- **Removed** the dead `Fulltext` class.
- **Removed** `#[Page]`, `#[Block]`, `#[Document]` metadata attributes.
- **Changed** routability/rendering semantics to use `#[Route]` and `#[Render]` conventions (renderer fallback remains node handle).
- **Changed** finder facade class from `Duon\Cms\Finder\Finder` to `Duon\Cms\Cms`.
- **Changed** plugin class from `Duon\Cms\Cms` to `Duon\Cms\Plugin`.
- **Changed** CMS configuration ownership. Regular apps can use the new `Duon\Cms\App` facade; advanced manual bootstraps pass `Duon\Cms\Config` to `new Plugin($config)` instead of passing it to `Duon\Core\App`. `Duon\Cms\Config` no longer implements the removed core config interfaces.
- **Changed** `Duon\Cms\Config` construction to `new Config(string $root, array $settings = [])`. App name, debug mode, environment, app secret, public path, frontend sessions, and database DSN now live in `app.name`, `app.debug`, `app.env`, `app.secret`, `path.public`, `session.enabled`, and `db.dsn` settings instead of constructor arguments or public properties. `path.public` defaults to `$root . '/public'`. `session.enabled` reads `CMS_SESSION`. `app.secret` reads `CMS_SECRET`. `db.dsn` reads `CMS_DB_DSN`, falling back to deprecated `CMS_DSN`. `app.name` is not validated or normalized.
- **Changed** `Duon\Cms\Boiler\Error\Handler` to read debug/env/error settings from `Duon\Cms\Config`; its constructor now accepts config, factory, and logger.
- **Changed** frontend session middleware configuration from `sessionEnabled` constructor arguments on `Duon\Cms\App` and `Duon\Cms\Plugin` to the `session.enabled` setting.
- **Changed** `Duon\Cms\App::create()` to accept a root path plus an optional settings array, create `Duon\Cms\Config` internally, and expose the config as public `$app->config`.
- **Changed** template embedding API from `find->block(...)` to `cms->render(...)`.
- **Changed** all Field and Value classes to depend on the `FieldOwner` interface instead of the `Node` class.
- **Changed** node type-hints throughout the framework from `Node` to `object`.
- **Changed** the `Node::class` registry tag to `Plugin::NODE_TAG` constant.

### Added

- `#[Name]`, `#[Handle]`, `#[Route]`, `#[Render]`, `#[Title]`, `#[FieldOrder]`, `#[Deletable]`, `#[Permission]` attributes for node metadata.
- `Title`, `HasInit`, `HandlesFormPost`, `ProvidesRenderContext` interfaces for behavioral hooks.
- `FieldOwner` interface decoupling fields from the node hierarchy.
- `FieldHydrator` service for external field initialization (two-phase init).
- `NodeFactory` service for creating node instances via `duon/wire` autowiring.
- `NodeSerializer` service for node data serialization, blueprint generation, and title resolution.
- `NodeManager` service for node CRUD operations (save, create, delete).
- `PathManager` service for URL path persistence.
- `ViewRenderer` service for rendering nodes to templates.
- `NodeProxy` for template-friendly access to node fields and methods.
- `NodeMeta` caching facade and `Meta` reflection reader for node metadata.
- `NodeFieldOwner` adapter bridging `FieldOwner` with `Context` and uid.
- `Plugin::NODE_TAG` constant replacing the old `Node::class` registry tag.
- Bundled Boiler renderer and error integration under the existing `Duon\Cms\Boiler` namespace. `duon/cms` now requires `duon/boiler` directly, so applications no longer need the separate `duon/cms-boiler` package.
- Default Boiler `view` renderer registration using the new `path.views` config key, which defaults to `/views` relative to `path.root`.
- `Duon\Cms\App` facade for regular CMS applications. It wraps the core app and CMS plugin, forwards the common app and CMS configuration APIs, installs the default error middleware, and adds the CMS catchall route during `run()`.
- Built-in fallback templates for Boiler error pages plus `error.*` config keys for enabling/disabling the default handler, replacing the error renderer, configuring error views, and toggling Whoops debug pages.
- Root-based `Config` initialization that loads `.env` with `vlucas/phpdotenv`, sets default `app.name` to `duoncms`, and exposes `Config::requireEnv(...)` for required environment variables.

### Migration guide

Replace inheritance with attributes and implement interfaces as needed:

```php
// Before
class Article extends Page
{
    public Text $title;

    public function title(): string
    {
        return $this->title?->value()->unwrap() ?? '';
    }
}

// After
#[Name('Article'), Route('/{title}')]
class Article implements Title
{
    #[Label('Title'), Translate]
    public Text $title;

    public function title(): string
    {
        return $this->title?->value()->unwrap() ?? '';
    }
}
```

Use the CMS app facade for regular application bootstrapping:

```php
use Duon\Cms\App;

$root = dirname(__DIR__);
$app = App::create($root, [
    'app.name' => 'cms',
    'path.public' => $root . '/public',
]);
$app->section('Content')->collection(\App\Cms\Collection\Pages::class);
$app->node(\App\Cms\Node\HomePage::class);
$app->run();
```

When bootstrapping manually with `duon/core`, pass the CMS config to the CMS plugin instead of the core app.

Constructor dependencies are autowired from the Registry via `duon/wire`:

```php
#[Name('Department'), Route('/{title}')]
final class Department implements Title
{
    public function __construct(
        protected readonly Request $request,
        protected readonly Cms $cms,
    ) {}

    #[Label('Title'), Required, Translate]
    public Text $title;

    public function title(): string
    {
        return $this->title?->value()->unwrap() ?? '';
    }
}
```

## [0.1.0-beta.2](https://github.com/duoncode/cms/releases/tag/0.1.0-beta.2) (2026-02-01)

Codename: Benjamin

- Added support for installing the panel from tagged releases (including alpha/beta/rc), instead of only nightly builds.
- Improved the `install-panel` command output and removed the unnecessary Quma command dependency.
- Updated the panel release workflow to support prerelease tag patterns and manual (retroactive) runs.

## [0.1.0-beta.1](https://github.com/duoncode/cms/releases/tag/0.1.0-beta.1) (2026-02-01)

Initial release - Codename: Sabine
