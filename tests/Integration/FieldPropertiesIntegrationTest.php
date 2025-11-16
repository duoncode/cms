<?php

declare(strict_types=1);

namespace Duon\Cms\Tests\Integration;

use Duon\Cms\Tests\Integration\Fixtures\Node\TestDocument;
use Duon\Cms\Tests\Integration\Fixtures\Node\TestMediaDocument;
use Duon\Cms\Tests\IntegrationTestCase;

final class FieldPropertiesIntegrationTest extends IntegrationTestCase
{
	public function testFieldPropertiesIncludesNameAndType(): void
	{
		$context = $this->createContext();
		$finder = $this->createFinder();

		$node = new TestDocument($context, $finder, ['content' => []]);

		$properties = $node->getField('title')->properties();

		$this->assertArrayHasKey('name', $properties);
		$this->assertArrayHasKey('type', $properties);
		$this->assertEquals('title', $properties['name']);
		$this->assertEquals(\Duon\Cms\Field\Text::class, $properties['type']);
	}

	public function testFieldPropertiesCollectsFromMultipleCapabilities(): void
	{
		$context = $this->createContext();
		$finder = $this->createFinder();

		$node = new TestDocument($context, $finder, ['content' => []]);

		$properties = $node->getField('title')->properties();

		// From Label capability
		$this->assertArrayHasKey('label', $properties);
		$this->assertEquals('Document Title', $properties['label']);

		// From Required capability
		$this->assertArrayHasKey('required', $properties);
		$this->assertTrue($properties['required']);

		// From Validate capability
		$this->assertArrayHasKey('validators', $properties);
		$this->assertContains('minLength:3', $properties['validators']);
		$this->assertContains('maxLength:100', $properties['validators']);
	}

	public function testFieldPropertiesHandlesHiddenAndImmutable(): void
	{
		$context = $this->createContext();
		$finder = $this->createFinder();

		$node = new TestDocument($context, $finder, ['content' => []]);

		$properties = $node->getField('internalId')->properties();

		$this->assertArrayHasKey('hidden', $properties);
		$this->assertTrue($properties['hidden']);

		$this->assertArrayHasKey('immutable', $properties);
		$this->assertTrue($properties['immutable']);
	}

	public function testFieldPropertiesHandlesResizableProperties(): void
	{
		$context = $this->createContext();
		$finder = $this->createFinder();

		$node = new TestDocument($context, $finder, ['content' => []]);

		$properties = $node->getField('intro')->properties();

		$this->assertArrayHasKey('rows', $properties);
		$this->assertEquals(5, $properties['rows']);

		$this->assertArrayHasKey('width', $properties);
		$this->assertEquals(12, $properties['width']);

		$this->assertArrayHasKey('translate', $properties);
		$this->assertTrue($properties['translate']);

		$this->assertArrayHasKey('description', $properties);
		$this->assertEquals('A brief introduction to the document', $properties['description']);
	}

	public function testGridFieldPropertiesIncludesColumns(): void
	{
		$context = $this->createContext();
		$finder = $this->createFinder();

		$node = new TestMediaDocument($context, $finder, ['content' => []]);

		$properties = $node->getField('contentGrid')->properties();

		$this->assertArrayHasKey('columns', $properties);
		$this->assertEquals(12, $properties['columns']);

		$this->assertArrayHasKey('minCellWidth', $properties);
		$this->assertEquals(2, $properties['minCellWidth']);

		$this->assertArrayHasKey('translate', $properties);
		$this->assertTrue($properties['translate']);
	}

	public function testImageFieldPropertiesIncludesMultipleAndTranslateFile(): void
	{
		$context = $this->createContext();
		$finder = $this->createFinder();

		$node = new TestMediaDocument($context, $finder, ['content' => []]);

		$properties = $node->getField('gallery')->properties();

		$this->assertArrayHasKey('multiple', $properties);
		$this->assertTrue($properties['multiple']);

		$this->assertArrayHasKey('translateFile', $properties);
		$this->assertTrue($properties['translateFile']);
	}

	public function testOptionFieldPropertiesIncludesOptions(): void
	{
		$context = $this->createContext();
		$finder = $this->createFinder();

		$node = new TestMediaDocument($context, $finder, ['content' => []]);

		$properties = $node->getField('category')->properties();

		$this->assertArrayHasKey('options', $properties);
		$this->assertEquals(['news', 'blog', 'tutorial'], $properties['options']);
	}

	public function testNodeFieldsMethodReturnsAllFieldProperties(): void
	{
		$context = $this->createContext();
		$finder = $this->createFinder();

		$node = new TestDocument($context, $finder, ['content' => []]);

		$fields = $node->fields();

		$this->assertIsArray($fields);
		$this->assertCount(3, $fields); // title, intro, internalId

		// Check that each field has the basic properties
		foreach ($fields as $field) {
			$this->assertArrayHasKey('name', $field);
			$this->assertArrayHasKey('type', $field);
		}

		// Find title field and verify its properties
		$titleField = array_values(array_filter($fields, fn($f) => $f['name'] === 'title'))[0];
		$this->assertEquals('Document Title', $titleField['label']);
		$this->assertTrue($titleField['required']);
	}
}
