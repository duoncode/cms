<?php

declare(strict_types=1);

namespace Duon\Cms\Tests\Fixtures\Node;

use Duon\Cms\Field\Text;
use Duon\Cms\Schema\Icon;

class NodeWithFieldIconAttribute
{
	#[Icon('bi:type', ['color' => '#00ff00', 'class' => 'cms-field-icon', 'style' => 'width: 1rem'])]
	protected Text $title;
}
