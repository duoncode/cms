<?php

declare(strict_types=1);

namespace Duon\Cms\Tests\Fixtures\Node;

use Duon\Cms\Schema\Icon;
use Duon\Cms\Schema\Label;

#[Label('Node with icon')]
#[Icon('bi:check', color: '#ff0000', class: 'cms-node-icon', style: 'height: 1rem')]
class NodeWithIconAttribute {}
