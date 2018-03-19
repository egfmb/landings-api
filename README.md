Master Symfony 2.6.7

First clone the project
*******
$ git clone git clone git@bitbucket.org:montedonico/master.git
*******

Install the composer
*******
$ composer install
*******

Install Bower to get the assets
*******
$ bower install
*******

Modify the DB
*******
$ php app/console doctrine:schema:update --force
*******


Run the server
**********
http://localhost/master/web/app_dev.php/login
http://localhost/master/web/app_dev.php/register
**********


