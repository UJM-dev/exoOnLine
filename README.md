exoOnLine
========================

exoOnLine is an applicatin of exercises online.
This application is being developed.

Quick start
----------------------------------

create an app/config/local/parameters.yml file according to app/config/local/parameters.yml.dist (currently only db settings are required)

* Install the composer (http://getcomposer.org/)
*  <code>php composer.phar install </code>
* php app/console assets:install
* php app/console doctrine:schema:update –force
* php app/console doctrine:fixtures:load
