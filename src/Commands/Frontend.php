<?php

namespace App\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;

class Frontend extends Command
{
    protected static $defaultName = 'frontend';

    protected function configure()
    {
        $this->setDescription('Lance le frontend');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $host = '127.0.0.1';
        $port = '8000';

        $output->writeln(sprintf('<info>Serving frontend at %s:%d</info>', $host, $port));

        $command = [
            (new PhpExecutableFinder)->find(false),
            '-S',
            sprintf('%s:%d', $host, $port)
        ];

        $process = new Process($command, null);

        $process->start(function ($type, $buffer) use ($output) {
            $output->write($buffer);
        });

        while ($process->isRunning()) {
            usleep(500 * 1000);
        }

        $status = $process->getExitCode();

        return $status;
    }
}