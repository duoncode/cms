<?php

declare(strict_types=1);

namespace Conia\Core;

abstract class Block
{
    use SetsInfo;

    protected array $list = [];
    protected array $fields = [];

    final public function __construct(
        ?string $label,
        ?string $name,
        ?string $description = null,
        public readonly bool $repeatable = false,
    ) {
        $this->setInfo($label, $name, $description);
    }

    abstract public function init(): void;

    public function render(): string
    {
        return '';
    }

    protected function layout(): array
    {
        return [];
    }

    protected function form(): array
    {
        $this->init();

        return [
            'type' => 'block',
            'label' => $this->label,
            'repeatable' => $this->repeatable,
            'description' => $this->description,
            'layout' => $this->layout(),
        ];
    }
}
