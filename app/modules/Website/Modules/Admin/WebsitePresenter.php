<?php

namespace App\Presenters\AdminModule;

use Forms\Website;
use WebChemistry,
    Nette,
    Doctrine;

class WebsitePresenter extends AdminPresenter {

    /** @var Website */
    private $websiteForm;

	/** @var array */
	private $forms;

	/**
	 * @param Website $websiteForm
	 */
	public function __construct(Website $websiteForm) {
		$this->websiteForm = $websiteForm;
	}

	/**
     * @isAllowed website:setting
     */
    public function actionDefault() {
		foreach ($this->websiteForm->getNames() as $name => $title) {
			$method = 'create' . ucfirst($name);
			$this->createBaseForm(call_user_func([$this->websiteForm, $method]), $name, $title);
		}

		$this->template->forms = $this->forms;
    }

	/**
	 * @param WebChemistry\Forms\Form $form
	 * @param string $name
	 * @param string $translated
	 * @throws \Exception
	 */
	protected function createBaseForm(WebChemistry\Forms\Form $form, $name, $translated) {
		$form->onSuccess[] = $this->successWebsite;

		$this->addComponent($form, $name);

		$this->forms[$name] = $translated;
	}

	/**
	 * @param WebChemistry\Forms\Form $form
	 * @param $values
	 */
	public function successWebsite(WebChemistry\Forms\Form $form, $values) {
		$this->createNotification('website.admin.default.notification', 'fa-globe', 'warning');

		$this->flashMessage('website.admin.default.successWebsite');
		$this->redraw();
	}

}
