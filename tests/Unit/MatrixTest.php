<?php

declare(strict_types=1);

namespace Duon\Cms\Tests\Unit;

use Duon\Cms\Field\Matrix;
use Duon\Cms\Tests\Fixtures\Field\TestMatrix;
use Duon\Cms\Tests\TestCase;
use Duon\Cms\Value\MatrixValue;

class MatrixTest extends TestCase
{
	private function createContext(): \Duon\Cms\Context
	{
		$psrRequest = $this->psrRequest();
		$locales = new \Duon\Cms\Locales();
		$locales->add('en', title: 'English', domains: ['www.example.com']);
		$locales->add('de', title: 'Deutsch', domains: ['www.example.de'], fallback: 'en');

		$psrRequest = $psrRequest
			->withAttribute('locales', $locales)
			->withAttribute('locale', $locales->get('en'))
			->withAttribute('defaultLocale', $locales->getDefault());

		$request = new \Duon\Core\Request($psrRequest);

		return new \Duon\Cms\Context(
			$this->db(),
			$request,
			$this->config(),
			$this->registry(),
			$this->factory(),
		);
	}

	public function testMatrixFieldCreation(): void
	{
		$context = $this->createContext();
		$finder = $this->createMock(\Duon\Cms\Finder\Finder::class);

		$node = new class ($context, $finder, ['content' => []]) extends \Duon\Cms\Node\Document {
			public function title(): string
			{
				return 'Test';
			}
		};

		$matrix = new TestMatrix('test_matrix', $node, new \Duon\Cms\Value\ValueContext('test_matrix', []));

		$this->assertInstanceOf(Matrix::class, $matrix);
		$this->assertInstanceOf(MatrixValue::class, $matrix->value());
		$this->assertIsArray($matrix->getSubfields());
		$this->assertArrayHasKey('title', $matrix->getSubfields());
		$this->assertArrayHasKey('content', $matrix->getSubfields());
	}

	public function testMatrixStructure(): void
	{
		$context = $this->createContext();
		$finder = $this->createMock(\Duon\Cms\Finder\Finder::class);

		$node = new class ($context, $finder, ['content' => []]) extends \Duon\Cms\Node\Document {
			public function title(): string
			{
				return 'Test';
			}
		};

		$matrix = new TestMatrix('test_matrix', $node, new \Duon\Cms\Value\ValueContext('test_matrix', []));
		$structure = $matrix->structure();

		$this->assertEquals('matrix', $structure['type']);
		$this->assertIsArray($structure['value']);
	}

	public function testMatrixSchema(): void
	{
		$context = $this->createContext();
		$finder = $this->createMock(\Duon\Cms\Finder\Finder::class);

		$node = new class ($context, $finder, ['content' => []]) extends \Duon\Cms\Node\Document {
			public function title(): string
			{
				return 'Test';
			}
		};

		$matrix = new TestMatrix('test_matrix', $node, new \Duon\Cms\Value\ValueContext('test_matrix', []));
		$schema = $matrix->schema();

		$this->assertInstanceOf(\Duon\Sire\Schema::class, $schema);
	}
}
