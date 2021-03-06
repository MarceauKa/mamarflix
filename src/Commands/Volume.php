<?php

namespace App\Commands;

use App\Utils\Env;
use App\Utils\VolumeReader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Volume extends Command
{
    protected static $defaultName = 'volume';

    protected function configure()
    {
        $this->setDescription('Liste les films du volume');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $path = Env::get('VOLUME_PATH');
        $output->writeln('Starting to read ' . $path);

        $reader = new VolumeReader();
        $output->writeln($reader->count() . ' movies found.');

        $table = new Table($output);
        $table->setHeaders(['#', 'File']);

        foreach ($reader->files() as $index => $file) {
            $table->addRow([
                $index + 1,
                $file->getName(),
            ]);
        }

        $table->render();

        return Command::SUCCESS;
    }
}