prezent/crud-bundle
===================

This bundle provides you with an easy way to create CRUD screens in your application.
It is based on several other bundles:

* [doctrine/doctrine-bundle](https://github.com/doctrine/DoctrineBundle) for persistence
* [prezent/grid-bundle](https://github.com/Prezent/prezent-grid-bundle) for lists/grids
* [white-october/pagerfanta-bundle](https://github.com/whiteoctober/WhiteOctoberPagerfantaBundle) for pagination
* [zurb/foundation-sites](https://github.com/zurb/foundation-sites) as CSS framework


Index
-----

1. Installation (see below)
2. [Getting started](getting-started.md)
3. [CRUD configuration](configuration.md)
4. [Extending the base controller](controller.md)
5. [Templating](templating.md)
6. [Translations](translations.md)
7. [Delete modals](modals.md)


Installation
------------

This bundle can be installed using Composer. Tell composer to install the bundle:

```bash
$ php composer.phar require prezent/crud-bundle
```

Then, activate the bundle and its dependencies in your kernel:

```php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // Assuming a Symfony standard edition, just add these bundles
        new Prezent\GridBundle\PrezentGridBundle(),
        new Prezent\CrudBundle\PrezentCrudBundle(),
        new WhiteOctober\PagerfantaBundle\WhiteOctoberPagerfantaBundle(),
    );
}
```


Configuration
-------------

This bundle does not need to be configured, but you can optionally configure the PagerfantaBundle and
the GridBundle. See their respective documentation sections.
