<?php

declare(strict_types=1);

namespace Conia\View;

use Chuck\Schema;
use Chuck\Body\Json;
use Conia\Request;
use Conia\Response;


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
        $response = $request->response;
        $response->header('Content-Type', 'application/json');

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
                $response->statusCode(401);
                $response->body(new Json(
                    array_merge(
                        ['error' => _('Incorrect username or password.')],
                        $schema->pristineValues()
                    )
                ));
            } else {
                $response->body(new Json($user));
            }
        } else {
            $response->statusCode(400);
            $response->body(new Json(
                array_merge(
                    ['error' => _('Please provide username and password.')],
                    $schema->pristineValues()
                )
            ));
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
