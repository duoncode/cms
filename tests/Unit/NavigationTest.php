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
	public function testNestedSectionsBuildRecursiveItemsAndPayload(): void
	{
		$navigation = new Navigation();
		$content = $navigation->section('Content');
		$articles = $content->collection(TestArticlesCollection::class);
		$articles->meta->label = 'Articles';
		$articles->meta->badge = 'new';
		$content
			->section('Nested')
			->collection(TestHierarchyCollection::class);

		$items = $navigation->items();
		$payload = $navigation->payload();

		$this->assertCount(1, $items);
		$this->assertSame('Content', $items[0]->meta->label);
		$this->assertNull($items[0]->slug());
		$this->assertCount(2, $items[0]->children());
		$this->assertSame('Articles', $items[0]->children()[0]->meta->label);
		$this->assertSame('test-articles', $items[0]->children()[0]->slug());
		$this->assertSame('Nested', $items[0]->children()[1]->meta->label);
		$this->assertSame('test-hierarchy', $items[0]->children()[1]->children()[0]->slug());

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
		$this->assertSame('Articles', $navigation->ref('test-articles')->meta->label);
	}

	public function testDuplicateHandlesAreRejectedAcrossSections(): void
	{
		$navigation = new Navigation();
		$navigation->section('Content')->collection(TestArticlesCollection::class);

		$this->throws(RuntimeException::class, 'Duplicate collection handle: test-articles');
		$navigation->section('Other')->collection(TestArticlesCollection::class);
	}

	public function testOrderedItemsAreSortedAndEmptySectionsAreFiltered(): void
	{
		$navigation = new Navigation();
		$navigation->collection(TestArticlesCollection::class)->meta->order = 20;
		$navigation->collection(TestHierarchyCollection::class)->meta->order = 10;
		$navigation->section('Empty');

		$items = $navigation->items();

		$this->assertCount(2, $items);
		$this->assertSame('test-hierarchy', $items[0]->slug());
		$this->assertSame('test-articles', $items[1]->slug());
	}
}
