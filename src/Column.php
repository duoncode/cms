<?php

declare(strict_types=1);

namespace Conia\Core;

use Closure;

final class Column
{
    private bool|Closure $bold = false;
    private bool|Closure $italic = false;
    private bool|Closure $badge = false;
    private bool $date = false;
    private string|Closure $color = '';

    public function __construct(
        public readonly string $title,
        public readonly string|Closure $field,
    ) {
    }

    public static function new(
        string|Closure $title,
        string|Closure $field,
    ): self {
        return new self($title, $field);
    }

    public function bold(bool|Closure $bold): self
    {
        $this->bold = $bold;

        return $this;
    }

    public function italic(bool|Closure $italic): self
    {
        $this->italic = $italic;

        return $this;
    }

    public function badge(bool|Closure $badge): self
    {
        $this->badge = $badge;

        return $this;
    }

    public function date(bool|Closure $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function get(Node $node): array
    {
        return [
            'value' => is_string($this->field)
                ? $this->getValue($node, $this->field)
                : ($this->field)($node),
            'bold' => is_bool($this->bold) ? $this->bold : ($this->bold)($node),
            'italic' => is_bool($this->italic) ? $this->italic : ($this->italic)($node),
            'badge' => is_bool($this->badge) ? $this->badge : ($this->badge)($node),
            'date' => is_bool($this->date) ? $this->date : ($this->date)($node),
            'color' => is_string($this->color) ? $this->color : ($this->color)($node),
        ];
    }

    private function getValue(Node $node, string $field): mixed
    {
        switch ($field) {
            case 'title':
                return $node->title();
            case 'type':
                return $node->type();
            case 'uid':
            case 'published':
            case 'hidden':
            case 'locked':
            case 'created':
            case 'changed':
            case 'deleted':
            case 'content':
            case 'type':
            case 'classname':
                return $node->meta($field);
            case 'editor':
                return (
                    $node->meta('editor_data')['name'] ??
                    $node->meta('editor_username')
                ) ?? $node->meta('editor_email');
            case 'creator':
                return (
                    $node->meta('creator_data')['name'] ??
                    $node->meta('creator_username')
                ) ?? $node->meta('creator_email');
            default:
                return $node->getValue($field);
        }
    }
}
