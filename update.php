<?php
/**
 * 
 * This file is part of the Aura project for PHP.
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 */
namespace Aura\Framework;

/**
 * Need 'allow_url_fopen' to be on.
 */
if (! ini_get('allow_url_fopen')) {
    echo "Cannot update when 'allow_url_fopen' is turned off." . PHP_EOL;
    exit(1);
}

/**
 * Pull changes the system as a whole.
 */
passthru('git pull');

/**
 * Update the library packages.
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

// for each of the repositories ...
foreach ($data->repositories as $repo) {
    
    // only use 'Aura.Package' repositories as packages
    if (! preg_match('/Aura\.[A-Z0-9_]+/', $repo->name)) {
        continue;
    }
    
    // does the package exist locally ?
    $sub = $dir . DIRECTORY_SEPARATOR . $repo->name;
    if (is_dir($sub)) {
        
        // pull changes to existing package
        echo "Pulling package '{$repo->name}'." . PHP_EOL;
        passthru("cd $sub; git pull --all");
        
    } else {
        
        // clone new package for installation
        echo "Cloning package '{$repo->name}'." . PHP_EOL;
        passthru("cd $dir; git clone {$repo->url}");
        
    }
}

// done!
echo 'Done!' . PHP_EOL;
