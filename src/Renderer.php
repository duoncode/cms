<?php

declare(strict_types=1);

namespace Conia\Cms;

interface Renderer
{
    public function render(string $id, array $context): string;

    public function contentType(): string;
}
