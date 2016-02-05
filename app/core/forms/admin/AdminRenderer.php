<?php

namespace WebChemistry;

use Nette, Nette\Utils\Html;
use Nette\Forms\Controls;

class AdminRenderer extends Nette\Forms\Rendering\DefaultFormRenderer {

	/**
	 * @param Nette\Forms\Form $form
	 * @param string $mode
	 * @return string
	 */
	public function render(Nette\Forms\Form $form, $mode = NULL) {
		$this->wrappers['controls']['container'] = NULL;
		$this->wrappers['pair']['container'] = 'div class=form-group';
		$this->wrappers['pair']['.error'] = 'has-error';
		$this->wrappers['control']['container'] = 'div class=col-sm-9';
		$this->wrappers['label']['container'] = 'div class="col-sm-3 control-label"';
		$this->wrappers['control']['description'] = 'span class="input-group-addon btn btn-primary"';
		$this->wrappers['control']['errorcontainer'] = 'span class=help-block';

		$form->getElementPrototype()->class('form-horizontal');

		foreach ($form->getControls() as $control) {
			if ($control instanceof Controls\Button) {
				$control->getControlPrototype()->addClass(empty($usedPrimary) ? 'btn btn-primary' : 'btn btn-default');
				$usedPrimary = TRUE;
			} elseif ($control instanceof Controls\TextBase || $control instanceof Controls\SelectBox || $control instanceof Controls\MultiSelectBox) {
				$control->getControlPrototype()->addClass('form-control');
			} elseif ($control instanceof Controls\Checkbox || $control instanceof Controls\CheckboxList || $control instanceof Controls\RadioList) {
				$control->getSeparatorPrototype()->setName('div')->addClass($control->getControlPrototype()->type);
			}

			// Description
			$desc = $control->getOption('description');

			if ($desc) {
				$control->setOption('description',
					Html::el('span')->addAttributes([
						'class' => 'input-group-addon btn btn-primary' . ($control instanceof Controls\Checkbox || $control instanceof Controls\RadioList ? ' with-border' : ''),
						'data-toggle' => 'popover',
						'data-container' => 'body',
						'title' => 'Informace',
						'data-content' =>  (string) $desc,
						'data-html' => $desc instanceof Html ? 'true' : 'false',
						'data-placement' => 'left'
					])->add(
						Html::el('span')->addAttributes(['class' => 'fa fa-info'])
					)/*->add(
                        $desc instanceof Html ? Html::el('div class="hidden input-group-desc"')->setHtml($desc) : Html::el('div class="hidden input-group-desc"')->setText($desc)
                    )*/
				);
			}
		}

		return parent::render($form, $mode);
	}

	/**
	 * Renders 'control' part of visual row of controls.
	 *
	 * @param Nette\Forms\IControl $control
	 * @return string
	 */
	public function renderControl(Nette\Forms\IControl $control) {
		$body = $this->getWrapper('control container');

		if ($this->counter % 2) {
			$body->class($this->getValue('control .odd'), TRUE);
		}

		$description = $control->getOption('description');
		if ($description instanceof Html) {
			$description = ' ' . $description;
		} elseif (is_string($description)) {
			$description = ' ' . $this->getWrapper('control description')->setText($control->translate($description));
		} else {
			$description = '';
		}

		if ($control->isRequired()) {
			$description = $this->getValue('control requiredsuffix') . $description;
		}

		$el = $control->getControl();

		if ($description) {
			return $body->setHtml(Html::el('div class=input-group')->setHtml($el . $description . $this->renderErrors($control)));
		}

		if (!isset($el->attrs['data-nette-rules']) || $el->attrs['data-nette-rules'] === NULL) {
			$el->attrs['data-nette-rules'] = '[]';
		}

		return $body->setHtml($el . $this->renderErrors($control));
	}

}
