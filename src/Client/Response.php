<?php declare(strict_types = 1);

namespace Contributte\Http\Client;

/**
 * HTTP Response object
 */
class Response
{

	private mixed $body;

	/** @var string[] */
	private array $headers;

	/** @var mixed[] */
	private array $info;

	private mixed $error = null;

	/**
	 * @param string[] $headers
	 * @param mixed[] $info
	 */
	public function __construct(mixed $body = null, array $headers = [], array $info = [])
	{
		$this->body = $body;
		$this->headers = $headers;
		$this->info = $info;
	}

	/**
	 * @return mixed[]
	 */
	public function getAllInfo(): array
	{
		return $this->info;
	}

	public function hasInfo(string $key): bool
	{
		return isset($this->info[$key]);
	}

	public function getInfo(string $key): mixed
	{
		if ($this->hasInfo($key)) {
			return $this->info[$key];
		}

		return null;
	}

	/**
	 * @return string[]
	 */
	public function getAllHeaders(): array
	{
		return $this->headers;
	}

	public function hasHeader(string $key): bool
	{
		return isset($this->headers[$key]);
	}

	public function getHeader(string $key): ?string
	{
		if ($this->hasHeader($key)) {
			return $this->headers[$key];
		}

		return null;
	}

	public function getBody(): mixed
	{
		return $this->body;
	}

	public function hasBody(): bool
	{
		return $this->body !== null;
	}

	public function isJson(): bool
	{
		$contentType = $this->getInfo('content_type');
		if ($contentType === null) {
			$contentType = $this->getHeader('Content-Type');
		}

		return is_string($contentType) && str_contains($contentType, 'application/json');
	}

	public function getJsonBody(): mixed
	{
		$body = $this->getBody();
		if (!is_string($body) || $body === '') {
			return null;
		}

		return json_decode($body, true);
	}

	public function getStatusCode(): int
	{
		$httpCode = $this->getInfo('http_code');
		if (is_int($httpCode)) {
			return $httpCode;
		}

		$statusCode = $this->getInfo('status_code');
		if (is_int($statusCode)) {
			return $statusCode;
		}

		return 0;
	}

	public function isOk(): bool
	{
		return $this->getStatusCode() === 200;
	}

	public function isSuccess(): bool
	{
		$code = $this->getStatusCode();

		return $code >= 200 && $code < 300;
	}

	public function getError(): mixed
	{
		return $this->error;
	}

	public function setError(mixed $error): void
	{
		$this->error = $error;
	}

}
