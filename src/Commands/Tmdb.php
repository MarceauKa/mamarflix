<?php

namespace App\Commands;

use App\CsvWriter;
use App\Movie;
use App\VolumeReader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Tmdb extends Command
{
    protected static $defaultName = 'tmdb';

    protected function configure()
    {
        $this->setDescription('Génére les infos TMDb de chaque film dans le volume');
        $this->addOption(
            'export',
            'e',
            InputOption::VALUE_NONE,
            "Permet de générer un fichier d'export .csv additionnel",
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $volume = new VolumeReader;
        $output->writeln('Starting to read ' . $volume->path());

        /** @var Movie[] $files */
        $files = [];
        $bar = new ProgressBar($output, $volume->count());
        $bar->start();

        foreach ($volume->files() as $movie) {
            $movie->getTmdb();
            $files[] = $movie;
            $bar->advance();
        }

        $bar->finish();
        $output->writeln('');

        if ($input->getOption('export')) {
            $output->writeln('Generating a CSV export');

            $csv = new CsvWriter('tmdb');
            $csv->headers(['Name', 'File']);

            foreach ($files as $movie) {
                $csv->addLine([
                    $movie->getName(),
                    $movie->getFilename(),
                ]);
            }

            $csv->write();
            $output->writeln('Writed to tmdb.csv');
        }

        $output->writeln('Done!');
    }
}