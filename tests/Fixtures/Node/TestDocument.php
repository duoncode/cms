<?php

declare(strict_types=1);

namespace Duon\Cms\Tests\Fixtures\Node;

use Duon\Cms\Field\Attr\DefaultValue;
use Duon\Cms\Field\Attr\Description;
use Duon\Cms\Field\Attr\Hidden;
use Duon\Cms\Field\Attr\Immutable;
use Duon\Cms\Field\Attr\Label;
use Duon\Cms\Field\Attr\Required;
use Duon\Cms\Field\Attr\Rows;
use Duon\Cms\Field\Attr\Translate;
use Duon\Cms\Field\Attr\Validate;
use Duon\Cms\Field\Attr\Width;
use Duon\Cms\Field\Text;
use Duon\Cms\Field\Textarea;
use Duon\Cms\Node\Attr\Name;
use Duon\Cms\Node\Document;

#[Name('Test Document')]
class TestDocument extends Document
{
	#[Label('Document Title')]
	#[Required]
	#[Validate('minLength:3', 'maxLength:100')]
	public Text $title;

	#[Label('Introduction')]
	#[Description('A brief introduction to the document')]
	#[Rows(5)]
	#[Width(12)]
	#[Translate]
	public Textarea $intro;

	#[Hidden]
	#[Immutable]
	#[DefaultValue('auto-generated-id')]
	public Text $internalId;

	public function title(): string
	{
		return $this->title?->get() ?? 'Test Document';
	}
}
