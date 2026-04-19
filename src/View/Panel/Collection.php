<?php

declare(strict_types=1);

namespace Duon\Cms\View\Panel;

use Duon\Cms\Collection as CmsCollection;
use Duon\Cms\Exception\RuntimeException;
use Duon\Cms\Navigation;
use Duon\Core\Exception\HttpNotFound;
use Duon\Core\Request;
use Duon\Wire\Creator;

final class Collection extends Panel
{
	public function collection(string $collection): array
	{
		$creator = new Creator($this->container);
		$navigation = $this->navigation();

		try {
			$ref = $navigation->ref($collection);
		} catch (RuntimeException $e) {
			throw new HttpNotFound($this->request, previous: $e);
		}

		$obj = $creator->create(
			$ref::class,
			predefinedTypes: [Request::class => $this->request],
		);
		assert($obj instanceof CmsCollection, 'The collection route must resolve a collection');
		$listing = $obj->list();

		return $this->context([
			'name' => $ref->meta->label,
			'slug' => $collection,
			'header' => $obj->header(),
			'nodes' => $listing['nodes'],
		]);
	}

	private function navigation(): Navigation
	{
		$navigation = $this->container->get(Navigation::class);
		assert($navigation instanceof Navigation, 'The navigation service must be available');

		return $navigation;
	}
}
