<?php declare(strict_types = 1);

/**
 * Test: DI\BasicAuthExtension
 */

use Contributte\Http\Auth\BasicAuthenticator;
use Contributte\Http\DI\BasicAuthExtension;
use Nette\DI\Compiler;
use Nette\DI\Container;
use Nette\DI\ContainerLoader;
use Tester\Assert;
use Tester\FileMock;

require_once __DIR__ . '/../../bootstrap.php';

test(function (): void {
	$loader = new ContainerLoader(TEMP_DIR, true);
	$class = $loader->load(function (Compiler $compiler): void {
		$compiler->addExtension('auth', new BasicAuthExtension());
		$compiler->loadConfig(FileMock::create('
		auth:
			enabled: true
			title: Foobar
			users:
				foo:
					password: bar
					unsecured: true
				baz:
					password: baz
					unsecured: true
		', 'neon'));
	}, 3);

	/** @var Container $container */
	$container = new $class();

	Assert::type(BasicAuthenticator::class, $container->getService('auth.authenticator'));
});
