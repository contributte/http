<?php declare(strict_types = 1);

namespace Contributte\Http\Auth;

use Nette\Http\IRequest;
use Nette\Http\IResponse;
use Tracy\Debugger;

class BasicAuthenticator
{

	/** @var string */
	private $title;

	/** @var mixed[] */
	private $users = [];

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
		$user = $request->getUrl()->getUser();
		$password = $request->getUrl()->getPassword();

		if (!$this->auth($user, $password)) {
			if (class_exists(Debugger::class)) {
				Debugger::$productionMode = true;
			}

			$response->setHeader('WWW-Authenticate', sprintf('Basic realm="%s"', $this->title));
			$response->setCode(IResponse::S401_UNAUTHORIZED);

			echo '<h1>Authentication failed.</h1>';
			die;
		}
	}

	protected function auth(string $user, string $password): bool
	{
		if (!isset($this->users[$user])) {
			return false;
		}

		if (
			($this->users[$user]['unsecured'] === true && !hash_equals($password, $this->users[$user]['password'])) ||
			($this->users[$user]['unsecured'] === false && !password_verify($password, $this->users[$user]['password']))
		) {
			return false;
		}

		return true;
	}

}
