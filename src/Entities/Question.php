<?php

namespace Tulinkry\Poll\Entities;

use Nette\Object;

class Question extends Object
{
	protected $text;

	public function getText() {
		return $this->text;
	}

	public function setText($text) {
		$this->text = $text;
		return $this;
	}
}