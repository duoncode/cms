<?php

declare(strict_types=1);

namespace Conia\Core;

use Conia\Chuck\Request;
use Conia\Core\Config;
use Conia\Core\Finder\Block;
use Conia\Core\Finder\Blocks;
use Conia\Core\Finder\Menu;
use Conia\Core\Finder\Page;
use Conia\Core\Finder\Pages;
use Conia\Quma\Database;
use Iterator;

class Finder
{
    public readonly Pages $pages;
    public readonly Page $page;
    public readonly Blocks $blocks;
    public readonly Block $block;
    public readonly Menu $menu;

    public function __construct(
        public readonly Database $db,
        public readonly Request $request,
        public readonly Config $config,
    ) {
        $this->pages = new Pages($this);
        $this->page = new Page($this);
        $this->blocks = new Blocks($this);
        $this->block = new Block($this);
        $this->menu = new Menu($this);
    }

    public function pages(
        string $query,
        array $types = [],
        int $limit = 0,
        string $order = '',
    ): Iterator {
        return $this->pages->find($query, $types, $limit, $order);
    }

    public function page(
        string $query,
        array $types = [],
        int $limit = 0,
        string $order = '',
    ): Iterator {
        return $this->page->find($query, $types, $limit, $order);
    }
}
