<?php

declare(strict_types=1);

namespace Conia\Core\Value;

class Youtube extends Value
{
    public function __toString(): string
    {
        return '<div class="video-container aspect-ratio-' . $this->data['aspect-ratio'] . '">' .
            '<iframe class="video" src="https://www.youtube.com/embed/' . $this->data['id'] .
            '" allowfullscreen></iframe>' .
        '</div>';
    }

    public function unwrap(): mixed
    {
        return $this->data['id'] ?? null;
    }

    public function json(): mixed
    {
        return $this->unwrap();
    }

    public function isset(): bool
    {
        return isset($this->data['id']) ? true : false;
    }
}
