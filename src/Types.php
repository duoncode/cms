<?php

declare(strict_types=1);

namespace Conia;

use \ValueError;
use \RuntimeException;


class Types
{
    protected array $types = [];

    public function add(string $class, ?string $label, ?string $description)
    {
        if (!is_subclass_of($class, Type::class)) {
            throw new ValueError('A type must be a subclass of ' . Type::class);
        }

        $name = $class::name();

        if (array_key_exists($name, $this->types)) {
            throw new RuntimeException("Type '$name' already exists");
        }

        $this->types[$name] = [
            'name' => $name,
            'class' => $class,
            'label' => empty($label) ? $class::className() : $label,
            'description' => $description,
        ];
    }
}
