<?php declare(strict_types = 1);

namespace Contributte\Http\Curl;

use Contributte\Http\Client\Response;

class ResponseFactory
{

	private mixed $body = null;

	/** @var string[] */
	private array $headers = [];

	/** @var mixed[] */
	private array $info = [];

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

	public function parseHeaders(mixed $handle, string $header): int
	{
		preg_match('#^(.+):(.+)$#U', $header, $matches);
		if ($matches !== []) {
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
