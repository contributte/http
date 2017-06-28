<?php

namespace Contributte\Http\DI;

use Contributte\Http\Auth\BasicAuthenticator;
use Nette\DI\CompilerExtension;
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

		// Skip if there's no user
		if (!$config['users']) return;

		$def = $builder->addDefinition($this->prefix('authenticator'))
			->setClass(BasicAuthenticator::class, [$config['title']]);

		foreach ($config['users'] as $user => $password) {
			$def->addSetup('addUser', [$user, $password]);
		}
	}

}
