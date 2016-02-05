<?php

namespace WebChemistry\Console;

use WebChemistry\Parameters\Provider;

class ParametersInsert implements IInsert {

	/** @var Provider */
	private $provider;

	/**
	 * @param Provider $provider
	 */
	public function __construct(Provider $provider) {
		$this->provider = $provider;
	}

	public function execute() {
		$this->provider->import();
	}

}
