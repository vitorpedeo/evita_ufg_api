FROM php:8.1 as php

RUN apt-get update -y
RUN apt-get install -y unzip libpq-dev libcurl4-gnutls-dev
RUN docker-php-ext-install pdo pdo_pgsql bcmath

WORKDIR /var/www
COPY . .

COPY --from=composer:2.3.5 /usr/bin/composer /usr/bin/composer

ENTRYPOINT [ "docker/wait-for-it.sh", "database:5432", "--strict" , "--timeout=300" , "--" , "docker/entrypoint.sh" ]
