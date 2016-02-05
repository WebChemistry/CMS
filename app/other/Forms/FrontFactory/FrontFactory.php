<?php

namespace WebChemistry\Factories;

use Kdyby\Translation\Translator;
use Nette\Object;
use WebChemistry\Forms\Doctrine;
use WebChemistry\Forms\Form;
use WebChemistry\Forms\Factory\IFactory;
use WebChemistry\FrontRenderer;

class FrontFactory extends Object implements IFactory {

	/** @var Doctrine */
	private $doctrine;

	/** @var array */
	private $parameters;

	/** @var Translator */
	private $translator;

	/**
	 * @param Doctrine $doctrine
	 * @param Translator $translator
	 */
	public function __construct(Doctrine $doctrine, Translator $translator = NULL) {
		$this->doctrine = $doctrine;
		$this->translator = $translator;
	}

	/**
	 * @param array $parameters
	 * @return self
	 */
	public function setParameters(array $parameters) {
		$this->parameters = $parameters;

		return $this;
	}

	/**
	 * @return Form
	 */
	public function create() {
		$form = new Form;

		$form->setRenderer(new FrontRenderer());
		if ($this->translator) {
			$form->setTranslator($this->translator);
		}
		$form->setSettings($this->parameters);
		$form->setDoctrine($this->doctrine);
		$form->setDoctrine($this->doctrine);

		return $form;
	}

}
