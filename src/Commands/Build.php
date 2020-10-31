<?php

namespace App\Commands;

use App\Utils\Env;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Build extends Command
{
    protected static $defaultName = 'build';

    protected function configure()
    {
        $this->setDescription('Build frontend');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Generating frontend...');

        ob_start();
        require base_path('index.php');
        $content = ob_get_clean();
        $volume = Env::get('VOLUME_PATH');
        $writing = file_put_contents($volume . 'mamarflix.html', $content);

        if (!$writing) {
            $output->writeln(sprintf("Can't write to volume %s!", $volume));

            return Command::FAILURE;
        }

        $output->writeln(sprintf('File %s writed in %s!', 'mamarflix.html', $volume));

        return Command::SUCCESS;
    }
}