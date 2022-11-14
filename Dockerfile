# This file is used for local development.

FROM php:7.4-apache

## Build system

WORKDIR /home/app/
COPY . /home/app/

# PHP + extensions
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

RUN install-php-extensions \
    exif \
    gd \
    http \
    iconv \
    intl \
    json \
    mbstring \
    opcache \
    pcntl \
    pdo_mysql-^7.4\
    mysqli\
    zip

# Composer binary
COPY --from=composer:2.0 /usr/bin/composer /usr/bin/composer


ENV NODE_VERSION=16.13.0

RUN apt install -y curl
RUN curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.0/install.sh | bash
ENV NVM_DIR=/root/.nvm

RUN . "$NVM_DIR/nvm.sh" && nvm install ${NODE_VERSION}
RUN . "$NVM_DIR/nvm.sh" && nvm use v${NODE_VERSION}
RUN . "$NVM_DIR/nvm.sh" && nvm alias default v${NODE_VERSION}
ENV PATH="/root/.nvm/versions/node/v${NODE_VERSION}/bin/:${PATH}"


