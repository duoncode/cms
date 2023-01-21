<?php

declare(strict_types=1);

namespace Conia\Field;

use Conia\Request;
use Conia\Value\Value;

abstract class Field
{
    public readonly string $type;
    public readonly array $args;
    protected ?string $description = null;
    protected bool $multilang = false;
    protected bool $required = false;
    protected ?int $width = null;
    protected ?int $height = null;

    public function __construct(protected string $label, mixed ...$args)
    {
        $this->type = basename(str_replace('\\', '/', $this::class));
        $this->args = $args;
    }

    public static function new(string $label, mixed ...$args): static
    {
        return new static($label, ...$args);
    }

    public function validate(): bool
    {
        return true;
    }

    public function description(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function required(bool $required = true): static
    {
        $this->required = $required;

        return $this;
    }

    public function isRequired(): bool
    {
        return $this->required;
    }

    public function multilang(bool $multilang): static
    {
        $this->multilang = $multilang;

        return $this;
    }

    public function isMultilang(): bool
    {
        return $this->multilang;
    }

    public function width(int $width): static
    {
        $this->width = $width;

        return $this;
    }

    public function getWidth(): int
    {
        return $this->width;
    }

    public function height(int $height): static
    {
        $this->height = $height;

        return $this;
    }

    public function getHeight(): int
    {
        return $this->height;
    }

    abstract public function value(Request $request, array $data): Value;
}
