<?php

declare(strict_types=1);

namespace Conia\Core\Finder;

use Conia\Core\Context;
use Conia\Core\Exception\RuntimeException;
use Iterator;

class Menu implements Iterator
{
    protected readonly array $items;
    protected int $pointer = 0;

    public function __construct(Context $context, string $menu)
    {
        $this->items = $context->db->menus->get(['menu' => $menu])->all();

        if (count($this->items) === 0) {
            throw new RuntimeException('Menu not found');
        }
    }

    public function rewind(): void
    {
        $this->pointer = 0;
    }

    public function current(): MenuItem
    {
        return new MenuItem($this->items[$this->pointer]);
    }

    public function key(): int
    {
        return $this->pointer;
    }

    public function next(): void
    {
        $this->pointer++;
    }

    public function valid(): bool
    {
        return isset($this->items[$this->pointer]);
    }
}
