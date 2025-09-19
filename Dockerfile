FROM php:8.2-apache

WORKDIR /bikedrivers_frontend

COPY composer.json /bikedrivers_frontend

COPY . /bikedrivers_frontend

ARG MYSQL_USER

ARG MYSQL_PASSWORD

ARG MYSQL_HOST

ARG MYSQL_PORT

ENV DATABASE_URL="mysql://${MYSQL_USER}:${MYSQL_PASSWORD}@${MYSQL_HOST}:${MYSQL_PORT}/bikedrivers_api"

RUN docker-php-ext-install pdo pdo_mysql

RUN apt-get update -qq && apt-get install -y unzip git curl zip && curl -sS https://getcomposer.org/installer | php \
  && chmod +x composer.phar && mv composer.phar /usr/local/bin/composer

RUN chmod +x commands.sh

RUN composer install

CMD ["./commands.sh"]

EXPOSE 8000