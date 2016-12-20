<?php

namespace Contributte\Http\Curl;

/**
 * @author Milan Felix Sulc <sulcmil@gmail.com>
 */
interface ICurlClient
{

	/**
	 * @param string $url
	 * @param array $headers
	 * @param array $opts
	 * @return Response
	 */
	public function makeRequest($url, array $headers = [], array $opts = []);

}
