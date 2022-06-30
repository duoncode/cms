<?php

declare(strict_types=1);

namespace Conia;


class Locale
{
    public readonly string $urlPrefix;
    public readonly array $domains;

    public function __construct(
        protected readonly Locales $locales,
        public readonly string $id,
        public readonly string $title,
        public readonly ?string $fallback = null,
        string|array|null $domains = null,
        ?string $urlPrefix = null,
    ) {
        if ($domains) {
            $this->domains = is_string($domains) ?
                [strtolower($domains)] :
                array_map(fn ($d) => strtolower($d), $domains);
        } else {
            $this->domains = [];
        }

        $this->urlPrefix = $urlPrefix ?: $id;
    }

    public function fallback(): ?Locale
    {
        return $this->fallback ? $this->locales->get($this->fallback) : null;
    }
}
