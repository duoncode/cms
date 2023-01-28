<?php

declare(strict_types=1);

namespace Conia\Core;

use Conia\Chuck\Request;
use Conia\Core\Config;
use Conia\Core\Exception\RuntimeException;
use Conia\Core\Finder\Block;
use Conia\Core\Finder\Blocks;
use Conia\Core\Finder\Menu;
use Conia\Core\Finder\Page;
use Conia\Core\Finder\Pages;
use Conia\Quma\Database;
use Iterator;

/**
 * @psalm-property-read Pages  $pages
 * @psalm-property-read Page   $page
 * @psalm-property-read Blocks $blocks
 * @psalm-property-read Block  $block
 * @psalm-property-read Menu   $menu
 */
class Finder
{
    public function __construct(
        public readonly Database $db,
        public readonly Request $request,
        public readonly Config $config,
    ) {
    }

    public function __get($key): Pages|Page|Blocks|Block|Menu
    {
        return match ($key) {
            'pages' => new Pages($this),
            'page' => new Page($this),
            'blocks' => new Blocks($this),
            'block' => new Block($this),
            'menu' => new Menu($this),
            default => throw new RuntimeException('Property not supported')
        };
    }

    public function pages(string $query = '', bool $deleted = false): Pages
    {
        return (new Pages($this, $deleted))->find($query);
    }

    public function page(
        string $query,
        array $types = [],
        int $limit = 0,
        string $order = '',
    ): array {
        return (new Page($this))->find($query, $types, $limit, $order);
    }
}
