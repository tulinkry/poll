<?php

namespace Tulinkry\Poll\Services;

use Tulinkry\Model\BaseModel;
use Tulinkry\Poll\Services\IPollProvider;

use Tulinkry\Poll\Entities;

use Nette\Http\Request;
use Nette\Http\Session;

use Nette\Utils\Json;
use Nette\Utils\FileSystem;
use Nette\Utils\Strings;

use Tracy\Debugger;


abstract class FileProvider extends BaseModel implements IPollProvider
{
	const SESSION = '__polls';

	/**
	 * @var Request
	 */
	private $request;

	/**
	 * @var Session
	 */
	private $session;

	private $path;

	protected $encoder;
	protected $decoder;

	public function __construct($path, callable $encoder, callable $decoder, Request $request, Session $session) {
		$this->path = $path;
		$this->request = $request;
		$this->session = $session;
		$this->encoder = $encoder;
		$this->decoder = $decoder;
	}


	private function load($id) {
		try {
			$data = call_user_func_array($this->decoder, array(FileSystem::read($this->path)));
			foreach($data['polls'] as $poll) {
				if($poll['id'] === $id)
					return array_merge(array('answers' => array(), "voted" => 0,
													'multiple' => true, 'votedBy' => array()), $poll);
			}
		} catch(\Exception $e) {
			Debugger::log($e);
			return null;
		}
	}


	public function item($id) {
		if(($poll = $this->load($id)) !== null) {
			return (new Entities\Poll())
				->setId($poll['id'])
				->setVoted($poll['voted'])
				->setMultiple(isset($poll['multiple']) ? $poll['multiple'] : false)
				->setAnswers($this->getAnswers($id))
				->setQuestion($this->getQuestion($id));
		}
		return null;
	}

	private function save($poll) {
		$saved = false;
		$data = array('polls' => array());
		try {
			$data = call_user_func_array($this->decoder, array(FileSystem::read($this->path)));
			foreach($data['polls'] as &$b_poll) {
				if($b_poll['id'] === $poll['id']) {
					$b_poll = $poll;
					$saved = true;
				}
			}
		} catch(\Exception $e) {
			Debugger::log($e);
		}

		if (!$saved) {
			$poll['id'] = count($data['polls']) > 0 ? max(array_map(function($e) { return $e['id']; }, $data['polls'])) + 1 : 1;
			$data['polls'][] = $poll;
		}
		FileSystem::createDir(dirname($this->path));
		FileSystem::write($this->path, call_user_func_array($this->encoder, array($data)), $mode = NULL);

	}

	public function vote($id, $votes) {
		if(($item = $this->load($id)) !== null) {
			if(!is_array($votes)) {
				$votes = array($votes);
			}
			foreach($votes as $vote) {
				foreach($item['answers'] as &$answer) {
					if($answer['id'] === $vote) {
						$answer['voted'] ++;
						$item['voted'] ++;
					}
				}
			}
			if(!in_array($this->request->getRemoteAddress(), $item['votedBy'])) {
				//$item['votedBy'][] = $this->request->getRemoteAddress();
			}
			//$this->session->getSection(self::SESSION)->offsetSet('poll-' . $id, true);
			$this->save($item);
			return;
		}
		throw new \Exception("No poll");
	}

	public function addAnswer($id, $newAnswer) {
		if(($item = $this->load($id)) !== null) {
			$answer = array();
			$answer['id'] = count($item['answers']) > 0 ? max(array_map(function($e) { return $e['id']; }, $item['answers'])) + 1 : 1;
			$answer['text'] = $newAnswer;
			$answer['voted'] = 0;
			$item['answers'][] = $answer;
			$this->save($item);
			return;
		}
		throw new \Exception("No poll");
	}

	public function getAnswers($id) {
		if(($item = $this->load($id)) != null) {
			return array_map(function($e) {
				return (new Entities\Answer())->setId($e['id'])->setVoted($e['voted'])->setText($e['text']);
			}, $item['answers']);
		}
		return array();
	}

	public function getQuestion($id) {
		if(($item = $this->load($id)) != null) {
			return (new Entities\Question())->setText($item['question']['text']);
		}
		return null;
	}

	public function isVotable($id) {
		$polls = $this->session->getSection(self::SESSION);
		if($polls->offsetExists('poll-' . $id) && $polls->offsetGet('poll-' . $id) === true) {
			return false;
		}

		if(($item = $this->load($id)) !== null && isset($item['votedBy'])) {
			return ! in_array($this->request->getRemoteAddress(), $item['votedBy']);
		}

		return true;
	}
}
