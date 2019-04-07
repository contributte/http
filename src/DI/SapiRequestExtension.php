<?php declare(strict_types = 1);

namespace Contributte\Http\DI;

use Nette\DI\CompilerExtension;
use Nette\DI\Definitions\ServiceDefinition;
use Nette\DI\Definitions\Statement;
use Nette\Http\Request;
use Nette\Http\UrlScript;
use Nette\Schema\Expect;
use Nette\Schema\Schema;
use RuntimeException;

class SapiRequestExtension extends CompilerExtension
{

	/** @var array */
	private $constructorConfig = [];

	public function __construct(?string $url = null)
	{
		$this->constructorConfig['url'] = $url;
	}

	public function getConfigSchema(): Schema
	{
		return Expect::structure([
			'url' => Expect::string($this->constructorConfig['url'])->nullable(),
			'post' => Expect::arrayOf('string')->nullable(),
			'files' => Expect::arrayOf('string')->nullable(),
			'cookies' => Expect::arrayOf('string')->nullable(),
			'headers' => Expect::arrayOf('string')->nullable(),
			'method' => Expect::string()->nullable(),
			'remoteAddress' => Expect::string()->nullable(),
			'remoteHost' => Expect::string()->nullable(),
			'rawBodyCallback' => Expect::string()->nullable() // Callback in neon config?
		]);
	}

	/**
	 * Decorate services
	 */
	public function beforeCompile(): void
	{
		// Breaks at other mode then CLI
		if (PHP_SAPI !== 'cli') return;

		$builder = $this->getContainerBuilder();

		// Verify that we have http.request
		if (!$builder->hasDefinition('http.request')) {
			throw new RuntimeException('Service http.request is needed');
		}

		$config = (array) $this->config;

		/** @var ServiceDefinition $definition */
		$definition = $builder->getDefinition('http.request');

		$definition->setFactory(Request::class, [
				new Statement(UrlScript::class, [$config['url']]),
				$config['post'],
				$config['files'],
				$config['cookies'],
				$config['headers'],
				$config['method'],
				$config['remoteAddress'],
				$config['remoteHost'],
				$config['rawBodyCallback'],
			]);
	}

}
