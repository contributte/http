<?php declare(strict_types = 1);

/**
 * Test: Client\FakeClient
 */

use Contributte\Http\Client\FakeClient;
use Contributte\Http\Client\Request;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';

// Test: Default response
test(static function (): void {
	$client = new FakeClient();

	$response = $client->get('https://example.com');

	Assert::equal(200, $response->getStatusCode());
	Assert::equal('', $response->getBody());
});

// Test: Custom response queue
test(static function (): void {
	$client = new FakeClient();
	$client->respondWith('Hello World', 200);
	$client->respondWith('Not Found', 404);

	$response1 = $client->get('https://example.com/1');
	$response2 = $client->get('https://example.com/2');

	Assert::equal('Hello World', $response1->getBody());
	Assert::equal(200, $response1->getStatusCode());
	Assert::equal('Not Found', $response2->getBody());
	Assert::equal(404, $response2->getStatusCode());
});

// Test: JSON response
test(static function (): void {
	$client = new FakeClient();
	$client->respondWithJson(['status' => 'ok', 'data' => [1, 2, 3]]);

	$response = $client->get('https://api.example.com');

	Assert::true($response->isJson());
	Assert::equal(['status' => 'ok', 'data' => [1, 2, 3]], $response->getJsonBody());
});

// Test: Error response
test(static function (): void {
	$client = new FakeClient();
	$client->respondWithError('Connection failed', 500);

	$response = $client->get('https://example.com');

	Assert::equal(500, $response->getStatusCode());
	Assert::equal('Connection failed', $response->getError());
});

// Test: Request recording
test(static function (): void {
	$client = new FakeClient();
	$client->respondWith('OK');

	$client->get('https://example.com/get');
	$client->post('https://example.com/post', 'body data');
	$client->put('https://example.com/put', 'put data');
	$client->delete('https://example.com/delete');

	Assert::equal(4, $client->getRequestCount());

	$requests = $client->getRecordedRequests();
	Assert::equal('https://example.com/get', $requests[0]->getUrl());
	Assert::equal(Request::METHOD_GET, $requests[0]->getMethod());

	Assert::equal('https://example.com/post', $requests[1]->getUrl());
	Assert::equal(Request::METHOD_POST, $requests[1]->getMethod());
	Assert::equal('body data', $requests[1]->getBody());

	Assert::equal('https://example.com/put', $requests[2]->getUrl());
	Assert::equal(Request::METHOD_PUT, $requests[2]->getMethod());

	Assert::equal('https://example.com/delete', $requests[3]->getUrl());
	Assert::equal(Request::METHOD_DELETE, $requests[3]->getMethod());
});

// Test: Get last request
test(static function (): void {
	$client = new FakeClient();

	$client->get('https://example.com/1');
	$client->get('https://example.com/2');

	$lastRequest = $client->getLastRequest();
	Assert::equal('https://example.com/2', $lastRequest->getUrl());
});

// Test: Assert helpers
test(static function (): void {
	$client = new FakeClient();

	$client->get('https://api.example.com/users');
	$client->post('https://api.example.com/users');

	Assert::true($client->assertRequestCount(2));
	Assert::true($client->assertRequestMade('https://api.example.com/users'));
	Assert::true($client->assertRequestMade('https://api.example.com/users', Request::METHOD_GET));
	Assert::true($client->assertRequestMade('https://api.example.com/users', Request::METHOD_POST));
	Assert::false($client->assertRequestMade('https://api.example.com/users', Request::METHOD_DELETE));
});

// Test: Reset
test(static function (): void {
	$client = new FakeClient();
	$client->respondWith('test');
	$client->get('https://example.com');

	Assert::equal(1, $client->getRequestCount());

	$client->reset();

	Assert::equal(0, $client->getRequestCount());
});

// Test: Request object
test(static function (): void {
	$client = new FakeClient();
	$client->respondWith('OK');

	$request = new Request('https://example.com/api', Request::METHOD_POST);
	$request->setBody('{"test": true}');
	$request->addHeader('Authorization', 'Bearer token');

	$response = $client->request($request);

	Assert::equal('OK', $response->getBody());

	$lastRequest = $client->getLastRequest();
	Assert::equal('https://example.com/api', $lastRequest->getUrl());
	Assert::equal(Request::METHOD_POST, $lastRequest->getMethod());
	Assert::equal('{"test": true}', $lastRequest->getBody());
	Assert::equal('Bearer token', $lastRequest->getHeader('Authorization'));
});
