<?php

namespace Wame\CoreModule\Commands;

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Kdyby\Doctrine\EntityManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FixtureCommand extends Command
{
    /** @var EntityManager @inject */
    public $em;

    
    protected function configure()
    {
        $this
            ->setName('orm:fixtures:load')
            ->setDescription('Load data fixtures to your database.');
        //->addOption...
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $loader = new Loader();
            $loader->loadFromDirectory(__DIR__);
            $fixtures = $loader->getFixtures();

            $purger = new ORMPurger($this->em);

            $executor = new ORMExecutor($this->em, $purger);
//            $executor->setLogger(function ($message) use ($output) {
//                $output->writeln(sprintf('  <comment>></comment> <info>%s</info>', $message));
//            });
            $executor->execute($fixtures, true);
            return 0; // zero return code means everything is ok
        } catch (\Exception $exc) {
            $output->writeln("<error>{$exc->getMessage()}</error>");
            return 1; // non-zero return code means error
        }
    }
}