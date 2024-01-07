<?php

declare(strict_types=1);

namespace Conia\Cms\Middleware;

use Attribute;
use Conia\Chuck\Di\Call;
use Conia\Chuck\Exception\HttpForbidden;
use Conia\Chuck\Exception\HttpUnauthorized;
use Conia\Chuck\Middleware;
use Conia\Chuck\Request;
use Conia\Chuck\Response;
use Conia\Cms\Auth;
use Conia\Cms\Config;
use Conia\Cms\Users;

#[Attribute, Call('init')]
class Permission implements Middleware
{
    protected Users $users;
    protected Config $config;

    public function __construct(protected string $permission)
    {
    }

    public function __invoke(Request $request, callable $next): Response
    {
        $session = $request->get('session', null);

        if (!$session) {
            throw new HttpUnauthorized();
        }

        $auth = new Auth(
            $request,
            $this->users,
            $this->config,
            $session,
        );
        $user = $auth->user();

        if ($user) {
            if (!$user->hasPermission($this->permission)) {
                throw new HttpForbidden();
            }

            return $next($request);
        }

        throw new HttpUnauthorized();
    }

    public function init(Users $users, Config $config): void
    {
        $this->users = $users;
        $this->config = $config;
    }
}
