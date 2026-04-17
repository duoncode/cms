<?php

declare(strict_types=1);

namespace Duon\Cms\Tests\Unit;

use Duon\Cms\Exception\RuntimeException;
use Duon\Cms\Navigation;
use Duon\Cms\Tests\Fixtures\Collection\TestArticlesCollection;
use Duon\Cms\Tests\Fixtures\Collection\TestHierarchyCollection;
use Duon\Cms\Tests\TestCase;

final class NavigationTest extends TestCase
{
	public function testNestedSectionsBuildRecursivePayload(): void
	{
		$navigation = new Navigation();
		$navigation
			->section('Content')
			->collection(TestArticlesCollection::class)
			->label('Articles')
			->badge('new')
			->section('Nested')
			->collection(TestHierarchyCollection::class);

		$payload = $navigation->payload();

		$this->assertCount(1, $payload);
		$this->assertSame('section', $payload[0]['type']);
		$this->assertSame('Content', $payload[0]['name']);
		$this->assertCount(2, $payload[0]['children']);
		$this->assertSame('Articles', $payload[0]['children'][0]['name']);
		$this->assertSame('new', $payload[0]['children'][0]['meta']['badge']);
		$this->assertSame('section', $payload[0]['children'][1]['type']);
		$this->assertSame('Nested', $payload[0]['children'][1]['name']);
		$this->assertSame(
			'test-hierarchy',
			$payload[0]['children'][1]['children'][0]['slug'],
		);
		$this->assertSame('Articles', $navigation->ref('test-articles')->name());
	}

	public function testDuplicateHandlesAreRejectedAcrossSections(): void
	{
		$navigation = new Navigation();
		$navigation->section('Content')->collection(TestArticlesCollection::class);

		$this->throws(RuntimeException::class, 'Duplicate collection handle: test-articles');
		$navigation->section('Other')->collection(TestArticlesCollection::class);
	}

	public function testOrderedItemsAreSortedInPayload(): void
	{
		$navigation = new Navigation();
		$navigation->collection(TestArticlesCollection::class)->order(20);
		$navigation->collection(TestHierarchyCollection::class)->order(10);
		$navigation->section('Empty');

		$payload = $navigation->payload();

		$this->assertCount(2, $payload);
		$this->assertSame('test-hierarchy', $payload[0]['slug']);
		$this->assertSame('test-articles', $payload[1]['slug']);
	}
}
