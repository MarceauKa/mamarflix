<?php

namespace App\Commands;

use App\Movie;
use App\Utils\CsvWriter;
use App\Utils\TmdbDb;
use App\Utils\VolumeReader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

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
            try {
                $movie->getTmdb();
            } catch (\InvalidArgumentException $e) {
                $helper = $this->getHelper('question');
                $text = sprintf('Please enter TMDb id for the move %s?', $movie->getName());
                $question = new Question($text, false);
                $output->writeln('');
                $answer = $helper->ask($input, $output, $question);

                if ($answer) {
                    TmdbDb::instance()->setId($movie->getSlug(), (int)$answer);
                    $movie->getTmdb(true);
                } else {
                    return Command::FAILURE;
                }
            }

            $files[] = $movie;
            $bar->advance();
        }

        $bar->finish();
        $output->writeln('');

        if ($input->getOption('export')) {
            $output->writeln('Generating a CSV export');

            $csv = new CsvWriter('tmdb');
            $csv->headers(['Name', 'File', 'Title', 'Original title', 'Release date', 'Resume', 'Genres', 'Note', 'Poster']);

            foreach ($files as $movie) {
                $tmdb = $movie->getTmdb();

                $csv->addLine([
                    $movie->getName(),
                    $movie->getFilename(),
                    $tmdb->getTitle(),
                    $tmdb->getOriginalTitle(),
                    $tmdb->getReleaseDate(),
                    $tmdb->getResume(),
                    implode(', ', $tmdb->getGenres()),
                    $tmdb->getVoteAverage(),
                    $tmdb->getPosterUrl(),
                ]);
            }

            $csv->write();
            $output->writeln('Writed to tmdb.csv');
        }

        $output->writeln('Done!');

        return 0;
    }
}