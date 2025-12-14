<?php declare(strict_types = 1);

namespace Contributte\Http\Auth;

use Nette\Http\IRequest;
use Nette\Http\IResponse;
use Tracy\Debugger;

class BasicAuthenticator
{

	private string $title;

	/** @var array<string, array{password: string, unsecured: bool}> */
	private array $users = [];

	public function __construct(string $title)
	{
		$this->title = $title;
	}

	public function addUser(string $user, string $password, bool $unsecured): self
	{
		$this->users[$user] = [
			'password' => $password,
			'unsecured' => $unsecured,
		];

		return $this;
	}

	public function authenticate(IRequest $request, IResponse $response): void
	{
		[$user, $password] = $this->parseBasicAuth($request);

		if (!$this->auth($user, $password)) {
			if (class_exists(Debugger::class)) {
				Debugger::$productionMode = true;
			}

			$response->setHeader('WWW-Authenticate', sprintf('Basic realm="%s"', $this->title));
			$response->setCode(IResponse::S401_Unauthorized);

			echo '<h1>Authentication failed.</h1>';
			die;
		}
	}

	protected function auth(string $user, string $password): bool
	{
		if ($user === '' || !isset($this->users[$user])) {
			return false;
		}

		$userData = $this->users[$user];
		if ($userData['unsecured'] && !hash_equals($password, $userData['password'])) {
			return false;
		}

		return $userData['unsecured'] || password_verify($password, $userData['password']);
	}

	/**
	 * Parse Basic auth credentials from request
	 *
	 * @return array{string, string}
	 */
	private function parseBasicAuth(IRequest $request): array
	{
		$header = $request->getHeader('Authorization');
		if ($header !== null && str_starts_with($header, 'Basic ')) {
			$credentials = base64_decode(substr($header, 6), true);
			if ($credentials !== false && str_contains($credentials, ':')) {
				$parts = explode(':', $credentials, 2);

				return [$parts[0], $parts[1]];
			}
		}

		return ['', ''];
	}

}
