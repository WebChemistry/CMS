<?php

namespace App\Forms;

use Nette\Security\AuthenticationException;
use WebChemistry\Forms\Control;
use WebChemistry\Forms\Form;
use WebChemistry\Forms\Factory\IContainer;
use Nette\Security\User;

class SignIn extends Control {

	/** @var User */
	private $user;

	public function __construct(IContainer $factory, User $user) {
		parent::__construct($factory);
		$this->user = $user;
	}

	/**
	 * @return Form
	 */
	public function createSignIn() {
		$form = $this->getForm();

		$form->addText('email', 'core.user.email')
			->setRequired()
			->addRule($form::EMAIL);

		$form->addPassword('password', 'core.user.password')
			->setRequired();

		$form->addCheckbox('remember', 'core.user.remember');

		$form->addSubmit('send', 'core.user.signIn');
		$form->onSuccess[] = $this->successSignIn;

		return $form;
	}

	public function successSignIn(Form $form, $values) {
		if ($values->remember) {
			$this->user->setExpiration('14 days', FALSE);
		} else {
			$this->user->setExpiration('20 minutes', TRUE);
		}

		try {
			$this->user->login($values->email, $values->password);
		} catch (AuthenticationException $e) {
			$form->addError($form->getTranslator()->translate($e->getMessage()));
		}
	}

}
