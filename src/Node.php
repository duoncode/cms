<?php

declare(strict_types=1);

namespace Conia\Core;

use Conia\Chuck\Exception\HttpBadRequest;
use Conia\Chuck\Factory;
use Conia\Chuck\Registry;
use Conia\Chuck\Renderer\Render;
use Conia\Chuck\Request;
use Conia\Chuck\Response;
use Conia\Core\Config;
use Conia\Core\Exception\NoSuchField;
use Conia\Core\Exception\RuntimeException;
use Conia\Core\Field\Attr;
use Conia\Core\Field\Field;
use Conia\Core\Finder;
use Conia\Core\Locale;
use Conia\Core\Schema\NodeSchemaFactory;
use Conia\Core\Value\Value;
use Conia\Core\Value\ValueContext;
use Conia\Quma\Database;
use ReflectionClass;
use ReflectionProperty;
use ReflectionUnionType;
use Throwable;

abstract class Node
{
    public readonly Request $request;
    public readonly Config $config;
    protected static string $name = ''; // The public name of the node type
    protected static string|array $route = ''; // The route pattern of node instances
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
    protected array $fieldNames = [];

    final public function __construct(
        protected readonly Context $context,
        protected readonly Finder $find,
        protected ?array $data = null,
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

    final public function setData(array $data): static
    {
        $this->data = $data;
        $this->initFields();

        return $this;
    }

    final public function getValue(string $fieldName): ?Value
    {
        $field = $this->{$fieldName};

        if (is_null($field)) {
            $type = $this::class;

            throw new NoSuchField("The field '{$fieldName}' does not exist on node with type '{$type}'.");
        }

        $value = $field->value();

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

    public function blueprint(array $values = []): array
    {
        $result = [];

        foreach ($this->fieldNames as $fieldName) {
            $field = $this->{$fieldName};
            $result[$fieldName] = $field->structure($values[$fieldName] ?? null);
        }

        return [
            'title' => _('Neues Dokument:') . ' ' . $this->name(),
            'fields' => $this->fields(),
            'data' => [
                'uid' => nanoid(),
                'route' => static::$route,
                'published' => false,
                'hidden' => false,
                'locked' => false,
                'content' => $result,
                'paths' => [],
            ],
        ];
    }

    public function fillData(array $data): array
    {
        return $this->blueprint($data)['data'];
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
        $paths = $this->data['paths'];

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
        return $this->saveRequest();
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
            'application/json' => $this->saveRequest(),
        };
    }

    public function save(array $data): array
    {
        $this->validate($data);

        // TODO: check permissions
        try {
            $editor = $this->request->get('session')->authenticatedUserId();
        } catch (\Throwable) {
            $editor = 1; // The System user
        }

        try {
            $db = $this->db;
            $db->begin();
            $node = $db->nodes->save([
                'uid' => $data['uid'],
                'hidden' => $data['hidden'],
                'published' => $data['published'],
                'locked' => $data['published'],
                'type' => $this->slug(),
                'content' => json_encode($data['content']),
                'editor' => $editor,
            ])->one()['node'];

            $defaultLocale = $this->config->locales->getDefault();
            $defaultPath = trim($data['paths'][$defaultLocale->id] ?? '');

            // if (!$defaultPath) {
            //     throw new RuntimeException(_("Die URL für die Hauptsprache {$defaultLocale->title} muss gesetzt sein"));
            // }

            error_log(print_r($data['paths'], true));
            // foreach ($data['paths'] as $locale => $path) {
            //     if ($path) {
            //         $db->nodes->deleteInactivePath(['path' => $path])->run();
            //     }
            // }

            $currentPaths = $db->nodes->getPaths(['node' => $node])->all();
            error_log(print_r($currentPaths, true));
            $db->commit();
        } catch (Throwable $e) {
            $db->rollback();

            throw new RuntimeException(_('Fehler beim Speichern: ') . $e->getMessage(), (int)$e->getCode(), $e);
        }

        return [
            'success' => true,
            'error' => false,
        ];
    }

    protected function validate(array $data): bool
    {
        $factory = new NodeSchemaFactory($this, $this->config->locales());
        $schema = $factory->create();
        $result = $schema->validate($data['content']);

        if (!$result) {
            $exception = new HttpBadRequest(_('Unvollständige oder fehlerhafte Daten'));
            $exception->setPayload($schema->errors());

            throw $exception;
        }

        return $result;
    }

    protected function saveRequest(): array
    {
        if ($this->request->header('Content-Type') !== 'application/json') {
            throw new HttpBadRequest();
        }

        $data = $this->request->json();

        return $this->save($data);
    }

    /**
     * Usually overwritten in the app. Used to handle form posts
     * from the fronend.
     */
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

    protected function getResponse(): Response
    {
        $factory = $this->registry->get(Factory::class);

        return Response::fromFactory($factory);
    }

    protected function initFields(): void
    {
        $this->fieldNames = [];

        $rc = new ReflectionClass(static::class);

        foreach ($rc->getProperties() as $property) {
            $name = $property->getName();

            if (!$property->hasType()) {
                continue;
            }

            $fieldType = $property->getType();

            if ($fieldType::class === ReflectionUnionType::class) {
                continue;
            }

            $fieldTypeName = $fieldType->getName();

            if (is_subclass_of($fieldTypeName, Field::class)) {
                if (isset($this->{$name})) {
                    continue;
                }

                $this->{$name} = $this->initField($property, $fieldTypeName);

                $this->fieldNames[] = $name;
            }
        }

        $this->init();
    }

    protected function initField(ReflectionProperty $property, string $fieldType): Field
    {
        $fieldName = $property->getName();
        $content = $this->data['content'][$fieldName] ?? [];
        $node = $this instanceof Node ? $this : $this->node;
        $field = new $fieldType($fieldName, $node, new ValueContext($fieldName, $content));

        foreach ($property->getAttributes() as $attr) {
            switch ($attr->getName()) {
                case Attr\Required::class:
                    $field->required(true);
                    break;
                case Attr\Translate::class:
                    $field->translate(true);
                    break;
                case Attr\Label::class:
                    $field->label($attr->newInstance()->label);
                    break;
                case Attr\Description::class:
                    $field->description($attr->newInstance()->description);
                    break;
                case Attr\Fulltext::class:
                    $field->fulltext($attr->newInstance()->fulltextWeight);
                    break;
                case Attr\Width::class:
                    $field->width($attr->newInstance()->width);
                    break;
                case Attr\Rows::class:
                    $field->rows($attr->newInstance()->rows);
                    break;
                case Attr\Multiple::class:
                    $field->multiple(true);
                    break;
                case Attr\Validate::class:
                    $field->validate(...$attr->newInstance()->validators);
                    break;
                case Attr\Options::class:
                    $field->options($attr->newInstance()->options);
                    break;
                case Attr\DefaultVal::class:
                    $field->default($attr->newInstance()->get());
                    break;
                case Attr\TranslateFile::class:
                    if (!($field instanceof \Conia\Core\Field\Image || !$field instanceof \Conia\Core\Field\File)) {
                        throw new RuntimeException('Cannot apply attribute Multiple to ' . $field::class);
                    }

                    $field->translateFile(true);

                    break;
            }
        }

        return $field;
    }
}
