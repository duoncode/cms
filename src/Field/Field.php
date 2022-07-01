<?php

declare(strict_types=1);

namespace Conia\Field;

use \ValueError;
use Conia\Block;
use Conia\Locale;
use Conia\Type;
use Conia\Value\Value;


abstract class Field
{
    public readonly string $type;
    public ?string $description = null;
    public bool $multilang = false;
    public bool $required = false;
    public ?int $width = null;
    public ?int $height = null;
    protected array $args;


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

    public function required(bool $required = true): static
    {
        $this->required = $required;

        return $this;
    }

    public function multilang(bool $multilang): static
    {
        $this->multilang = $multilang;

        return $this;
    }

    public function width(int $width): static
    {
        $this->width = $width;

        return $this;
    }

    public function height(int $height): static
    {
        $this->height = $height;

        return $this;
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

    abstract public function value(array $data, Locale $locale): Value;
}
