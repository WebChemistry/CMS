<?php

namespace App\Presenters\FrontModule;

use App\UserModule\Components;
use WebChemistry;
use Entity;

class ForgotPresenter extends FrontPresenter {

	/** @var Components\Forms\User */
	public $userForm;

	/**
	 * * @param Components\Forms\User $userForm
	 */
	public function __construct(Components\Forms\User $userForm) {
		$this->userForm = $userForm;
	}

	/************************* Forgot **************************/

	/**
	 * Form component
	 *
	 * @return WebChemistry\Forms\Form
	 */
	protected function createComponentForgot() {
	    $form = $this->userForm->createForgot();

	    $form->onSuccess[] = $this->successForgot;

	    return $form;
	}

	/**
	 * @param WebChemistry\Forms\Form $form
	 */
	public function successForgot(WebChemistry\Forms\Form $form) {
	    $this->flashMessage('user.front.flashes.forgot');
		$this->redraw();
	}

	/************************* New password **************************/

	public function actionNewPassword($id, $hash) {
		$user = $this->getUserRepository()->getUserByForgotHash($hash, $id);

		if (!$user) {
			$this->flashMessage('user.front.flashes.hashNotFound');
			$this->redirect('home.front');
		}

		$this->userForm->setUser($user);
	}

	/**
	 * Form component
	 *
	 * @return WebChemistry\Forms\Form
	 */
	protected function createComponentNewPassword() {
	    $form = $this->userForm->createNewPassword();

	    $form->onSuccess[] = $this->successNewPassword;

	    return $form;
	}

	/**
	 * @param WebChemistry\Forms\Form $form
	 */
	public function successNewPassword(WebChemistry\Forms\Form $form) {
	    $this->flashMessage('user.front.flashes.newPassword');
		$this->redirect('home.front');
	}

	/**
	 * @return \Repository\User
	 */
	private function getUserRepository() {
	    return $this->em->getRepository(Entity\User::class);
	}

}
