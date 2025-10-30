<?php

declare(strict_types=1);

namespace Duon\Cms\Field;

use Duon\Cms\Field\Attr\FulltextWeight;
use Duon\Cms\Node\Node;
use Duon\Cms\Value\Value;
use Duon\Cms\Value\ValueContext;

abstract class Field
{
	public readonly string $type;
	protected ?string $label = null;
	protected ?string $description = null;
	protected bool $translate = false;
	protected bool $immutable = false;
	protected bool $hidden = false;
	protected ?int $width = null;
	protected ?int $rows = null;
	protected array $validators = [];
	protected mixed $default = null;
	protected ?FulltextWeight $fulltextWeight = null;

	public const CAPABILITY_DEFAULT_VALUE = 1 << 0;
	public const CAPABILITY_DESCRIPTION = 1 << 1;
	public const CAPABILITY_FULLTEXT = 1 << 2;
	public const CAPABILITY_HIDDEN = 1 << 3;
	public const CAPABILITY_IMMUTABLE = 1 << 4;
	public const CAPABILITY_LABEL = 1 << 5;
	public const CAPABILITY_REQUIRED = 1 << 6;
	public const CAPABILITY_ROWS = 1 << 7;
	public const CAPABILITY_VALIDATE = 1 << 8;
	public const CAPABILITY_WIDTH = 1 << 9;
	public const CAPABILITY_COLUMNS = 1 << 10;
	public const CAPABILITY_MULTIPLE = 1 << 11;
	public const CAPABILITY_OPTIONS = 1 << 12;
	public const CAPABILITY_TRANSLATE = 1 << 13;
	public const CAPABILITY_TRANSLATE_FILE = 1 << 14;
	public const BASE_CAPABILITIES = (
		self::CAPABILITY_COLUMNS |
		self::CAPABILITY_DEFAULT_VALUE |
		self::CAPABILITY_DESCRIPTION |
		self::CAPABILITY_IMMUTABLE |
		self::CAPABILITY_LABEL |
		self::CAPABILITY_REQUIRED |
		self::CAPABILITY_ROWS  |
		self::CAPABILITY_VALIDATE |
		self::CAPABILITY_WIDTH
	);
	public const EXTRA_CAPABILITIES = 0;
	public const OMIT_CAPABILITIES = 0;

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

	final public static function capabilities(): int
	{
		return (static::BASE_CAPABILITIES | static::EXTRA_CAPABILITIES) & ~static::OMIT_CAPABILITIES;
	}

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

	public function immutable(bool $immutable = true): static
	{
		$this->immutable = $immutable;

		return $this;
	}

	public function hidden(bool $hidden = true): static
	{
		$this->hidden = $hidden;

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

			foreach ($this->node->context->locales() as $locale) {
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

		foreach ($this->node->context->locales() as $locale) {
			$result['files'][$locale->id] = [];
		}

		return $result;
	}
}
