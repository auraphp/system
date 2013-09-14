<?php
spl_autoload_register(function ($class) {
    $parts   = explode('\\', $class);
    $vendor  = array_shift($parts);
    $package = array_shift($parts);
    
    // look for a src file
    $file = dirname(__DIR__)
          . "/package/{$vendor}.{$package}/src/{$vendor}/{$package}/"
          . implode('/', $parts)
          . '.php';
    if (is_readable($file)) {
        require $file;
        return;
    }
    
    // look for a tests file
    $file = dirname(__DIR__)
          . "/package/{$vendor}.{$package}/tests/{$vendor}/{$package}/"
          . implode('/', $parts)
          . '.php';
    if (is_readable($file)) {
        require $file;
    }
});
