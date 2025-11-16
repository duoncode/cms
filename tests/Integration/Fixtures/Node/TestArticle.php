<?php

declare(strict_types=1);

namespace Duon\Cms\Tests\Integration\Fixtures\Node;

use Duon\Cms\Field\Meta\Label;
use Duon\Cms\Field\Meta\Translate;
use Duon\Cms\Field\Text;
use Duon\Cms\Field\Textarea;
use Duon\Cms\Node\Meta\Name;
use Duon\Cms\Node\Page;

#[Name('Test Article')]
class TestArticle extends Page
{
	#[Label('Title')]
	#[Translate]
	public Text $title;

	#[Label('Content')]
	#[Translate]
	public Textarea $content;

	public function title(): string
	{
		return $this->title?->get() ?? 'Test Article';
	}
}
