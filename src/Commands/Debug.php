<?php

namespace App\Commands;

use App\Utils\VolumeReader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
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
            return;
        }

        $table = new Table($output);

        $table->addRow(['Name', $movie->getName()]);
        $table->addRow(['File', $movie->getFfprobe()->getSize()]);
        $table->addRow(['Duration', $movie->getFfprobe()->getDuration()]);
        $table->addRow(['Format', $movie->getFfprobe()->getVideoFormat()]);
        $table->addRow(['HDR', $movie->getFfprobe()->getVideoHasHdr() ? 'Oui' : 'Non']);
        $table->addRow(['Audio', implode(', ', $movie->getFfprobe()->getAudioTracks())]);
        $table->addRow(['Subtitles', implode(', ', $movie->getFfprobe()->getSubtitleTracks())]);
        $table->addRow(['Title', $movie->getTmdb()->getTitle()]);
        $table->addRow(['Original title', $movie->getTmdb()->getOriginalTitle()]);
        $table->addRow(['Release date', $movie->getTmdb()->getReleaseDate()]);
        $table->addRow(['Genres', implode(', ', $movie->getTmdb()->getGenres())]);
        $table->addRow(['Note', $movie->getTmdb()->getVoteAverage()]);
        $table->addRow(['Resume', $movie->getTmdb()->getResume()]);
        $table->addRow(['Poster', $movie->getTmdb()->getPosterUrl()]);

        $table->render();

        return 0;
    }
}