<?php declare(strict_types = 1);

namespace Contributte\Http\DI;

use Contributte\Http\Auth\BasicAuthenticator;
use Nette\DI\CompilerExtension;
use Nette\Http\IRequest;
use Nette\Http\IResponse;
use Nette\PhpGenerator\ClassType;
use Nette\Schema\Expect;
use Nette\Schema\Schema;
use stdClass;

/**
 * @property-read stdClass $config
 */
class BasicAuthExtension extends CompilerExtension
{

	public function getConfigSchema(): Schema
	{
		return Expect::structure([
			'enabled' => Expect::bool(false),
			'title' => Expect::string('Restricted zone'),
			'users' => Expect::arrayOf(Expect::structure([
				'password' => Expect::string()->required(),
				'unsecured' => Expect::bool(false),
			]))->min(1),
		]);
	}

	public function loadConfiguration(): void
	{
		$builder = $this->getContainerBuilder();
		$config = $this->config;

		// Skip if its disabled
		if (!$config->enabled) {
			return;
		}

		$def = $builder->addDefinition($this->prefix('authenticator'))
			->setType(BasicAuthenticator::class)
			->setArguments([$config->title]);

		foreach ($config->users as $user => $values) {
			$def->addSetup('addUser', [$user, $values->password, $values->unsecured]);
		}
	}

	public function afterCompile(ClassType $class): void
	{
		$config = $this->config;

		// Skip if its disabled
		if (!$config->enabled) {
			return;
		}

		$initialize = $class->methods['initialize'];
		$initialize->addBody('$this->getService(?)->authenticate($this->getByType(?), $this->getByType(?));', [
			$this->prefix('authenticator'),
			IRequest::class,
			IResponse::class,
		]);
	}

}
