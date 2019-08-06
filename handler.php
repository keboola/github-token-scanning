<?php
// phpcs:ignoreFile
declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use Keboola\GithubTokenScanning\Router;

lambda(new Router());
