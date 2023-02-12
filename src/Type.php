<?php

declare(strict_types=1);

namespace Conia\Core;

use Conia\Chuck\Request;
use Conia\Core\Config;
use Conia\Core\Exception\NoSuchField;
use Conia\Core\Exception\RuntimeException;
use Conia\Core\Exception\ValueError;
use Conia\Core\Field\Attr\Description;
use Conia\Core\Field\Attr\Fulltext;
use Conia\Core\Field\Attr\Height;
use Conia\Core\Field\Attr\Label;
use Conia\Core\Field\Attr\Multiple;
use Conia\Core\Field\Attr\Required;
use Conia\Core\Field\Attr\Translate;
use Conia\Core\Field\Attr\TranslateImage;
use Conia\Core\Field\Attr\Width;
use Conia\Core\Field\Field;
use Conia\Core\Finder;
use Conia\Core\Value\Value;
use Conia\Core\Value\ValueContext;
use Conia\Quma\Database;
use Generator;
use ReflectionClass;
use ReflectionProperty;

abstract class Type
{
    public readonly Request $request;
    public readonly Config $config;
    protected readonly Database $db;
    protected static string $name = '';
    protected static string $template = '';
    protected static array $permissions = [];
    protected static int $columns = 12;
    protected array $list = [];

    final public function __construct(
        protected readonly Context $context,
        protected readonly Finder $find,
        protected readonly array $data,
    ) {
        $this->initFields();

        $this->db = $context->db;
        $this->request = $context->request;
        $this->config = $context->config;
    }

    final public function __get(string $fieldName): Value
    {
        return $this->get($fieldName);
    }

    final public function get(string $fieldName): Value
    {
        if (!isset($this->{$fieldName})) {
            $type = $this::class;

            throw new NoSuchField("The field '{$fieldName}' does not exist on Type '{$type}'.");
        }

        $content = $this->data['content'][$fieldName] ?? [];
        $field = $this->{$fieldName};

        return $field->value($this, new ValueContext($fieldName, $content));
    }

    public function init(): void
    {
        // can be overwritten
    }

    abstract public function title(): string;

    public function form(): Generator
    {
        foreach ($this->list as $field) {
            yield $this->fields[$field];
        }
    }

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

    public function json(): array
    {
        $data = $this->data;

        unset($data['classname']);

        $content = [
            'content' => $this->getJsonContent(),
        ];

        return array_merge($data, $content);
    }

    protected function initFields(): void
    {
        $this->init();

        $rc = new ReflectionClass(static::class);

        foreach ($rc->getProperties() as $property) {
            $name = $property->getName();

            if (!$property->hasType()) {
                continue;
            }

            $fieldType = $property->getType()->getName();

            if (is_subclass_of($fieldType, Field::class)) {
                if (isset($this->{$name})) {
                    continue;
                }

                $this->{$name} = $this->initField($property, $fieldType);
            }
        }
    }

    protected function initField(ReflectionProperty $property, string $fieldType): Field
    {
        $field = new $fieldType($property->getName());

        foreach ($property->getAttributes() as $attr) {
            switch ($attr->getName()) {
                case Required::class:
                    $field->required(true);
                    break;
                case Translate::class:
                    $field->translate(true);
                    break;
                case Label::class:
                    $field->label($attr->newInstance()->label);
                    break;
                case Description::class:
                    $field->description($attr->newInstance()->description);
                    break;
                case Fulltext::class:
                    $field->fulltext($attr->newInstance()->fulltextWeight);
                    break;
                case Width::class:
                    $field->width($attr->newInstance()->width);
                    break;
                case Height::class:
                    $field->height($attr->newInstance()->height);
                    break;
                case Multiple::class:
                    if (!$field instanceof \Conia\Core\Field\Image) {
                        throw new RuntimeException('Cannot apply attribute Multiple to ' . $field::class);
                    }

                    $field->multiple(true);

                    break;
                case TranslateImage::class:
                    if (!$field instanceof \Conia\Core\Field\Image) {
                        throw new RuntimeException('Cannot apply attribute Multiple to ' . $field::class);
                    }

                    $field->translateImage(true);

                    break;
            }
        }

        return $field;
    }

    protected function getJsonContent(): array
    {
        $result = [];

        foreach ($this->list as $field) {
            $result[$field] = $this->__get($field)->json();
        }

        return $result;
    }
}
