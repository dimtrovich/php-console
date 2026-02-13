<?php

require __DIR__ . '/../vendor/autoload.php';

use Dimtrovich\Console\Application;

Application::create('Blitz PHP')
	->withLocale('fr')
	->withHeadTitle('BlitzPHP Command Line Interface, v0.13 | Server Time: ' . date('Y-m-d'))
	->run();
