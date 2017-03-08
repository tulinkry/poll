<?php

namespace Tulinkry\Poll\Control;

use Tulinkry\Application\UI;
use Tulinkry\Poll\Services\IPollProvider;

class PollControl extends UI\Control
{
	const COLLAPSE_THRESHOLD = 'collapseThreshold';

	protected $polls;
	protected $id;
	protected $options;

	public function __construct ( IPollProvider $polls, $id ) {
		$this->polls = $polls;
		$this->id = $id;
		$this->options = new \StdClass;
	}

	public function getPoll() {
		return $this->polls->item($this->id);
	}

	public function getAnswers() {
		return $this->polls->getAnswers($this->id);
	}

	public function getQuestion() {
		return $this->polls->getQuestion($this->id);
	}

	public function getOptions() {
		return $this->options;
	}

	public function getOption($name, $default = NULL) {
		if(!isset($this->options->$name)) {
			return $default;
		}
		return $this->options->$name;
	}

	public function setOption($name, $value) {
		$this->options->$name = $value;
		return $this;
	}

	public function render() {
		$this->useTemplate();
		$this->template->poll = $this->polls->item($this->id);
		$this->template->answers = $this->getAnswers();
		uasort($this->template->answers, function($a, $b) {
			if ($b->voted - $a->voted !== 0) {
				return $b->voted - $a->voted;
			}
			return strcmp($a->text, $b->text);
		});
		$this->template->question = $this->getQuestion();
		$this->template->isVotable = $this->polls->isVotable($this->id);
		$this->template->options = $this;
		$this->template->voted = $this->template->poll !== null ? $this->template->poll->voted : 0;
		$this->template->uniqueVoted = $this->template->poll !== null ? $this->template->poll->uniqueVoted : 0;
		parent::render();
	}
}