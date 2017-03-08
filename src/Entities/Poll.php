<?php

namespace Tulinkry\Poll\Entities;

use Nette\Object;

class Poll extends Object
{
	protected $id;
	protected $multiple = false;
	protected $voted;
	protected $uniqueVoted;
	protected $answers = array();
	protected $question;

	public function getId() {
		return $this->id;
	}

	public function setId($id) {
		$this->id = $id;
		return $this;
	}

	public function getMultiple() {
		return $this->multiple;
	}

	public function isMultiple() {
		return $this->multiple;
	}

	public function setMultiple($multiple) {
		$this->multiple = $multiple;
		return $this;
	}

	public function getVoted() {
		return $this->voted;
	}

	public function setVoted($voted) {
		$this->voted = $voted;
		return $this;
	}

	public function getUniqueVoted() {
		return $this->uniqueVoted;
	}

	public function setUniqueVoted($uniqueVoted) {
		$this->uniqueVoted = $uniqueVoted;
		return $this;
	}

	public function getAnswers() {
		return $this->answers;
	}

	public function setAnswers($answers) {
		$this->answers = $answers;
		return $this;
	}

	public function getQuestion() {
		return $this->question;
	}

	public function setQuestion($question) {
		$this->question = $question;
		return $this;
	}
}