<?php
// prep the framework in 'test' mode via the bootstrap factory
// and get back the DI container
require dirname(dirname(__DIR__))
      . '/package/Aura.Framework/src/Aura/Framework/Bootstrap/Factory.php';
$factory = new \Aura\Framework\Bootstrap\Factory;
$di = $factory->prep('test');

// set the loader mode so that missing PHPUnit files don't blow up the tests;
// there are issues with PHP_Invoker
$loader = $di->get('framework_loader');
$loader->setMode(\Aura\Autoload\Loader::MODE_SILENT);

// set the dependency injection container into the globals so we can access
// it in the tests.
$GLOBALS['AURA_DI_CONTAINER'] = $di;
