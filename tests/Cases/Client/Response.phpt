<?php declare(strict_types = 1);

/**
 * Test: Client\Response
 */

use Contributte\Http\Client\Response;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';

// Test: Constructor defaults
test(static function (): void {
	$response = new Response();

	Assert::null($response->getBody());
	Assert::false($response->hasBody());
	Assert::equal([], $response->getAllHeaders());
	Assert::equal([], $response->getAllInfo());
});

// Test: Constructor with values
test(static function (): void {
	$response = new Response(
		'response body',
		['Content-Type' => 'text/html'],
		['http_code' => 200]
	);

	Assert::equal('response body', $response->getBody());
	Assert::true($response->hasBody());
	Assert::equal(['Content-Type' => 'text/html'], $response->getAllHeaders());
	Assert::equal(['http_code' => 200], $response->getAllInfo());
});

// Test: Headers
test(static function (): void {
	$response = new Response(null, [
		'Content-Type' => 'application/json',
		'X-Custom' => 'value',
	]);

	Assert::true($response->hasHeader('Content-Type'));
	Assert::true($response->hasHeader('X-Custom'));
	Assert::false($response->hasHeader('Missing'));

	Assert::equal('application/json', $response->getHeader('Content-Type'));
	Assert::equal('value', $response->getHeader('X-Custom'));
	Assert::null($response->getHeader('Missing'));
});

// Test: Info
test(static function (): void {
	$response = new Response(null, [], [
		'http_code' => 200,
		'content_type' => 'text/html',
	]);

	Assert::true($response->hasInfo('http_code'));
	Assert::true($response->hasInfo('content_type'));
	Assert::false($response->hasInfo('missing'));

	Assert::equal(200, $response->getInfo('http_code'));
	Assert::equal('text/html', $response->getInfo('content_type'));
	Assert::null($response->getInfo('missing'));
});

// Test: Status code from http_code
test(static function (): void {
	$response = new Response(null, [], ['http_code' => 404]);

	Assert::equal(404, $response->getStatusCode());
});

// Test: Status code from status_code (fallback)
test(static function (): void {
	$response = new Response(null, [], ['status_code' => 500]);

	Assert::equal(500, $response->getStatusCode());
});

// Test: Status code default
test(static function (): void {
	$response = new Response();

	Assert::equal(0, $response->getStatusCode());
});

// Test: isOk
test(static function (): void {
	$response200 = new Response(null, [], ['http_code' => 200]);
	$response404 = new Response(null, [], ['http_code' => 404]);

	Assert::true($response200->isOk());
	Assert::false($response404->isOk());
});

// Test: isSuccess
test(static function (): void {
	$response200 = new Response(null, [], ['http_code' => 200]);
	$response201 = new Response(null, [], ['http_code' => 201]);
	$response204 = new Response(null, [], ['http_code' => 204]);
	$response400 = new Response(null, [], ['http_code' => 400]);

	Assert::true($response200->isSuccess());
	Assert::true($response201->isSuccess());
	Assert::true($response204->isSuccess());
	Assert::false($response400->isSuccess());
});

// Test: isJson from info
test(static function (): void {
	$jsonResponse = new Response(null, [], ['content_type' => 'application/json']);
	$htmlResponse = new Response(null, [], ['content_type' => 'text/html']);

	Assert::true($jsonResponse->isJson());
	Assert::false($htmlResponse->isJson());
});

// Test: isJson from header
test(static function (): void {
	$jsonResponse = new Response(null, ['Content-Type' => 'application/json; charset=utf-8']);

	Assert::true($jsonResponse->isJson());
});

// Test: getJsonBody
test(static function (): void {
	$response = new Response('{"name":"John","age":30}');

	$data = $response->getJsonBody();
	Assert::equal(['name' => 'John', 'age' => 30], $data);
});

// Test: getJsonBody with null body
test(static function (): void {
	$response = new Response();

	Assert::null($response->getJsonBody());
});

// Test: getJsonBody with invalid JSON
test(static function (): void {
	$response = new Response('not json');

	Assert::null($response->getJsonBody());
});

// Test: Error
test(static function (): void {
	$response = new Response();

	Assert::null($response->getError());

	$response->setError('Connection timeout');

	Assert::equal('Connection timeout', $response->getError());
});
