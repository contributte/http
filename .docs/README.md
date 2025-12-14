# Contributte HTTP

## Content

- [Setup](#setup)
- [HTTP Client](#http-client)
	- [IClient Interface](#iclient-interface)
	- [CurlClient](#curlclient)
	- [CurlBuilder](#curlbuilder)
	- [FakeClient](#fakeclient)
	- [Request & Response](#request--response)
- [SAPI - fake request (SapiRequestExtension)](#sapi)
- [BasicAuth - simple basic authentication](#basic-authentication)
- [Useful classes](#useful-classes)
	- [Url](#url)

## Setup

**Requirements:** PHP 8.2+

```bash
composer require contributte/http
```

## HTTP Client

This package provides a simple HTTP client abstraction with cURL implementation.

### IClient Interface

The `IClient` interface defines a common contract for HTTP clients:

```php
use Contributte\Http\Client\IClient;
use Contributte\Http\Client\Request;
use Contributte\Http\Client\Response;

interface IClient
{
	public function request(Request $request): Response;
	public function get(string $url, array $headers = []): Response;
	public function post(string $url, $body = null, array $headers = []): Response;
	public function put(string $url, $body = null, array $headers = []): Response;
	public function delete(string $url, array $headers = []): Response;
}
```

### CurlClient

The `CurlClient` is the default implementation using cURL.

Register it via the DI extension:

```neon
extensions:
	curl: Contributte\Http\DI\CurlExtension
```

Or use it directly:

```php
use Contributte\Http\Curl\CurlClient;

$client = new CurlClient();

// Simple GET request
$response = $client->get('https://api.example.com/users');

// POST with JSON body
$response = $client->post('https://api.example.com/users', json_encode(['name' => 'John']));

// Using Request object
$request = new Request('https://api.example.com/users', Request::METHOD_POST);
$request->setBody(json_encode(['name' => 'John']));
$request->addHeader('Authorization', 'Bearer token');
$response = $client->request($request);

// Configure default headers
$client->setDefaultHeaders([
	'Content-Type' => 'application/json',
	'Accept' => 'application/json',
]);

// Add a single default header
$client->addDefaultHeader('X-Api-Key', 'your-api-key');
```

### CurlBuilder

The `CurlBuilder` provides a fluent interface for building HTTP requests:

```php
use Contributte\Http\Curl\CurlBuilder;
use Contributte\Http\Curl\CurlClient;

$client = new CurlClient();

// Simple GET request
$request = CurlBuilder::create()
	->get('https://api.example.com/users')
	->build();
$response = $client->request($request);

// POST with JSON body
$request = CurlBuilder::create()
	->post('https://api.example.com/users')
	->setJsonBody(['name' => 'John', 'email' => 'john@example.com'])
	->build();
$response = $client->request($request);

// With authentication
$request = CurlBuilder::create()
	->get('https://api.example.com/protected')
	->setBearerToken('your-jwt-token')
	->build();

// Or basic auth
$request = CurlBuilder::create()
	->get('https://api.example.com/protected')
	->setBasicAuth('username', 'password')
	->build();

// With custom headers and options
$request = CurlBuilder::create()
	->post('https://api.example.com/upload')
	->addHeader('X-Custom', 'value')
	->setContentType('multipart/form-data')
	->setTimeout(60)
	->setFollowRedirects(true)
	->setSslVerify(true)
	->setUserAgent('MyApp/1.0')
	->build();

// Form data
$request = CurlBuilder::create()
	->post('https://example.com/form')
	->setFormBody(['username' => 'john', 'password' => 'secret'])
	->build();
```

Available builder methods:

| Method | Description |
|--------|-------------|
| `get($url)` | Set GET method and URL |
| `post($url)` | Set POST method and URL |
| `put($url)` | Set PUT method and URL |
| `delete($url)` | Set DELETE method and URL |
| `patch($url)` | Set PATCH method and URL |
| `head($url)` | Set HEAD method and URL |
| `options($url)` | Set OPTIONS method and URL |
| `addHeader($name, $value)` | Add a header |
| `setHeaders($headers)` | Set all headers |
| `setContentType($type)` | Set Content-Type header |
| `setAccept($type)` | Set Accept header |
| `setAuthorization($value)` | Set Authorization header |
| `setBearerToken($token)` | Set Bearer token authentication |
| `setBasicAuth($user, $pass)` | Set Basic authentication |
| `setBody($body)` | Set raw body |
| `setJsonBody($data)` | Set JSON body (auto-sets Content-Type) |
| `setFormBody($data)` | Set form body (auto-sets Content-Type) |
| `setTimeout($seconds)` | Set request timeout |
| `setFollowRedirects($follow)` | Enable/disable redirect following |
| `setSslVerify($verify)` | Enable/disable SSL verification |
| `setUserAgent($agent)` | Set User-Agent header |
| `setOption($key, $value)` | Set a cURL option |
| `build()` | Build and return the Request object |

### FakeClient

The `FakeClient` is a test double for mocking HTTP requests in tests:

```php
use Contributte\Http\Client\FakeClient;
use Contributte\Http\Client\Request;

$client = new FakeClient();

// Queue responses (FIFO)
$client->respondWith('Hello World', 200);
$client->respondWithJson(['status' => 'ok', 'data' => [1, 2, 3]]);
$client->respondWithError('Connection failed', 500);

// Make requests
$response1 = $client->get('https://example.com'); // Returns "Hello World"
$response2 = $client->get('https://example.com'); // Returns JSON response
$response3 = $client->get('https://example.com'); // Returns error response

// Record and inspect requests
$client->get('https://api.example.com/users');
$client->post('https://api.example.com/users', '{"name":"John"}');

// Get all recorded requests
$requests = $client->getRecordedRequests();

// Get last request
$lastRequest = $client->getLastRequest();
echo $lastRequest->getUrl();    // https://api.example.com/users
echo $lastRequest->getMethod(); // POST
echo $lastRequest->getBody();   // {"name":"John"}

// Assertions
$client->assertRequestCount(2);
$client->assertRequestMade('https://api.example.com/users');
$client->assertRequestMade('https://api.example.com/users', Request::METHOD_POST);

// Reset for next test
$client->reset();
```

### Request & Response

The `Request` class represents an HTTP request:

```php
use Contributte\Http\Client\Request;

$request = new Request('https://api.example.com/users', Request::METHOD_POST);
$request->setHeaders(['Content-Type' => 'application/json']);
$request->addHeader('Authorization', 'Bearer token');
$request->setBody(json_encode(['name' => 'John']));

// Available methods
$request->getUrl();
$request->getMethod();
$request->getHeaders();
$request->getHeader('Content-Type');
$request->hasHeader('Authorization');
$request->getBody();
$request->getOptions();
```

The `Response` class represents an HTTP response:

```php
use Contributte\Http\Client\Response;

// After making a request
$response = $client->get('https://api.example.com/users');

// Body
$body = $response->getBody();
$jsonData = $response->getJsonBody(); // Decoded JSON
$response->hasBody();

// Status
$statusCode = $response->getStatusCode();
$response->isOk();      // Status is 200
$response->isSuccess(); // Status is 2xx

// Headers
$response->getAllHeaders();
$response->getHeader('Content-Type');
$response->hasHeader('X-Custom');

// Content type
$response->isJson(); // Check if response is JSON

// Errors
$error = $response->getError();
```

## SAPI

Every modern PHP application needs sometimes to run a few console commands. Let's say sending newsletter campaigns. There is
a tiny problem, there is no request/URL in console/SAPI (Server API) mode. Don't worry, just use our fake request -
`SapiRequestExtension`.

```neon
extensions:
	sapi: Contributte\Http\DI\SapiRequestExtension
```

List of all options:

```neon
sapi:
	url: https://contributte.org
	# other params
	query: null
	post: null
	files: null
	cookies: null
	headers: null
	method: null
	remoteAddress: null
	remoteHost: null
	rawBodyCallback: null
```

## Basic Authentication

```neon
extensions:
	auth: Contributte\Http\DI\BasicAuthExtension
```

You have to `enable` this extension by yourself. It's disabled by default.

```neon
auth:
	enabled: true/false
	title: My security zone
	users:
		username1:
			password: password1
			unsecured: true
		username2:
			password: $2y$10$p.U5q.BuQp02srggig.VDOqj5m7pE1rCwKavVQ3S2TrqWlkqu3qlC
			unsecured: false # secured by default
		username3:
			password: $2y$10$bgievYVQMzsRn5Ysup.NKOVUk66aitAniAmts2EJAa91eqkAhohvC
```

## Useful classes

### Url

Few methods added:

#### `$url->appendPath($path)`

```php
use Contributte\Http\Url;

$url = new Url('https://github.com');

$url->appendPath('foo');
# https://github.com/foo

$url->appendPath('bar');
# https://github.com/foobar
```
