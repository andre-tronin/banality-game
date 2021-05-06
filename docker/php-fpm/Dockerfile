FROM php:8.0-fpm

ARG UID=1000
ARG GID=1000

ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin

RUN chmod +x /usr/local/bin/install-php-extensions && sync && \
    install-php-extensions intl zip mysqli pdo pdo_mysql xdebug @composer-2.0.9

COPY symfony.pool.conf $PHP_INIT_DIR/conf.d/

RUN groupmod -g ${GID} www-data && \
    usermod -u ${UID} -d /home/www-data www-data

USER www-data

WORKDIR /var/www/symfony

CMD ["php-fpm", "-F"]

EXPOSE 9003