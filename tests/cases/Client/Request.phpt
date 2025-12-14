<?php declare(strict_types = 1);

/**
 * Test: Client\Request
 */

use Contributte\Http\Client\Request;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';

// Test: Constructor defaults
test(static function (): void {
	$request = new Request('https://example.com');

	Assert::equal('https://example.com', $request->getUrl());
	Assert::equal(Request::METHOD_GET, $request->getMethod());
	Assert::equal([], $request->getHeaders());
	Assert::null($request->getBody());
	Assert::equal([], $request->getOptions());
});

// Test: Constructor with method
test(static function (): void {
	$request = new Request('https://example.com', Request::METHOD_POST);

	Assert::equal(Request::METHOD_POST, $request->getMethod());
});

// Test: Setters and getters
test(static function (): void {
	$request = new Request('https://example.com');

	$request->setUrl('https://api.example.com');
	Assert::equal('https://api.example.com', $request->getUrl());

	$request->setMethod(Request::METHOD_PUT);
	Assert::equal(Request::METHOD_PUT, $request->getMethod());

	$request->setBody('request body');
	Assert::equal('request body', $request->getBody());
});

// Test: Headers
test(static function (): void {
	$request = new Request('https://example.com');

	$request->addHeader('Content-Type', 'application/json');
	$request->addHeader('Accept', 'application/xml');

	Assert::true($request->hasHeader('Content-Type'));
	Assert::true($request->hasHeader('Accept'));
	Assert::false($request->hasHeader('X-Custom'));

	Assert::equal('application/json', $request->getHeader('Content-Type'));
	Assert::equal('application/xml', $request->getHeader('Accept'));
	Assert::null($request->getHeader('X-Custom'));

	Assert::equal([
		'Content-Type' => 'application/json',
		'Accept' => 'application/xml',
	], $request->getHeaders());
});

// Test: Set all headers at once
test(static function (): void {
	$request = new Request('https://example.com');
	$request->addHeader('Old-Header', 'old');

	$request->setHeaders([
		'New-Header' => 'new',
	]);

	Assert::false($request->hasHeader('Old-Header'));
	Assert::true($request->hasHeader('New-Header'));
});

// Test: Options
test(static function (): void {
	$request = new Request('https://example.com');

	$request->setOption('timeout', 30);
	$request->setCurlOption(CURLOPT_FOLLOWLOCATION, 1);

	$options = $request->getOptions();
	Assert::equal(30, $options['timeout']);
	Assert::equal(1, $options[CURLOPT_FOLLOWLOCATION]);
});

// Test: Set all options at once
test(static function (): void {
	$request = new Request('https://example.com');
	$request->setOption('old', 'value');

	$request->setOptions([
		'new' => 'value',
	]);

	$options = $request->getOptions();
	Assert::false(isset($options['old']));
	Assert::equal('value', $options['new']);
});

// Test: Fluent interface
test(static function (): void {
	$request = new Request('https://example.com');

	Assert::type(Request::class, $request->setUrl('url'));
	Assert::type(Request::class, $request->setMethod('POST'));
	Assert::type(Request::class, $request->setHeaders([]));
	Assert::type(Request::class, $request->addHeader('X', 'Y'));
	Assert::type(Request::class, $request->setBody('body'));
	Assert::type(Request::class, $request->setOptions([]));
	Assert::type(Request::class, $request->setOption('k', 'v'));
	Assert::type(Request::class, $request->setCurlOption(CURLOPT_TIMEOUT, 10));
});

// Test: Method constants
test(static function (): void {
	Assert::equal('GET', Request::METHOD_GET);
	Assert::equal('POST', Request::METHOD_POST);
	Assert::equal('PUT', Request::METHOD_PUT);
	Assert::equal('DELETE', Request::METHOD_DELETE);
	Assert::equal('PATCH', Request::METHOD_PATCH);
	Assert::equal('HEAD', Request::METHOD_HEAD);
	Assert::equal('OPTIONS', Request::METHOD_OPTIONS);
});
