<?php

namespace WebChemistry\Presenters\AdminModule;

use WebChemistry\Components\Notifications;
use App\Presenters\BasePresenter;
use Nette, WebChemistry, Kdyby;

abstract class AdminPresenter extends BasePresenter {

	/** @var Notifications */
	private $notifications;

	/** @var WebChemistry\Administration\Configuration */
	private $configuration;

	/** @var string */
	protected $theme = 'default';

	/**
	 * @param Notifications $notifications
	 */
	public function injectNotifications(Notifications $notifications) {
		$this->notifications = $notifications;
	}

	/**
	 * @param WebChemistry\Administration\Configuration $configuration
	 */
	public function injectConfiguration(WebChemistry\Administration\Configuration $configuration) {
		$this->configuration = $configuration;
	}

	/**
	 * @return void
	 */
	protected function startup() {
		parent::startup();

		if (!$this->user->isLoggedIn()) {
			if ($this->name !== 'Admin:Homepage' || $this->action !== 'login') {
				$this->redirectStore('Homepage:login');
			}
		} else {
			if (!$this->user->isAdmin()) {
				$this->error('You are not administrator.', Nette\Http\IResponse::S403_FORBIDDEN);
			} else {
				$this->processNotifications();
			}
		}
	}

	protected function beforeRender() {
		parent::beforeRender();

		$this->template->lang = $this->translator && $this->translator->getLocale() ? $this->translator->getLocale() : 'cs';
		$this->template->_extraMenu = $this->configuration->getExtraMenu();
		$this->template->_skin = $this->configuration->getSkin();
		$this->template->_miscSkin = $this->configuration->getSkin('misc');
		$this->template->_sidebarCollapse = $this->getHttpRequest()->getCookie('admin-sidebar-collapse');
		$this->template->_cookiePath = $this->getHttpResponse()->cookiePath;
		$this->template->_labels = $this->configuration->getLabels();
	}

	private function processNotifications() {
		$this->notifications->notificationToTemplate($this->getTemplate());
	}

	/************************* Methods **************************/

	/**
	 * @param string $message
	 * @param string $icon
	 */
	public function createNotification($message, $icon) {
		call_user_func_array([$this->notifications, 'createNotification'], func_get_args());
	}

	/**
	 * @return WebChemistry\Components\Menu
	 */
	protected function createComponentMenu() {
		return new WebChemistry\Components\Menu($this->configuration);
	}

	/************************* Change skin **************************/

	/**
	 * @param string $skin
	 */
	public function handleChangeSkin($skin) {
		$this->getHttpResponse()->setCookie('admin-skin', $skin, '365 days');
		$this->redirect('this');
	}

	/************************* Sidebar collapse **************************/

	public function handleSidebarCollapse() {
		$collapse = $this->getHttpRequest()->getCookie('admin-sidebar-collapse');
		$this->getHttpResponse()->setCookie('admin-sidebar-collapse', $collapse ? '' : 'true', '365 days');
		$this->redirect('this');
	}

}
