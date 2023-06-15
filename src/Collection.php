<?php

declare(strict_types=1);

namespace Conia\Core;

use Conia\Core\Finder\Nodes;

abstract class Collection
{
    public function __construct(
        public readonly Finder $find,
    ) {
    }

    abstract public function entries(): Nodes;

    public function title(): string
    {
        return preg_replace('/(?<!^)[A-Z]/', ' $0', static::class);
    }

    public function listing(): array
    {
        return array_map(function ($node) {
            return [
                'uid' => $node->meta('uid'),
                'title' => $node->title(),
                'type' => $node->type(),
                'changed' => $node->meta('changed'),
                'created' => $node->meta('created'),
                'editor' => (
                    $node->meta('editor_data')['name'] ??
                    $node->meta('editor_username')
                ) ?? $node->meta('editor_email'),
                'creator' => (
                    $node->meta('creator_data')['name'] ??
                    $node->meta('creator_username')
                ) ?? $node->meta('creator_email'),
            ];
        }, iterator_to_array($this->entries()));
    }
}
