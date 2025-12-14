<?php declare(strict_types = 1);

namespace Contributte\Http\Client;

/**
 * HTTP Request object
 */
class Request
{

	public const METHOD_GET = 'GET';
	public const METHOD_POST = 'POST';
	public const METHOD_PUT = 'PUT';
	public const METHOD_DELETE = 'DELETE';
	public const METHOD_PATCH = 'PATCH';
	public const METHOD_HEAD = 'HEAD';
	public const METHOD_OPTIONS = 'OPTIONS';

	private string $url;

	private string $method;

	/** @var string[] */
	private array $headers = [];

	private mixed $body = null;

	/** @var mixed[] */
	private array $options = [];

	public function __construct(string $url, string $method = self::METHOD_GET)
	{
		$this->url = $url;
		$this->method = $method;
	}

	public function getUrl(): string
	{
		return $this->url;
	}

	public function setUrl(string $url): self
	{
		$this->url = $url;

		return $this;
	}

	public function getMethod(): string
	{
		return $this->method;
	}

	public function setMethod(string $method): self
	{
		$this->method = $method;

		return $this;
	}

	/**
	 * @return string[]
	 */
	public function getHeaders(): array
	{
		return $this->headers;
	}

	/**
	 * @param string[] $headers
	 */
	public function setHeaders(array $headers): self
	{
		$this->headers = $headers;

		return $this;
	}

	public function addHeader(string $name, string $value): self
	{
		$this->headers[$name] = $value;

		return $this;
	}

	public function hasHeader(string $name): bool
	{
		return isset($this->headers[$name]);
	}

	public function getHeader(string $name): ?string
	{
		return $this->headers[$name] ?? null;
	}

	public function getBody(): mixed
	{
		return $this->body;
	}

	public function setBody(mixed $body): self
	{
		$this->body = $body;

		return $this;
	}

	/**
	 * @return mixed[]
	 */
	public function getOptions(): array
	{
		return $this->options;
	}

	/**
	 * @param mixed[] $options
	 */
	public function setOptions(array $options): self
	{
		$this->options = $options;

		return $this;
	}

	public function setOption(string $key, mixed $value): self
	{
		$this->options[$key] = $value;

		return $this;
	}

	/**
	 * @param int $key CURLOPT_* constant
	 */
	public function setCurlOption(int $key, mixed $value): self
	{
		$this->options[$key] = $value;

		return $this;
	}

}
