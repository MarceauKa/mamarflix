<?php

namespace App\Commands;

use App\Utils\TmdbDb;
use App\Utils\VolumeReader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Debug extends Command
{
    protected static $defaultName = 'debug';

    protected function configure()
    {
        $this->setDescription("Dump les infos d'un film");
        $this->addArgument(
            'slug',
            InputArgument::REQUIRED,
            "Slug du film",
        );
        $this->addOption(
            'tmdb',
            null,
            InputOption::VALUE_REQUIRED,
            "Forcer un ID de TMDb pour ce film",
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $volume = new VolumeReader;
        $output->writeln('Starting to read ' . $volume->path());
        $movie = null;

        foreach ($volume->files() as $file) {
            if ($file->getSlug() === $input->getArgument('slug')) {
                $movie = $file;
                break;
            }
        }

        if (empty($movie)) {
            $output->writeln("Movie {$input->getArgument('slug')} not found");
            return Command::FAILURE;
        }

        if ($input->getOption('tmdb')) {
            $id = $input->getOption('tmdb');
            TmdbDb::instance()->setId($movie->getSlug(), $id);
            $output->writeln("Forced ID $id for {$movie->getName()}");
        }

        $data = array_merge(
            ['file' => $movie->getFilename()],
            $movie->getFfprobe()->toArray(),
            $movie->getTmdb()->toArray()
        );

        foreach ($data as $key => $value) {
            $output->writeln(sprintf('<info>%s</info>', ucfirst($key)));
            $output->writeln(values_dumper($value));
        }

        return Command::SUCCESS;
    }
}