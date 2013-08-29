<?php
/**
 * 
 * This file is part of the Aura project for PHP.
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 */


if (empty($argv[1])) {
    echo 'Specify a branch to check out.' . PHP_EOL;
    echo 'Usage: php checkout.php branch-name' . PHP_EOL;
    exit(0);
}

$branch = $argv[1];

// change system branch 
echo '------------------------------' . PHP_EOL . PHP_EOL;
echo 'system' . PHP_EOL . PHP_EOL;
passthru("git checkout $branch");

// the package directory
$dir = __DIR__ . DIRECTORY_SEPARATOR . 'package';

// get the list of available repositories
$url = 'https://api.github.com/orgs/auraphp/repos';
$context = stream_context_create([
    'http' => [
        'method' => "GET",
        'header' => "Accept: application/json"
    ],
]);
$json = file_get_contents($url, FALSE, $context);
$data = json_decode($json);

// sort the repos
$repos = [];
foreach ($data as $repo) {
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
        passthru("cd $sub; git checkout $branch");
    }
}

echo 'Done!' . PHP_EOL;

