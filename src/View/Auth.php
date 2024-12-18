<?php

declare(strict_types=1);

namespace FiveOrbs\Cms\View;

use FiveOrbs\Cms\Middleware\Permission;
use FiveOrbs\Cms\Schema;
use FiveOrbs\Core\Factory;
use FiveOrbs\Core\Request;
use FiveOrbs\Core\Response;

class Auth
{
	public function __construct(
		protected readonly Factory $factory,
		protected readonly \FiveOrbs\Cms\Auth $auth,
	) {}

	#[Permission('authenticated')]
	public function me()
	{
		return [
			'name' => 'User',
			'permissions' => [],
		];
	}

	public function login(Request $request): Response
	{
		$schema = new Schema\Login();
		$response = Response::create($this->factory);

		if ($schema->validate($request->json())) {
			$values = $schema->values();
			$user = $this->auth->authenticate(
				$values['login'],
				$values['password'],
				$values['rememberme'],
				true,
			);

			if ($user === false) {
				return $response->json(array_merge(
					['error' => _('Falscher Benutzername oder Passwort'), 'loginType' => 'panel'],
					$schema->pristineValues(),
				), 400);
			}

			return $response->json($user->array());
		}

		$response->json(
			array_merge(
				['error' => _('Bitte Benutzernamen und Passwort eingeben'), 'loginType' => 'panel'],
				$schema->pristineValues(),
			),
			400,
		);

		return $response;
	}

	public function tokenLogin(Request $request): Response
	{
		$schema = new Schema\TokenLogin();
		$response = Response::create($this->factory);

		if ($schema->validate($request->json())) {
			$values = $schema->values();
			$user = $this->auth->authenticateByOneTimeToken(
				$values['token'],
				true,
			);

			if ($user === false) {
				return $this->unauthorized($response, _('Invalid token'), 'token');
			}

			return $response->json($user->array());
		}

		return $this->unauthorized($response, _('No or invalid auth token provided'), 'token');
	}

	public function token(Request $request): Response
	{
		$response = Response::create($this->factory);

		$authToken = '';
		$bearer = $request->header('Authentication');

		if (preg_match('/Bearer\s(\S+)/', $bearer, $matches)) {
			$authToken = $matches[1];
		}

		if (!$authToken) {
			return $this->unauthorized($response, _('No auth token provided'), 'token');
		}

		$oneTimeToken = $this->auth->getOneTimeToken($authToken);

		if (!$oneTimeToken) {
			return $this->unauthorized($response, _('Invalid auth token'), 'token');
		}

		return $response->json([
			'onetimeToken' => $oneTimeToken,
		], 200);
	}

	public function invalidateToken(Request $request): Response
	{
		$token = $request->json()['token'];

		if ($token) {
			$this->auth->invalidateOneTimeToken($token);
		}

		return  Response::create($this->factory)->json([
			'success' => true,
		], 200);
	}

	protected function unauthorized(Response $response, string $message, string $loginType)
	{
		$response->header('WWW-Authenticate', 'Bearer realm="FiveOrbs CMS"');

		return $response->json([
			'error' => $message,
			'loginType' => $loginType,
		], 401);
	}

	public function logout(): array
	{
		$this->auth->logout();

		return ['ok' => true];
	}
}
