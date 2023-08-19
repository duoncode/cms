<?php

declare(strict_types=1);

namespace Conia\Core\Middleware;

use Conia\Chuck\Middleware;
use Conia\Chuck\Request;
use Conia\Chuck\Response;
use Conia\Core\Config;
use Conia\Core\Users;
use Conia\Quma\Database;

class Session implements Middleware
{
    public function __construct(protected Config $config, protected Database $db)
    {
    }

    public function __invoke(Request $request, callable $next): Response
    {
        $session = new \Conia\Core\Session(
            $this->config->app(),
            $this->config->get('session.options'),
            $this->config->get('session.handler', null),
        );

        $session->start();
        $expires = $this->config->get('session.options')['gc_maxlifetime'];
        $lastActivity = $session->lastActivity();

        if ($lastActivity && (time() - $lastActivity > $expires)) {
            $session->forget();
            $session->start();
        }

        $session->signalActivity();
        $userId = $session->authenticatedUserId();

        if ($userId) {
            $user = (new Users($this->db))->byId($userId);
            $request->set('user', $user);
        }

        $request->set('session', $session);

        return $next($request);
    }
}
