# Entity XLIFF

This is a Drupal extension that integrates [Eggs'n'Cereal]()--a PHP library that
generically facilitates serialization and unserialization of data in the [XLIFF]()
format--with Drupal. It does very little on its own, other than provide a basic
UI to export and import Drupal entities in the XLIFF format.

Install this module if you're using another module that depends on it, or if you
are a developer looking to build a custom localization workflow for Drupal based
around importing and exporting XLIFFs.

## Installation
1. Install this module and its dependencies, [Composer Manager](), via drush:
  `drush dl entity_xliff entity composer_manager`
2. Enable Composer Manager: `drush en composer_manager`
3. Then enable this module: `drush en entity_xliff`
4. Composer Manager may automatically download and enable requisite PHP
   libraries, but if not, run `drush composer-manager install` or
   `drush composer-manager update`.

More information on [installing and using Composer Manager]() is available on
GitHub.

## Developing with this module
@todo

## Please note
This module and its underlying dependencies are still under active development.
Use at your own risk with the intention of finding and filing bugs.

[Eggs'n'Cereal]: https://github.com/tableau-mkt/eggs-n-cereal
[XLIFF]: http://docs.oasis-open.org/xliff/xliff-core/xliff-core.html
[Composer Manager]: https://www.drupal.org/project/composer_manager
[Entity API]: https://www.drupal.org/project/entity
[installing and using Composer Manager]: https://github.com/cpliakas/composer-manager-docs/blob/master/README.md#installation
