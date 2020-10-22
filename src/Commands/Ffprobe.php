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

class Ffprobe extends Command
{
    protected static $defaultName = 'ffprobe';

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
        $output->writeln('Starting to read ' . $volume->path());

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
            $output->writeln('Generating a CSV export');

            $csv = new CsvWriter('ffprobe');
            $csv->headers(['Name', 'File', 'Size', 'Duration', 'Format', 'HDR', 'Audio', 'Subtitles']);

            foreach ($files as $movie) {
                $csv->addLine([
                    $movie->getName(),
                    $movie->getFilename(),
                    $movie->getFfprobe()->getSize(),
                    $movie->getFfprobe()->getDuration(),
                    $movie->getFfprobe()->getVideoFormat(),
                    $movie->getFfprobe()->getVideoHasHdr() ? 'Oui' : 'Non',
                    implode(', ', $movie->getFfprobe()->getAudioTracks()),
                    implode(', ', $movie->getFfprobe()->getSubtitleTracks()),
                ]);
            }

            $csv->write();
            $output->writeln('Writed to ffprobe.csv');
        }

        $output->writeln('Done!');

        return 0;
    }
}