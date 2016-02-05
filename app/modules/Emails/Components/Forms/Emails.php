<?php

namespace Forms;

use WebChemistry, Nette;

class Emails extends WebChemistry\Forms\Control {

	/**
	 * @return WebChemistry\Forms\Form
	 */
	public function createEmail() {
		$form = $this->getForm();

		$form->addText('email', 'emails.forms.email.email')
			 ->setRequired()
			 ->addRule($form::EMAIL);

		$form->addText('name', 'emails.forms.email.name');

		$form->addText('subject', 'emails.forms.email.subject')
			 ->setRequired();

		$form->addTextArea('template', 'emails.forms.email.template');

		$form->addSubmit('preview', 'emails.forms.email.preview');
		$form->addSubmit('submit', 'emails.forms.email.submit');

		return $form;
	}

}
