<?php

declare(strict_types=1);

namespace Duon\Cms\Tests\Fixtures\Node;

use Duon\Cms\Field\Meta\Label;
use Duon\Cms\Field\Meta\Translate;
use Duon\Cms\Field\Text;
use Duon\Cms\Node\Meta\Name;
use Duon\Cms\Node\Page;

#[Name('Test Home')]
class TestHome extends Page
{
	#[Label('Title')]
	#[Translate]
	public Text $title;

	public function title(): string
	{
		return $this->title?->value()->unwrap() ?? 'Test Home';
	}
}
