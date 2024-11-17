<?php

declare(strict_types=1);

namespace FiveOrbs\Cms\Value;

use FiveOrbs\Cms\Assets\Assets;
use FiveOrbs\Cms\Exception\NoSuchProperty;
use FiveOrbs\Cms\Field\Field;
use FiveOrbs\Cms\Locale;
use FiveOrbs\Cms\Node\Node;

abstract class Value
{
	public readonly string $fieldType;
	protected readonly Locale $locale;
	protected readonly Locale $defaultLocale;
	protected readonly string $fieldName;
	protected readonly array $data;
	protected readonly bool $translate;

	public function __construct(
		protected readonly Node $node,
		protected readonly Field $field,
		protected readonly ValueContext $context,
	) {
		$this->locale = $node->request->get('locale');
		$this->defaultLocale = $node->request->get('defaultLocale');
		$this->data = $context->data;
		$this->fieldName = $context->fieldName;
		$this->fieldType = $field->type;
		$this->translate = $field->isTranslatable();
	}

	public function __get(string $name): mixed
	{
		if (array_key_exists($name, $this->data)) {
			return $this->data[$name];
		}

		throw new NoSuchProperty("The field '{$this->fieldName}' doesn't have the property '{$name}'");
	}

	abstract public function __toString(): string;

	abstract public function isset(): bool;

	abstract public function json(): mixed;

	abstract public function unwrap(): mixed;

	public function styleClass(): ?string
	{
		return $this->data['class'] ?? null;
	}

	public function elementId(): ?string
	{
		return $this->data['id'] ?? null;
	}

	protected function assetsPath(): string
	{
		return 'node/' . $this->node->uid() . '/';
	}

	protected function getAssets(): Assets
	{
		static $assets = null;

		if (!$assets) {
			$assets = new Assets($this->node->request, $this->node->config);
		}

		return $assets;
	}
}
