<?php

declare(strict_types=1);

namespace Conia;

use \ReflectionClass;

abstract class Block implements Data
{
    use SetsMeta;
    use SetsInfo;

    protected array $list = [];
    protected array $fields = [];

    public final function __construct(
        string $label,
        ?string $description = null,
        public readonly bool $repeatable = false,
    ) {
        $reflector = new ReflectionClass($this::class);
        $this->setMeta($reflector);
        $this->setInfo($label, $description);
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
