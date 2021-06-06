# banality-game
Online Game. Be banal to win!
***
> **Dictionaries are not part of the source code!**
> 
> you'll need to find or create dictionary files by your self and put them to the ***app/data/dictionary*** folder.
> 
> format for filenaming: `<locale>.txt`
***
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
1. go to ***app*** dir
1. install requirements checker:
   ```
   composer require symfony/requirements-checker
   ```
1. copy content of ***.env.prod.dist*** into new file ***.env.prod.local*** and set the variables
1. install libraries:
   ```
   composer install --no-dev --optimize-autoloader
   ```
1. clear cache:
   ```
   APP_ENV=prod APP_DEBUG=0 php bin/console cache:clear
   ```
1. fix access:
   ```
   sudo chown www-data:www-data -R ./var/cache/prod
   ```
1. create database scheme:
   ```
   bin/console doctrine:migrations:migrate --all-or-nothing -vv
   ```
1. go to parent dir:
   ```
   cd ..
   ```
1. install frontend things:
   ```
   make frontend-prod
   ```
1. create a link to the ***app*** dir (e.g. in /var/www/)
   ```
   ln -s /home/\<user>/banality/banality-game/app /var/www/banality
   ```
1. configure and restart ***php-fpm*** and ***nginx***

## For developers
> you'll need to have on your machine:
> - git
> - docker

1. checkout source code
2. `make dev-up`
3. `make dev-init`
4. `make frontend-dev`
5. see the ***Makefile*** for further commands