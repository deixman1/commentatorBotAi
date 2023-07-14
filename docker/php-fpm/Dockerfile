FROM php:8.2-fpm-alpine

ARG UID
ARG GID

ENV UID=${UID}
ENV GID=${GID}

RUN apk add --no-cache bash

RUN addgroup -g ${GID} --system appgroup
RUN adduser -G appgroup --system -D -s /bin/bash -u ${UID} appuser

RUN cd /root/ \
    && php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php \
    && mv -v composer.phar /usr/local/bin/composer \
    && chmod 0755 /usr/local/bin/composer

USER appuser