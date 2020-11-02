<?php

namespace App\Commands;

use App\Movie;
use App\Utils\CsvWriter;
use App\Utils\JsonWriter;
use App\Utils\VolumeReader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Database extends Command
{
    protected static $defaultName = 'database';

    protected function configure()
    {
        $this->setDescription('Génére un fichier de base de données');
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
            $movie->getFfprobe();
            $movie->getTmdb();
            $files[] = $movie;
            $bar->advance();
        }

        $bar->finish();
        $output->writeln('');

        $json = new JsonWriter('database');

        foreach ($files as $movie) {
            $ffprobe = $movie->getFfprobe();
            $tmdb = $movie->getTmdb();

            $json->push(array_merge(
                [
                    'file' => $movie->getFilename(),
                    'path' => $movie->getPath(),
                ],
                $tmdb->toArray(),
                $ffprobe->toArray()
            ));
        }

        $json->write();
        $output->writeln('Database writed!');

        if ($input->getOption('export')) {
            $csv = new CsvWriter('database');
            $csv->headers([
                'File',
                'Title',
                'Original title',
                'Release date',
                'Resume',
                'Genres',
                'Note',
                'Poster',
                'Casting',
                'Size',
                'Duration',
                'Format',
                'HDR',
                'Audio',
                'Subtitles',
            ]);

            foreach ($files as $movie) {
                $ffprobe = $movie->getFfprobe();
                $tmdb = $movie->getTmdb();

                $csv->addLine([
                    $movie->getFilename(),
                    ...array_map('values_dumper', array_values($tmdb->toArray())),
                    ...array_map('values_dumper', array_values($ffprobe->toArray())),
                ]);
            }

            $csv->write();
            $output->writeln('Writed to database.csv');
        }

        $output->writeln('Done!');

        return 0;
    }
}