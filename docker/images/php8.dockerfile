FROM php:8.2-fpm-bullseye

RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    default-mysql-client \
    locales \
    mc \
    nano \
    unzip \
    wget \
    zip \
    libicu-dev \
    libbz2-dev \
    libzip-dev

RUN sed -i -e 's/# en_US.UTF-8 UTF-8/en_US.UTF-8 UTF-8/' /etc/locale.gen \
  && sed -i -e 's/# ru_RU.UTF-8 UTF-8/ru_RU.UTF-8 UTF-8/' /etc/locale.gen \
  && dpkg-reconfigure --frontend=noninteractive locales

RUN docker-php-ext-install \
  bcmath exif \
  mysqli pdo pdo_mysql \
  && docker-php-ext-configure zip \
  && docker-php-ext-install zip \
  && docker-php-ext-install intl

RUN curl --silent --show-error https://getcomposer.org/installer | php && \
  mv composer.phar /usr/local/bin/composer

ENV LANG ru_RU.UTF-8
ENV LC_ALL ru_RU.UTF8

# users init
RUN usermod -u 1000 www-data && groupmod -g 1000 www-data && \
    mkdir /home/www-data && chown www-data:www-data /home/www-data && \
    usermod -d /home/www-data www-data
