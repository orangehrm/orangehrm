FROM php:7.1-apache

MAINTAINER Orangehrm <samanthaj@orangehrm.com>


ENV DEBIAN_FRONTEND noninteractive
ENV APACHE_DOCUMENT_ROOT /var/www/html

# Add source to image
COPY .  /var/www/html

COPY ./docker-build-files/config.ini  /var/www/html/installer/

# Install mysql
RUN apt-get update \
  && apt-get install -y mysql-server mysql-client \
  && docker-php-ext-install pdo pdo_mysql \
  && apt-get clean \
  && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Install gd
RUN apt-get update && apt-get install -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libmcrypt-dev \
        libpng-dev \
    && docker-php-ext-install -j$(nproc) iconv mcrypt \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install -j$(nproc) gd

# Fix Permission
RUN cd /var/www/html; bash fix_permissions.sh

#Install application
RUN cd /var/www/html; php installer/cli_install.php 0

# Expose the port
EXPOSE 80