<?php

declare(strict_types=1);

namespace Conia\Core;

use Conia\Core\Finder\Pages;

abstract class Collection
{
    public function __construct(
        public readonly Finder $find,
    ) {
    }

    abstract public function entries(): Pages;

    public function title(): string
    {
        return preg_replace('/(?<!^)[A-Z]/', ' $0', static::class);
    }

    public function listing(): array
    {
        return array_map(function ($page) {
            return [
                'title' => $page->title(),
                'type' => $page->type(),
            ];
        }, iterator_to_array($this->entries()));
    }
}
