<?php

namespace Contributte\Http\Curl\DI;

use Contributte\Http\Curl\CurlClient;
use Nette\DI\CompilerExtension;

/**
 * @author Milan Felix Sulc <sulcmil@gmail.com>
 */
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
