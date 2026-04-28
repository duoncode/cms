# Duon Content Management Framework

> **Note**: This library is under active development, some of the listed features are still experimental and subject to change. Large parts of the documentation are missing.

## Bootstrapping

Use `Duon\Cms\App` for regular CMS applications. It creates the config, core app, and CMS plugin internally, installs the default error handler, adds CMS routes, and registers the catchall route when you call `run()`.

```php
use Duon\Cms\App;
use Duon\Cms\Locales;

$app = App::create(dirname(__DIR__), [
    'app.name' => 'mycms',
    'session.enabled' => true,
]);

$locales = new Locales();
$locales->add('en', title: 'English', pgDict: 'english');
$app->load($locales);

$app->section('Content')->collection(\App\Cms\Collection\Pages::class);
$app->node(\App\Cms\Node\HomePage::class);

$app->run();
```

The CMS app exposes the common CMS configuration API (`section()`, `collection()`, `node()`, `renderer()`, `icons()`) and the common core app API (`load()`, `middleware()`, `get()`, `post()`, `routes()`, `run()`). Use `core()` or `plugin()` only when you need the lower-level APIs directly.

## Defining content types

Content types (nodes) are plain PHP classes annotated with attributes. There is no base class to extend. Dependencies are autowired from the Registry via `duon/wire`.

```php
use Duon\Cms\Field\Text;
use Duon\Cms\Field\Grid;
use Duon\Cms\Field\Image;
use Duon\Cms\Cms;
use Duon\Cms\Schema\Label;
use Duon\Cms\Schema\Required;
use Duon\Cms\Schema\Route;
use Duon\Cms\Schema\Translate;
use Duon\Cms\Node\Contract\Title;
use Duon\Core\Request;

#[Label('Department'), Route('/{title}')]
final class Department implements Title
{
    public function __construct(
        protected readonly Request $request,
        protected readonly Cms $cms,
    ) {}

    #[Label('Title'), Required, Translate]
    public Text $title;

    #[Label('Content'), Translate]
    public Grid $content;

    #[Label('Image')]
    public Image $clipart;

    public function title(): string
    {
        return $this->title?->value()->unwrap() ?? '';
    }
}
```

### Derived behavior

| Signal                        | Behavior                                   |
| ----------------------------- | ------------------------------------------ |
| `#[Route('...')]` is present  | Node is routable and has URL path settings |
| `#[Render('...')]` is present | Explicit renderer id is used               |
| `#[Render]` is absent         | Node handle is used as renderer id         |

### Metadata attributes

| Attribute | Purpose |
| --- | --- |
| `#[Label('...')]` | Human-readable display name |
| `#[Handle('...')]` | URL-safe identifier (auto-derived if omitted) |
| `#[Route('...')]` | URL pattern for routable nodes |
| `#[Render('...')]` | Template name override |
| `#[Title('...')]` | Field name to use as title |
| `#[FieldOrder('...')]` | Admin panel field order |
| `#[Deletable(false)]` | Prevent deletion in admin panel (default: `true`) |
| `#[Children(Foo::class, ...)]` | Allowed direct child node types for hierarchy-enabled collection lists |

### Hierarchy lists in panel

- Set `showChildren` to `true` on a collection to switch its list endpoint to hierarchy mode.
- Root requests (`GET /panel/api/collection/{collection}`) return nodes with no parent.
- Child requests (`GET /panel/api/collection/{collection}?parent=<uid>`) return direct children for that parent uid.
- Row payload includes `hasChildren`, `childBlueprints`, and `parent`.
- Child create options are derived from `#[Children(...)]` declarations.

### Behavioral interfaces

| Interface | Method | Purpose |
| --- | --- | --- |
| `Title` | `title(): string` | Computed title (takes precedence over `#[Title]`) |
| `HasInit` | `init(): void` | Post-hydration initialization hook |
| `HandlesFormPost` | `formPost(?array $body): Response` | Frontend form submission handling |
| `ProvidesRenderContext` | `renderContext(): array` | Extra template variables |

### Rendering by uid

Render a node by uid from templates with the neutral cms API:

```php
<?= $cms->render('some-node-uid') ?>
```

## Boiler rendering

`duon/cms` bundles the Boiler renderer under the existing `Duon\Cms\Boiler` namespace and registers it as the default `view` renderer. You do not need to require `duon/cms-boiler` separately or register a renderer for the common case.

By default, views are loaded from `{path.root}{path.views}`. `path.root` is the project root passed to `App::create()`. `path.views` defaults to `/views` and can be overridden in CMS config:

```php
use Duon\Cms\App;

$app = App::create(dirname(__DIR__), [
    'path.views' => '/views',
]);
```

To replace the default renderer or pass custom Boiler arguments, register a `view` renderer before the app boots:

```php
use Duon\Cms\App;
use Duon\Cms\Boiler\Renderer;

$app = App::create(dirname(__DIR__), [
    'app.name' => 'mycms',
]);
$app->renderer('view', Renderer::class)->args(
    dirs: __DIR__ . '/custom-views',
    defaults: ['siteName' => 'My Site'],
);
```

`Duon\Cms\App` installs the bundled error handler by default. Error pages use a dedicated Boiler renderer, so replacing the CMS `view` renderer does not affect error rendering. Project templates named `http-error.php` and `http-server-error.php` in `{path.root}{path.views}` override the built-in fallback templates. Set `error.enabled` to `false` if you want to install custom PSR-15 error middleware yourself.

For advanced integrations, the bundled error integration remains available as `Duon\Cms\Boiler\Error\Handler`. Pass a `Duon\Cms\Config`, core factory, and logger when you create it manually.

## Settings

`App::create()` creates `Config` from the root path and settings array and exposes it as `$app->config`. `Config` loads `.env` from the root path with `Dotenv::safeLoad()`. Use `requireEnv()` when an application wants to fail fast for required environment variables. Because settings are evaluated before `Config` is created, set extra environment-derived values with `$app->config->set(...)` after construction. `app.name` is not validated or normalized, so keep it stable and safe for app-specific identifiers.

```php
use Duon\Cms\App;

$app = App::create(dirname(__DIR__), [
    'app.name' => 'mycms',
]);

$app->config->requireEnv(['DATABASE_URL', 'APP_SECRET']);
```

```text
'app.name' => env('APP_NAME', 'duoncms'), // App name used by sessions and helpers
'app.debug' => env('APP_DEBUG', false), // Debug mode from the loaded environment
'app.env' => env('APP_ENV', ''),      // App environment from the loaded environment
'app.secret' => env('APP_SECRET', null), // App secret from the loaded environment
'path.root' => $root,                 // Required project root passed to Config
'path.public' => $root . '/public',   // Public document root
'path.views' => '/views',             // View directory relative to path.root
'db.dsn' => env('DATABASE_URL', null), // Database DSN
'session.enabled' => env('SITE_SESSION_ENABLED', false), // Add session middleware to frontend routes
'session.options.cookie_lifetime' => (int) env('SESSION_COOKIE_LIFETIME', '0'), // Browser session cookie
'session.options.cookie_secure' => env('SESSION_COOKIE_SECURE', true), // Send session cookies only over HTTPS
'session.options.gc_maxlifetime' => (int) env('SESSION_IDLE_TIMEOUT', '3600'), // Session idle timeout
'error.enabled' => true,              // Install default error middleware in Duon\Cms\App
'error.renderer' => null,             // Optional Duon\Error\Renderer replacement
'error.views' => null,                // Error template directory; defaults to path.views
'error.whoops' => true,               // Use filp/whoops in debug mode when installed
'session.authcookie' => '<app>_auth', // Name of the auth cookie
'session.expires' => 60 * 60 * 24,    // One day by default
```

### Admin panel theming

You can style the admin panel through `panel.theme` in your CMS config. Set it to a single stylesheet path (`string`) or multiple stylesheet paths (`string[]`). The panel links those CSS files and reads theme overrides from `--theme-*` variables that mirror the built-in token names, such as `--theme-color-*`, `--theme-space-*`, `--theme-radius-*`, `--theme-font-*`, and `--theme-sidebar-width`.

```php
return [
	'panel.theme' => [
		'/assets/cms/theme/base.css',
		'/assets/cms/theme/brand.css',
	],
];
```

Test database:

```bash
echo "duoncms" | createuser --pwprompt --createdb duoncms
createdb --owner duoncms duoncms
```

System Requirements:

```bash
apt install php8.5 php8.5-pgsql php8.5-gd php8.5-xml php8.5-intl php8.5-curl
```

For development

```bash
apt install php8.5 php8.5-xdebug
```

macOS/homebrew:

```bash
brew install php php-intl
```

## License

This project is licensed under the [MIT license](LICENSE.md).
