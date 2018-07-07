<?php declare(strict_types = 1);

namespace Contributte\Http\DI;

use Contributte\Http\Curl\CurlClient;
use Nette\DI\CompilerExtension;

class CurlExtension extends CompilerExtension
{

	/**
	 * Register services
	 */
	public function loadConfiguration(): void
	{
		$builder = $this->getContainerBuilder();

		$builder->addDefinition($this->prefix('client'))
			->setClass(CurlClient::class);
	}

}
