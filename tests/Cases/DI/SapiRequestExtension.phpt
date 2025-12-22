<?php declare(strict_types = 1);

/**
 * Test: DI\SapiRequestExtension
 */

use Contributte\Http\DI\SapiRequestExtension;
use Nette\Bridges\HttpDI\HttpExtension;
use Nette\DI\Compiler;
use Nette\DI\Container;
use Nette\DI\ContainerLoader;
use Nette\Http\Request;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';

test(static function (): void {
	$loader = new ContainerLoader(TEMP_DIR, true);
	$class = $loader->load(static function (Compiler $compiler): void {
		$compiler->addConfig([
			'sapi' => [
				'url' => 'https://contributte.org',
			],
		]);
		$compiler->addExtension('http', new HttpExtension(true));
		$compiler->addExtension('sapi', new SapiRequestExtension());
	}, 1);

	/** @var Container $container */
	$container = new $class();

	Assert::equal('https://contributte.org/', (string) $container->getByType(Request::class)->getUrl());
});
