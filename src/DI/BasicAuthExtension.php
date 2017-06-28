<?php

namespace Contributte\Http\DI;

use Contributte\Http\Auth\BasicAuthenticator;
use LogicException;
use Nette\DI\CompilerExtension;
use Nette\Http\IRequest;
use Nette\Http\IResponse;
use Nette\PhpGenerator\ClassType;
use Nette\Utils\Validators;

/**
 * @author Milan Felix Sulc <sulcmil@gmail.com>
 */
class BasicAuthExtension extends CompilerExtension
{

	/** @var array */
	private $defaults = [
		'enabled' => FALSE,
		'title' => 'Restrict zone',
		'users' => [],
	];

	/**
	 * Register services
	 *
	 * @return void
	 */
	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();
		$config = $this->validateConfig($this->defaults);

		Validators::assertField($config, 'enabled', 'bool');
		Validators::assertField($config, 'users', 'array');

		// Skip if its disabled
		if (!$config['enabled']) return;

		// Throws if there's no user
		if (!$config['users']) throw new LogicException('You have to define any user or disable extension');

		$def = $builder->addDefinition($this->prefix('authenticator'))
			->setClass(BasicAuthenticator::class, [$config['title']]);

		foreach ($config['users'] as $user => $password) {
			$def->addSetup('addUser', [$user, $password]);
		}
	}

	/**
	 * Decorate initialize
	 *
	 * @param ClassType $class
	 * @return void
	 */
	public function afterCompile(ClassType $class)
	{
		$config = $this->validateConfig($this->defaults);

		// Skip if its disabled or no user defined
		if (!$config['enabled'] || !$config['users']) return;

		$initialize = $class->methods['initialize'];
		$initialize->addBody('$this->getService(?)->authenticate($this->getByType(?), $this->getByType(?));', [
			$this->prefix('authenticator'),
			IRequest::class,
			IResponse::class,
		]);
	}

}
