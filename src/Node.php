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
use Conia\Core\Field\Field;
use Conia\Core\Finder;
use Conia\Core\Locale;
use Conia\Core\Schema\NodeSchemaFactory;
use Conia\Core\Value\Value;
use Conia\Quma\Database;
use Throwable;

abstract class Node
{
    use InitializesFields;

    public readonly Request $request;
    public readonly Config $config;
    protected static string $name = ''; // The public name of the node type
    protected static string $route = '/{slug}'; // The route pattern of node instances
    protected static string $slug = ''; // The slug which is used to address the node type in the panel
    protected static string $template = '';
    protected static array $permissions = [
        'read' => 'everyone',
        'create' => 'authenticated',
        'change' => 'authenticated',
        'delete' => 'authenticated',
    ];
    protected readonly Database $db;
    protected readonly Registry $registry;

    final public function __construct(
        protected readonly Context $context,
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

    final public static function fromForm(
        Context $context,
        Finder $find,
        array $data,
    ): static {
        return new static($context, $find, $data);
    }

    final public function getValue(string $fieldName): ?Value
    {
        $field = null;
        $field = $this->{$fieldName};
        $value = $field->value();

        if (is_null($field)) {
            $type = $this::class;

            throw new NoSuchField("The field '{$fieldName}' does not exist on node with type '{$type}'.");
        }

        if ($value->isset()) {
            return $value;
        }

        return null;
    }

    final public function getField(string $fieldName): Field
    {
        return $this->{$fieldName};
    }

    public function meta(string $fieldName): mixed
    {
        return $this->data[$fieldName];
    }

    public function data(): array
    {
        static $result = null;

        if ($result === null) {
            $result = $this->data;
            $content = [];

            // Fill the field's value with missing keys from the structure and fix type
            foreach ($this->fieldNames as $fieldName) {
                $field = $this->{$fieldName};
                $structure = $field->structure();
                $content[$fieldName] = array_merge($structure, $result['content'][$fieldName] ?? []);
                $content[$fieldName]['type'] = $structure['type'];
            }

            $result['content'] = $content;
        }

        return $result;
    }

    public function blueprint(): array
    {
        $result = [];

        foreach ($this->fieldNames as $fieldName) {
            $field = $this->{$fieldName};
            $result[$fieldName] = $field->structure();
        }

        return [
            'title' => _('Neues Dokument:') . ' ' . $this->name(),
            'fields' => $this->fields(),
            'data' => [
                'uid' => nanoid(),
                'published' => false,
                'hidden' => false,
                'locked' => false,
                'content' => $result,
            ],
        ];
    }

    /**
     * Is called after self::initFields.
     *
     * Can be used to make adjustments the already initialized fields
     */
    public function init(): void
    {
    }

    public static function className(): string
    {
        return basename(str_replace('\\', '/', static::class));
    }

    /**
     * Should return the general title of the node.
     *
     * Shown in the admin interface. But can also be used in the frontend.
     */
    abstract public function title(): string;

    public static function name(): string
    {
        return static::$name ?: static::className();
    }

    public static function slug(): string
    {
        return static::$slug ?: strtolower(static::className());
    }

    public static function template(): ?string
    {
        if (!empty(static::$template)) {
            return static::$template;
        }

        return static::slug();
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

    public function order(): ?array
    {
        return $this->fieldNames;
    }

    public function fieldNames(): array
    {
        return $this->fieldNames;
    }

    public function fields(): array
    {
        $fields = [];
        $orderedFields = $this->order();
        $missingFields = array_diff($this->fieldNames, $orderedFields);
        $allFields = array_merge($orderedFields, $missingFields);

        foreach ($allFields as $fieldName) {
            $fields[] = $this->{$fieldName}->asArray();
        }

        return $fields;
    }

    public function response(): array|Response
    {
        $request = $this->request;

        return match ($request->method()) {
            'GET' => $this->read(),
            'POST' => $this->create(),
            'PUT' => $this->change(),
            'DELETE' => $this->delete(),
            default => throw new HttpBadRequest(),
        };
    }

    public function read(): array|Response
    {
        if ($this->request->header('Accept') === 'application/json') {
            $data = $this->data();
            unset($data['classname']);

            return [
                'title' => $this->title(),
                'uid' => $this->meta('uid'),
                'fields' => $this->fields(),
                'data' => $data,
            ];
        }

        return $this->render();
    }

    public function change(): array
    {
        if ($this->request->header('Content-Type') !== 'application/json') {
            throw new HttpBadRequest();
        }

        $data = $this->request->json();
        $this->validate($data);

        $this->db->nodes->change([
            'uid' => $data['uid'],
            'hidden' => $data['hidden'],
            'published' => $data['published'],
            'locked' => $data['published'],
            'content' => json_encode($data['content']),
            'editor' => $this->request->get('session')->authenticatedUserId(),
        ])->run();

        return [
            'success' => true,
            'error' => false,
        ];
    }

    public function delete(): array
    {
        if ($this->request->header('Accept') !== 'application/json') {
            throw new HttpBadRequest();
        }

        $this->db->nodes->delete([
            'uid' => $this->uid(),
        ])->run();

        return [
            'success' => true,
            'error' => false,
        ];
    }

    public function create(): array|Response
    {
        $request = $this->request;

        return match ($request->header('Content-Type')) {
            'application/x-www-form-urlencoded' => $this->formPost($request->form()),
            'application/json' => $this->jsonPost($request->json()),
        };
    }

    protected function validate(array $data): bool
    {
        $factory = new NodeSchemaFactory($this, $this->config->locales());
        $schema = $factory->create();
        $result = $schema->validate($data['content']);

        if (!$result) {
            $exception = new HttpBadRequest('Bitte alle Pflichtfelder ausfüllen');
            $exception->setPayload($schema->errors());

            throw $exception;
        }

        return $result;
    }

    protected function jsonPost(array $body): array
    {
        error_log(print_r($body, true));

        return ['success' => true];
    }

    protected function formPost(?array $body): Response
    {
        throw new HttpBadRequest();
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
