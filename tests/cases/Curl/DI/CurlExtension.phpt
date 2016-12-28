<?php

/**
 * Test: Curl\DI\CurlExtension
 */

use Contributte\Http\Curl\CurlClient;
use Contributte\Http\Curl\DI\CurlExtension;
use Contributte\Http\Curl\ICurlClient;
use Nette\DI\Compiler;
use Nette\DI\Container;
use Nette\DI\ContainerLoader;
use Tester\Assert;

require_once __DIR__ . '/../../../bootstrap.php';

test(function () {
	$loader = new ContainerLoader(TEMP_DIR, TRUE);
	$class = $loader->load(function (Compiler $compiler) {
		$compiler->addExtension('curl', new CurlExtension());
	}, 1);

	/** @var Container $container */
	$container = new $class;

	Assert::type(CurlClient::class, $container->getByType(ICurlClient::class));
});
