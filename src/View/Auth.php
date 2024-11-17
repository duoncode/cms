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
					['error' => _('Falscher Benutzername oder Passwort')],
					$schema->pristineValues(),
				), 400);
			}

			return $response->json($user->array());
		}

		$response->json(
			array_merge(
				['error' => _('Bitte Benutzernamen und Passwort eingeben')],
				$schema->pristineValues(),
			),
			400,
		);

		return $response;
	}

	public function logout(): array
	{
		$this->auth->logout();

		return ['ok' => true];
	}
}
