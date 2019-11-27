<?php

namespace App\Commands;

use App\Env;
use App\Reader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ListCommand extends Command
{
    protected static $defaultName = 'app:list';

    protected function configure()
    {
        $this->setDescription('Just list movies in the volume');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $path = Env::get('VOLUME_PATH');
        $output->writeln('Starting to read ' . $path);

        $reader = new Reader($path);
        $output->writeln($reader->count() . ' movies found.');

        $table = new Table($output);
        $table->setHeaders(['#', 'File']);

        foreach ($reader->files as $index => $file) {
            $table->addRow([
                $index + 1,
                basename($file)
            ]);
        }

        $table->render();
    }
}