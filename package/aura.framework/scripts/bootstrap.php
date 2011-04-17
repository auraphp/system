<?php
/**
 * 
 * This file is part of the Aura project for PHP.
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 * @package aura.framework
 * 
 */
namespace aura\framework;
use aura\autoload\Loader;
use aura\di\Manager;
use aura\di\Forge;
use aura\di\Config;

/**
 * 
 * Loads config files in a restricted scope.
 * 
 * @return void
 * 
 */
function load_config($file, $system, $loader, $di) {
    require $file;
}

/**
 * Setup.
 */
// turn up error reporting
error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', true);
ini_set('html_errors', false);

// the aura system directory
$system = dirname(dirname(dirname(__DIR__)));

// what config mode to operate in?
$config_mode = empty($_ENV['AURA_CONFIG_MODE'])
            ? 'default'
            : $_ENV['AURA_CONFIG_MODE'];

// force the include path
set_include_path("$system/include");

/**
 * Autoloader
 */
require "$system/package/aura.autoload/src.php";
$loader = new Loader;
$loader->register();

/**
 * DI container
 */
$loader->addPrefix('aura\di\\', "$system/package/aura.di/src");
$di = new Manager(new Forge(new Config));

/**
 * Config
 */
$cache_file = $system . DIRECTORY_SEPARATOR
            . 'tmp' . DIRECTORY_SEPARATOR
            . 'cache' . DIRECTORY_SEPARATOR
            . 'config' . DIRECTORY_SEPARATOR
            . $config_mode . '.php';

if (file_exists($cache_file)) {
    load_config($cache_file, $system, $loader, $di);
    $di->lock();
    return;
}

/**
 * Load config from scratch
 */
require __DIR__ . DIRECTORY_SEPARATOR . 'config.php';
$di->lock();
