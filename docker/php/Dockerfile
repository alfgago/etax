FROM php:7.2.3-fpm

RUN apt-get update && apt-get install -y mysql-client apt-utils supervisor git \
    && docker-php-ext-install pdo_mysql 

RUN apt-get update -y && apt-get install -y sendmail libpng-dev

RUN apt-get update && \
    apt-get install -y \
        zlib1g-dev \
        libjpeg-dev \
	libjpeg62-turbo-dev \
	libpng-dev 

RUN docker-php-ext-install bcmath

RUN docker-php-ext-install zip
RUN docker-php-ext-install pcntl

RUN docker-php-ext-configure gd --with-png-dir=/usr --with-jpeg-dir=/usr; 
RUN docker-php-ext-install gd

RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

RUN mkdir -p /var/www
WORKDIR /var/www

#Composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && php -r "unlink('composer-setup.php');"
    