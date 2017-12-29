<?php

namespace Wame\Core\Commands;

use Nette\Utils\Finder;
use Wame\Utils\Dir;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


final class MigrationsCollectCommand extends Command
{
    /**
     * @return void
     */
    protected function configure()
    {
        $this->setName('migrations:collect');
        $this->setDescription('Collect migrations from all modules --except-dummy except dummy-data');
        $this->addOption('except-dummy', 'ed',InputOption::VALUE_NONE, 'Except dummy-data');
    }


    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($input->getOption('except-dummy') == 1) $output->writeln('<comment>Copies everything except dummy-data</comment>');

        Dir::createDir(BASE_PATH . '/migrations/basic-data');
        Dir::createDir(BASE_PATH . '/migrations/dummy-data');
        Dir::createDir(BASE_PATH . '/migrations/structures');

        foreach (Finder::findDirectories('migrations/basic-data', 'migrations/dummy-data', 'migrations/structures')->from(VENDOR_PATH . DIRECTORY_SEPARATOR . PACKAGIST_NAME) as $folder) {
            $name = $folder->getBasename();
            $path = $folder->getPathname();

            if ($input->getOption('except-dummy') == 1 && $name == 'dummy-data') continue;

            $output->writeln('> COPY <info>' . $path . '</info>');

            Dir::copyDir($path, BASE_PATH . DIRECTORY_SEPARATOR . 'migrations' . DIRECTORY_SEPARATOR . $name);
        }
    }

}
