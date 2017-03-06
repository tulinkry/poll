<?php

namespace Tulinkry\Poll\Services;

interface IPollProvider
{
	public function item($id);
	public function vote($id, $votes);
	public function addAnswer($id, $answer);
	public function getAnswers($id);
	public function getQuestion($id);
	public function isVotable($id);
}