<?php

namespace App\Commands;

use App\Movie;
use App\Utils\CsvWriter;
use App\Utils\TmdbDb;
use App\Utils\VolumeReader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class DatabaseTmdb extends Command
{
    protected static $defaultName = 'database:tmdb';

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
        $output->writeln(sprintf('<info>Reading %s</info>', $volume->path()));

        /** @var Movie[] $files */
        $files = [];
        $bar = new ProgressBar($output, $volume->count());
        $bar->start();

        foreach ($volume->files() as $movie) {
            $tmdb = $movie->getTmdb();

            // Movie has choices
            if (count($tmdb->choices) > 0) {
                $output->writeln('');

                $table = new Table($output);
                $table->setHeaders(['ID', 'Title', 'Original title', 'Release']);

                $choices = [];

                foreach ($tmdb->choices as $choice) {
                    $choices[] = [
                        $choice['id'],
                        $choice['title'],
                        $choice['original_title'],
                        $choice['release_date']
                    ];
                }

                $table->setRows($choices);
                $table->render();
            }

            // Movie has no ID
            if (count($tmdb->infos) === 0) {
                $default = count($tmdb->choices) > 0 ? $tmdb->choices[0]['id'] : false;

                if (count($tmdb->choices) > 1 || empty($default)) {
                    $helper = $this->getHelper('question');
                    $text = sprintf('<question>Please enter TMDb id for the movie %s (default: %s)</question>', $movie->getFilename(), $default ?? '?');
                    $question = new Question($text, $default);

                    $output->writeln('');
                    $answer = $helper->ask($input, $output, $question);
                } else {
                    $answer = $tmdb->choices[0]['id'];
                    $output->writeln(sprintf('<comment>Movie %s set to %d</comment>', $movie->getFilename(), $answer));
                }

                if (empty($answer)) {
                    return Command::FAILURE;
                }

                TmdbDb::instance()->setId($movie->getSlug(), (int)$answer);
                $movie->getTmdb(true);
                $output->writeln('');
            }
            // Movie is existing
            else {
                $output->writeln(sprintf('<comment>Existing movie %s (%d)</comment>', $movie->getFilename(), $tmdb->getId()));
            }

            $files[] = $movie;
            $bar->advance();
        }

        $bar->finish();
        $output->writeln('');

        if ($input->getOption('export')) {
            $output->writeln('Generating a CSV export');

            $csv = new CsvWriter('tmdb');
            $csv->headers(['File', 'Title', 'Original title', 'Release date', 'Resume', 'Genres', 'Note', 'Poster']);

            foreach ($files as $movie) {
                $csv->addLine([
                    $movie->getFilename(),
                    ...array_map('values_dumper', array_values($movie->getTmdb()->toArray()))
                ]);
            }

            $csv->write();
            $output->writeln('<info>Writed to tmdb.csv</info>');
        }

        $output->writeln('<info>Done!</info>');

        return Command::SUCCESS;
    }
}