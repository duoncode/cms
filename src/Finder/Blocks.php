<?php

declare(strict_types=1);

namespace Conia\Core\Finder;

use Conia\Chuck\Request;
use Conia\Core\Config;
use Conia\Quma\Database;

class Blocks
{
    public function __construct(
        public readonly Database $db,
        public readonly Request $request,
        public readonly Config $config,
    ) {
    }
}
