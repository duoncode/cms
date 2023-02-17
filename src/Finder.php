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

    public function menu(string $menu): Menu
    {
        return new Menu($this->context, $menu);
    }

    public function block(
        string $uid,
        array $templateContext = [],
        ?bool $deleted = false,
        ?bool $published = true
    ): Block
    {
        return new Block($this->context, $this, $uid, $templateContext, $deleted, $published);
    }
}
