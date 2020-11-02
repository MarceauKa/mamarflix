<?php

namespace App\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Build extends Command
{
    protected static $defaultName = 'build';

    protected function configure()
    {
        $this->setDescription("Génère tous les fichiers");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Start building...</info>');

        // Ffprobe
        $command = $this->getApplication()->find('database:ffprobe');
        $status = $command->run(new ArrayInput([]), $output);

        if ($status === Command::FAILURE) {
            $output->writeln("<error>Error running database:ffprobe</error>");
            return $status;
        }

        // Tmdb
        $command = $this->getApplication()->find('database:tmdb');
        $status = $command->run(new ArrayInput([]), $output);

        if ($status === Command::FAILURE) {
            $output->writeln("<error>Error running database:tmdb</error>");
            return $status;
        }

        // Database
        $command = $this->getApplication()->find('database:build');
        $status = $command->run(new ArrayInput([]), $output);

        if ($status === Command::FAILURE) {
            $output->writeln("<error>Error running database:build</error>");
            return $status;
        }

        // Frontend
        $command = $this->getApplication()->find('frontend:build');
        $status = $command->run(new ArrayInput([]), $output);

        if ($status === Command::FAILURE) {
            $output->writeln("<error>Error running frontend:build</error>");
            return $status;
        }

        return Command::SUCCESS;
    }
}