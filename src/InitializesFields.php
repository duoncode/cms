<?php

declare(strict_types=1);

namespace Conia\Core;

use Conia\Core\Exception\RuntimeException;
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
use Conia\Core\Value\ValueContext;
use ReflectionClass;
use ReflectionProperty;

trait InitializesFields
{
    protected function initFields(): void
    {
        $rc = new ReflectionClass(static::class);
        $isFieldSet = $this instanceof FieldSet;

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

            if (is_subclass_of($fieldType, FieldSet::class)) {
                if ($isFieldSet) {
                    throw new RuntimeException('FieldSets cannot contain FieldSets');
                }

                $this->{$name} = $this->initFieldSet($property);
            }
        }

        $this->init();
    }

    protected function initField(ReflectionProperty $property, string $fieldType): Field
    {
        $fieldName = $property->getName();
        $content = $this->data['content'][$fieldName] ?? [];
        $field = new $fieldType($fieldName, $this, new ValueContext($fieldName, $content));

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
}
