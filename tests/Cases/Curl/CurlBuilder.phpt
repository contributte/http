<?php declare(strict_types = 1);

/**
 * Test: Curl\CurlBuilder
 */

use Contributte\Http\Client\Request;
use Contributte\Http\Curl\CurlBuilder;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';

// Test: Basic GET request
test(static function (): void {
	$request = CurlBuilder::create()
		->get('https://example.com')
		->build();

	Assert::equal('https://example.com', $request->getUrl());
	Assert::equal(Request::METHOD_GET, $request->getMethod());
});

// Test: POST request with JSON body
test(static function (): void {
	$request = CurlBuilder::create()
		->post('https://api.example.com/users')
		->setJsonBody(['name' => 'John', 'email' => 'john@example.com'])
		->build();

	Assert::equal('https://api.example.com/users', $request->getUrl());
	Assert::equal(Request::METHOD_POST, $request->getMethod());
	Assert::equal('{"name":"John","email":"john@example.com"}', $request->getBody());
	Assert::equal('application/json', $request->getHeader('Content-Type'));
});

// Test: Form body
test(static function (): void {
	$request = CurlBuilder::create()
		->post('https://example.com/form')
		->setFormBody(['username' => 'john', 'password' => 'secret'])
		->build();

	Assert::equal('username=john&password=secret', $request->getBody());
	Assert::equal('application/x-www-form-urlencoded', $request->getHeader('Content-Type'));
});

// Test: Headers
test(static function (): void {
	$request = CurlBuilder::create()
		->get('https://example.com')
		->addHeader('X-Custom', 'value')
		->setContentType('text/plain')
		->setAccept('application/json')
		->build();

	Assert::equal('value', $request->getHeader('X-Custom'));
	Assert::equal('text/plain', $request->getHeader('Content-Type'));
	Assert::equal('application/json', $request->getHeader('Accept'));
});

// Test: Authorization
test(static function (): void {
	$request = CurlBuilder::create()
		->get('https://api.example.com')
		->setBearerToken('my-token')
		->build();

	Assert::equal('Bearer my-token', $request->getHeader('Authorization'));
});

// Test: Basic auth
test(static function (): void {
	$request = CurlBuilder::create()
		->get('https://example.com')
		->setBasicAuth('user', 'pass')
		->build();

	Assert::equal('Basic ' . base64_encode('user:pass'), $request->getHeader('Authorization'));
});

// Test: HTTP methods
test(static function (): void {
	$builder = CurlBuilder::create();

	$builder->put('https://example.com');
	Assert::equal(Request::METHOD_PUT, $builder->getMethod());

	$builder->delete('https://example.com');
	Assert::equal(Request::METHOD_DELETE, $builder->getMethod());

	$builder->patch('https://example.com');
	Assert::equal(Request::METHOD_PATCH, $builder->getMethod());

	$builder->head('https://example.com');
	Assert::equal(Request::METHOD_HEAD, $builder->getMethod());

	$builder->options('https://example.com');
	Assert::equal(Request::METHOD_OPTIONS, $builder->getMethod());
});

// Test: cURL options
test(static function (): void {
	$request = CurlBuilder::create()
		->get('https://example.com')
		->setTimeout(30)
		->setFollowRedirects(false)
		->setSslVerify(false)
		->setUserAgent('MyApp/1.0')
		->build();

	$options = $request->getOptions();
	Assert::equal(30, $options[CURLOPT_TIMEOUT]);
	Assert::equal(0, $options[CURLOPT_FOLLOWLOCATION]);
	Assert::equal(0, $options[CURLOPT_SSL_VERIFYPEER]);
	Assert::equal('MyApp/1.0', $options[CURLOPT_USERAGENT]);
});

// Test: Custom option
test(static function (): void {
	$request = CurlBuilder::create()
		->get('https://example.com')
		->setOption(CURLOPT_MAXREDIRS, 5)
		->build();

	$options = $request->getOptions();
	Assert::equal(5, $options[CURLOPT_MAXREDIRS]);
});

// Test: Fluent interface
test(static function (): void {
	$builder = CurlBuilder::create();

	Assert::type(CurlBuilder::class, $builder->setUrl('https://example.com'));
	Assert::type(CurlBuilder::class, $builder->setMethod('GET'));
	Assert::type(CurlBuilder::class, $builder->addHeader('X-Test', 'value'));
	Assert::type(CurlBuilder::class, $builder->setBody('test'));
	Assert::type(CurlBuilder::class, $builder->setTimeout(10));
});
