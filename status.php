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
 * Show status of the system as a whole.
 */
`git status`;

/**
 * Show status of the library packages.
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
    
    // only use 'Aura.Package' repositories as packages
    if (! preg_match('/Aura\.[A-Z0-9_]+/', $repo->name)) {
        continue;
    }
    
    // does the package exist locally ?
    $sub = $dir . DIRECTORY_SEPARATOR . $repo->name;
    if (is_dir($sub)) {
        echo $sub . PHP_EOL;
        passthru("cd $sub; git status");
        echo PHP_EOL . '# # #' . PHP_EOL . PHP_EOL;
    }
}

// done!
echo 'Done!' . PHP_EOL;
