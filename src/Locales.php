<?php

declare(strict_types=1);

namespace Conia;

use \Closure;
use \RuntimeException;
use \ValueError;


class Locales
{
    /** @var array<string, Locale> */
    protected array $locales = [];
    protected ?string $default = null;
    protected ?Closure $negotiator = null;

    public function add(
        string $id,
        string $title,
        ?string $fallback = null,
        string|array|null $domain = null,
        ?string $urlPrefix = null,
    ) {
        $this->locales[$id] =  new Locale($this, $id, $title, $fallback, $domain, $urlPrefix);
    }

    public function setDefault(string $locale): void
    {
        if ($this->exists($locale)) {
            $this->default = $locale;
            return;
        }

        throw new ValueError('Locale does not exist. Add all your locales first before setting the default.');
    }

    public function setNegotiator(Closure $func): void
    {
        $this->negotiator = $func;
    }

    protected function exists(string $id): bool
    {
        return array_key_exists($id, $this->locales);
    }

    public function get(string $id): Locale
    {
        return $this->locales[$id];
    }

    protected function fromBrowser(): string|false
    {
        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $accepted = locale_accept_from_http($_SERVER['HTTP_ACCEPT_LANGUAGE']);

            if ($accepted !== false) {
                if ($this->exists($accepted)) {
                    return $accepted;
                } else {
                    // e. g. 'en_US' -> 'en'
                    $short = strtok($accepted, '_');

                    if ($this->exists($short)) {
                        return $short;
                    }
                }
            }
        }

        return false;
    }

    public function fromRequest(Request $request): Locale
    {
        // By domain
        $host = strtolower(explode(':', $request->host())[0]);
        foreach ($this->locales as $locale) {
            foreach ($locale->domains as $domain) {
                if ($host === $domain) {
                    return $locale;
                }
            }
        }

        // From URL path prefix. e. g. http://example.com/en_EN/path/to/page
        $prefix = explode('/', trim(parse_url($request->url())['path'], '/'))[0];
        foreach ($this->locales as $locale) {
            if ($prefix === $locale->urlPrefix) {
                return $locale;
            }
        }

        // From session
        $locale = $request->session()->get('locale', false);
        if ($locale && $this->exists($locale)) {
            return $this->locales[$locale];
        }

        // From the locales the browser says the user accepts
        $locale =  $this->fromBrowser();
        if ($locale && $this->exists($locale)) {
            return $this->locales[$locale];
        }

        // default locale from config file
        if ($this->default !== null) {
            return $this->locales[$this->default];
        }

        return null;
    }

    public function negotiate(Request $request): Locale
    {
        if (count($this->locales) === 0) {
            throw new RuntimeException('No locales available. Add at least your default language.');
        }

        if ($this->negotiator) {
            return ($this->negotiator)($request);
        }

        return  $this->fromRequest($request);
    }
}
