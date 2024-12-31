FROM php:8.2.8-apache

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN sed -i 's/deb.debian.org/mirrors.ustc.edu.cn/g' /etc/apt/sources.list.d/debian.sources && apt update
RUN apt install -y libzip-dev && docker-php-ext-install zip
RUN composer create-project laravel/laravel . --ignore-platform-reqs

RUN chmod -R 777 /var/www/html/storage \
&& chmod -R 777 /var/www/html/vendor \
&& chmod -R 777 /var/www/html/resources \
&& chmod 777 /var/www/

COPY ./docker/routes/console.php /var/www/html/routes/console.php

ENTRYPOINT cp -rf /app/docker/* ./ \
&& composer config repositories.local '{"type": "path", "url": "/app"}' \
&& composer require laravel/justit --no-scripts  \
&& bash
