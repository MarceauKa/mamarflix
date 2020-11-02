<?php

namespace App\Commands;

use App\Movie;
use App\Utils\CsvWriter;
use App\Utils\VolumeReader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class DatabaseFfprobe extends Command
{
    protected static $defaultName = 'database:ffprobe';

    protected function configure()
    {
        $this->setDescription('Génére les infos ffprobe de chaque film dans le volume');
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
        $output->writeln(sprintf('<info>Reading %s</info>', $volume->path()));

        /** @var Movie[] $files */
        $files = [];
        $bar = new ProgressBar($output, $volume->count());
        $bar->start();

        foreach ($volume->files() as $movie) {
            $movie->getFfprobe();
            $files[] = $movie;
            $bar->advance();
        }

        $bar->finish();
        $output->writeln('');

        if ($input->getOption('export')) {
            $output->writeln('<info>Generating a CSV export</info>');

            $csv = new CsvWriter('ffprobe');
            $csv->headers(['Name', 'File', 'Size', 'Duration', 'Format', 'HDR', 'Audio', 'Subtitles']);

            foreach ($files as $movie) {
                $csv->addLine([
                    $movie->getFilename(),
                    ...array_map('values_dumper', array_values($movie->getFfprobe()->toArray())),
                ]);
            }

            $csv->write();
            $output->writeln('<info>Writed to ffprobe.csv</info>');
        }

        $output->writeln('<info>Done!</info>');

        return Command::SUCCESS;
    }
}