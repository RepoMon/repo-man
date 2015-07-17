FROM ubuntu:latest

MAINTAINER Tim Rodger <tim.rodger@gmail.com>

EXPOSE 80

# RUN add-apt-repository ppa:ondrej/php5-5.6

RUN apt-get update -qq && \
    apt-get install -y \
    php5-cli \
    php5-fpm \
    curl \
    git

RUN curl -sS https://getcomposer.org/installer | php \
  && mv composer.phar /usr/bin/composer

CMD ["php", "-S", "0.0.0.0:80"]

# Move application files into place
COPY src/ /home/repo-man/

# remove any development cruft
RUN rm -rf /home/repo-man/vendor/*

WORKDIR /home/repo-man

# Install dependencies
RUN composer install --prefer-dist && \
    apt-get clean

WORKDIR /home/repo-man/public

USER root

