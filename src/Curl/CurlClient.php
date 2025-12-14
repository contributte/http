<?php declare(strict_types = 1);

namespace Contributte\Http\Curl;

use Contributte\Http\Client\IClient;
use Contributte\Http\Client\Request;
use Contributte\Http\Client\Response;

class CurlClient implements IClient
{

	/** @var mixed[] */
	private array $options = [
		CURLOPT_USERAGENT => 'Contributte',
		CURLOPT_FOLLOWLOCATION => 1,
		CURLOPT_SSL_VERIFYPEER => 1,
		CURLOPT_RETURNTRANSFER => 1,
	];

	/** @var string[] */
	private array $headers = [
		'Content-Type' => 'application/json',
		'Time-Zone' => 'Europe/Prague',
	];

	/**
	 * Set default headers for all requests
	 *
	 * @param string[] $headers
	 */
	public function setDefaultHeaders(array $headers): self
	{
		$this->headers = $headers;

		return $this;
	}

	/**
	 * Add a default header for all requests
	 */
	public function addDefaultHeader(string $name, string $value): self
	{
		$this->headers[$name] = $value;

		return $this;
	}

	/**
	 * Set default cURL options for all requests
	 *
	 * @param mixed[] $options
	 */
	public function setDefaultOptions(array $options): self
	{
		$this->options = $options;

		return $this;
	}

	/**
	 * Execute a Request object
	 */
	public function request(Request $request): Response
	{
		$method = $request->getMethod();
		$opts = $request->getOptions();

		// Set HTTP method
		switch ($method) {
			case Request::METHOD_POST:
				$opts[CURLOPT_POST] = true;
				break;
			case Request::METHOD_PUT:
				$opts[CURLOPT_CUSTOMREQUEST] = 'PUT';
				break;
			case Request::METHOD_DELETE:
				$opts[CURLOPT_CUSTOMREQUEST] = 'DELETE';
				break;
			case Request::METHOD_PATCH:
				$opts[CURLOPT_CUSTOMREQUEST] = 'PATCH';
				break;
			case Request::METHOD_HEAD:
				$opts[CURLOPT_NOBODY] = true;
				break;
			case Request::METHOD_OPTIONS:
				$opts[CURLOPT_CUSTOMREQUEST] = 'OPTIONS';
				break;
		}

		// Set body for POST/PUT/PATCH
		$body = $request->getBody();
		if ($body !== null) {
			$opts[CURLOPT_POSTFIELDS] = $body;
		}

		return $this->execute($request->getUrl(), $request->getHeaders(), $opts);
	}

	/**
	 * Convenience method for GET requests
	 *
	 * @param string[] $headers
	 */
	public function get(string $url, array $headers = []): Response
	{
		$request = new Request($url, Request::METHOD_GET);
		$request->setHeaders($headers);

		return $this->request($request);
	}

	/**
	 * Convenience method for POST requests
	 *
	 * @param string[] $headers
	 */
	public function post(string $url, mixed $body = null, array $headers = []): Response
	{
		$request = new Request($url, Request::METHOD_POST);
		$request->setHeaders($headers);
		$request->setBody($body);

		return $this->request($request);
	}

	/**
	 * Convenience method for PUT requests
	 *
	 * @param string[] $headers
	 */
	public function put(string $url, mixed $body = null, array $headers = []): Response
	{
		$request = new Request($url, Request::METHOD_PUT);
		$request->setHeaders($headers);
		$request->setBody($body);

		return $this->request($request);
	}

	/**
	 * Convenience method for DELETE requests
	 *
	 * @param string[] $headers
	 */
	public function delete(string $url, array $headers = []): Response
	{
		$request = new Request($url, Request::METHOD_DELETE);
		$request->setHeaders($headers);

		return $this->request($request);
	}

	/**
	 * Execute the cURL request
	 *
	 * @param string[] $headers
	 * @param mixed[] $opts
	 */
	private function execute(string $url, array $headers = [], array $opts = []): Response
	{
		$ch = curl_init();
		$responseFactory = new ResponseFactory();

		// Set-up headers
		$_headers = array_merge($this->headers, $headers);
		$_headers = array_map(
			static fn (string $key, string $value): string => sprintf('%s: %s', $key, $value),
			array_keys($_headers),
			array_values($_headers),
		);

		// Set-up cURL options
		$_opts = [
			CURLOPT_URL => $url,
			CURLOPT_HTTPHEADER => $_headers,
			CURLOPT_HEADERFUNCTION => [$responseFactory, 'parseHeaders'],
		];
		$_opts = $opts + $_opts + $this->options;
		curl_setopt_array($ch, $_opts);

		// Make request
		$result = curl_exec($ch);

		// Check for errors
		$error = curl_error($ch);

		// Store information about request/response
		$responseFactory->setInfo(curl_getinfo($ch));

		// Close connection
		curl_close($ch);

		// Store response
		if (is_string($result)) {
			$responseFactory->setBody($result);
		}

		$response = $responseFactory->create();

		if ($error !== '') {
			$response->setError($error);
		}

		return $response;
	}

}
