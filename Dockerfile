FROM php:8.2-apache

WORKDIR /bikedrivers

COPY composer.json /bikedrivers

COPY . /bikedrivers

RUN docker-php-ext-install pdo pdo_mysql

RUN apt-get update -qq && apt-get install -y unzip git curl zip && curl -sS https://getcomposer.org/installer | php \
  && chmod +x composer.phar && mv composer.phar /usr/local/bin/composer

RUN chmod +x commands.sh

RUN composer install

CMD ["./commands.sh"]

EXPOSE 8000