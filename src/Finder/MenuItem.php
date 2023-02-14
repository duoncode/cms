<?php

declare(strict_types=1);

namespace Conia\Core\Finder;

class MenuItem
{
    protected readonly array $data;

    public function __construct(protected readonly array $menu)
    {
        $this->data = json_decode($menu['data'], true);
    }

    public function type(): string
    {
        return $this->menu['type'];
    }

    public function title(): string
    {
        return $this->data['title']['de'];
    }

    public function path(): string
    {
        return $this->data['path']['de'];
    }
}
