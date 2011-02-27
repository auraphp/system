<?php
/**
 * 
 * This file is part of the Aura project for PHP.
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 */
namespace aura\framework;

/**
 * Pull changes the system as a whole.
 */
`git pull`;

/**
 * Update the library packages.
 */
 
// the package directory
$dir = __DIR__ . DIRECTORY_SEPARATOR . 'package';

// get the list of available repositories
$url = 'http://github.com/api/v2/json/repos/show/auraphp';
$context = stream_context_create(array(
    'http' => array(
        'method' => "GET",
    ),
));
$json = file_get_contents($url, FALSE, $context);
$data = json_decode($json);

// for each of the repositories ...
foreach ($data->repositories as $repo) {
    
    // only use 'aura.package' repositories as packages
    if (! preg_match('/aura\.[a-z0-9_]+/', $repo->name)) {
        continue;
    }
    
    // does the package exist locally ?
    $sub = $dir . DIRECTORY_SEPARATOR . $repo->name;
    if (is_dir($sub)) {
        
        // pull changes to existing package
        echo "Pulling package '{$repo->name}'." . PHP_EOL;
        `cd $sub; git pull`;
        
    } else {
        
        // clone new package for installation
        echo "Cloning package '{$repo->name}'." . PHP_EOL;
        `cd $dir; git clone {$repo->url}`;
        
    }
}

// done!
echo 'Done!' . PHP_EOL;
