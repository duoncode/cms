<?php

declare(strict_types=1);

namespace Conia;

use \Closure;
use \ValueError;


class Locales
{
    /** @var array<string, Locale> */
    protected array $locales = [];
    protected ?string $default = null;
    protected ?Closure $negotiator = null;

    public function areUsed(): bool
    {
        return count($this->locales) > 0;
    }

    public function add(
        string $locale,
        string $title,
        ?string $fallback = null,
        string|array|null $domain = null,
        ?string $urlPrefix = null,
    ) {
        $this->locales[$locale] =  new Locale($locale, $title, $fallback, $domain, $urlPrefix);
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

    protected function exists(string $locale): bool
    {
        return array_key_exists($locale, $this->locales);
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

    public function getLocale(Request $request): ?Locale
    {
        // TODO: from domain
        // TODO: from urlprefix

        // from session
        $locale = $request->session()->get('locale', false);
        if ($locale && $this->exists($locale)) {
            return $this->locales[$locale];
        }

        // from the locales the browser says the user accepts
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

    public function negotiate(Request $request): ?Locale
    {
        if ($this->negotiator) {
            return ($this->negotiator)($request);
        }

        return  $this->getLocale($request);
    }
}
