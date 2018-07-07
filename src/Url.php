<?php declare(strict_types = 1);

namespace Contributte\Http;

use Nette\Http\Url as NetteUrl;

class Url extends NetteUrl
{

	public function appendPath(string $path): self
	{
		$this->setPath($this->getPath() . $path);

		return $this;
	}

}
