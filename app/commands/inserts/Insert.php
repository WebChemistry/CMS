<?php

namespace App\Console;

use WebChemistry\Console\IInsert;

class Insert {

	/** @var IInsert[] */
	private $inserts = [];

	public function add(IInsert $insert) {
		$this->inserts[] = $insert;
	}

	public function apply() {
		foreach ($this->inserts as $insert) {
			$insert->execute();
		}
	}

}
