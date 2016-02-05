<?php

namespace WebChemistry\Components;

use Nette;
use WebChemistry\Administration\Configuration;
use WebChemistry\Administration\ILabel;
use WebChemistry\Exceptions\ConfigurationException;

class Menu extends Nette\Application\UI\Control {

	/** @var Configuration */
	private $configuration;

	public function __construct(Configuration $configuration) {
		$this->configuration = $configuration;
	}

	/**
	 * @param mixed $label
	 * @return bool
	 * @throws ConfigurationException
	 */
	public function checkLabel($label) {
		if (is_object($label) && !$label instanceof ILabel) {
			throw new ConfigurationException('Label must implements WebChemistry\Administration\ILabel');
		}
		if (is_object($label)) {
			$label->getLabel();
		}

		return $label;
	}

	public function isMenuCurrent($url, array $children = []) {
		if (!$children) {
			return $this->presenter->isLinkCurrent($url);
		} else {
			foreach ($children as $child) {
				if (isset($child['url']) && $this->presenter->isLinkCurrent($child['url'])) {
					return TRUE;
				}
			}
		}

		return FALSE;
	}

	/**
	 * @param array $row
	 * @throws MenuException
	 */
	public function check(array $row) {
		if (!isset($row['parent'])) {
			throw new MenuException('Menu must have item "parent".');
		}
		if (!is_array($row['parent'])) {
			throw new MenuException('Parent must be an array.');
		}
		if (!isset($row['parent']['name'])) {
			throw new MenuException('Parent must have item "name".');
		}
		if (isset($row['children'])) {
			if (!is_array($row['children'])) {
				throw new MenuException('Children must be an array.');
			}
		}
	}

	/**
	 * @param array $row
	 * @throws MenuException
	 */
	public function checkChildren(array $row) {
		if (!isset($row['url'])) {
			throw new MenuException('Children must have item "url".');
		}
	}

	public function isAllowed(array $parent) {
		if (!isset($parent['need'])) {
			return TRUE;
		}
		$status = NULL;
		$resource = $parent['need'];
		// And
		foreach(explode('&', $resource) as $and) {
			if ($status === FALSE) {
				return FALSE;
			}
			$status = FALSE;

			// Or
			foreach (explode('|', $and) as $or) {
				$explode = explode(':', $or);
				$res = $explode[0];

				if (isset($explode[1])) {
					$privilege = $explode[1];
				} else {
					$privilege = NULL;
				}
				if ($this->getPresenter()->getUser()->isAllowed($res, $privilege) === TRUE) {
					$status = TRUE;
					break;
				}
			}
		}

		return $status;
	}

	public function link($destination, $args = []) {
		return $this->presenter->link($destination, $args);
	}

	public function render(array $menu = []) {
		$this->template->setFile(__DIR__ . '/templates/template.latte');
		$this->template->menu = $menu ? : $this->configuration->getMenu();
		$this->template->render();
	}

}
