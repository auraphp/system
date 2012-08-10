Aura System
===========

The Aura System is a skeleton for a full-stack Aura framework composed of Aura
packages.  You may clone the system for aiding in Aura development, or you may
download a system tarball (below) for your own use.  The system tarballs include
all the Aura packages of the same version number.


Getting Started
---------------

1. Download a copy of the [latest system tarball](https://github.com/downloads/auraphp/system/auraphp-system-1.0.0-beta2.tgz) 
which also includes all the aura packages.

2. Uncompress it and place it in your document root.

3. Browse to `'/path/to/auraphp-system-{$VERSION}/web/index.php'` to see "Hello World!"

Altenatively via composer
-------------------------

1. Download system [tar](https://github.com/auraphp/system/tarball/master) or [zip](https://github.com/auraphp/system/zipball/master)

2. Uncompress it and place it in your document root.

3. Download composer via wget if you are a `*nix` environment or get it from http://getcomposer.org

    wget http://getcomposer.org/composer.phar

And now run

    php composer.phar update
    
4. Browse to `'/path/to/auraphp-system-{$VERSION}/web/index.php'` to see "Hello World!"

For a more production-oriented solution, you should add a virtual host and
and point its document root to `'/path/to/auraphp-system-version/web'`.


Documentation
-------------

The documentation is incomplete at this time. You may read what we have so far
at <http://auraphp.github.com/system>.


Downloads
---------

- [Aura System 1.0.0-beta2](https://github.com/downloads/auraphp/system/auraphp-system-1.0.0-beta2.tgz)
