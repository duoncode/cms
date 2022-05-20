<?php

declare(strict_types=1);

namespace Conia;

use \ReflectionClass;

abstract class Block implements Data
{
    use Meta;

    protected array $list = [];
    protected array $fields = [];

    public final function __construct(
        public readonly bool $repeatable = false,
    ) {
        $reflector = new ReflectionClass($this::class);
        $this->setMeta($reflector);
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
