<?php

declare(strict_types=1);

namespace Conia;


class Locale
{
    public readonly string $urlPrefix;

    public function __construct(
        public readonly string $id,
        public readonly string $title,
        public readonly ?string $fallback = null,
        public readonly string|array|null $domain = null,
        ?string $urlPrefix = null,
    ) {
        $this->urlPrefix = $urlPrefix ?: $id;
    }
}
