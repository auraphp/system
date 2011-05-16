---
title: The Aura System package provides a full-stack Aura framework built around Aura library packages.
layout: default
---

Aura System
===========

The Aura System package provides a full-stack Aura framework built around Aura library packages.

Aura is very new, so the system is quite limited at this point. It provides a scaffold for developing and testing library packages, also provide a [web-oriented controller](http://auraphp.github.com/aura.web/) and [view system](http://auraphp.github.com/aura.view/) . Also provides a [cli-oriented](http://auraphp.github.com/aura.cli/) controller system *is* provided.)

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

    $ php cli.php aura.framework.run-tests package/aura.di/tests
    
Structure of System
===================
You can issue the tree command from terminal ( if you are in GNU/Linux ).

|-- cli.php //Running cli commands
|-- config
|-- include
|-- package
|   |-- [aura.autoload](http://auraphp.github.com/aura.autoload/)
|   |-- [aura.cli](http://auraphp.github.com/aura.cli/)
|   |-- [aura.di](http://auraphp.github.com/aura.di/)
|   |-- aura.framework
|   |   |-- src
|   |   |   |-- example
|   |   |   |   |-- HelloWorld
|   |   |   |   |   |-- Layout
|   |   |   |   |   `-- View
|   |   |   |   |       |-- index.php
|   |   |   |   |       `-- read.php
|   |   |   |   `-- HelloWorld.php
|   |-- [aura.http](http://auraphp.github.com/aura.http/)
|   |-- [aura.router](http://auraphp.github.com/aura.router/)
|   |-- [aura.signal](http://auraphp.github.com/aura.signal/)
|   |-- [aura.view](http://auraphp.github.com/aura.view/)
|   |-- [aura.web](http://auraphp.github.com/aura.web/)
|   `-- vendor.package //Naming your own package
|       |-- config
|       |   `-- default.php //will override default.php of the main config
|       |-- README.md
|       |-- src
|       |   |-- CreatePackage.php //Sample
|       |   `-- Example.php //Sample
|       `-- templates
|           `-- sample.txt
|-- status.php 
|-- tips 
|-- tmp 
|-- update.php //updating the aura packages
`-- web
    `-- index.php //Webroot directory
    
Setting Your Virtual Host
-------------------------
Open your terminal, and type this,

$sudo su 

$echo "127.0.0.1 blog.local www.aurasystem.local" >> /etc/hosts

$vim /etc/apache2/sites-available/aurasystem.local

Paste the below lines 

<VirtualHost *:80>
    ServerName aurasystem.local
    ServerAlias www.aurasystem.local
    DocumentRoot /var/www/system/web
    ;Hoping you have downloaded the system folder in www folder. Else put the real path in the above and below line
    <Directory /var/www/system/web>
        AllowOverride All
    </directory>
</VirtualHost>

$a2ensite aurasystem.local
$apache2ctl restart

Probably need $a2enmod rewrite

Example of Web Controller
=========================
The aura system package comes with a default HelloWorld controller which extends the base Page controller. 
The example resides at system/package/aura.framework/src/example directory.
Currently the HelloWorld controller is placed outside the HelloWorld folder. We will be moving the controller inside the HelloWorld folder in the next commits.
You can create action starting with the name action<Action Name> . So for eg an action named read will be named as actionRead() .

    /*
    * Read Action
    */
    public function actionRead()
    {
        $this->response->setView('read');
    }
    
You need to set the routes in the default.php of the config folder in system or you can create a routes.php file for the package and attach the file in router. This way its highly portable and will not make much dependencies.

Attaching layout to the corresponding action
--------------------------------------------
Todo

View of the action
------------------
Todo

Example of a Command
====================
For the time please go through [http://auraphp.github.com/aura.cli/](http://auraphp.github.com/aura.cli/) . We are working on some changes.

Feel free to drop your feed back at [http://groups.google.com/group/auraphp](http://groups.google.com/group/auraphp) or in #irc freenode.net #auraphp .
