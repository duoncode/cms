<?php

declare(strict_types=1);

namespace Duon\Cms\Tests\Integration\Fixtures\Node;

use Duon\Cms\Field\Meta\Label;
use Duon\Cms\Field\Text;
use Duon\Cms\Node\Meta\Name;
use Duon\Cms\Node\Block;

#[Name('Test Widget')]
class TestWidget extends Block
{
	#[Label('Title')]
	public Text $title;

	public function title(): string
	{
		return $this->title?->get() ?? 'Test Widget';
	}
}
