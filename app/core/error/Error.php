<?php

namespace WebChemistry;

use Nette\Application\BadRequestException;
use Nette\Object;
use Tracy\Debugger;

class Error extends Object {

	/** @var int|mixed */
	public $code;

	/** @var string */
	public $title;

	/** @var string */
	public $content;

	/** @var array */
	public static $messages = [
		400 => [
			'Špatný požadavek',
			'Server nerozumí požadavku, musíte požadavek opravit a poslat znovu.'
		],
		403 => [
			'Nedostatečné oprávnění',
			'Nemáte potřebná práva k prohližení této stránky nebo vykonaní akce.'
		],
		404 => [
			'Stránka nebyla nalezena',
			'Stránka byla odstraněna nebo neexistuje.'
		],
		500 => [
			'Chyba serveru',
			'Na serveru došlo k neočekávané chybě. Prosím zkuste to znovu později.'
		]
	];

	public function __construct(\Exception $e, $protocol, $code) {
		$this->code = self::getCode($e);

		$message = $this->getMessage();

		$this->title = $message[0];
		$this->content = $message[1];

		if ($protocol) {
			$code = $this->code === 500 ? $code : $this->code;
			header("$protocol $code", TRUE, $code);
		}
	}

	/**
	 * @param \Exception $exception
	 */
	public static function handleErrorPage(\Exception $exception) {
		require Debugger::$errorTemplate;
	}

	/**
	 * @param \Exception $e
	 * @return int
	 */
	public static function getCode(\Exception $e) {
		if ($e instanceof BadRequestException) {
			return in_array($e->getCode(), array(403, 404, 405, 410, 500)) ? $e->getCode() : 404;
		}

		return 500;
	}

	/**
	 * @return string
	 */
	private function getMessage() {
		$messages = self::$messages;
		if (isset($messages[$this->code])) {
			return $messages[$this->code];
		} else {
			return $messages[500];
		}
	}

}
