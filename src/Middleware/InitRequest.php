<?php

declare(strict_types=1);

namespace Conia\Core\Middleware;

use Conia\Chuck\Middleware;
use Conia\Chuck\Request;
use Conia\Chuck\Response;
use Conia\Core\Config;

class InitRequest implements Middleware
{
    public function __construct(protected Config $config)
    {
    }

    public function __invoke(Request $request, callable $next): Response
    {
        // Set current locale
        $locales = $this->config->locales();
        $locale = $locales->negotiate($request);
        $request->set('locale', $locale);
        $request->set('defaultLocale', $locales->getDefault());

        // See if it's a JSON request
        if (
            isset($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'
        ) {
            $request->set('isJson', true);
        } else {
            if ($request->hasHeader('Accept') && $request->header('Accept') === 'application/json') {
                $request->set('isJson', true);
            } else {
                $request->set('isJson', false);
            }
        }

        return $next($request);
    }
}
