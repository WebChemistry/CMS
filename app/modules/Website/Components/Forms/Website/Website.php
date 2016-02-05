<?php

namespace Forms;

use Entity\Parameter;
use Kdyby\Doctrine\EntityManager;
use WebChemistry, Nette;

class Website extends WebChemistry\Forms\BaseControl {

	/** @var WebChemistry\Parameters\Provider */
	private $parameters;

	/** @var array */
	private $values;

	/** @var array [formName => title] Used in WebsitePresenter */
	private $names = [
		'website' => 'website.forms.websiteName',
		'social' => 'website.forms.socialName'
	];

	/**
	 * @param WebChemistry\Forms\Factory\IContainer $factory
	 * @param EntityManager $em
	 * @param WebChemistry\Parameters\Provider $parameters
	 */
	public function __construct(WebChemistry\Forms\Factory\IContainer $factory,
								EntityManager $em, WebChemistry\Parameters\Provider $parameters) {
		parent::__construct($factory, $em);

		$this->parameters = $parameters;
		$this->values = $this->parameters->getParameters()->getArray();
	}

	/**
	 * @desc Nastavuje základní parametry webu
	 * @return WebChemistry\Forms\Form
	 */
	public function createWebsite() {
		$form = $this->getForm();

		$form->addText('websiteName', 'website.forms.website.name')
			 ->setRequired();

		$form->addText('websiteTitle', 'website.forms.website.title.name')
			 ->setOption('description',
				 Nette\Utils\Html::el()->setHtml($form->getTranslator()->translate('website.forms.website.title.desc'))
			 );

		$form->addTextArea('websiteDesc', 'website.forms.website.desc');

		$form->addTags('websiteKeys', 'website.forms.website.keys');

		$form->addText('websiteEmail', 'website.forms.website.email')
			 ->addCondition($form::FILLED)
			 ->addRule($form::EMAIL);

		$form->addImageUpload('websiteFavicon', 'website.forms.website.favicon')
			 ->setNamespace('others');

		$form->addCheckbox('websiteOldBrowsers', 'website.forms.website.oldBrowsers.name')
			 ->setOption('description', Nette\Utils\Html::el()->setHtml(
				 $form->getTranslator()->translate('website.forms.website.oldBrowsers.desc')));

		$form->addSubmit('submit', 'website.forms.website.submit');

		$form->setDefaults($this->values);

		$form->onSuccess[] = $this->successForm;

		return $form;
	}

	/**
	 * @return WebChemistry\Forms\Form
	 */
	public function createSocial() {
	    $form = $this->getForm();

		$form->addText('google.name', 'website.forms.social.google.name');
		$form->addText('google.id', 'website.forms.social.google.id');
		$form->addText('google.url', 'website.forms.social.google.plus')
			 ->addCondition($form::FILLED)
			 ->addRule($form::URL);

		$form->addText('twitter.url', 'website.forms.social.twitter.url')
			 ->addCondition($form::FILLED)
			 ->addRule($form::URL);

		$form->addText('facebook.id', 'website.forms.social.facebook.id');
		$form->addText('facebook.url', 'website.forms.social.facebook.url')
			 ->addCondition($form::FILLED)
			 ->addRule($form::URL);

		$form->addText('instagram', 'website.forms.social.instagram')
			 ->addCondition($form::FILLED)
			 	->addRule($form::URL);

	    $form->addSubmit('send', 'website.forms.social.submit');

		$form->setDefaults($this->values);
		$form->onSuccess[] = $this->successForm;

	    return $form;
	}

	/**
	 * @param WebChemistry\Forms\Form $form
	 * @param array $values
	 */
	public function successForm(WebChemistry\Forms\Form $form, array $values) {
		foreach ($values as $name => $value) {
			$this->parameters[$name] = $value;
		}

		$this->parameters->merge();
	}

	/**
	 * @return array
	 */
	public function getNames() {
		return $this->names;
	}

}
