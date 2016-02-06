<?php

namespace App\UserModule\Components\Emails;

use WebChemistry, Nette;

class User extends WebChemistry\Emails\BaseControl {

	/**
	 * @return string
	 */
	public function getConfig() {
		return __DIR__ . '/email.neon';
	}

	/**
	 * @desc 'user.emails.register.desc'
	 *
	 * @variable $id 'user.emails.register.id'
	 * @variable $name 'user.emails.register.name'
	 * @variable $email 'user.emails.register.email'
	 *
	 * @return Nette\Application\UI\ITemplate
	 */
	public function createRegister($id, $name, $email) {
		$template = $this->createTemplate();
		$template->setFile(__DIR__ . '/templates/register.latte');

		$template->id = $id;
		$template->name = $name;
		$template->email = $email;

		return $template;
	}

	/**
	 * @return \Nette\Application\UI\ITemplate
	 */
	public function previewRegister() {
		return $this->createRegister(1, 'John Doe', 'john.doe@example.com');
	}

	/**
	 * @desc 'user.emails.forgot.desc'
	 * @variable $link 'user.emails.forgot.link'
	 *
	 * @return Nette\Application\UI\ITemplate
	 */
	public function createForgot($forgotHash, $userId) {
		$template = $this->createTemplate();

		$template->setFile(__DIR__ . '/templates/forgot.latte');
		$template->link = $this->link('Front:Forgot:newPassword', ['id' => $userId, 'hash' => $forgotHash]);

		return $template;
	}

	/**
	 * @return \Nette\Application\UI\ITemplate
	 */
	public function previewForgot() {
		return $this->createForgot(Nette\Utils\Random::generate(), 1);
	}

}
