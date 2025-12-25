<?php declare(strict_types = 1);

/**
 * Test: DI\CurlExtension
 */

use Contributte\Http\Client\IClient;
use Contributte\Http\Curl\CurlClient;
use Contributte\Http\DI\CurlExtension;
use Nette\DI\Compiler;
use Nette\DI\Container;
use Nette\DI\ContainerLoader;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';

// Test: IClient interface registration
test(static function (): void {
	$loader = new ContainerLoader(TEMP_DIR, true);
	$class = $loader->load(static function (Compiler $compiler): void {
		$compiler->addExtension('curl', new CurlExtension());
	}, 1);

	/** @var Container $container */
	$container = new $class();

	Assert::type(CurlClient::class, $container->getByType(IClient::class));
});
