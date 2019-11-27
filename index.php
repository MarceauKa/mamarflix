<?php

use App\Commands\GenerateCommand;
use App\Commands\ListCommand;
use Symfony\Component\Console\Application;

require 'vendor/autoload.php';

$application = new Application();

$application->add(new ListCommand());
$application->add(new GenerateCommand());

$application->run();
