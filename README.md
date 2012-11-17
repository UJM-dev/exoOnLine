exoOnLine (V2)
========================

exoOnLine is an applicatin of exercises online.
This application is being developed.

Quick start (manually)
----------------------------------

create an app/config/local/parameters.yml file according to app/config/local/parameters.yml.dist (currently only db settings are required)

* Install the composer (http://getcomposer.org/)
* <code>php composer.phar install </code>
* <code>php app/console assets:install</code>
* <code>php app/console doctrine:database:create</code>
* <code>php app/console doctrine:schema:update –force</code>
* <code>php app/console doctrine:fixtures:load</code>
