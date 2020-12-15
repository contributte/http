<?php declare(strict_types = 1);

namespace Contributte\Http\Curl;

class ResponseFactory
{

	/** @var mixed */
	private $body;

	/** @var string[] */
	private $headers = [];

	/** @var mixed[] */
	private $info = [];

	/**
	 * @param mixed[] $info
	 */
	public function setInfo(array $info): void
	{
		$this->info = $info;
	}

	public function setBody(string $body): void
	{
		$this->body = $body;
	}

	/**
	 * @param mixed $handle
	 */
	public function parseHeaders($handle, string $header): int
	{
		preg_match('#^(.+):(.+)$#U', $header, $matches);
		if ($matches) {
			[, $key, $value] = $matches;
			$this->headers[trim($key)] = trim($value);
		}

		return strlen($header);
	}

	public function create(): Response
	{
		return new Response($this->body, $this->headers, $this->info);
	}

}
