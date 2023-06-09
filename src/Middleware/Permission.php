<?php

declare(strict_types=1);

namespace Conia\Core\Middleware;

use Conia\Chuck\Di\Call;
use Conia\Chuck\Exception\HttpForbidden;
use Conia\Chuck\Exception\HttpUnauthorized;
use Conia\Chuck\Middleware;
use Conia\Chuck\Request;
use Conia\Chuck\Response;
use Conia\Core\Auth;
use Conia\Core\Config;
use Conia\Core\Permissions;
use Conia\Core\Users;

#[Call('init')]
class Permission implements Middleware
{
    protected Users $users;
    protected Config $config;

    public function __construct(protected string $permission)
    {
    }

    public function __invoke(Request $request, callable $next): Response
    {
        $auth = new Auth($request, $this->users, $this->config, $request->get('session'));
        $user = $auth->user();

        if ($user) {
            $permission = new Permissions();

            if (!$permission->has($user['role'], $this->permission)) {
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
