<?php

namespace App\Console;

use Nette\DI\Container;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use WebChemistry\Console\IInsert;

class InsertCommand extends Command {

	/**
	 * Configures the current command.
	 */
	protected function configure() {
		$this->setName('insert-values');
	}

	/**
	 * Executes the current command.
	 *
	 * This method is not abstract because you can use this class
	 * as a concrete class. In this case, instead of defining the
	 * execute() method, you set the code to execute by passing
	 * a Closure to the setCode() method.
	 *
	 * @param InputInterface  $input  An InputInterface instance
	 * @param OutputInterface $output An OutputInterface instance
	 *
	 * @return null|int null or 0 if everything went fine, or an error code
	 *
	 * @throws \LogicException When this abstract method is not implemented
	 *
	 * @see setCode()
	 */
	protected function execute(InputInterface $input, OutputInterface $output) {
		/** @var Container $container */
		$container = $this->getHelper('container');
		/** @var IInsert $em */
		$insert = $container->getByType('App\Console\Insert');

		try {
			$insert->apply();

			return 0;
		} catch (\Exception $e) {
			$output->writeln('<error>Error: ' . $e->getMessage() . '</error>');
			return 1;
		}
	}
}