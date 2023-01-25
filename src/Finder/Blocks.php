<?php

declare(strict_types=1);

namespace Conia\Core\Finder;

use Conia\Chuck\Request;
use Conia\Core\Config;
use Conia\Core\Finder;
use Conia\Quma\Database;

class Blocks
{
    public readonly Database $db;
    public readonly Request $request;
    public readonly Config $config;

    public function __construct(
        protected readonly Finder $find,
    ) {
        $this->db = $find->db;
        $this->request = $find->request;
        $this->config = $find->config;
    }
}
