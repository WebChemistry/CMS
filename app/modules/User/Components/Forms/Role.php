<?php

namespace App\UserModule\Components\Forms;

use Entity;
use Kdyby\Doctrine\EntityManager;
use WebChemistry, Nette;

class Role extends WebChemistry\Forms\Control {

	/** @var \Kdyby\Doctrine\EntityManager */
	private $em;

	/** @var \Nette\Security\User */
	private $user;

	/** @var Entity\Role */
	private $role;

	/** @var WebChemistry\Permission\IStorage */
	private $storage;

	/**
	 * @param WebChemistry\Forms\Factory\IContainer $factory
	 * @param EntityManager $em
	 * @param Nette\Security\User $user
	 * @param WebChemistry\Permission\IStorage $storage
	 */
	public function __construct(WebChemistry\Forms\Factory\IContainer $factory, EntityManager $em,
								Nette\Security\User $user, WebChemistry\Permission\IStorage $storage) {
		parent::__construct($factory, $em);
		$this->em = $em;
		$this->user = $user;
		$this->storage = $storage;
	}

	/************************* Validators **************************/
	/**
	 * @param Nette\Forms\IControl $control
	 * @return bool
	 */
	public function validateExistsByControlName(Nette\Forms\IControl $control) {
		$name = $control->getName();
		if ($this->role && $this->role->$name === $control->getValue()) {
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
		if ($this->role && $this->role->$name === $control->getValue()) {
			return TRUE;
		}

		return !(bool) $this->getRepository()->countBy([$name => $control->getValue()]);
	}

	/**
	 * @param Nette\Forms\IControl $control
	 * @return bool
	 */
	public function validateUserExistsByControlName(Nette\Forms\IControl $control) {
		return (bool) $this->em->getRepository('Entity\User')->countBy([$control->getName() => $control->getValue()]);
	}

	/************************* / Validators **************************/

	/**
	 * @return WebChemistry\Forms\Form
	 */
	public function createRole() {
		$form = $this->getForm();

		$form->addGroup('role.admin.forms.create.main');

		$form->addText('name', 'role.admin.forms.create.name')
			->setRequired()
			->addRule($form::MAX_LENGTH, NULL, 120)
			->addRule([$this, 'validateNotExistsByControlName'], 'role.admin.forms.create.roleExists');

		$form->addSelect('extends', 'role.admin.forms.create.extends', $this->getRepository()->findPairs('name',
			'id'))
			->setPrompt('role.admin.forms.create.chooseRole')
			->setTranslate(FALSE);

		$form->addCheckbox('isAdmin', 'role.admin.forms.create.admin');

		$form->addCheckbox('allowAll', 'role.admin.forms.create.allowAll');

		$form->addCheckbox('monitor', 'role.admin.forms.create.monitoring');

		// Privileges
		$container = $form->addContainer('privileges');

		$later = [];
		$privileges = $this->storage->getPrivileges();
		foreach ($this->storage->getResources() as $resource => $name) {
			if (isset($privileges[$resource])) {
				$form->addGroup($name);
				$container->setCurrentGroup($form->getGroup($name));
				$privilegeContainer = $container->addContainer($resource);

				foreach ($privileges[$resource] as $privilege => $privilegeName) {
					$privilegeContainer->addCheckbox($privilege, $privilegeName);
				}
			} else {
				$later[$resource] = $name;
			}
		}

		$form->setCurrentGroup();
		$container->setCurrentGroup();

		// Resources without privileges
		foreach ($later as $resource => $name) {
			$container->addCheckbox($resource, $name);
		}

		$form->addSubmit('send', 'role.admin.forms.create.send');
		$form->onSuccess[] = [$this, 'successRole'];
		$form->onProcess[] = [$this, 'processRole'];


		return $form;
	}

	/**
	 * @param array $values
	 * @return array
	 */
	private function arrayFilter(array $values) {
		foreach ($values as $index => $value) {
			if (is_array($value)) {
				$values[$index] = $this->arrayFilter($value);
			}
			if (!$values[$index]) {
				unset($values[$index]);
			}
 		}

		return $values;
	}

	/**
	 * @param WebChemistry\Forms\Form $form
	 * @param array $values
	 * @return mixed
	 */
	public function processRole(WebChemistry\Forms\Form $form, array $values) {
		$values['privileges'] = $this->arrayFilter($values['privileges']);
		$this->storage->clearCache();

		return $values;
	}

	/**
	 * @param WebChemistry\Forms\Form $form
	 * @param Nette\Utils\ArrayHash $values
	 * @throws \Exception
	 */
	public function successRole(WebChemistry\Forms\Form $form, $values) {
		$entity = $form->getEntity(Entity\Role::class);
		$entity->privileges = new Entity\Privileges();
		$entity->privileges->allow = $values->privileges;

		$this->em->persist($entity);
		$this->em->flush();
	}

	/**
	 * @param string $term
	 * @return array
	 */
	public function suggestUserEmail($term) {
		return $this->getUserRepository()->suggestUser($term);
	}

	/**
	 * @return WebChemistry\Forms\Form
	 */
	public function createAssignRole() {
	    $form = $this->getForm();

	   	$form->addSuggestion('email', 'role.admin.forms.assign.user', [$this, 'suggestUserEmail'])
			 ->setRequired()
			 ->addRule($form::EMAIL)
			 ->addRule(~$form::EQUAL, 'role.admin.forms.assign.assignYourself', $this->user->identity->email)
			 ->addRule($this->validateUserExistsByControlName, 'role.admin.forms.assign.useNotExists');

		$form->addSelect('role', 'role.admin.forms.assign.role', $this->getRepository()->findPairs([], 'name', 'id'))
			 ->setPrompt('role.admin.forms.assign.chooseRole')
			 ->setTranslate(FALSE)
			 ->setRequired();

	    $form->addSubmit('send', 'role.admin.forms.assign.send');

		$form->onProcess[] = $this->processAssignRole;
		$form->onSuccess[] = $this->successAssignRole;

	    return $form;
	}

	/**
	 * @param WebChemistry\Forms\Form $form
	 * @param Nette\Utils\ArrayHash $values
	 * @return Nette\Utils\ArrayHash
	 */
	public function processAssignRole(WebChemistry\Forms\Form $form, $values) {
		$values->user = $this->getUserRepository()->getUserByEmail($values->email);
		$values->user->role = $values->role = $this->getRepository()->find($values->role);

		return $values;
	}

	/**
	 * @param WebChemistry\Forms\Form $form
	 * @param Nette\Utils\ArrayHash $values
	 * @throws \Exception
	 */
	public function successAssignRole(WebChemistry\Forms\Form $form, $values) {
		$this->em->merge($values->user);
		$this->em->flush();
	}

	/**
	 * @return WebChemistry\Forms\Form
	 */
	public function createEditRole() {
	    $form = $this->createRole();

		$form->resetEvents('onSuccess');

		if ($this->role) {
			$settings = new WebChemistry\Forms\Doctrine\Settings();
			$settings->setJoinOneColumn([
				'privileges' => 'allow'
			]);
			$form->setEntity($this->role, $settings);
		}
		$form->onSuccess[] = $this->successEditRole;

	    return $form;
	}

	/**
	 * @param WebChemistry\Forms\Form $form
	 * @param Nette\Utils\ArrayHash $values
	 * @throws \Exception
	 */
	public function successEditRole(WebChemistry\Forms\Form $form, $values) {
		$entity = $form->getEntity();
		$entity->privileges->allow = $values->privileges;
		$this->em->merge($entity);
		$this->em->flush();
	}

	/************************* Repository **************************/

	/**
	 * @return \Repository\User
	 */
	public function getUserRepository() {
	    return $this->em->getRepository(Entity\User::class);
	}

	/**
	 * @return \Repository\User
	 */
	protected function getRepository() {
		return $this->em->getRepository(Entity\Role::class);
	}

	/**
	 * @param Entity\Role $role
	 * @return Role
	 */
	public function setRole(Entity\Role $role) {
		$this->role = $role;

		return $this;
	}
}
