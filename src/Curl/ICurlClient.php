<?php declare(strict_types = 1);

namespace Contributte\Http\Curl;

interface ICurlClient
{

	/**
	 * @param string[] $headers
	 * @param mixed[] $opts
	 */
	public function makeRequest(string $url, array $headers = [], array $opts = []): Response;

}
