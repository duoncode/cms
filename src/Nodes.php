<?php

declare(strict_types=1);

namespace Conia\Core;

use RuntimeException;
use ValueError;

class Nodes
{
    protected array $types = [];

    public function add(string $class, ?string $label, ?string $description)
    {
        if (!is_subclass_of($class, Node::class)) {
            throw new ValueError('A type must be a subclass of ' . Node::class);
        }

        $name = $class::name();

        if (array_key_exists($name, $this->types)) {
            throw new RuntimeException("Node '{$name}' already exists");
        }

        $this->types[$name] = [
            'name' => $name,
            'class' => $class,
            'label' => empty($label) ? $class::className() : $label,
            'description' => $description,
        ];
    }

    public function get(string $name): Node
    {
        return $this->types[$name];
    }
}
