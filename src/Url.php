<?php

namespace Contributte\Http;

use Nette\Http\Url as NetteUrl;

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
