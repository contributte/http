<?php

namespace Contributte\Http\DI;

use Contributte\Http\Curl\CurlClient;
use Nette\DI\CompilerExtension;

class CurlExtension extends CompilerExtension
{

	/**
	 * Register services
	 *
	 * @return void
	 */
	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();

		$builder->addDefinition($this->prefix('client'))
			->setClass(CurlClient::class);
	}

}
