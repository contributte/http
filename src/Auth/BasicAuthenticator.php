<?php

namespace Contributte\Http\Auth;

use Nette\Http\IRequest;
use Nette\Http\IResponse;

class BasicAuthenticator
{

	/** @var string */
	private $title;

	/** @var array */
	private $users = [];

	/**
	 * @param string $title
	 */
	public function __construct($title)
	{
		$this->title = $title;
	}

	/**
	 * @param string $user
	 * @param string $password
	 * @return static
	 */
	public function addUser($user, $password)
	{
		$this->users[$user] = $password;

		return $this;
	}

	/**
	 * @param IRequest $request
	 * @param IResponse $response
	 * @return void
	 */
	public function authenticate(IRequest $request, IResponse $response)
	{
		$user = $request->getUrl()->getUser();
		$password = $request->getUrl()->getPassword();

		if (!$this->auth($user, $password)) {
			$response->setHeader('WWW-Authenticate', sprintf('Basic realm="%s"', $this->title));
			$response->setCode(IResponse::S401_UNAUTHORIZED);
			echo '<h1>Authentication failed.</h1>';
			die();
		}
	}

	/**
	 * @param string $user
	 * @param string $password
	 * @return bool
	 */
	protected function auth($user, $password)
	{
		if (!isset($this->users[$user])) return FALSE;

		return $this->users[$user] === $password;
	}

}
