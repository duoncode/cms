<?php

declare(strict_types=1);

namespace Conia;


abstract class Block
{
    use SetsInfo;

    protected array $list = [];
    protected array $fields = [];

    public final function __construct(
        ?string $label,
        ?string $name,
        ?string $description = null,
        public readonly bool $repeatable = false,
    ) {
        $this->setInfo($label, $name, $description);
    }

    abstract public function init(): void;

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

    public function render(): string
    {
        return '';
    }
}
