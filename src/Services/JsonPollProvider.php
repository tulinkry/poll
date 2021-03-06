<?php

namespace Tulinkry\Poll\Services;

use Tulinkry\Poll\Services\IPollProvider;

use Nette\Http\Request;
use Nette\Http\Session;

use Nette\Utils\DateTime;
use Nette\Utils\Json;
use Nette\Utils\Strings;


class JsonPollProvider extends FileProvider
{
	public function __construct($path, Request $request, Session $session) {
		if(!Strings::endsWith($path, '.json')) {
			throw new \Nette\InvalidArgumentException(sprintf('The JsonPollProvider expects the path to be json. "%s" given', $path));
		}

		parent::__construct($path,
							function($data) { return Json::encode($data, Json::PRETTY); }, 
							function($data) {
								$data = Json::decode($data, Json::FORCE_ARRAY);
								if($data !== FALSE && isset($data['polls'])) {
									foreach($data['polls'] as &$poll) {
										if(isset($poll['votedBy']) && is_array($poll['votedBy'])) {
											foreach($poll['votedBy'] as &$vote) {
												$vote['date'] = DateTime::from($vote['date']);
											}
										}
									}
								}
							},
							$request, 
							$session);
	}
}
