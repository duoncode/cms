<?php

declare(strict_types=1);

namespace Conia\Cms\Middleware;

use Attribute;
use Conia\Cms\Auth;
use Conia\Cms\Users;
use Conia\Core\Config;
use Conia\Core\Exception\HttpForbidden;
use Conia\Core\Exception\HttpUnauthorized;
use Conia\Wire\Call;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as Handler;

#[Attribute, Call('init')]
class Permission implements Middleware
{
    protected Users $users;
    protected Config $config;

    public function __construct(public readonly string $permission)
    {
    }

    public function process(Request $request, Handler $handler): Response
    {
        $session = $request->getAttribute('session', null);

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

            return $handler->handle($request);
        }

        throw new HttpUnauthorized();
    }

    public function init(Users $users, Config $config): void
    {
        $this->users = $users;
        $this->config = $config;
    }
}
