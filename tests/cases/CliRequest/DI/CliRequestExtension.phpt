<?php

/**
 * Test: CliRequest\DI\CliRequestExtension
 */

use Contributte\Http\CliRequest\DI\CliRequestExtension;
use Nette\Bridges\HttpDI\HttpExtension;
use Nette\DI\Compiler;
use Nette\DI\Container;
use Nette\DI\ContainerLoader;
use Nette\Http\Request;
use Tester\Assert;

require_once __DIR__ . '/../../../bootstrap.php';

test(function () {
	$loader = new ContainerLoader(TEMP_DIR, TRUE);
	$class = $loader->load(function (Compiler $compiler) {
		$compiler->addExtension('http', new HttpExtension(TRUE));
		$compiler->addExtension('cli', new CliRequestExtension('https://contributte.org'));
	}, 1);

	/** @var Container $container */
	$container = new $class;

	Assert::equal('https://contributte.org/', (string) $container->getByType(Request::class)->getUrl());
});
