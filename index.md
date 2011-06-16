---
title: The Aura System package provides a full-stack Aura framework built around Aura library packages.
layout: default
---

System Package ( The Aura Framework )
=====================================

The Aura System package provides a full-stack Aura framework built around Aura library packages.

Aura is very new, so the system is quite limited at this point. In particular, while it provides a scaffold for developing and testing library packages, it does provide a [web-oriented controller] ( http://auraphp.github.com/Aura.Web ), [view]( http://auraphp.github.com/Aura.View ) and a [router] ( http://auraphp.github.com/Aura.Router ) system. A [cli-oriented controller]( http://auraphp.github.com/Aura.Cli ) system is also provided.)

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

    $ php cli.php aura.framework.run-tests package/Aura.Di/tests
    
Working with System
===================
The Aura framework has a HelloWorld example for both cli and web. You can see it in package/Aura.Framework/src/

Creating your own packages
==========================

You can create your own packages like the directory structure below.

    Vendor.Package/
            src/
                Web/
                    SomePageName/
                        Page.php
                        view/
                        layout/
                        etc/
                Cli/
                    SomeCommandName/
                        Command.php
                        
All web controllers are placed in src/Web folder. Each controller has its own folder and the name of all the controllers is Page.php which resides inside the controller folder.

All cli controllers are placed in src/Cli folder. And the name will be Command.php
                        
Lets look into an eg: 
---------------------

Let vendor is example and package is blog. We are going to create a post controller . From the directory structure it will be clear.

    Example.Blog/
            assets/
                images/
                styles/
                scripts/
            scripts/
                
            tests/
                
            config/
                
            src/
                Web/
                    Post/
                        Page.php
                        view/
                        layout/
                        etc/
                Cli/
                    Feed/
                        Command.php

In this example `Post` is the name of the web contoller. All the web controller names are named as Page.php which extends the [Aura\Web\Page] ( http://auraphp.github.com/Aura.Web ) which are placed in the post directory.

    <?php
    namespace Aura\Framework\Web\Post;
    use Aura\Web\Page as WebPage;
    class Page extends WebPage
    {
        public function actionIndex()
        {
            $this->response->setView('index');
        }
    }

Every action starts with word `action` and then the action name.

All the images, js, css etc are placed in assets/images, assets/scripts, assets/styles respectievely. It can be accessed from via as /assests/Vendor.Package/* . 
For eg to access an image from the view as `<img src="/asset/Aura.Framework/images/auralogo.jpg" />`.

Assets can be cached, so remember only to use in production and not in development. An example of the assets is in the Aura.Framework/src/Web/Hello controller's asset action. You can see it in action from localhost/hello/asset .
The cache folder of the web root should be writeable to create the cache.

    $di->setter['Aura\Framework\Web\Asset\Page'] = array(
        'setSystem' => $di->lazyGet('system'),
        'setWebCacheDir' => 'cache/asset',
        'setCacheConfigModes' => array('prod', 'staging'),
    );
    
By default its not cached, so if you want to cache add `default` also to the list. So it will be array('default', 'prod', 'staging').

More coming soon .... Till then bye!.
