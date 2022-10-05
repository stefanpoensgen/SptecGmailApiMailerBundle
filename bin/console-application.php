<?php

declare(strict_types=1);

use App\Kernel;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Dotenv\Dotenv;

$symfonyRoot = dirname(__DIR__, 4);
require_once $symfonyRoot . '/vendor/autoload.php';

(new Dotenv())->bootEnv($symfonyRoot . '/.env');

$kernel = new Kernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG']);
return new Application($kernel);
