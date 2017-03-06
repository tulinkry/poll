<?php

namespace Tulinkry\Poll\Entities;

use Nette\Object;

class Answer extends Object
{
	protected $id;
	protected $text;
	protected $voted;

	public function getId() {
		return $this->id;
	}

	public function setId($id) {
		$this->id = $id;
		return $this;
	}

	public function getText() {
		return $this->text;
	}

	public function setText($text) {
		$this->text = $text;
		return $this;
	}

	public function getVoted() {
		return $this->voted;
	}

	public function setVoted($voted) {
		$this->voted = $voted;
		return $this;
	}
}