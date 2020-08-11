# ./docker/php/Dockerfile
FROM php:7.1-fpm

RUN apt-get update && apt-get install -y \
       libfreetype6-dev \
       libjpeg62-turbo-dev \
       libpng-dev \
   && docker-php-ext-install -j$(nproc) iconv \
   && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
   && docker-php-ext-install -j$(nproc) gd

RUN docker-php-ext-install pdo_mysql

RUN export CFLAGS="$PHP_CFLAGS" CPPFLAGS="$PHP_CPPFLAGS" LDFLAGS="$PHP_LDFLAGS" \
   && apt-get update \
   && apt-get install -y --no-install-recommends \
       libmagickwand-dev \
   && rm -rf /var/lib/apt/lists/* \
   && pecl install imagick-3.4.3 \
   && docker-php-ext-enable imagick

RUN docker-php-ext-install zip

RUN pecl install apcu-5.1.8

RUN docker-php-ext-enable apcu

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php -r "if (hash_file('SHA384', 'composer-setup.php') === '93b54496392c062774670ac18b134c3b3a95e5a5e5c8f1a9f115f203b75bf9a129d5daa8ba6a13e2cc8a1da0806388a8') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;" \
    && php composer-setup.php --filename=composer \
    && php -r "unlink('composer-setup.php');" \
    && mv composer /usr/local/bin/composer

WORKDIR /var/www/html

COPY . /var/www/html

RUN chmod +w /var/www/html/src/Migrations

RUN mkdir /var/www/html/vendor

RUN chmod -R 777 /var/www/html/

RUN PATH=$PATH:/var/www/html/vendor/bin:bin
