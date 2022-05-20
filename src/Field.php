<?php

declare(strict_types=1);

namespace Conia;

use \ValueError;


abstract class Field
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
        $this->type = basename(str_replace('\\', '/', $this::class));
    }

    public function validate(): bool
    {
        return true;
    }

    public function meta(Type|Block $doc): array
    {
        if ($doc->columns < $this->width) {
            throw new ValueError('Field width larger than number of columns');
        }

        return [
            'label' => $this->label,
            'description' => $this->description,
            'type' => $this->type,
            'required' => $this->required,
            'width' => $this->width,
            'height' => $this->height,
            'multilang' => $this->multilang,
        ];
    }

    abstract public function __toString(): string;
}
