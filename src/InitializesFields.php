<?php

declare(strict_types=1);

namespace Conia\Core;

use Conia\Core\Exception\RuntimeException;
use Conia\Core\Field\Attr\Description;
use Conia\Core\Field\Attr\Fulltext;
use Conia\Core\Field\Attr\Label;
use Conia\Core\Field\Attr\Multiple;
use Conia\Core\Field\Attr\Options;
use Conia\Core\Field\Attr\Required;
use Conia\Core\Field\Attr\Rows;
use Conia\Core\Field\Attr\Translate;
use Conia\Core\Field\Attr\TranslateFile;
use Conia\Core\Field\Attr\Validate;
use Conia\Core\Field\Attr\Width;
use Conia\Core\Field\Field;
use Conia\Core\Value\ValueContext;
use ReflectionClass;
use ReflectionProperty;

trait InitializesFields
{
    protected array $fieldNames = [];

    protected function initFields(): void
    {
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
                case Rows::class:
                    $field->rows($attr->newInstance()->rows);
                    break;
                case Multiple::class:
                    $field->multiple(true);
                    break;
                case Validate::class:
                    $field->validate(...$attr->newInstance()->validators);
                    break;
                case Options::class:
                    $field->options($attr->newInstance()->options);
                    break;
                case TranslateFile::class:
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
