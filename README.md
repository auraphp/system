Introduction
============

The Aura System package provides a full-stack Aura framework built around Aura library packages.

Aura is very new, so the system is quite limited at this point. In particular, while it provides a scaffold for developing and testing library packages, it does not yet provide a web-oriented controller and view system.  (A cli-oriented controller system *is* provided.)

Because the library packages are relatively volatile, they are not provided as submodules in the system.  Instead, use the `update.php` command-line script to install and upgrade the library packages via `git`.


Installation
============

The `git` and `curl` commands must be in your `$PATH` for this to work.

1.  Clone the Aura `system` repository

        $ git clone https://github.com/auraphp/system
    
    This will give you the overall system skeleton, along with the built-in
    `aura.framework` library package and the `package/update.php` script.

2.  Issue `php update.php` to install the remaining library packages.

        $ cd system
        $ php update.php

You can subsequently update the system and all library packages (including installation of newly-available packages) with the same `php update.php` command.


Running Tests
=============

After installation or upgrade, you can run the tests for all packages like so:

    $ php cli.php aura.framework.run-tests

To run the tests for a single package, specify the package tests directory:

    $ php cli.php aura.framework.run-tests package/aura.di/tests

