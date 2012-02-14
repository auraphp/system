---
title: The Aura System
layout: default
---

The Aura System
=====================================

The Aura System provides a full-stack Aura framework built around Aura library packages.


Installation
============

You can use a cloned version of the system and then manually install packages,
or you can download a full system tarball.

Tarball
-------

1.  Download the latest tarball from the
    [downloads page](https://github.com/auraphp/system/downloads).

2.  Uncompress the tarball to your document root.

3.  Browse to `/path/to/system/web/index.php` to see "Hello World!".


Cloning
-------

The `git` command must be in your `$PATH` for this to work.

1.  Clone the Aura `system` repository to your document root.

        $ git clone https://github.com/auraphp/system.git
    
    This will give you the overall system skeleton along with an
    `update.php` script.

2.  Issue `php update.php` to install the remaining library packages.

        $ cd system
        $ php update.php
        
    You can subsequently update the system and all library packages (including
    installation of newly-available packages) with the same `php update.php`
    command.

3. Browse to `/path/to/system/web/index.php` to see "Hello World!".


Better URLs
-----------

To see better URLs under either installation process, add a virtual host to
your web server, and point its document root to `/path/to/system/web`. The `mod_rewrite` module should be installed. That will allow you to browse to the virtual host without needing `index.php` in the URL.

Command Line
------------

You can also try Aura from the command line.  Go to the system directory and run a CLI command from the Aura Framework package:

    $ php package/Aura.Framework/cli/hello-world

You should see output of `'Hello World!'`.


Running Tests
=============

For testing, you need to have [PHPUnit 3.6](http://www.phpunit.de/manual/current/en/) or later installed.

To run the tests in each individual package, change to that package's `tests` direcotry and issue `phpunit`:

    $ cd /path/to/system/package/Aura.Autoload/tests
    $ phpunit


System Organization
===================

The system directory structure is pretty straightforward:

    {$system}/
        config/                     # mode-specific config files
            default.php             # default config overrides
            dev.php                 # shared development server
            local.php               # local (individual) development server
            prod.php                # production
            stage.php               # staging
            test.php                # testing
        include/                    # a place for generic includes
        package/                    # a place for Aura packages
        tmp/                        # temporary files
        web/                        # web server document root
            .htaccess               # mod_rewrite rules
            cache/                  # public cached files
            favicon.ico             # favicon to reduce error_log lines
            index.php               # bootstrap script

Package Organization
====================

In Aura, all code is grouped into packages.  There is no difference between library packages, support packages, web packages, and so on -- they are all just "packages."

The package directory structure looks like this:

    Vendor.Package/
        cli/                        # command-line script invokers
        composer.json               # composer/packagist file
        config/                     # package-level configs
            default.php             # default configs
            test.php                # configs for "test" mode
        meta/                       # metadata for packaging scripts
        LICENSE                     # license file
        README.md                   # readme file
        src/                        # the actual source code organized for PSR-0
            Vendor/
                Package/
                    Class.php
        tests/                      # test files for phpunit
            Vendor/
                Package/
                    ClassTest.php
            bootstrap.php
            phpunit.xml
        web/                        # public web assets
            styles/                 # css files
            images/                 # image files
            scripts/                # javascript (or other script) files

In general, your `src/` files should be organized like so:

    Vendor/
        Package/
            Cli/                    # all CLI commands
                CommandName/        # a particular CLI command and its support files
                    Command.php     # the actual command logic
                    data/           # other data for the command
            Web/                    # all web pages
                PageName/           # a particular web page and its support files
                    Page.php        # the actual page action logic
                    view/           # views for the page
                    layout/         # layouts for the page
                    data/           # other data for the page
            View/
                Helper/
                    HelperName.php  # a view helper

You can of course place other libraries in the package if you like.
            
                                    
An Example Page Controller
==========================

Let's create a package and a page controller, and wire it up for browsing.

Package Structure
-----------------

First, create the package structure (just the parts we need):

    $ cd /path/to/system/package
    $ mkdir -p Example.Package/src/Example/Package/Web/Quick/view


Page Controller and View
------------------------

Change to the page controller directory ...
    
    $ cd Example.Package/src/Example/Package/Web/Quick/
    
... and edit a file called `Page.php`. Add this code:

    <?php
    namespace Example\Package\Web\Quick;
    use Aura\Framework\Web\AbstractPage;
    class Page extends AbstractPage
    {
        public function actionIndex()
        {
            $this->view->setInnerView('index.php');
        }
    }

Next, create a view for the action. Edit a file called `view/index.php` and
add the following text:

    The quick brown fox jumps over the lazy dog.

At this point your package directory should look like this:

    Example.Package/
        src/
            Example/
                Package/
                    Web/
                        Quick/
                            Page.php
                            view/
                                index.php

Config
------

Now we need to wire up the page controller to the autoloader and the routing
system. We could do this at the package-level config, but let's concentrate on
the system-level config for now.

Change to the system config directory:

    $ cd /path/to/system/config
    
Edit the `default.php` file and add this code at the end of the file:

    <?php
    /** Example Package configs */
    
    // add the package to the autoloader
    $loader->add('Example\Package\\', dirname(__DIR__) . '/package/Example.Package/src');
    
    // add a route to the page and action
    $di->get('router_map')->add('quick_index', '/quick', [
        'values' => [
            'controller' => 'quick',
            'action' => 'index',
        ],
    ]);
    
    // map the 'quick' controller value to a page controller class
    $di->params['Aura\Framework\Web\Factory']['map']['quick'] = 'Example\Package\Web\Quick\Page';

Try It Out
----------

You should now be able to browse to the `/quick` URL and see `"The quick brown fox jumps over the lazy dog."`

