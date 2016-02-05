<?php

namespace WebChemistry;

use Nette;
use Nette\Forms\Rendering\DefaultFormRenderer;
use Nette\Forms\Controls;

class FrontRenderer extends DefaultFormRenderer {

	/**
	 * Provides complete form rendering.
	 *
	 * @param Nette\Forms\Form $form
	 * @param  string $mode 'begin', 'errors', 'ownerrors', 'body', 'end' or empty to render all
	 * @return string
	 */
	public function render(Nette\Forms\Form $form, $mode = NULL) {
		// setup form rendering
		$this->wrappers['controls']['container'] = NULL;
		$this->wrappers['pair']['container'] = 'div class=form-group';
		$this->wrappers['pair']['.error'] = 'has-error';
		$this->wrappers['control']['container'] = 'div class=col-sm-9';
		$this->wrappers['label']['container'] = 'div class="col-sm-3 control-label"';
		$this->wrappers['control']['description'] = 'span class=help-block';
		$this->wrappers['control']['errorcontainer'] = 'span class=help-block';
		// make form and controls compatible with Twitter Bootstrap
		
		foreach ($form->getControls() as $control) {
			if ($control instanceof Controls\Button) {
				$control->getControlPrototype()->addClass('btn btn-default');
			} elseif ($control instanceof Controls\TextBase || $control instanceof Controls\SelectBox || $control instanceof Controls\MultiSelectBox) {
				$control->getControlPrototype()->addClass('form-control');
			} elseif ($control instanceof Controls\Checkbox || $control instanceof Controls\CheckboxList || $control instanceof Controls\RadioList) {
				$control->getSeparatorPrototype()->setName('div')->addClass($control->getControlPrototype()->type);
			}
		}

		return parent::render($form, $mode);
	}

}
