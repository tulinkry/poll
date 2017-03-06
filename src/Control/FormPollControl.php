<?php

namespace Tulinkry\Poll\Control;

use Tulinkry\Poll\Services\IPollProvider;
use Tulinkry\Application\UI\Form;

use Tracy\Debugger;

class FormPollControl extends PollControl
{
	protected function createComponentPollControlForm () {
		$form = new Form;

		$answers = array();
		foreach($this->getAnswers() as $answer) {
			$answers[$answer->id] = $answer->text;
		}

		sort($answers);

		if($this->getPoll()->isMultiple()) {
			$checkbox = $form->addCheckboxList('poll_options', 'Hlasujte', $answers)
				->setRequired()
				->addRule(Form::FILLED, 'Vyberte prosím alespoň jednu možnost.');
			$checkbox->getLabelPrototype()->class = 'answer answer-form';
			$checkbox->getSeparatorPrototype()->class = 'answer answer-form-separator';
		} else {
			$form->addRadioList('poll_options', 'Hlasujte', $answers)
				->setRequired()
				->addRule(Form::FILLED, 'Vyberte prosím alespoň jednu možnost.');

		}

		$form->addSubmit('submit', 'Odeslat')
			;//->setAttribute('class', 'ajax btn btn-primary');

		// $form->getElementPrototype()->class = 'ajax';
		$form->onSuccess[] = array($this, 'onSuccess');
		$form->onSubmit[] = array($this, 'onSubmit');
		return $form;
	}

	public function onSubmit(Form $form) {
		$this->invalidateControl();
	}	

	public function onSuccess(Form $form) {
		try {
			$this->polls->vote($this->id, $form->values['poll_options']);
			$this->flashMessage('Váš hlas byl uložen.', 'success');
		} catch (\Exception $e) {
			Debugger::log($e);
			$this->flashMessage('Již jste hlasoval.', 'danger');
		}

		if(!$this->isAjax()) {
			$this->redirect('this');
		}
	}
}