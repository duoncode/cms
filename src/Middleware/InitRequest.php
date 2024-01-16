<?php

declare(strict_types=1);

namespace Conia\Cms\Middleware;

use Conia\Http\Middleware;
use Conia\Http\Request;
use Conia\Http\Response;

class InitRequest extends Middleware
{
    public function handle(Request $request, callable $next): Response
    {
        // See if it's a JSON request
        if (
            isset($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'
        ) {
            $request->set('isXhr', true);
        } else {
            if ($request->hasHeader('Accept') && $request->header('Accept') === 'application/json') {
                $request->set('isXhr', true);
            } else {
                $request->set('isXhr', false);
            }
        }

        return $next($request);
    }
}
