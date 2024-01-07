<?php

declare(strict_types=1);

namespace Conia\Cms\Middleware;

use Conia\Cms\Config;
use Conia\Cms\Users;
use Conia\Http\Middleware;
use Conia\Http\Request;
use Conia\Http\Response;
use Conia\Quma\Database;

class Session extends Middleware
{
    public function __construct(protected Config $config, protected Database $db)
    {
    }

    public function handle(Request $request, callable $next): Response
    {
        $session = new \Conia\Cms\Session(
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
