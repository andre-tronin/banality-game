FROM php:8.0-fpm

ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin

RUN chmod +x /usr/local/bin/install-php-extensions && sync && \
    install-php-extensions intl zip mysqli pdo_mysql xdebug @composer-2.0.9

COPY symfony.pool.conf $PHP_INIT_DIR/conf.d/

USER www-data

WORKDIR /var/www/symfony

CMD ["php-fpm", "-F"]

EXPOSE 9003