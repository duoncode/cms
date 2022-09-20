<?php

declare(strict_types=1);

namespace Conia\View;

use Conia\Sire\Schema;
use Conia\Chuck\Response\Response;
use Conia\Request;


class LoginSchema extends Schema
{
    protected function rules(): void
    {
        $this->add('login', _('Username or email'), 'text', 'required', 'maxlen:254');
        $this->add('password', _('Password'), 'text', 'required', 'maxlen:512');
        $this->add('rememberme', _('remember me'), 'bool');
    }
}

class Auth
{
    public function me()
    {
        return [
            'name' => 'User',
            'permissions' => [],
        ];
    }

    public function login(Request $request): Response
    {
        $schema = new LoginSchema();
        $response = $request->response();

        if ($schema->validate($request->json())) {
            $values = $schema->values();
            $auth = new \Conia\Auth($request);
            $user = $auth->authenticate(
                $values['login'],
                $values['password'],
                $values['rememberme'],
                true,
            );

            if ($user === false) {
                return $response->json(array_merge(
                    ['error' => _('Incorrect username or password.')],
                    $schema->pristineValues()
                ), 401);
            } else {
                return $response->json($user);
            }
        } else {
            $response->json(
                array_merge(
                    ['error' => _('Please provide username and password.')],
                    $schema->pristineValues()
                ),
                400
            );
        }

        return $response;
    }

    public function logout(Request $request): array
    {
        $auth = new \Conia\Auth($request);
        $auth->logout();

        return ['ok' => true];
    }
}
