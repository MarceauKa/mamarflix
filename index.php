<?php

use Symfony\Component\Console\Application;

require 'vendor/autoload.php';

$application = new Application();

$application->add(new \App\Commands\Overview);
$application->add(new \App\Commands\Debug);
$application->add(new \App\Commands\Ffprobe);

$application->run();
