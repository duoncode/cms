<?php

declare(strict_types=1);

namespace Conia\Middleware;

use Conia\Chuck\Request;
use Conia\Chuck\Response;
use Conia\Config;

class Session
{
    public function __construct(protected Config $config)
    {
    }

    public function __invoke(Request $request, callable $next): Response
    {
        $session = new \Conia\Session\Session(
            $this->config->app(),
            $this->config->get('sessionOptions')
        );
        $session->start();

        $request->attribute('session', $session);

        return $next($request);
    }
}
