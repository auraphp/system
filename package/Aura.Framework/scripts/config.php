<?php
/**
 * 
 * This file is part of the Aura project for PHP.
 * 
 * Should be called from a bootstrap file that has defined $system, $loader,
 * $di, $config_mode, and a load() function.
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 * @package Aura.Framework
 * 
 */
namespace Aura\Framework;

/**
 * Look for cached configs for the packages.
 */
$cache_file = $system . DIRECTORY_SEPARATOR
            . 'tmp' . DIRECTORY_SEPARATOR
            . 'cache' . DIRECTORY_SEPARATOR
            . 'config' . DIRECTORY_SEPARATOR
            . "{$config_mode}.php";

if (file_exists($cache_file)) {
    
    /**
     * Use the cached package configs.
     */
    load($cache_file, $system, $loader, $di);
    
} else {
    
    /**
     * Load config files for each package in the system.
     */
    $package_glob = $system . DIRECTORY_SEPARATOR
                  . 'package' . DIRECTORY_SEPARATOR
                  . '*';

    $package_list = glob($package_glob, GLOB_ONLYDIR);

    foreach ($package_list as $package_path) {
    
        // run its default config file, if any
        $package_config = $package_path . DIRECTORY_SEPARATOR
                        . 'config' . DIRECTORY_SEPARATOR
                        . 'default.php';
        if (file_exists($package_config)) {
            load($package_config, $system, $loader, $di);
        }
    
        // load its config-mode-specific file, if any
        if ($config_mode != 'default') {
            $package_config = $package_path . DIRECTORY_SEPARATOR
                            . 'config' . DIRECTORY_SEPARATOR
                            . "{$config_mode}.php";
            if (file_exists($package_config)) {
                load($package_config, $system, $loader, $di);
            }
        }
    }
}

/**
 * Finally, load the override config file for the config mode
 */
$config_file = $system . DIRECTORY_SEPARATOR
            . 'config' . DIRECTORY_SEPARATOR
            . "{$config_mode}.php";

if (file_exists($config_file)) {
    load($config_file, $system, $loader, $di);
}
