FROM php:8.2-apache

WORKDIR /mybank

COPY composer.json /mybank

COPY . /mybank

ENV DATABASE_URL=mysql://${MYSQL_USER}:${MYSQL_PASSWORD}@${MYSQL_HOST}:${MYSQL_PORT}/bikedrivers_api

RUN docker-php-ext-install pdo pdo_mysql

RUN apt-get update -qq && apt-get install -y unzip git curl zip && curl -sS https://getcomposer.org/installer | php \
  && chmod +x composer.phar && mv composer.phar /usr/local/bin/composer

RUN composer install

CMD ["./commands.sh"]

EXPOSE 8000