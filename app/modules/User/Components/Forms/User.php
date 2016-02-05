<?php

namespace App\UserModule\Components\Forms;

use App\UserModule\Components;
use Kdyby\Doctrine\EntityManager;
use WebChemistry, Nette, Entity;

class User extends WebChemistry\Forms\Control {

	/** @var EntityManager */
	private $em;

	/** @var Entity\User */
	private $user;

	/** @var Nette\Security\User */
	private $securityUser;

	/** @var Components\Emails\User */
	private $userEmail;

	/** @var Nette\Mail\IMailer */
	private $mailer;

	/** @var WebChemistry\Forms\IPlugin[] */
	private $plugins = [];

	/**
	 * @param WebChemistry\Forms\Factory\IContainer $factory
	 * @param EntityManager $em
	 * @param Nette\Security\User $user
	 * @param Components\Emails\User $userEmail
	 * @param Nette\Mail\IMailer $mailer
	 */
	public function __construct(WebChemistry\Forms\Factory\IContainer $factory, EntityManager $em, Nette\Security\User $user,
								Components\Emails\User $userEmail, Nette\Mail\IMailer $mailer) {
		parent::__construct($factory);
		
		$this->em = $em;
		$this->securityUser = $user;
		$this->userEmail = $userEmail;
		$this->mailer = $mailer;
	}

	/************************* Validators **************************/

	/**
	 * @param Nette\Forms\IControl $control
	 * @return bool
	 */
	public function validateExistsByControlName(Nette\Forms\IControl $control) {
		$name = $control->getName();

		if ($this->user && $this->user->$name === $control->getValue()) {
			return TRUE;
		}

		return (bool) $this->getRepository()->countBy([$name => $control->getValue()]);
	}

	/**
	 * @param Nette\Forms\IControl $control
	 * @return bool
	 */
	public function validateNotExistsByControlName(Nette\Forms\IControl $control) {
		$name = $control->getName();

		if ($this->user && $this->user->$name === $control->getValue()) {
			return TRUE;
		}

		return !(bool) $this->getRepository()->countBy([$name => $control->getValue()]);
	}

	/************************* / Validators **************************/

	/**
	 * @param WebChemistry\Forms\Form $form
	 * @param Nette\Utils\ArrayHash $values
	 */
	public function sendRegistrationEmail(WebChemistry\Forms\Form $form, $values) {
		$user = $this->getRepository()->getUserByEmail($values->email);
		$template = $this->userEmail->createRegister($user->id, $user->getUserName(), $user->email);

		$message = new Nette\Mail\Message();
		$message->addTo($values->email);
		$message->setSubject($this->userEmail->getSubject('register'));
		$message->setFrom($this->userEmail->getEmail('register'));
		$message->setHtmlBody((string) $template);

		$this->mailer->send($message);
	}

	/************************* New user **************************/

	/**
	 * @param bool $passwordRequired
	 * @return WebChemistry\Forms\Form
	 */
	public function createUser($passwordRequired = TRUE) {
	    $form = $this->getForm();

	    $form->addText('name', 'user.admin.forms.create.name')
			 ->setRequired()
			 ->addRule($form::MAX_LENGTH, NULL, 100)
			 ->addRule($this->validateNotExistsByControlName, 'user.admin.forms.create.exists');

		$form->addText('email', 'user.admin.forms.create.email')
			 ->setRequired()
			 ->addRule($form::MAX_LENGTH, NULL, 80)
			 ->addRule($form::EMAIL)
			 ->addRule($this->validateNotExistsByControlName, 'user.admin.forms.create.emailExists');

		$password = $form->addPassword('password', 'user.admin.forms.create.password');
		if ($passwordRequired) {
			$password->setRequired();
		}

		$form->addPassword('rePassword', 'user.admin.forms.create.rePassword')
			 ->addRule($form::EQUAL, 'user.admin.forms.create.passwordNotEquals', $form['password']);

		foreach ($this->plugins as $plugin) {
			$plugin->run($form);
			$form->onProcess[] = [$plugin, 'process'];
		}

	    $form->addSubmit('send', 'user.admin.forms.create.send');

		$form->onSuccess[] = $this->successUser;

	    return $form;
	}

	/**
	 * @param WebChemistry\Forms\Form $form
	 * @throws \Exception
	 */
	public function successUser(WebChemistry\Forms\Form $form) {
		$this->em->persist($form->getEntity(Entity\User::class));
		$this->em->flush();
	}

	/**
	 * @param bool $passwordRequired
	 * @return WebChemistry\Forms\Form
	 */
	public function createUserAdmin($passwordRequired = TRUE) {
	    $form = $this->createUser($passwordRequired);

		unset($form['send']);

		$form->addSelect('role', 'user.admin.forms.create.role', $this->getRoleRepository()->findPairs([], 'name', 'id'))
			 ->setPrompt('user.admin.forms.create.chooseRole')
			 ->setRequired();

		$form->addSubmit('send', 'user.admin.forms.create.send');

		$form->onProcess[] = $this->processUserAdmin;

	    return $form;
	}

	/**
	 * @param WebChemistry\Forms\Form $form
	 * @param Nette\Utils\ArrayHash $values
	 * @return mixed
	 */
	public function processUserAdmin(WebChemistry\Forms\Form $form, $values) {
		$values->role = $this->em->getRepository(Entity\Role::class)->find($values->role);

		return $values;
	}

	/************************* Edit **************************/

	/**
	 * @return WebChemistry\Forms\Form
	 */
	public function createEditProfile() {
	    $form = $this->createUser(FALSE);

		$form->resetEvents('onSuccess');

		if ($this->user) {
			$settings = new WebChemistry\Forms\Doctrine\Settings();
			$settings->setJoinOneColumn([
				'role' => 'id'
			]);
			$form->setEntity($this->user, $settings);
		}

		$form->onProcess[] = $this->processEditProfile;
		$form->onSuccess[] = $this->successEditProfile;

	    return $form;
	}

	/**
	 * @return WebChemistry\Forms\Form
	 */
	public function createEditProfileAdmin() {
	    $form = $this->createUserAdmin(FALSE);

		$form->resetEvents('onSuccess');

		if ($this->user) {
			$settings = new WebChemistry\Forms\Doctrine\Settings();
			$settings->setJoinOneColumn([
				'role' => 'id'
			]);
			$form->setEntity($this->user, $settings);
		}

		$form->onProcess[] = $this->processEditProfile;
		$form->onSuccess[] = $this->successEditProfile;

	    return $form;
	}

	public function processEditProfile(WebChemistry\Forms\Form $form, $values) {
		if (!$values->password) {
			unset($values->password);
		}

		unset($values->rePassword);

		return $values;
	}

	public function successEditProfile(WebChemistry\Forms\Form $form) {
		$this->em->merge($form->getEntity());
		$this->em->flush();
	}

	/************************* Forgot **************************/

	/**
	 * @return WebChemistry\Forms\Form
	 */
	public function createForgot() {
	    $form = $this->getForm();

	    $form->addText('email', 'user.admin.forms.forgot.email')
			 ->setRequired()
			 ->addRule($form::EMAIL)
			 ->addRule($this->validateExistsByControlName, 'user.admin.forms.forgot.email');

	    $form->addSubmit('send', 'user.admin.forms.forgot.send');

		$form->onSuccess[] = $this->successForgot;

	    return $form;
	}

	/**
	 * @param WebChemistry\Forms\Form $form
	 * @param Nette\Utils\ArrayHash $values
	 * @throws \Exception
	 */
	public function successForgot(WebChemistry\Forms\Form $form, $values) {
		$user = $this->getRepository()->getUserByEmail($values->email);

		$user->forgetHash = $forgotHash = Nette\Utils\Random::generate();
		$user->forgetTime = new \DateTime('+ 1 day');

		$this->em->merge($user);
		$this->em->flush();

		$message = new Nette\Mail\Message();
		$message->setSubject($this->userEmail->getSubject('forgot'));
		$message->setHtmlBody((string) $this->userEmail->createForgot($forgotHash, $user->id));
		$message->setFrom($this->userEmail->getEmail('forgot'));
		$message->addTo($user->email);

		$this->mailer->send($message);
	}

	/**
	 * @return WebChemistry\Forms\Form
	 */
	public function createNewPassword() {
	    $form = $this->getForm();

	    $form->addPassword('password', 'user.admin.forms.newPassword.password')
			 ->setRequired()
			 ->addRule($form::MIN_LENGTH, NULL, 3);

		$form->addPassword('re_password', 'user.admin.forms.newPassword.rePassword')
			 ->addRule($form::EQUAL, 'user.admin.forms.newPassword.passwordNotEquals', $form['password']);

	    $form->addSubmit('send', 'user.admin.forms.newPassword.send');

		$form->onSuccess[] = $this->successNewPassword;

	    return $form;
	}

	/**
	 * @param WebChemistry\Forms\Form $form
	 * @param Nette\Utils\ArrayHash $values
	 * @throws \Exception
	 */
	public function successNewPassword(WebChemistry\Forms\Form $form, $values) {
		$this->user->forgetHash = NULL;
		$this->user->forgetTime = NULL;
		$this->user->password = $values->password;

		$this->em->merge($this->user);
		$this->em->flush();
	}

	/************************* Repository **************************/

	/**
	 * @return \Repository\User
	 */
	protected function getRepository() {
		return $this->em->getRepository(Entity\User::class);
	}

	/**
	 * @return \Repository\Role
	 */
	private function getRoleRepository() {
	    return $this->em->getRepository(Entity\Role::class);
	}

	/**
	 * @param Entity\User $user
	 * @return User
	 */
	public function setUser(Entity\User $user) {
		$this->user = $user;

		return $this;
	}

	/**
	 * @param WebChemistry\Forms\IPlugin $plugin
	 * @return self
	 */
	public function addPlugin(WebChemistry\Forms\IPlugin $plugin) {
		$this->plugins[] = $plugin;

		return $this;
	}

}
