<?php

declare(strict_types=1);

namespace Duon\Cms\Tests\End2End;

use Duon\Cms\Tests\End2EndTestCase;

/**
 * End-to-end tests for panel authentication and login flow.
 *
 * @internal
 *
 * @coversNothing
 */
final class PanelAuthTest extends End2EndTestCase
{
	public function testProtectedPanelRouteRedirectsGuestToLogin(): void
	{
		$response = $this->makeRequest('GET', '/panel');

		$this->assertResponseStatus(303, $response);
		$this->assertSame('/panel/login?next=%2Fpanel', $response->getHeaderLine('Location'));
	}

	public function testLoginPageRendersForGuest(): void
	{
		$response = $this->makeRequest('GET', '/panel/login');

		$this->assertResponseOk($response);
		$html = $this->getHtmlResponse($response);
		$this->assertStringContainsString('<h1>Login</h1>', $html);
		$this->assertStringContainsString('action="/panel/login"', $html);
	}

	public function testLoginWithValidCredentialsRedirectsToPanel(): void
	{
		$login = 'panel-login-user';
		$userId = $this->createTestUser([
			'uid' => 'panel-login-user',
			'username' => $login,
			'email' => 'panel-login@example.com',
			'pwhash' => password_hash('password', PASSWORD_ARGON2ID),
		]);
		$this->createdUserIds[] = $userId;

		$response = $this->makeRequest('POST', '/panel/login', [
			'body' => [
				'login' => $login,
				'password' => 'password',
				'rememberme' => false,
				'next' => '/panel',
			],
		]);

		$this->assertResponseStatus(303, $response);
		$this->assertSame('/panel', $response->getHeaderLine('Location'));
	}

	public function testLoginWithInvalidCredentialsShowsMessage(): void
	{
		$response = $this->makeRequest('POST', '/panel/login', [
			'body' => [
				'login' => 'nobody@example.com',
				'password' => 'wrong-password',
				'rememberme' => false,
			],
		]);

		$this->assertResponseOk($response);
		$html = $this->getHtmlResponse($response);
		$this->assertStringContainsString('Invalid username or password', $html);
	}

	public function testAuthenticatedPanelUserGetsRedirectedAwayFromLogin(): void
	{
		$this->authenticateAs('editor');

		$response = $this->makeRequest('GET', '/panel/login', [
			'authToken' => $this->defaultAuthToken,
		]);

		$this->assertResponseStatus(303, $response);
		$this->assertSame('/panel', $response->getHeaderLine('Location'));
	}

	public function testHtmxGuestRequestReturnsHxRedirectHeader(): void
	{
		$response = $this->makeRequest('GET', '/panel', [
			'headers' => ['HX-Request' => 'true'],
		]);

		$this->assertResponseStatus(401, $response);
		$this->assertSame('/panel/login?next=%2Fpanel', $response->getHeaderLine('HX-Redirect'));
	}

	public function testUserWithoutPanelPermissionGetsRedirectedToLogin(): void
	{
		$token = $this->createAuthenticatedUser('system');

		$response = $this->makeRequest('GET', '/panel', [
			'authToken' => $token,
		]);

		$this->assertResponseStatus(303, $response);
		$this->assertSame('/panel/login?next=%2Fpanel', $response->getHeaderLine('Location'));
	}
}
