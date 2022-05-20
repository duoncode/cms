<?php

declare(strict_types=1);

namespace Conia;

use \RuntimeException;


abstract class Field implements Data
{
    public readonly string $type;
    public readonly string $component; // The handle for the component used in the admin

    public function __construct(
        protected string $label,
        protected bool $required = false,
        protected bool $multilang = false,
        protected ?string $description = null,
        public readonly ?int $width = null,
        public readonly ?int $height = null,
    ) {
        $this->type = basename(str_replace('\\', '/', strtolower($this::class)));
    }

    public function validate(): bool
    {
        return true;
    }

    public function meta(): array
    {
        return [
            'type' => $this->type,
            'required' => $this->required,
            'width' => $this->width,
            'height' => $this->width,
            'multilang' => $this->multilang,
            'description' => $this->description,
        ];
    }

    abstract public function __toString(): string
}
