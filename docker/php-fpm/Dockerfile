FROM php:8.3-fpm-alpine

ARG UID
ARG GID

ENV UID=${UID}
ENV GID=${GID}

RUN apk update
RUN apk add --no-cache libzip-dev bash autoconf build-base linux-headers \
    && pecl install ast  \
    && docker-php-ext-install sockets  \
    && pecl install pcov \
    && docker-php-ext-enable pcov \
    && docker-php-ext-install pcntl \
    && docker-php-ext-install zip

RUN addgroup -g ${GID} --system appgroup
RUN adduser -G appgroup --system -D -s /bin/bash -u ${UID} appuser

RUN cd /root/ \
    && php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php \
    && mv -v composer.phar /usr/local/bin/composer \
    && chmod 0755 /usr/local/bin/composer

USER appuser
