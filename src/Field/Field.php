<?php

declare(strict_types=1);

namespace Conia\Cms\Field;

use Conia\Cms\Field\Attr\FulltextWeight;
use Conia\Cms\Node\Node;
use Conia\Cms\Value\Value;
use Conia\Cms\Value\ValueContext;

abstract class Field
{
    public readonly string $type;
    protected ?string $label = null;
    protected ?string $description = null;
    protected bool $translate = false;
    protected ?int $width = null;
    protected ?int $rows = null;
    protected array $validators = [];
    protected mixed $default = null;
    protected ?FulltextWeight $fulltextWeight = null;

    public function __construct(
        public readonly string $name,
        protected readonly Node $node,
        protected readonly ValueContext $valueContext
    ) {
        $this->type = $this::class;
    }

    public function __toString(): string
    {
        return $this->value()->__toString();
    }

    abstract public function value(): Value;

    abstract public function structure(mixed $value = null): array;

    public function isset(): bool
    {
        return $this->value()->isset();
    }

    public function label(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function default(mixed $default): static
    {
        $this->default = $default;

        return $this;
    }

    public function getLabel(): ?string
    {
        return $this->label;
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

    public function required(): static
    {
        $this->validators[] = 'required';

        return $this;
    }

    public function isRequired(): bool
    {
        return in_array('required', $this->validators);
    }

    public function validate(string ...$validators): static
    {
        $this->validators = array_merge($this->validators, $validators);

        return $this;
    }

    public function validators(): array
    {
        return array_values(array_unique($this->validators));
    }

    public function translate(bool $translate = true): static
    {
        $this->translate = $translate;

        return $this;
    }

    public function isTranslatable(): bool
    {
        return $this->translate;
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

    public function rows(int $rows): static
    {
        $this->rows = $rows;

        return $this;
    }

    public function getRows(): int
    {
        return $this->rows;
    }

    public function properties(): array
    {
        return [
            'rows' => $this->rows,
            'width' => $this->width,
            'translate' => $this->translate,
            'required' => $this->isRequired(),
            'description' => $this->description,
            'label' => $this->label,
            'name' => $this->name,
            'type' => $this::class,
            'validators' => $this->validators,
        ];
    }

    public function getFileStructure(string $type, mixed $value = null): array
    {
        if (is_null($value)) {
            if (is_null($this->default)) {
                $value = [];
            } else {
                $value = $this->default;
            }
        }

        return ['type' => $type, 'files' => $value];
    }

    public function getSimpleStructure(string $type, mixed $value = null): array
    {
        $value = $value ?: $this->default;

        return ['type' => $type, 'value' => $value];
    }

    protected function getTranslatableStructure(string $type, mixed $value = null): array
    {
        $value = $value ?: $this->default;

        $result = ['type' => $type];

        if ($value) {
            $result['value'] = $value;

            return $result;
        }

        if ($this->translate) {
            $result['value'] = [];

            foreach ($this->node->config->locales() as $locale) {
                $result['value'][$locale->id] = null;
            }
        } else {
            $result['value'] = null;
        }

        return $result;
    }

    protected function getTranslatableFileStructure(string $type, mixed $value = null): array
    {
        $value = $value ?: $this->default;

        $result = ['type' => $type];

        if ($value) {
            $result['files'] = $value;

            return $result;
        }

        $result['files'] = [];

        foreach ($this->node->config->locales() as $locale) {
            $result['files'][$locale->id] = [];
        }

        return $result;
    }
}
