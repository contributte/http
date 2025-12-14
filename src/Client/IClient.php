<?php declare(strict_types = 1);

namespace Contributte\Http\Client;

/**
 * Generic HTTP client interface
 */
interface IClient
{

	/**
	 * Execute a Request object
	 */
	public function request(Request $request): Response;

	/**
	 * Convenience method for GET requests
	 *
	 * @param string[] $headers
	 */
	public function get(string $url, array $headers = []): Response;

	/**
	 * Convenience method for POST requests
	 *
	 * @param string[] $headers
	 */
	public function post(string $url, mixed $body = null, array $headers = []): Response;

	/**
	 * Convenience method for PUT requests
	 *
	 * @param string[] $headers
	 */
	public function put(string $url, mixed $body = null, array $headers = []): Response;

	/**
	 * Convenience method for DELETE requests
	 *
	 * @param string[] $headers
	 */
	public function delete(string $url, array $headers = []): Response;

}
