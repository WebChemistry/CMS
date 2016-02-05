<?php

namespace App\Presenters\ConsoleModule;

use App\Presenters\BasePresenter;
use stekycz\Cronner\Cronner;
use stekycz\Cronner\Tasks\Task;

/**
 * Url: console/cronner
 */
class CronnerPresenter extends BasePresenter {

	/** @var Cronner */
	private $cronner;

    /**
     * @param Cronner $cronner
     */
    public function __construct(Cronner $cronner = NULL) {
        $this->cronner = $cronner;
    }

    public function actionDefault() {
		if (!$this->cronner) {
			echo 'Cronner not found.';
			$this->terminate();
		}
		$this->cronner->onTaskError[] = function ($cronner, \Exception $exception, Task $task) {
			echo $task->getName() . ': Error (' . $exception->getMessage() . ')<br>';
		};

		$this->cronner->onTaskFinished[] = function ($cronner, Task $task) {
			echo $task->getName() . ':Success <br>';
		};

		$this->cronner->run();
		echo 'Completed';
		$this->terminate();
	}

}
