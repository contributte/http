<?php declare(strict_types = 1);

namespace Contributte\Http\Curl;

use Contributte\Http\Client\Request;

/**
 * Fluent builder for creating HTTP requests to be executed by CurlClient
 */
class CurlBuilder
{

	private string $url = '';

	private string $method = Request::METHOD_GET;

	/** @var string[] */
	private array $headers = [];

	private mixed $body = null;

	/** @var mixed[] */
	private array $options = [];

	private ?int $timeout = null;

	private bool $followRedirects = true;

	private bool $sslVerify = true;

	private ?string $userAgent = null;

	public static function create(): self
	{
		return new self();
	}

	public function setUrl(string $url): self
	{
		$this->url = $url;

		return $this;
	}

	public function get(string $url): self
	{
		$this->method = Request::METHOD_GET;
		$this->url = $url;

		return $this;
	}

	public function post(string $url): self
	{
		$this->method = Request::METHOD_POST;
		$this->url = $url;

		return $this;
	}

	public function put(string $url): self
	{
		$this->method = Request::METHOD_PUT;
		$this->url = $url;

		return $this;
	}

	public function delete(string $url): self
	{
		$this->method = Request::METHOD_DELETE;
		$this->url = $url;

		return $this;
	}

	public function patch(string $url): self
	{
		$this->method = Request::METHOD_PATCH;
		$this->url = $url;

		return $this;
	}

	public function head(string $url): self
	{
		$this->method = Request::METHOD_HEAD;
		$this->url = $url;

		return $this;
	}

	public function options(string $url): self
	{
		$this->method = Request::METHOD_OPTIONS;
		$this->url = $url;

		return $this;
	}

	public function setMethod(string $method): self
	{
		$this->method = $method;

		return $this;
	}

	public function addHeader(string $name, string $value): self
	{
		$this->headers[$name] = $value;

		return $this;
	}

	/**
	 * @param string[] $headers
	 */
	public function setHeaders(array $headers): self
	{
		$this->headers = $headers;

		return $this;
	}

	public function setContentType(string $contentType): self
	{
		$this->headers['Content-Type'] = $contentType;

		return $this;
	}

	public function setAccept(string $accept): self
	{
		$this->headers['Accept'] = $accept;

		return $this;
	}

	public function setAuthorization(string $authorization): self
	{
		$this->headers['Authorization'] = $authorization;

		return $this;
	}

	public function setBearerToken(string $token): self
	{
		$this->headers['Authorization'] = 'Bearer ' . $token;

		return $this;
	}

	public function setBasicAuth(string $username, string $password): self
	{
		$this->headers['Authorization'] = 'Basic ' . base64_encode($username . ':' . $password);

		return $this;
	}

	public function setBody(mixed $body): self
	{
		$this->body = $body;

		return $this;
	}

	/**
	 * @param mixed[] $data
	 */
	public function setJsonBody(array $data): self
	{
		$this->body = json_encode($data);
		$this->headers['Content-Type'] = 'application/json';

		return $this;
	}

	/**
	 * @param mixed[] $data
	 */
	public function setFormBody(array $data): self
	{
		$this->body = http_build_query($data);
		$this->headers['Content-Type'] = 'application/x-www-form-urlencoded';

		return $this;
	}

	/**
	 * @param int $key CURLOPT_* constant
	 */
	public function setOption(int $key, mixed $value): self
	{
		$this->options[$key] = $value;

		return $this;
	}

	/**
	 * @param mixed[] $options
	 */
	public function setOptions(array $options): self
	{
		$this->options = $options;

		return $this;
	}

	public function setTimeout(int $seconds): self
	{
		$this->timeout = $seconds;

		return $this;
	}

	public function setFollowRedirects(bool $follow): self
	{
		$this->followRedirects = $follow;

		return $this;
	}

	public function setSslVerify(bool $verify): self
	{
		$this->sslVerify = $verify;

		return $this;
	}

	public function setUserAgent(string $userAgent): self
	{
		$this->userAgent = $userAgent;

		return $this;
	}

	public function build(): Request
	{
		$request = new Request($this->url, $this->method);
		$request->setHeaders($this->headers);
		$request->setBody($this->body);

		$options = $this->options;

		if ($this->timeout !== null) {
			$options[CURLOPT_TIMEOUT] = $this->timeout;
		}

		$options[CURLOPT_FOLLOWLOCATION] = $this->followRedirects ? 1 : 0;
		$options[CURLOPT_SSL_VERIFYPEER] = $this->sslVerify ? 1 : 0;

		if ($this->userAgent !== null) {
			$options[CURLOPT_USERAGENT] = $this->userAgent;
		}

		$request->setOptions($options);

		return $request;
	}

	public function getUrl(): string
	{
		return $this->url;
	}

	public function getMethod(): string
	{
		return $this->method;
	}

	/**
	 * @return string[]
	 */
	public function getHeaders(): array
	{
		return $this->headers;
	}

	public function getBody(): mixed
	{
		return $this->body;
	}

	/**
	 * @return mixed[]
	 */
	public function getOptions(): array
	{
		return $this->options;
	}

}
