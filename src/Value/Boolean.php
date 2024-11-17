<?php

declare(strict_types=1);

namespace FiveOrbs\Cms\Value;

use FiveOrbs\Cms\Field\Field;
use FiveOrbs\Cms\Node\Node;

class Boolean extends Value
{
	public readonly bool $value;

	public function __construct(Node $node, Field $field, ValueContext $context)
	{
		parent::__construct($node, $field, $context);

		if (is_bool($this->data['value'] ?? null)) {
			$this->value = $this->data['value'];
		} else {
			$this->value = false;
		}
	}

	public function __toString(): string
	{
		return (string) $this->value;
	}

	public function unwrap(): bool
	{
		return $this->value;
	}

	public function json(): mixed
	{
		return $this->value;
	}

	public function isset(): bool
	{
		return true;
	}
}
