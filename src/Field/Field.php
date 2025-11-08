<?php

declare(strict_types=1);

namespace Duon\Cms\Field;

use Duon\Cms\Node\Node;
use Duon\Cms\Value\Value;
use Duon\Cms\Value\ValueContext;

abstract class Field implements
	Capability\Defaultable,
	Capability\Describable,
	Capability\Hidable,
	Capability\Labelable,
	Capability\Requirable,
	Capability\Resizable,
	Capability\Validatable
{
	use Capability\IsRequirable;
	use Capability\IsLabelable;
	use Capability\IsDescribable;
	use Capability\IsHidable;
	use Capability\IsDefaultable;
	use Capability\IsResizable;
	use Capability\IsValidatable;

	public readonly string $type;
	protected array $validators = [];

	final public function __construct(
		public readonly string $name,
		protected readonly Node $node,
		protected readonly ValueContext $valueContext,
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

	public function properties(): array
	{
		return [
			'rows' => $this->rows,
			'width' => $this->width,
			'translate' => $this->translate,
			'required' => $this->isRequired(),
			'immutable' => $this->immutable,
			'hidden' => $this->hidden,
			'description' => $this->description,
			'label' => $this->label,
			'name' => $this->name,
			'type' => $this::class,
			'validators' => $this->validators,
		];
	}

	public function getFileStructure(string $type, mixed $value = null): array
	{
		if ($value === null) {
			if ($this->default === null) {
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
}
