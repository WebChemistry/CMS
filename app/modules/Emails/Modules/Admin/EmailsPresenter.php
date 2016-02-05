<?php

namespace App\Presenters\AdminModule;

use Forms\Emails;
use WebChemistry, Nette, Doctrine;

class EmailsPresenter extends AdminPresenter {

	/** @var array */
	private $components = [];

	/** @var WebChemistry\Emails\Provider */
	private $provider;

	/** @var Emails */
	private $form;

	/**
	 * @param WebChemistry\Emails\Provider $factory
	 * @param Emails $form
	 */
	public function __construct(WebChemistry\Emails\Provider $factory, Emails $form) {
		$this->provider = $factory;
		$this->form = $form;
	}

	public function actionDefault() {
		$this->components = [];

		foreach ($this->provider->getServices() as $service) {
			foreach ($service->getEmails() as $email) {
				$this->components[] = [
					$this->baseForm($email['name'], $email['params'], get_class($service), $email['template']),
					$email['desc'],
					$email['variables']
				];
			}
		}

		$this->template->services = $this->components;
	}

	/**
	 * @param string $name
	 * @param array $params
	 * @param string $className
	 * @param string $template
	 * @return Nette\ComponentModel\IComponent|NULL
	 * @throws \Exception
	 */
	public function baseForm($name, array $params, $className, $template) {
		$componentName = str_replace('\\', '_', $className) . '___' . $name;
		if ($this->getComponent($componentName, FALSE)) {
			return $this->getComponent($componentName);
		}

		$form = $this->form->createEmail();

		$params['template'] = file_get_contents($template);

		$form->setDefaults($params);
		$form->onSuccess[] = $this->successForm;

		$this->addComponent($form, $componentName);

		return $this->getComponent($componentName);
	}

	/**
	 * @param WebChemistry\Forms\Form $form
	 * @param object $values
	 */
	public function successForm(WebChemistry\Forms\Form $form, $values) {
		if ($form->getSubmittedName() === 'preview') {
			$explode = explode('___', $form->getName());
			$class = str_replace('_', '\\', $explode[0]);
			$method = $explode[1];

			$class = $this->provider->getByClass($class);
			$preview = $class->showPreview($method);
			$preview->setContent($values->template);
			$preview->setPreview();

			$this->template->html = (string) $preview;
			if ($preview->hasError()) {
				$this->flashMessage('emails.admin.default.variableNotExists', 'error');
			}
		} else {
			$explode = explode('___', $form->getName());
			$class = str_replace('_', '\\', $explode[0]);
			$method = $explode[1];

			$class = $this->provider->getByClass($class);
			/** @var WebChemistry\Emails\Templating\Engine $template */
			$template = call_user_func($class->getCallback($method));
			$store = $template->read();
			$template->getLocalFile()->rewriteSafe($values->template);

			@unlink($template->getCacheFile());
			$template->renderToString();
			if ($template->hasError()) {
				$template->getLocalFile()->rewriteSafe($store);
				$this->flashMessage('emails.admin.default.variableNotExists', 'error');
				return;
			}

			// Other
			$class->setData($method, $form->getValues(TRUE));

			$this->flashMessage('emails.admin.default.successForm');
			$this->redraw();
		}
	}

}
