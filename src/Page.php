<?php

declare(strict_types=1);

namespace Conia;

use Conia\Field\Field;


class Page
{
    protected array $fields;
    protected array $blocks;

    public function __construct(
        protected string $name,
        protected string|array $label,
        protected bool $singleton = false,
        protected bool $extensible = true,
        protected array $permissions = [],
    ) {
    }

    public function addField(Field $field)
    {
        $this->fields[] = $field;
    }

    public function getFields(): array
    {
        return $this->fields;
    }

    public function addBlock(string $name)
    {
        $this->block[] = $name;
    }

    public function getBlocks(): array
    {
        return $this->blocks;
    }
}
