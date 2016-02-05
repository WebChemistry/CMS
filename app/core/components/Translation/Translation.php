<?php

namespace WebChemistry\Translation;

use Kdyby\Translation\Translator;
use Nette;
use Symfony\Component\Translation\PluralizationRules;
use WebChemistry;

class Translation {

	/** @var string */
	public static $locale = 'cs';

	/** @var Translator */
	private $translator;

	private $timeAgo = [
		'now' => 'core.timeAgo.now',
		'minutes' => 'core.timeAgo.minutes',
		'hours' => 'core.timeAgo.hours',
		'days' => 'core.timeAgo.days',
		'months' => 'core.timeAgo.months',
		'years' => 'core.timeAgo.years'
	];

	/**
	 * @param Translator $translator
	 */
	public function __construct(Translator $translator = NULL) {
		$this->translator = $translator ? : new MockTranslator;

		$this->translateForms();
		$this->translateDateTime();
		$this->translateErrorPage();
		$this->translateStrings();
		$this->translateTimeAgo();
	}

	protected function translateForms() {
		Nette\Forms\Validator::$messages = [
			Nette\Forms\Controls\CsrfProtection::PROTECTION    => 'core.forms.protection',
			Nette\Application\UI\Form::EQUAL         => 'core.forms.equal',
			Nette\Application\UI\Form::NOT_EQUAL     => 'core.forms.notEqual',
			Nette\Application\UI\Form::FILLED        => 'core.forms.filled',
			Nette\Application\UI\Form::BLANK         => 'core.forms.blank',
			Nette\Application\UI\Form::MIN_LENGTH    => 'core.forms.minLength',
			Nette\Application\UI\Form::MAX_LENGTH    => 'core.forms.maxLength',
			Nette\Application\UI\Form::LENGTH        => 'core.forms.length',
			Nette\Application\UI\Form::EMAIL         => 'core.forms.email',
			Nette\Application\UI\Form::URL           => 'core.forms.url',
			Nette\Application\UI\Form::INTEGER       => 'core.forms.integer',
			Nette\Application\UI\Form::FLOAT         => 'core.forms.float',
			Nette\Application\UI\Form::MIN           => 'core.forms.min',
			Nette\Application\UI\Form::MAX           => 'core.forms.max',
			Nette\Application\UI\Form::RANGE         => 'core.forms.range',
			Nette\Application\UI\Form::MAX_FILE_SIZE => 'core.forms.maxFileSize',
			Nette\Application\UI\Form::MAX_POST_SIZE => 'core.forms.maxPostSize',
			Nette\Application\UI\Form::IMAGE         => 'core.forms.image',
			Nette\Application\UI\Form::MIME_TYPE     => 'core.forms.mimeType',
			Nette\Forms\Controls\SelectBox::VALID    => 'core.forms.select.valid',
			WebChemistry\Forms\Controls\Date::VALID  => 'core.forms.webchemistry.date',
			WebChemistry\Forms\Controls\Tags::VALID  => 'core.forms.webchemistry.tags',
		];
	}

	protected function translateDateTime() {
		$translate = [
			1 => 'core.months.jan', 'core.months.feb', 'core.months.mar', 'core.months.apr', 'core.months.may',
			'core.months.june', 'core.months.july', 'core.months.aug', 'core.months.sep', 'core.months.oct',
			'core.months.nov', 'core.months.dec'
		];
		foreach ($translate as $index => $value) {
			WebChemistry\Utils\DateTime::$translatedMonths[$index] = WebChemistry\Template\Filters::$months[$index] = $value;
		}
		WebChemistry\Utils\DateTime::$datetime = $this->translator->translate('core.date.datetime');
		WebChemistry\Utils\DateTime::$date = $this->translator->translate('core.date.date');
		WebChemistry\Utils\DateTime::$time = $this->translator->translate('core.date.time');

		// Days
		$translate = [
			'core.days.sun', 'core.days.mon', 'core.days.tue', 'core.days.wed', 'core.days.thu', 'core.days.fri',
			'core.days.sat'
		];
		foreach ($translate as $index => $value) {
			WebChemistry\Template\Filters::$days[$index] = $value;
		}
	}

	protected function translateErrorPage() {
		$errors = [
			400 => [
				'core.error.400.title',
				'core.error.400.msg'
			],
			403 => [
				'core.error.403.title',
				'core.error.403.msg'
			],
			404 => [
				'core.error.404.title',
				'core.error.404.msg'
			],
			500 => [
				'core.error.500.title',
				'core.error.500.msg'
			]
		];
		foreach ($errors as $i => $values) {
			foreach ($values as $index => $row) {
				$errors[$i][$index] = $this->translator->translate($row);
			}
		}
		WebChemistry\Error::$messages = $errors;
	}

	protected function translateStrings() {
		WebChemistry\Utils\Strings::$decPoint = $this->translator->translate('core.strings.decPoint');
		WebChemistry\Utils\Strings::$sepThousands = $this->translator->translate('core.strings.sepThousands');
	}

	protected function translateTimeAgo() {
		WebChemistry\Utils\DateTime::$timeAgoCallback = [$this, 'timeAgo'];
	}

	public function timeAgo($type, $count) {
		if ($this->translator instanceof MockTranslator) {
			$position = PluralizationRules::get($count, self::$locale);
			$msg = $this->timeAgo[$type];
			$explode = explode('|', $msg);

			if (isset($explode[$position])) {
				return str_replace('%count%', $count, $explode[$position]);
			} else {
				return $msg;
			}
		} else {
			return $this->translator->translate($this->timeAgo[$type], $count, ['count' => $count]);
		}
	}

}

class MockTranslator implements Nette\Localization\ITranslator {

	/**
	 * Translates the given string.
	 *
	 * @param  string   message
	 * @param  int      plural count
	 * @return string
	 */
	public function translate($message, $count = NULL) {
		return $message;
	}

}
