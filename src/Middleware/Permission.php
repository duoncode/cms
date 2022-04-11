<?php

declare(strict_types=1);

namespace Conia\Middleware;

use Chuck\RequestInterface;
use Chuck\ResponseInterface;
use Chuck\Error\{HttpUnauthorized, HttpForbidden};
use Conia\Request;
use Conia\Auth;
use Conia\Permissions;


class Permission
{
    public function __construct(protected string $permission)
    {
    }

    public function __invoke(Request $request, callable $next): RequestInterface|ResponseInterface
    {
        $auth = new Auth($request);
        $user = $auth->user();

        if ($user) {
            $permission = new Permissions($request->getConfig());

            if (!$permission->has($user['role'], $this->permission)) {
                throw new HttpForbidden();
            }

            return $next($request);
        }

        throw new HttpUnauthorized();
    }
}
