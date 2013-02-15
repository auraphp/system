<?php
/**
 * 
 * This file is part of the Aura project for PHP.
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 */

if (! isset($_SERVER['argv'][1])) {
    echo "Please enter a date after which to get logs.";
    exit(1);
}

$date = $_SERVER['argv'][1];
echo "Getting logs since {$date}." . PHP_EOL . PHP_EOL;

/**
 * Show status of the system as a whole.
 */
echo '------------------------------' . PHP_EOL . PHP_EOL;
echo 'system' . PHP_EOL . PHP_EOL;
passthru("git status");
echo PHP_EOL;
passthru("git log --since=$date");

/**
 * Show logs from the library packages.
 */

// the package directory
$glob = __DIR__ . DIRECTORY_SEPARATOR . 'package' . DIRECTORY_SEPARATOR . '*';
$dirs = glob($glob, GLOB_ONLYDIR);

// for each of the repositories ...
foreach ($dirs as $dir) {
    echo PHP_EOL . '------------------------------' . PHP_EOL . PHP_EOL;
    echo basename($dir) . PHP_EOL . PHP_EOL;
    passthru("cd $dir; git status");
    echo PHP_EOL;
    passthru("cd $dir; git log --since={$date}");
}

// done!
echo PHP_EOL . '------------------------------' . PHP_EOL . PHP_EOL;
echo 'Done!' . PHP_EOL;
