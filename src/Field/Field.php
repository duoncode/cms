<?php

declare(strict_types=1);

namespace Conia\Core\Field;

use Conia\Core\Field\Attr\FulltextWeight;
use Conia\Core\Type;
use Conia\Core\Value\Value;
use Conia\Core\Value\ValueContext;

abstract class Field
{
    public readonly string $type;
    protected ?string $description = null;
    protected bool $multilang = false;
    protected bool $required = false;
    protected ?int $width = null;
    protected ?int $height = null;
    protected ?FulltextWeight $fulltextWeight = null;

    public function __construct(protected string $label)
    {
        $this->type = $this::class;
    }

    public static function new(string $label): static
    {
        return new static($label);
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

    public function label(string $label): static
    {
        $this->label = $label;

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

    public function multilang(bool $multilang = true): static
    {
        $this->multilang = $multilang;

        return $this;
    }

    public function isMultilang(): bool
    {
        return $this->multilang;
    }

    public function fulltext(FulltextWeight $fulltextWeight): static
    {
        $this->fulltextWeight = $fulltextWeight;

        return $this;
    }

    public function getFulltextWeight(): ?FulltextWeight
    {
        return $this->fulltextWeight;
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

    abstract public function value(Type $page, ValueContext $context): Value;
}
