<?php
/**
 * 
 * This file is part of the Aura project for PHP.
 * 
 * Should be called from a bootstrap file that has defined $system, $loader,
 * $di, $config_mode, and a load_config() function.
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 * @package Aura.Framework
 * 
 */
namespace Aura\Framework;

// get the list of all packages in the system
$package_glob = $system . DIRECTORY_SEPARATOR
              . 'package' . DIRECTORY_SEPARATOR
              . '*';

$package_list = glob($package_glob, GLOB_ONLYDIR);

// for each package ...
foreach ($package_list as $package_path) {
    
    // ... add it to the autoloader ...
    $package_ns = str_replace('.', ' ', basename($package_path)) . '\\';
    $package_ns = ucwords($package_ns); // TEMP FIX FOR UPPERCASE
    $package_ns = str_replace(' ', '\\', $package_ns);
    $package_src = $package_path . DIRECTORY_SEPARATOR . 'src';
    $loader->addPrefix($package_ns, $package_src);
    
    // if we are in 'test' mode, add the tests dir too
    $package_test = $package_path . DIRECTORY_SEPARATOR . 'tests';
    $loader->addPrefix($package_ns, $package_test);
    
    // ... and run its default config file, if any.
    $package_config = $package_path . DIRECTORY_SEPARATOR
                    . 'config' . DIRECTORY_SEPARATOR
                    . 'default.php';
    if (file_exists($package_config)) {
        load_config($package_config, $system, $loader, $di);
    }
}

// where is the override config file for the config mode?
$config_file = $system . DIRECTORY_SEPARATOR
            . 'config' . DIRECTORY_SEPARATOR
            . "{$config_mode}.php";

if (file_exists($config_file)) {
    load_config($config_file, $system, $loader, $di);
}
