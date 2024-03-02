FROM php:8.2-fpm-alpine

ARG USER_ID=1000
ARG GROUP_ID=1000

RUN echo "@community https://dl-cdn.alpinelinux.org/alpine/edge/community" >> /etc/apk/repositories && \
    apk update && \
    apk add --no-cache \
        shadow \
        php82-pecl-opentelemetry@community && \
    echo "extension=/usr/lib/php82/modules/opentelemetry.so" > /usr/local/etc/php/conf.d/docker-fpm.ini && \
    php --ri opentelemetry

RUN usermod -u $USER_ID www-data && groupmod -g $GROUP_ID www-data

USER www-data
