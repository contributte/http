<?php declare(strict_types = 1);

namespace Contributte\Http\Curl;

class Response
{

	/** @var mixed */
	private $body;

	/** @var string[] */
	private $headers;

	/** @var mixed[] */
	private $info;

	/** @var mixed */
	private $error;

	/**
	 * @param mixed $body
	 * @param string[] $headers
	 * @param mixed[] $info
	 */
	public function __construct($body = null, array $headers = [], array $info = [])
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

	/**
	 * @return mixed
	 */
	public function getInfo(string $key)
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

	/**
	 * @return mixed
	 */
	public function getBody()
	{
		return $this->body;
	}

	public function hasBody(): bool
	{
		return $this->body !== null;
	}

	public function isJson(): bool
	{
		return $this->getInfo('content_type') === 'application/json';
	}

	/**
	 * @return mixed
	 */
	public function getJsonBody()
	{
				$body = $this->getBody();
				if ($body === null) return null;

				return @json_decode((string) $this->getBody(), true);
	}

	public function getStatusCode(): int
	{
		return $this->getInfo('http_code') ?? 0;
	}

	public function isOk(): bool
	{
		return $this->getStatusCode() === 200;
	}

	/**
	 * @return mixed
	 */
	public function getError()
	{
		return $this->error;
	}

	/**
	 * @param mixed $error
	 */
	public function setError($error): void
	{
		$this->error = $error;
	}

}
