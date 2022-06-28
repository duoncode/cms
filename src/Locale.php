<?php

declare(strict_types=1);

namespace Conia;


class Locale
{
    public function __construct(
        public readonly string $locale,
        public readonly string $title,
        public readonly ?string $fallback = null,
        public readonly string|array|null $domain = null,
        public readonly ?string $urlPrefix = null,
    ) {
    }
}
