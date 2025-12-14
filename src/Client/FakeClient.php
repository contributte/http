<?php declare(strict_types = 1);

namespace Contributte\Http\Client;

/**
 * Fake HTTP client for testing purposes
 *
 * Allows setting predefined responses and recording all requests made.
 */
class FakeClient implements IClient
{

	/** @var Response[] */
	private array $responses = [];

	private Response $defaultResponse;

	/** @var Request[] */
	private array $recordedRequests = [];

	private bool $recordRequests = true;

	public function __construct()
	{
		$this->defaultResponse = new Response('', [], ['http_code' => 200]);
	}

	/**
	 * Add a response to the queue (FIFO)
	 */
	public function addResponse(Response $response): self
	{
		$this->responses[] = $response;

		return $this;
	}

	/**
	 * Set the default response when queue is empty
	 */
	public function setDefaultResponse(Response $response): self
	{
		$this->defaultResponse = $response;

		return $this;
	}

	/**
	 * Create and add a simple response
	 *
	 * @param string[] $headers
	 * @param mixed[] $info
	 */
	public function respondWith(string $body, int $statusCode = 200, array $headers = [], array $info = []): self
	{
		$info['http_code'] = $statusCode;
		$info['status_code'] = $statusCode;
		$this->responses[] = new Response($body, $headers, $info);

		return $this;
	}

	/**
	 * Create and add a JSON response
	 *
	 * @param mixed[] $data
	 */
	public function respondWithJson(array $data, int $statusCode = 200): self
	{
		$body = json_encode($data);
		$headers = ['Content-Type' => 'application/json'];
		$info = ['http_code' => $statusCode, 'content_type' => 'application/json'];
		$this->responses[] = new Response($body, $headers, $info);

		return $this;
	}

	/**
	 * Create and add an error response
	 */
	public function respondWithError(string $error, int $statusCode = 500): self
	{
		$response = new Response('', [], ['http_code' => $statusCode]);
		$response->setError($error);
		$this->responses[] = $response;

		return $this;
	}

	/**
	 * Enable or disable request recording
	 */
	public function setRecordRequests(bool $record): self
	{
		$this->recordRequests = $record;

		return $this;
	}

	/**
	 * Get all recorded requests
	 *
	 * @return Request[]
	 */
	public function getRecordedRequests(): array
	{
		return $this->recordedRequests;
	}

	/**
	 * Get the last recorded request
	 */
	public function getLastRequest(): ?Request
	{
		if (count($this->recordedRequests) === 0) {
			return null;
		}

		return $this->recordedRequests[count($this->recordedRequests) - 1];
	}

	/**
	 * Get the number of recorded requests
	 */
	public function getRequestCount(): int
	{
		return count($this->recordedRequests);
	}

	/**
	 * Clear all recorded requests
	 */
	public function clearRecordedRequests(): self
	{
		$this->recordedRequests = [];

		return $this;
	}

	/**
	 * Clear all queued responses
	 */
	public function clearResponses(): self
	{
		$this->responses = [];

		return $this;
	}

	/**
	 * Reset the client (clear responses and recorded requests)
	 */
	public function reset(): self
	{
		$this->responses = [];
		$this->recordedRequests = [];

		return $this;
	}

	/**
	 * Assert that a specific number of requests were made
	 */
	public function assertRequestCount(int $expected): bool
	{
		return count($this->recordedRequests) === $expected;
	}

	/**
	 * Assert that a request was made to a specific URL
	 */
	public function assertRequestMade(string $url, ?string $method = null): bool
	{
		foreach ($this->recordedRequests as $request) {
			if ($request->getUrl() === $url) {
				if ($method === null || $request->getMethod() === $method) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Execute a Request object
	 */
	public function request(Request $request): Response
	{
		if ($this->recordRequests) {
			$this->recordedRequests[] = $request;
		}

		if (count($this->responses) > 0) {
			return array_shift($this->responses);
		}

		return $this->defaultResponse;
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

}
