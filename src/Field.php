<?php

declare(strict_types=1);

namespace Conia;

use \Exception;
use \RuntimeException;


abstract class Field implements Data
{
    public readonly string $type;
    public string $defaultl;
    public string $value;

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
            'multilang' => $this->multilang,
            'description' => $this->description,
        ];
    }

    public function __toString(): string
    {
        return $this->getValue();
    }

    public function getValue(): string
    {
        if (isset($this->value)) {
            return $this->value;
        }

        if (isset($this->default)) {
            return $this->default;
        }

        throw new RuntimeException('No value available');
    }

    public function setValue(string $value): void
    {
        $this->default = $value;
    }

    public function setDefault(string|array $value): void
    {
        $this->default = $value;
    }
}
