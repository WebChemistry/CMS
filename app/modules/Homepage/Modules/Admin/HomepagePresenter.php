<?php

namespace App\Presenters\AdminModule;

use App\Forms\SignIn;
use WebChemistry;
use Nette;
use Entity;
use Kdyby;
use App\UserModule\Components;

class HomepagePresenter extends AdminPresenter {

	/** @persistent */
	public $backlink;

	/** @var SignIn */
	public $signForm;

    /** @var WebChemistry\Administration\Configuration */
    private $configuration;

    public function __construct(WebChemistry\Administration\Configuration $configuration, SignIn $signForm) {
        $this->configuration = $configuration;
		$this->signForm = $signForm;
	}

	/************************* Default **************************/

	public function actionDefault() {
		$this->template->boxes = $this->configuration->getBoxes();
		$this->template->contents = $this->configuration->getHomepages();
	}

	/************************* Sign in **************************/

	public function actionLogin() {
		if ($this->user->isLoggedIn()) {
			$this->redirect('home.admin');
		}
	}

	/**
	 * @return WebChemistry\Forms\Form
	 */
	protected function createComponentSignInForm() {
		$form = $this->signForm->createSignIn();

		unset($form[$form::PROTECTOR_ID]);

		$form->onError[] = $this->errorSignIn;
		$form->onSuccess[] = $this->successSignIn;

		return $form;
	}

	public function errorSignIn(WebChemistry\Forms\Form $form) {
		foreach ($form->getErrors() as $error) {
			$this->flashMessage($error, 'error');
		}
	}

	public function successSignIn(WebChemistry\Forms\Form $form) {
		$this->redirectRestore($this->backlink, 'home.admin');
	}

	/************************* Sign out **************************/

	public function actionSignOut() {
		$this->getUser()->logout(TRUE);

		$this->flashMessage($this->translate('homepage.admin.signout'));
		$this->redirect('login');
	}

}
