<?php

declare(strict_types=1);

namespace Conia\Core;

use Conia\Chuck\Request;
use Conia\Core\Exception\NoSuchField;
use Conia\Core\Exception\ValueError;
use Conia\Core\Field\Attr\Description;
use Conia\Core\Field\Attr\Height;
use Conia\Core\Field\Attr\Label;
use Conia\Core\Field\Attr\MultiLang;
use Conia\Core\Field\Attr\Required;
use Conia\Core\Field\Attr\Width;
use Conia\Core\Field\Field;
use Conia\Core\Value\Value;
use Generator;
use ReflectionClass;
use ReflectionProperty;

abstract class Type
{
    protected static string $name = '';
    protected static string $template = '';
    protected static array $permissions = [];
    protected static int $columns = 12;

    protected array $list = [];

    final public function __construct(
        protected readonly Request $request,
        protected readonly array $data,
    ) {
        $this->initFields();
    }

    final public function __get(string $field): Value
    {
        if (!isset($this->{$field})) {
            $type = $this::class;

            throw new NoSuchField("The field '{$field}' does not exist on Type '{$type}'.");
        }

        $content = $this->data['content'][$field] ?? [];
        $field = $this->{$field};

        return $field->value($this, $this->request, $content);
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

        return static::className();
    }

    public function uid(): string
    {
        return $this->data['uid'];
    }

    public static function template(): ?string
    {
        if (!empty(static::$template)) {
            return static::$template;
        }

        return static::className();
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

                case MultiLang::class:
                    $field->multilang(true);

                    break;


                case Label::class:
                    $field->label($attr->newInstance()->label);

                    break;

                case Description::class:
                    $field->description($attr->newInstance()->description);

                    break;

                case Width::class:
                    $field->width($attr->newInstance()->width);

                    break;

                case Height::class:
                    $field->height($attr->newInstance()->height);

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
