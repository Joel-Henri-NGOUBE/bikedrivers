FROM php:8.2-apache

WORKDIR /bikedrivers_frontend

COPY composer.json /bikedrivers_frontend

COPY . /bikedrivers_frontend

# ARG MYSQL_USER

# ARG MYSQL_PASSWORD

# ARG MYSQL_HOST

# ARG MYSQL_PORT

# ENV DATABASE_URL="mysql://${MYSQL_USER}:${MYSQL_PASSWORD}@${MYSQL_HOST}:${MYSQL_PORT}/bikedrivers_api"

RUN docker-php-ext-install pdo pdo_mysql

RUN apt-get update -qq && apt-get install -y unzip git curl zip && curl -sS https://getcomposer.org/installer | php \
  && chmod +x composer.phar && mv composer.phar /usr/local/bin/composer

# RUN echo "" >> config/jwt/private.pem

# RUN echo "" >> config/jwt/public.pem

# RUN openssl genrsa -out config/jwt/private.pem -aes256 4096

# RUN openssl rsa -pubout -in config/jwt/private.pem -out config/jwt/public.pem
# RUN apt install build-essential checkinstall zlib1g-dev -y

# RUN cd /usr/local/src/

# RUN wget https://www.openssl.org/source/openssl-3.0.7.tar.gz

# RUN tar -xf openssl-3.0.7.tar.gz

# RUN cd openssl-3.0.7

# RUN chmod +x commands.sh

# RUN openssl version -a

RUN composer install

RUN php bin/console lexik:jwt:generate-keypair --skip-if-exists

CMD ["./commands.sh"]

EXPOSE 8000