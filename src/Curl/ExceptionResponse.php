<?php

namespace Contributte\Http\Curl;

use Exception;

/**
 * @author Milan Felix Sulc <sulcmil@gmail.com>
 */
class ExceptionResponse extends Response
{

	/** @var Exception */
	private $exception;

	/**
	 * @param Exception $exception
	 */
	public function __construct(Exception $exception)
	{
		parent::__construct();
		$this->exception = $exception;
	}

	/**
	 * @return Exception
	 */
	public function getException()
	{
		return $this->exception;
	}

}
