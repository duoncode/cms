<?php

declare(strict_types=1);

namespace Conia\Core\Middleware;

use Attribute;
use Conia\Core\Auth;
use Conia\Core\Config;
use Conia\Core\Exception\HttpForbidden;
use Conia\Core\Exception\HttpUnauthorized;
use Conia\Core\Users;
use Conia\Http\Middleware;
use Conia\Http\Request;
use Conia\Http\Response;
use Conia\Wire\Call;

#[Attribute, Call('init')]
class Permission extends Middleware
{
    protected Users $users;
    protected Config $config;

    public function __construct(protected string $permission)
    {
    }

    public function handle(Request $request, callable $next): Response
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
