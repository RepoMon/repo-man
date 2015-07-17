FROM ubuntu:latest

MAINTAINER Tim Rodger <tim.rodger@gmail.com>

EXPOSE 80

RUN apt-get update -qq && \
    apt-get install -y \
    php5-cli \
    php5-fpm \
    git

RUN curl -sS https://getcomposer.org/installer | php \
  && mv composer.phar /usr/bin/composer

CMD ["php -S localhost:80"]

# Move application files into place
COPY src/ /home/repo-man/

# remove any development cruft
RUN rm -rf /home/repo-man/vendor/*

WORKDIR /home/repo-man/public

# Install dependencies
RUN composer install --prefer-dist && \
    apt-get clean

USER root

