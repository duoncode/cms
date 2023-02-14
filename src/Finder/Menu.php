<?php

declare(strict_types=1);

namespace Conia\Core\Finder;

use Conia\Core\Context;
use Conia\Core\Exception\RuntimeException;
use Iterator;

class Menu implements Iterator
{
    protected array $items;
    protected int $pointer = 0;

    public function __construct(
        protected readonly Context $context,
        string $menu
    ) {
        $this->items = $this->makeTree(
            $context->db->menus->get(['menu' => $menu])->all()
        );

        if (count($this->items) === 0) {
            throw new RuntimeException('Menu not found');
        }
    }

    public function rewind(): void
    {
        reset($this->items);
    }

    public function current(): MenuItem
    {
        return new MenuItem($this->context, current($this->items));
    }

    public function key(): string
    {
        return key($this->items);
    }

    public function next(): void
    {
        next($this->items);
    }

    public function valid(): bool
    {
        return key($this->items) !== null;
    }

    protected function makeTree(array $items): array
    {
        $tree = [];

        foreach ($items as $item) {
            $arr = &$tree;

            foreach (explode('.', $item['path']) as $segment) {
                if (isset($arr[$segment])) {
                    $arr = &$arr[$segment]['children'];
                } else {
                    $arr[$segment] = $item;
                    $arr[$segment]['children'] = [];
                }
            }
        }

        return $tree;
    }
}
