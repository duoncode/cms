<?php

declare(strict_types=1);

namespace Conia\Cms\Node;

use Conia\Chuck\Exception\HttpBadRequest;
use Conia\Chuck\Factory;
use Conia\Chuck\Registry;
use Conia\Chuck\Request;
use Conia\Chuck\Response;
use Conia\Cms\Config;
use Conia\Cms\Context;
use Conia\Cms\Exception\NoSuchField;
use Conia\Cms\Exception\RuntimeException;
use Conia\Cms\Field\Attr;
use Conia\Cms\Field\Field;
use Conia\Cms\Finder\Finder;
use Conia\Cms\Locale;
use Conia\Cms\Schema\NodeSchemaFactory;
use Conia\Cms\Value\Value;
use Conia\Cms\Value\ValueContext;
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
    protected static string $handle = ''; // Used also as slug to address the node type in the panel
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
            $result['deletable'] = $this->deletable();
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

        // TODO: We should improve the nodetype detection?
        $nodetype = 'document';

        if (is_subclass_of($this, Page::class)) {
            $nodetype = 'page';
        } else {
            if (is_subclass_of($this, Block::class)) {
                $nodetype = 'block';
            }
        }

        return [
            'title' => _('Neues Dokument:') . ' ' . $this->name(),
            'fields' => $this->fields(),
            'data' => [
                'uid' => nanoid(),
                'published' => false,
                'hidden' => false,
                'locked' => false,
                'deletable' => $this->deletable(),
                'content' => $result,
                'type' => static::handle(),
                'nodetype' => $nodetype,
                'paths' => [],
                'generatedPaths' => [],
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

    public static function handle(): string
    {
        return static::$handle ?:
            ltrim(
                strtolower(preg_replace(
                    '/[A-Z]([A-Z](?![a-z]))*/',
                    '-$0',
                    static::className()
                )),
                '-'
            );
    }

    public function uid(): string
    {
        return $this->data['uid'];
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
            $fields[] = $this->{$fieldName}->properties();
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

    /**
     * Called on GET request.
     */
    public function read(): array|Response
    {
        $data = $this->data();

        return [
            'title' => $this->title(),
            'uid' => $this->meta('uid'),
            'fields' => $this->fields(),
            'data' => $data,
        ];
    }

    /**
     * Called on PUT request.
     */
    public function change(): array
    {
        return $this->save($this->getRequestData());
    }

    /**
     * Called on DELETE request.
     */
    public function delete(): array
    {
        if ($this->request->header('Accept') !== 'application/json') {
            throw new HttpBadRequest();
        }

        $this->db->nodes->delete([
            'uid' => $this->uid(),
            'editor' => $this->request->get('session')->authenticatedUserId(),
        ])->run();

        return [
            'success' => true,
            'error' => false,
        ];
    }

    /**
     * Called on POST request.
     */
    public function create(): array|Response
    {
        $request = $this->request;

        return match ($request->header('Content-Type')) {
            // TODO: prepare form data and send it to save
            'application/x-www-form-urlencoded' => $this->formPost($request->form()),
            'application/json' => $this->save($this->getRequestData()),
        };
    }

    /**
     * Validates the data and persists it in the database.
     */
    public function save(array $data): array
    {
        $data = $this->validate($data);

        if ($data['locked']) {
            throw new HttpBadRequest(_('Das Dokument ist gesperrt'));
        }

        // TODO: check permissions
        try {
            $editor = $this->request->get('session')->authenticatedUserId();

            if (!$editor) {
                $editor = 1;
            }
        } catch (Throwable) {
            $editor = 1; // The System user
        }

        try {
            $db = $this->db;
            $db->begin();

            $this->persist($db, $data, $editor);

            $db->commit();
        } catch (Throwable $e) {
            $db->rollback();

            throw new RuntimeException(_('Fehler beim Speichern: ') . $e->getMessage(), (int)$e->getCode(), $e);
        }

        return [
            'success' => true,
            'uid' => $data['uid'],
        ];
    }

    protected function persist(Database $db, array $data, int $editor): void
    {
        $this->persistNode($db, $data, $editor);
    }

    protected function persistNode(Database $db, array $data, int $editor): int
    {
        return (int)$db->nodes->save([
            'uid' => $data['uid'],
            'hidden' => $data['hidden'],
            'published' => $data['published'],
            'locked' => $data['locked'],
            'type' => $this->handle(),
            'content' => json_encode($data['content']),
            'editor' => $editor,
        ])->one()['node'];
    }

    protected function validate(array $data): array
    {
        $factory = new NodeSchemaFactory($this, $this->config->locales());
        $schema = $factory->create();

        if (!$schema->validate($data)) {
            $exception = new HttpBadRequest(_('Unvollständige oder fehlerhafte Daten'));
            $exception->setPayload($schema->errors());

            throw $exception;
        }

        return $schema->values();
    }

    protected function getRequestData(): array
    {
        if ($this->request->header('Content-Type') !== 'application/json') {
            throw new HttpBadRequest();
        }

        return $this->request->json();
    }

    /**
     * Usually overwritten in the app. Used to handle form posts
     * from the frontend.
     */
    protected function formPost(?array $body): Response
    {
        throw new HttpBadRequest();
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
                    if (!($field instanceof \Conia\Cms\Field\Image || !$field instanceof \Conia\Cms\Field\File)) {
                        throw new RuntimeException('Cannot apply attribute Multiple to ' . $field::class);
                    }

                    $field->translateFile(true);

                    break;
                case Attr\Columns::class:
                    if (!$field instanceof \Conia\Cms\Field\Grid) {
                        throw new RuntimeException('Cannot apply attribute Columns to ' . $field::class);
                    }

                    $instance = $attr->newInstance();
                    $field->columns($instance->columns, $instance->minCellWidth);

                    break;
            }
        }

        return $field;
    }

    protected function deletable(): bool
    {
        return true;
    }
}
