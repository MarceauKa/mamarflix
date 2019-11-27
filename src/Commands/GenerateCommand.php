<?php

namespace App\Commands;

use App\Crawler;
use App\Env;
use App\Movie;
use App\Reader;
use App\Writer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateCommand extends Command
{
    protected static $defaultName = 'app:generate';

    protected function configure()
    {
        $this->setDescription('Do the work!')
            ->addOption('limit', 'l', InputOption::VALUE_OPTIONAL, 'Limit files', null)
            ->addOption('offset', 'o', InputOption::VALUE_OPTIONAL, 'Offset files', null)
            ->addOption('file', 'f', InputOption::VALUE_OPTIONAL, 'Name of the generated file', Env::get('EXPORT_NAME'));
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $path = Env::get('VOLUME_PATH');
        $output->writeln('Starting to read ' . $path);

        $reader = new Reader($path, $input->getOption('limit'), $input->getOption('offset'));

        $files = [];
        $bar = new ProgressBar($output, $reader->count());
        $bar->start();

        foreach ($reader->files as $file) {
            $files[] = new Movie($file);
            $bar->advance();
        }

        $bar->finish();
        $output->writeln('');

        $table = new Table($output);

        $headers = [
            'Name',
            'Year',
            'Duration',
            'Audio',
            'Subtitles',
            'Resolution',
            'HDR',
            'Size',
        ];

        $table->setHeaders($headers);

        foreach ($files as $file) {
            /** @var Movie $file */
            $table->addRow([
                $file->name,
                $file->year,
                $file->ffmpeg->duration,
                $file->ffmpeg->audio,
                $file->ffmpeg->subs,
                $file->ffmpeg->resolution,
                $file->ffmpeg->hdr ? 'Yes' : 'No',
                $file->ffmpeg->size,
            ]);
        }

        $table->render();

        $reader->files = $files;

        $writer = new Writer($reader);
        $writer->write($input->getOption('file'));

        $output->writeln('Generated');
    }
}