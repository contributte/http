<?php

namespace Contributte\Http\CliRequest\DI;

use Nette\DI\CompilerExtension;
use Nette\DI\Statement;
use Nette\Http\Request;
use Nette\Http\UrlScript;
use RuntimeException;

/**
 * @author Milan Felix Sulc <sulcmil@gmail.com>
 */
class CliRequestExtension extends CompilerExtension
{

	/** @var array */
	private $defaults = [
		'url' => NULL,
		'query' => NULL,
		'post' => NULL,
		'files' => NULL,
		'cookies' => NULL,
		'headers' => NULL,
		'method' => NULL,
		'remoteAddress' => NULL,
		'remoteHost' => NULL,
		'rawBodyCallback' => NULL,
	];

	/**
	 * @param string $url
	 */
	public function __construct($url = NULL)
	{
		if ($url !== NULL) {
			$this->defaults['url'] = $url;
		}
	}

	/**
	 * Decorate services
	 *
	 * @return void
	 */
	public function beforeCompile()
	{
		// Breaks at other mode then CLI
		if (PHP_SAPI != 'cli') return;

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
