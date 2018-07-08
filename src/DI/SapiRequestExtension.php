<?php declare(strict_types = 1);

namespace Contributte\Http\DI;

use Nette\DI\CompilerExtension;
use Nette\DI\Statement;
use Nette\Http\Request;
use Nette\Http\UrlScript;
use RuntimeException;

class SapiRequestExtension extends CompilerExtension
{

	/** @var mixed[] */
	private $defaults = [
		'url' => null,
		'query' => null,
		'post' => null,
		'files' => null,
		'cookies' => null,
		'headers' => null,
		'method' => null,
		'remoteAddress' => null,
		'remoteHost' => null,
		'rawBodyCallback' => null,
	];

	public function __construct(?string $url = null)
	{
		if ($url !== null) {
			$this->defaults['url'] = $url;
		}
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

		$config = $this->validateConfig($this->defaults);
		$builder->getDefinition('http.request')
			->setClass(Request::class, [
				new Statement(UrlScript::class, [$config['url']]),
				$config['query'],
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
