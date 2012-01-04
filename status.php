<?php
/**
 * 
 * This file is part of the Aura project for PHP.
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 */

/**
 * Show status of the system as a whole.
 */
echo '------------------------------' . PHP_EOL . PHP_EOL;
echo 'system' . PHP_EOL . PHP_EOL;
passthru('git status');

/**
 * Show status of the library packages.
 */
 
// the package directory
$dir = __DIR__ . DIRECTORY_SEPARATOR . 'package';

// get the list of available repositories
$url = 'http://github.com/api/v2/json/repos/show/auraphp';
$context = stream_context_create([
    'http' => [
        'method' => "GET",
    ],
]);
$json = file_get_contents($url, FALSE, $context);
$data = json_decode($json);

// sort the repos
$repos = [];
foreach ($data->repositories as $repo) {
    $repos[$repo->name] = $repo;
}
ksort($repos);

// for each of the repositories ...
foreach ($repos as $repo) {
    
    // only use 'Aura.Package' repositories as packages
    if (! preg_match('/Aura\.[A-Z0-9_]+/', $repo->name)) {
        continue;
    }
    
    // does the package exist locally ?
    $sub = $dir . DIRECTORY_SEPARATOR . $repo->name;
    if (is_dir($sub)) {
        echo PHP_EOL . '------------------------------' . PHP_EOL . PHP_EOL;
        echo $repo->name . PHP_EOL . PHP_EOL;
        passthru("cd $sub; git status");
    }
}

// done!
echo PHP_EOL . '------------------------------' . PHP_EOL . PHP_EOL;
echo 'Done!' . PHP_EOL;
