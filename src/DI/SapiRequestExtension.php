<?php declare(strict_types = 1);

namespace Contributte\Http\DI;

use Nette\DI\CompilerExtension;
use Nette\DI\Definitions\Statement;
use Nette\DI\ServiceDefinition;
use Nette\Http\Request;
use Nette\Http\UrlScript;
use Nette\Schema\Expect;
use Nette\Schema\Schema;
use RuntimeException;
use stdClass;

/**
 * @property-read stdClass $config
 */
class SapiRequestExtension extends CompilerExtension
{

	public function getConfigSchema(): Schema
	{
		return Expect::structure([
			'url' => Expect::string()->required(),
			'post' => Expect::array(),
			'files' => Expect::array(),
			'cookies' => Expect::array(),
			'headers' => Expect::array(),
			'method' => Expect::string(),
			'remoteAddress' => Expect::string(),
			'remoteHost' => Expect::string(),
			'rawBodyCallback' => Expect::mixed(),
		]);
	}

	public function beforeCompile(): void
	{
		// Breaks at other mode then CLI
		if (PHP_SAPI !== 'cli') {
			return;
		}

		$builder = $this->getContainerBuilder();

		// Verify that we have http.request
		if (!$builder->hasDefinition('http.request')) {
			throw new RuntimeException('Service http.request is needed');
		}

		$requestDefinition = $builder->getDefinition('http.request');

		assert($requestDefinition instanceof ServiceDefinition);

		$config = $this->config;
		$requestDefinition->setFactory(Request::class, [
			new Statement(UrlScript::class, [$config->url]),
			$config->post,
			$config->files,
			$config->cookies,
			$config->headers,
			$config->method,
			$config->remoteAddress,
			$config->remoteHost,
			$config->rawBodyCallback,
		]);
	}

}
