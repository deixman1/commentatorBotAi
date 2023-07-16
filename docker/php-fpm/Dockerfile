FROM php:8.2-fpm-alpine

ARG UID
ARG GID

ENV UID=${UID}
ENV GID=${GID}

RUN apk add --no-cache bash autoconf build-base linux-headers \
    && pecl install ast  \
    && docker-php-ext-install sockets  \
    && pecl install pcov \
    && docker-php-ext-enable pcov \
    && docker-php-ext-install pcntl

RUN addgroup -g ${GID} --system appgroup
RUN adduser -G appgroup --system -D -s /bin/bash -u ${UID} appuser

RUN cd /root/ \
    && php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php \
    && mv -v composer.phar /usr/local/bin/composer \
    && chmod 0755 /usr/local/bin/composer

USER appuser