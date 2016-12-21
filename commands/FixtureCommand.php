<?php

namespace Wame\Core\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Wame\DoctrineFixtures\Contract\Alice\AliceLoaderInterface;

class FixtureCommand extends Command
{
    /** @var AliceLoaderInterface @inject */
    public $aliceLoader;


    /** {@inheritDoc} */
    protected function configure()
    {
        $this
            ->setName('orm:fixtures:load')
            ->setDescription('Load data fixtures to your database.')
            ->addOption('fixtures', null, InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 'The directory to load data fixtures from.');
//            ->addOption('append', null, InputOption::VALUE_NONE, 'Append the data fixtures instead of deleting all data from the database first.');
    }

    /** {@inheritdoc} */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {

            $objects = $this->aliceLoader->load(__DIR__);
            $output->writeln(sprintf('Successfully created "<info>%s</info>" fixtures:', count($objects)));
            foreach($objects as $object) {
                $output->writeln('  - ' . get_class($object));
            }
            return 0; // zero return code means everything is ok
        } catch (\Exception $exc) {
            $output->writeln("<error>{$exc->getMessage()}</error>");
            return 1; // non-zero return code means error
        }
    }

    /**
     * Ask confirmation
     *
     * @param InputInterface  $input        input
     * @param OutputInterface $output       output
     * @param string          $question     question
     * @param bool            $default      default
     *
     * @return bool
     */
    private function askConfirmation(InputInterface $input, OutputInterface $output, $question, $default)
    {
        if (!class_exists('Symfony\Component\Console\Question\ConfirmationQuestion')) {
            $dialog = $this->getHelperSet()->get('dialog');
            return $dialog->askConfirmation($output, $question, $default);
        }
        $questionHelper = $this->getHelperSet()->get('question');
        $question = new ConfirmationQuestion($question, $default);
        return $questionHelper->ask($input, $output, $question);
    }

}