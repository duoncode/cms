<?php

declare(strict_types=1);

namespace Duon\Cms\Middleware;

use Attribute;
use Duon\Cms\Auth;
use Duon\Cms\Config;
use Duon\Cms\Users;
use Duon\Core\Exception\HttpForbidden;
use Duon\Core\Exception\HttpUnauthorized;
use Duon\Wire\Call;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as Handler;

#[Attribute, Call('init')]
class Permission implements Middleware
{
	protected Users $users;
	protected Config $config;

	public function __construct(public readonly string $permission) {}

	public function process(Request $request, Handler $handler): Response
	{
		$session = $request->getAttribute('session', null);

		$auth = new Auth(
			$request,
			$this->users,
			$this->config,
			$session,
		);
		$user = $auth->user();

		if ($user) {
			if (!$user->hasPermission($this->permission)) {
				throw new HttpForbidden($request);
			}

			return $handler->handle($request);
		}

		throw new HttpUnauthorized($request);
	}

	public function init(Users $users, Config $config): void
	{
		$this->users = $users;
		$this->config = $config;
	}
}
