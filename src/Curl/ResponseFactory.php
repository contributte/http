<?php

namespace Contributte\Http\Curl;

class ResponseFactory
{

	/** @var mixed */
	private $body;

	/** @var array */
	private $headers = [];

	/** @var array */
	private $info = [];

	/**
	 * @param array $info
	 * @return void
	 */
	public function setInfo(array $info)
	{
		$this->info = $info;
	}

	/**
	 * @param string $body
	 * @return void
	 */
	public function setBody($body)
	{
		$this->body = $body;
	}

	/**
	 * @param mixed $handle
	 * @param string $header
	 * @return int
	 */
	public function parseHeaders($handle, $header)
	{
		preg_match('#^(.+):(.+)$#U', $header, $matches);
		if ($matches) {
			list($whole, $key, $value) = $matches;
			$this->headers[trim($key)] = trim($value);
		}

		return strlen($header);
	}

	/**
	 * @return Response
	 */
	public function create()
	{
		return new Response($this->body, $this->headers, $this->info);
	}

}
