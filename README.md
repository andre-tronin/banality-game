# banality-game
Online Game. Be banal to win!

## Installation on server
> you'll need following software installed:
> - git
> - mysql
> - php-fpm (extensions: intl, pdo_mysql)
> - nginx
> - composer
> - docker (for yarn)

1. create a database and two database user (one for migrations and one for the game)
1. checkout source code to some dir (e.g. /home/\<user>/banality)
2. go to ***app*** dir
3. install requirements checker:
> `composer require symfony/requirements-checker`
5. copy content of ***.env.prod.dist*** into new file ***.env.prod.local*** and set the variables
6. install libraries:
> `composer install --no-dev --optimize-autoloader`
7. clear cache:
> `APP_ENV=prod APP_DEBUG=0 php bin/console cache:clear`
8. fix access:
> `sudo chown www-data:www-data -R ./var/cache/prod`
9. create database scheme:
> `bin/console doctrine:migrations:migrate --all-or-nothing -vv`
10. go to parent dir:
> `cd ..`
11. install frontend things:
> `make frontend-prod`
12. create a link to the ***app*** dir (e.g. in /var/www/)
> `ln -s /home/\<user>/banality/banality-game/app /var/www/banality`
13. configure and restart ***php-fpm*** and ***nginx***

## For developers
> you'll need to have on your machine:
> - git
> - docker

1. checkout source code
2. `make dev-up`
3. `make dev-init`
4. `make frontend-dev`
5. see the ***Makefile*** for further commands