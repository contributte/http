<?php declare(strict_types = 1);

namespace Contributte\Http\DI;

use Contributte\Http\Auth\BasicAuthenticator;
use LogicException;
use Nette\DI\CompilerExtension;
use Nette\Http\IRequest;
use Nette\Http\IResponse;
use Nette\PhpGenerator\ClassType;
use Nette\Schema\Expect;
use Nette\Schema\Schema;

class BasicAuthExtension extends CompilerExtension
{

	public function getConfigSchema(): Schema
	{
		return Expect::structure([
			'enabled' => Expect::bool(false),
			'title' => Expect::string('Restrict zone'),
			'users' => Expect::arrayOf('string'),
		]);
	}

	/**
	 * Register services
	 */
	public function loadConfiguration(): void
	{
		$builder = $this->getContainerBuilder();
		$config = (array) $this->config;

		// Skip if its disabled
		if ($config['enabled'] !== true) {
			return;
		}

		// Throws if there's no user
		if ($config['users'] === []) {
			throw new LogicException('You have to define any user or disable extension');
		}

		$def = $builder->addDefinition($this->prefix('authenticator'))
			->setType(BasicAuthenticator::class)
			->setArguments([$config['title']]);

		foreach ($config['users'] as $user => $values) {
			if (is_string($values)) {
				trigger_error('Usage of `$username => $password` is deprecated, use `$username => ["password" => $password]` instead', E_USER_DEPRECATED);
				$password = $values;
				$unsecured = true;
			} else {
				$password = $values['password'];
				$unsecured = $values['unsecured'] ?? false;
			}
			$def->addSetup('addUser', [$user, $password, $unsecured]);
		}
	}

	/**
	 * Decorate initialize
	 */
	public function afterCompile(ClassType $classType): void
	{
		$config = (array) $this->config;

		// Skip if its disabled or no user defined
		if ($config['enabled'] !== true || $config['users'] === []) {
			return;
		}

		$initialize = $classType->getMethod('initialize');
		$initialize->addBody('$this->getService(?)->authenticate($this->getByType(?), $this->getByType(?));', [
			$this->prefix('authenticator'),
			IRequest::class,
			IResponse::class,
		]);
	}

}
