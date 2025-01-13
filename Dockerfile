FROM php:8.2-apache

WORKDIR /var/www/html/


# RUN apt-get update --allow-releaseinfo-change \
#     && apt-get install -y wget \
#     && apt-get install -y libxml2-dev \
#     && apt-get install -y libmemcached-dev \
#     && apt-get install -y libicu-dev \
#     && apt-get install -y zlib1g-dev \
#     && pecl install memcached \
#     && docker-php-ext-install xml intl \
#     && docker-php-ext-enable memcached xml intl \
#     && apt-get install -y default-libmysqlclient-dev \
#     && docker-php-ext-install mysqli pdo_mysql
RUN apt-get update -y --allow-releaseinfo-change && \
    apt-get install -y --no-install-recommends wget libxml2-dev libicu-dev zlib1g-dev libpng-dev default-libmysqlclient-dev libzip-dev cron git && \
    docker-php-ext-install xml intl gd zip mysqli pdo_mysql

#mysql > SET GLOBAL sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));
# RUN apt-get install -y php-cli
# RUN apt-get install -y php-zip
RUN apt-get install -y unzip

RUN curl -sS https://getcomposer.org/installer |php

RUN mv composer.phar /usr/local/bin/composer

RUN mkdir vendor

RUN usermod -u 1001 www-data
RUN groupmod -g 1001 www-data

COPY . .

RUN composer update --ignore-platform-req=ext-gd --ignore-platform-req=ext-zip
#RUN bin/cake migrations migrate --plugin EmailQueue
#RUN touch logs/log.txt  

# RUN mkdir tmp
RUN if [ ! -d "tmp" ]; then mkdir tmp; fi
RUN if [ ! -d "logs" ]; then mkdir logs; fi
# RUN mkdir logs
RUN chmod -R 777 tmp
RUN chmod -R 777 logs   

RUN chown -R www-data:www-data .

RUN a2enmod rewrite

# WORKDIR /var/www/html/webroot/
#COPY --chown=www-data . .
# FROM node:16.14-alpine as build-stage
RUN curl -sL https://deb.nodesource.com/setup_22.x | bash
RUN apt-get install --yes nodejs
# # RUN apk add --no-cache python3
# RUN npm install -g env-cmd 
# # install simple http server for serving static content
# RUN npm install -g http-server
# RUN npm install -g @vue/cli 

# # make the 'app' folder the current working directory
# WORKDIR /5t.vue

# # copy both 'package.json' and 'package-lock.json' (if available)
# COPY package*.json ./

# # install project dependencies
# RUN npm install --force

# # copy project files and folders to the current working directory (i.e. 'app' folder)
# COPY . .
# RUN apk add --no-cache git
# FROM registry:5000/php7-ext

# Install Xdebug 2.9.8 compatible with PHP 7.4
RUN pecl install xdebug-3.3.2 && \
    docker-php-ext-enable xdebug

COPY /docker/php/conf.d/xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini  
COPY /docker/php/conf.d/error_reporting.ini /usr/local/etc/php/conf.d/error_reporting.ini
RUN rm ./webroot/valdaso
RUN ln -s /var/www/html/plugins/Valdaso/webroot/ ./webroot/valdaso
# COPY ./webroot/valdaso/img /var/www/html/webroot/valdaso/img
# COPY ./webroot/valdaso/webfonts /var/www/html/webroot/valdaso/webfonts
# RUN npm install
# RUN npm run dev
