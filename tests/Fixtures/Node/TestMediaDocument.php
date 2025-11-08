<?php

declare(strict_types=1);

namespace Duon\Cms\Tests\Fixtures\Node;

use Duon\Cms\Field\Attr\Columns;
use Duon\Cms\Field\Attr\Label;
use Duon\Cms\Field\Attr\Multiple;
use Duon\Cms\Field\Attr\Options;
use Duon\Cms\Field\Attr\Translate;
use Duon\Cms\Field\Attr\TranslateFile;
use Duon\Cms\Field\Grid;
use Duon\Cms\Field\Image;
use Duon\Cms\Field\Option;
use Duon\Cms\Field\Video;
use Duon\Cms\Node\Attr\Name;
use Duon\Cms\Node\Document;

#[Name('Test Media Document')]
class TestMediaDocument extends Document
{
	#[Label('Gallery')]
	#[Multiple]
	#[TranslateFile]
	public Image $gallery;

	#[Label('Video')]
	#[TranslateFile]
	public Video $video;

	#[Label('Content Grid')]
	#[Columns(12, 2)]
	#[Translate]
	public Grid $contentGrid;

	#[Label('Category')]
	#[Options(['news', 'blog', 'tutorial'])]
	public Option $category;

	public function title(): string
	{
		return 'Test Media Document';
	}
}
