<?php

namespace Tulinkry\Poll\Services;

use Tulinkry\Poll\Services\IPollProvider;

use Nette\Http\Request;
use Nette\Http\Session;

use Nette\Neon\Neon;
use Nette\Utils\Strings;


class NeonPollProvider extends FileProvider
{
	public function __construct($path, Request $request, Session $session) {
		if(!Strings::endsWith($path, '.neon')) {
			throw new \Nette\InvalidArgumentException(sprintf('The NeonPollProvider expects the path to be neon. "%s" given', $path));
		}

		parent::__construct($path,
							function($data) { return Neon::encode($data, Neon::BLOCK); }, 
							function($data) { return Neon::decode($data); }, 
							$request, 
							$session);
	}
}
