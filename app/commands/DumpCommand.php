<?php

namespace App\Console;

use Doctrine\ORM\Tools\SchemaTool;
use Kdyby\Doctrine\EntityManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DumpCommand extends Command {

	/**
	 * Configures the current command.
	 */
	protected function configure() {
		$this->setName('dump:db')
			 ->setDescription('Dump db to file.')
			 ->addArgument('type', InputArgument::OPTIONAL, 'Type of dump [create, update].', 'update');
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
		/** @var EntityManager $em */
		$em = $this->getHelper('container')->getByType('Kdyby\Doctrine\EntityManager');

		try {
			$schemaTool = new SchemaTool($em);

			@mkdir(__DIR__ . '/others');

			if ($input->getArgument('type') === 'create') {
				$array = $schemaTool->getCreateSchemaSql($em->getMetadataFactory()->getAllMetadata());
				file_put_contents(__DIR__ . '/others/dump.sql', implode(";\n", $array));
			} else if ($input->getArgument('type') === 'both') {
				$array = $schemaTool->getCreateSchemaSql($em->getMetadataFactory()->getAllMetadata());

				foreach ($array as $index => $row) {
					if (substr($row, 0, 12) === 'CREATE TABLE') {
						$array[$index] = 'CREATE TABLE IF NOT EXISTS' . substr($row, 12);
					}
				}

				$arrayTwo = $schemaTool->getUpdateSchemaSql($em->getMetadataFactory()->getAllMetadata(), TRUE);

				file_put_contents(__DIR__ . '/others/dump.sql', implode(";\n", $array) . ";\n" . implode(";\n", $arrayTwo));
			} else {
				$output->writeln('<error>Type ' . $input->getArgument('type') . ' not found.</error>');
				return 1;
			}

			return 0;
		} catch (\Exception $e) {
			$output->writeln('<error>' . $e->getMessage() . '</error>');
			return 1;
		}
	}

}
