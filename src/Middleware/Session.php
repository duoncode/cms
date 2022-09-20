<?php

declare(strict_types=1);

namespace Conia\Middleware;

use Conia\Chuck\RequestInterface;
use Conia\Chuck\Response\ResponseInterface;


class Session
{
    public function __invoke(RequestInterface $request, callable $next): RequestInterface|ResponseInterface
    {
        $config = $request->config();
        $session = new \Conia\Session($config->app(), $config->get('session.authcookie'));
        $session->start();

        $request->addMethod('session', function () use ($session): \Conia\Session {
            return $session;
        });

        return $next($request);
    }
}
