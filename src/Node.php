<?php

declare(strict_types=1);

namespace Conia\Core;

use Conia\Chuck\Exception\HttpBadRequest;
use Conia\Chuck\Registry;
use Conia\Chuck\Renderer\Render;
use Conia\Chuck\Request;
use Conia\Chuck\Response;
use Conia\Core\Config;
use Conia\Core\Exception\NoSuchField;
use Conia\Core\Exception\RuntimeException;
use Conia\Core\Exception\ValueError;
use Conia\Core\Finder;
use Conia\Core\Locale;
use Conia\Core\Value\Value;
use Conia\Quma\Database;
use Throwable;

abstract class Node
{
    use InitializesFields;

    public readonly Request $request;
    public readonly Config $config;
    protected readonly Database $db;
    protected readonly Registry $registry;
    protected static string $name = '';
    protected static string $template = '';
    protected static array $permissions = [];
    protected static int $columns = 12;
    protected static array $fieldSets = [];

    final public function __construct(
        Context $context,
        protected readonly Finder $find,
        protected readonly array $data,
    ) {
        $this->initFields();

        $this->db = $context->db;
        $this->request = $context->request;
        $this->config = $context->config;
        $this->registry = $context->registry;
    }

    final public function __get(string $fieldName): ?Value
    {
        return $this->getValue($fieldName);
    }

    // TODO: should be optimized as this could result
    //       in many ::get() calls
    final public function __isset(string $fieldName): bool
    {
        if (isset($this->{$fieldName})) {
            return $this->{$fieldName}->value()->isset();
        }

        return false;
    }

    final public function getValue(string $fieldName): ?Value
    {
        $field = null;

        if (isset($this->{$fieldName})) {
            $type = $this::class;

            $field = $this->{$fieldName};
            $value = $field->value();
        } else {
            foreach ($this->fieldSets as $fieldSet) {
                if (isset($fieldSet->{$fieldName})) {
                    $field = $fieldSet->{$fieldName};
                    $value = $field->value();
                    break;
                }
            }
        }

        if (is_null($field)) {
            throw new NoSuchField("The field '{$fieldName}' does not exist on node with type '{$type}'.");
        }

        if ($value->isset()) {
            return $value;
        }

        return null;
    }

    /**
     * Is called after self::initFields.
     *
     * Can be used to make adjustments the already initialized fields
     */
    public function init(): void
    {
    }

    /**
     * Should return the general title of the node.
     *
     * Shown in the admin interface. But can also be used in the frontend.
     */
    abstract public function title(): string;

    public static function columns(): int
    {
        if (static::$columns < 12 || static::$columns > 25) {
            throw new ValueError('The value of $columns must be >= 12 and <= 25');
        }

        return static::$columns;
    }

    public static function className(): string
    {
        return basename(str_replace('\\', '/', static::class));
    }

    public static function name(): ?string
    {
        if (!empty(static::$name)) {
            return static::$name;
        }

        return strtolower(static::className());
    }

    public function uid(): string
    {
        return $this->data['uid'];
    }

    public function path(Locale $locale = null): string
    {
        $paths = json_decode($this->data['paths'], true);

        if (!$locale) {
            $locale = $this->request->get('locale');
        }

        while ($locale) {
            if (isset($paths[$locale->id])) {
                return $paths[$locale->id];
            }

            $locale = $locale->fallback();
        }

        throw new RuntimeException('No url path found');
    }

    public static function template(): ?string
    {
        if (!empty(static::$template)) {
            return static::$template;
        }

        return static::name();
    }

    public function response(): Response
    {
        $request = $this->request;

        return match ($request->method()) {
            'GET' => $this->get(),
            'POST' => $this->post(),
            'PUT' => $this->put(),
            'DELETE' => $this->delete(),
            default => throw new HttpBadRequest(),
        };
    }

    public function get(): Response
    {
        // Create a JSON response if the URL ends with .json
        if ($this->request->get('isJson', false)) {
            return Response::fromFactory($this->factory)->json($this->json());
        }

        return $this->render();
    }

    public function post(): Response
    {
        throw new HttpBadRequest();
    }

    public function put(): Response
    {
        throw new HttpBadRequest();
    }

    public function delete(): Response
    {
        throw new HttpBadRequest();
    }

    public function json(array $context = []): array
    {
        $data = $this->data;

        unset($data['classname']);

        $content = [
            'content' => $this->getJsonContent(),
        ];

        return array_merge($data, $content, $context);
    }

    protected function render(array $context = []): Response
    {
        $context = array_merge([
            'page' => $this,
            'find' => $this->find,
            'locale' => $this->request->get('locale'),
            'locales' => $this->config->locales,
        ], $context);

        try {
            $render = new Render('template', self::template());

            return $render->response($this->registry, $context);
        } catch (Throwable $e) {
            if ($this->config->debug()) {
                throw $e;
            }

            throw new HttpBadRequest();
        }
    }

    protected function locale(): Locale
    {
        return $this->request->get('locale');
    }
}
