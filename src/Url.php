<?php

namespace Contributte\Http;

use Nette\Http\Url as NetteUrl;

/**
 * @author Milan Felix Sulc <sulcmil@gmail.com>
 */
class Url extends NetteUrl
{

	/**
	 * @param string $path
	 * @return self
	 */
	public function appendPath($path)
	{
		$this->setPath($this->getPath() . $path);

		return $this;
	}

}
