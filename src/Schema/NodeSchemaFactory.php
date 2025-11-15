<?php

declare(strict_types=1);

namespace Duon\Cms\Schema;

use Duon\Cms\Field\Field;
use Duon\Cms\Locales;
use Duon\Cms\Node\Node;
use Duon\Sire\Schema;

class NodeSchemaFactory
{
	protected readonly Schema $schema;

	public function __construct(
		protected readonly Node $node,
		protected readonly Locales $locales,
	) {
		$this->schema = new Schema(keepUnknown: true);
		$this->schema->add('uid', 'text', 'required', 'maxlen:64');
		$this->schema->add('published', 'bool', 'required');
		$this->schema->add('locked', 'bool', 'required');
		$this->schema->add('hidden', 'bool', 'required');
	}

	public function create(): Schema
	{
		$contentSchema = new Schema(title: 'Content', keepUnknown: true);

		foreach ($this->node->fieldNames() as $fieldName) {
			$this->add($contentSchema, $fieldName, $this->node->getField($fieldName));
		}

		$this->schema->add('content', $contentSchema);

		return $this->schema;
	}

	protected function add(Schema $schema, string $fieldName, Field $field): void
	{
		$schema->add($fieldName, $field->schema())->label($field->getLabel());
	}
}
