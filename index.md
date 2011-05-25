---
title: The Aura System package provides a full-stack Aura framework built around Aura library packages.
layout: default
---

System Package ( The Aura Framework )
=====================================

The Aura System package provides a full-stack Aura framework built around Aura library packages.

Aura is very new, so the system is quite limited at this point. In particular, while it provides a scaffold for developing and testing library packages, it does provide a [web-oriented controller] ( http://auraphp.github.com/aura.web ), [view]( http://auraphp.github.com/aura.view ) and a [router] ( http://auraphp.github.com/aura.router ) system. A [cli-oriented controller]( http://auraphp.github.com/aura.cli ) system is also provided.)

Because the library packages are relatively volatile, they are not provided as submodules in the system.  Instead, use the `update.php` command-line script to install and upgrade the library packages via `git`.


Installation
============

The `git` command must be in your `$PATH` for this to work.

1.  Clone the Aura `system` repository

        $ git clone https://github.com/auraphp/system
    
    This will give you the overall system skeleton, along with the built-in
    `aura.framework` library package and the `update.php` script.

2.  Issue `php update.php` to install the remaining library packages.

        $ cd system
        $ php update.php

You can subsequently update the system and all library packages (including installation of newly-available packages) with the same `php update.php` command.


Running Tests
=============

After installation or upgrade, you can run the tests for all packages like so:

    $ php cli.php aura.framework.run-tests

To run the tests for a single package, specify the package tests directory:

    $ php cli.php aura.framework.run-tests pachttp://harikt.github.com/system/kage/aura.di/tests
    
Working with System
===================
The Aura framework has a hello_world example for both cli and web. You can see it in package/aura.framework/src/

Creating your own packages
==========================

You can create your own packages like the directory structure below.

    vendor.package/
            src/
                web/
                    some_page_name/
                        Page.php
                        view/
                        layout/
                        etc/
                cli/
                    some_command_name/
                        Command.php
                        
All web controllers are placed in src/web folder. Each controller has its own folder and the name of all the controllers is Page.php which resides inside the controller folder.

All cli controllers are placed in src/cli folder. And the name will be Command.php
                        
Lets look into an eg: 
---------------------

Let vendor is example and package is blog. We are going to create a post controller . From the directory structure it will be clear.

    example.blog/
            src/
                web/
                    post/
                        Page.php
                        view/
                        layout/
                        etc/
                cli/
                    feed/
                        Command.php

In this example `post` is the name of the web contoller. All the web controller names are named as Page.php which extends the [aura\web\Page] ( http://auraphp.github.com/aura.web ) which are placed in the post directory.

    <?php
    namespace aura\framework\web\post;
    use aura\web\Page as WebPage;
    class Page extends WebPage
    {
        public function actionIndex()
        {
            $this->response->setView('index');
        }
    }

Every action starts with word `action` and then the action name.
