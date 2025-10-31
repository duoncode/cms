<?php

declare(strict_types=1);

namespace Duon\Cms\Field\Capability;

use Duon\Cms\Field\Attr\FulltextWeight;

interface Searchable
{
	public function fulltext(FulltextWeight $fulltextWeight): static;

	public function getFulltextWeight(): ?FulltextWeight;
}
