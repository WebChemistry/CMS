<?php
namespace App\Presenters\FrontModule;

use App\Forms\SignIn;
use App\UserModule\Components;
use Nette, WebChemistry;

class SignPresenter extends FrontPresenter {

	/** @persistent */
	public $backlink;

	/** @var SignIn */
	public $signInForm;

	/** @var Components\Emails\User */
	public $userEmail;

	/** @var Components\Forms\User */
	private $userForm;

	/**
	 * @param SignIn $signInForm
	 * @param Components\Forms\User $userForm
	 * @param Components\Emails\User $userEmail
	 */
	public function __construct(SignIn $signInForm, Components\Forms\User $userForm, Components\Emails\User $userEmail) {
		$this->signInForm = $signInForm;
		$this->userEmail = $userEmail;
		$this->userForm = $userForm;
	}

	/************************* Sign in **************************/

	/**
	 * @user loggedOut
	 */
	public function actionIn() {}

	protected function createComponentSignIn() {
		$form = $this->signInForm->createSignIn();

		$form->onSuccess[] = $this->successSignIn;

		return $form;
	}

	public function successSignIn(WebChemistry\Forms\Form $form) {
		$this->flashMessage('user.front.flashes.signIn');
		$this->redirectRestore($this->backlink, 'home.front', ['backlink' => NULL]);
	}

	/************************* Sign out **************************/

	public function actionOut() {
		$this->getUser()->logout(TRUE);

		$this->flashMessage('user.front.flashes.signOut');
		$this->redirectRestore($this->backlink, 'home.front', ['backlink' => NULL]);
	}

	/************************* Sign up **************************/

	/**
	 * @user loggedOut
	 */
	public function actionUp() {}

	/**
	 * @return WebChemistry\Forms\Form
	 */
	protected function createComponentRegisterForm() {
		$form = $this->userForm->createUser();

		$form->onSuccess[] = $this->afterRegister;

		return $form;
	}

	public function afterRegister(WebChemistry\Forms\Form $form, $values) {
		$this->flashMessage('user.front.flashes.register');
		$this->redraw();
	}

}
