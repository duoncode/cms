<?php

declare(strict_types=1);

namespace Conia\Core\Middleware;

use Conia\Chuck\Middleware;
use Conia\Chuck\Request;
use Conia\Chuck\Response;
use Conia\Core\Config;

class Locale implements Middleware
{
    public function __construct(protected Config $config)
    {
    }

    public function __invoke(Request $request, callable $next): Response
    {
        $locales = $this->config->locales();
        $locale = $locales->negotiate($request);
        $request->set('locale', $locale);

        return $next($request);
    }
}
