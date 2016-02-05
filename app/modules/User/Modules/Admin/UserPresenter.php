<?php

namespace App\Presenters\AdminModule;

use App\UserModule\Settings;
use App\UserModule\Components;
use WebChemistry;
use Nette;
use Entity;

class UserPresenter extends AdminPresenter {

	/** @var Components\Forms\User */
	public $userForm;

	/** @var Components\Forms\Role */
	public $roleForm;

	/** @var Components\Grids\User */
	public $grid;

	/** @var Components\Grids\IUserAdmin */
	public $adminGrid;

	/** @var Components\Grids\Role */
	public $roleGrid;

	/** @var WebChemistry\Administration\Configuration */
	private $configuration;

	/**
	 * @param WebChemistry\Administration\Configuration $configuration
	 * @param Components\Forms\User $userForm
	 * @param Components\Forms\Role $roleForm
	 * @param Components\Grids\User $grid
	 * @param Components\Grids\IUserAdmin $adminGrid
	 * @param Components\Grids\Role $roleGrid
	 */
	public function __construct(WebChemistry\Administration\Configuration $configuration, Components\Forms\User $userForm,
								Components\Forms\Role $roleForm, Components\Grids\User $grid,
								Components\Grids\IUserAdmin $adminGrid, Components\Grids\Role $roleGrid) {
		$this->configuration = $configuration;
		$this->userForm = $userForm;
		$this->roleForm = $roleForm;
		$this->grid = $grid;
		$this->adminGrid = $adminGrid;
		$this->roleGrid = $roleGrid;
	}

	/************************* Default **************************/

	/**
	 * @return WebChemistry\Forms\Form
	 */
	protected function createComponentAdminRegistration() {
		$form = $this->userForm->createUserAdmin();

		$form->onSuccess[] = $this->successAdminRegistration;

		return $form;
	}

	/**
	 * @param WebChemistry\Forms\Form $form
	 * @param $values
	 */
	public function successAdminRegistration(WebChemistry\Forms\Form $form, $values) {
		$this->createNotification('user.admin.notifications.add', Settings::ICON, $values->name);

		$this->flashMessage('user.admin.flashes.add');
		$this->redraw();
	}

	/**
	 * @return Components\Grids\User
	 */
	protected function createComponentGrid() {
		return $this->grid;
	}

	/**
	 * @return Components\Grids\UserAdmin
	 */
	protected function createComponentAdminGrid() {
		return $this->adminGrid->create();
	}

	/************************* Role **************************/

	/**
	 * @return WebChemistry\Forms\Form
	 */
	protected function createComponentAddRole() {
		$form = $this->roleForm->createRole();

		$form->onSuccess[] = $this->successAddRole;

		return $form;
	}

	/**
	 * @param WebChemistry\Forms\Form $form
	 * @param Nette\Utils\ArrayHash $values
	 */
	public function successAddRole(WebChemistry\Forms\Form $form, $values) {
		$this->createNotification('role.admin.notification.add', Settings::ICON, $values->name);

		$this->flashMessage('role.admin.flashes.add');
		$this->redraw();
	}

	/**
	 * @return WebChemistry\Forms\Form
	 */
	protected function createComponentAssignRole() {
		$form = $this->roleForm->createAssignRole();

		$form->onSuccess[] = $this->successAssignRole;

		return $form;
	}

	/**
	 * @param WebChemistry\Forms\Form $form
	 * @param Nette\Utils\ArrayHash $values
	 */
	public function successAssignRole(WebChemistry\Forms\Form $form, $values) {
		$this->createNotification('role.admin.notification.assign', Settings::ICON,
			$values->user->role->name, $values->user->name);

		$this->flashMessage('role.admin.flashes.assign');
		$this->redraw();
	}

	/**
	 * @return Components\Grids\Role
	 */
	protected function createComponentRoleGrid() {
		return $this->roleGrid;
	}

	/************************* Edit role **************************/

	/**
	 * @isAllowed role:edit
	 */
    public function actionEditRole($id) {
        $row = $this->getRoleRepository()->find($id);
        if (!$row) {
            $this->error('Role not found.');
        }
		$this->roleForm->setRole($row);
    }

	/**
	 * @return WebChemistry\Forms\Form
	 */
	protected function createComponentEditRole() {
		$form = $this->roleForm->createEditRole();

		$form->onSuccess[] = $this->successEditRole;

		return $form;
	}

	/**
	 * @param WebChemistry\Forms\Form $form
	 * @param Nette\Utils\ArrayHash $values
	 */
	public function successEditRole(WebChemistry\Forms\Form $form, $values) {
		$this->createNotification('role.admin.notification.edit', Settings::ICON, $values->name);

		$this->flashMessage('role.admin.flashes.edit');
		$this->redraw();
	}

	/************************* Edit **************************/

	/**
	 * @isAllowed user:edit
	 */
    public function actionEdit($id) {
        $this->template->row = $row = $this->getUserRepository()->find($id);
        if (!$row) {
            $this->error('User not found.');
        }
		$this->userForm->setUser($row);
    }

	/**
	 * @return WebChemistry\Forms\Form
	 */
	protected function createComponentEditProfile() {
		$form = $this->userForm->createEditProfileAdmin();

		$form->onSuccess[] = $this->successEditProfile;

		return $form;
	}

	/**
	 * @param WebChemistry\Forms\Form $form
	 * @param Nette\Utils\ArrayHash $values
	 */
	public function successEditProfile(WebChemistry\Forms\Form $form, $values) {
		$this->createNotification('user.admin.notifications.edit', Settings::ICON, $values->name);

		$this->flashMessage('user.admin.flashes.edit');
		$this->redraw();
	}

	/************************* Show **************************/

	/**
	 * @param int $id
	 * @throws Nette\Application\BadRequestException
	 */
	public function actionShow($id) {
        $this->template->row = $row = $this->getUserRepository()->getUserById($id);
		$this->template->tabs = $this->configuration->getUserTabs();
        if (!$row) {
            $this->error('User not found.');
        }
    }

	/**
	 * @return \Repository\User
	 */
	private function getUserRepository() {
	    return $this->em->getRepository(Entity\User::class);
	}

	/**
	 * @return \Repository\Role
	 */
	private function getRoleRepository() {
	    return $this->em->getRepository(Entity\Role::class);
	}

}
