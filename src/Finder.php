<?php

declare(strict_types=1);

namespace Conia\Core;

use Conia\Core\Exception\RuntimeException;
use Conia\Core\Finder\Block;
use Conia\Core\Finder\Blocks;
use Conia\Core\Finder\Menu;
use Conia\Core\Finder\Page;
use Conia\Core\Finder\Pages;

/**
 * @psalm-property-read Pages  $pages
 * @psalm-property-read Page   $page
 * @psalm-property-read Blocks $blocks
 * @psalm-property-read Block  $block
 * @psalm-property-read Menu   $menu
 */
class Finder
{
    public function __construct(private readonly Context $context)
    {
    }

    public function __get($key): Pages|Page|Blocks|Block|Menu
    {
        return match ($key) {
            'pages' => new Pages($this->context, $this),
            'page' => new Page($this->context, $this),
            'blocks' => new Blocks($this->context, $this),
            'block' => new Block($this->context, $this),
            'menu' => new Menu($this->context, $this),
            default => throw new RuntimeException('Property not supported')
        };
    }

    public function pages(
        string $query = '',
    ): Pages {
        return (new Pages($this->context, $this))->find($query);
    }

    public function page(
        string $query,
        array $types = [],
        int $limit = 0,
        string $order = '',
    ): array {
        return (new Page($this->context, $this))->find($query, $types, $limit, $order);
    }
}
