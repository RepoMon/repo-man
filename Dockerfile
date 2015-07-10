FROM ubuntu:latest

MAINTAINER Tim Rodger <tim.rodger@gmail.com>

EXPOSE 80

RUN apt-get update -qq && \
    apt-get install -y \
    nginx \
    php5-cli \
    php5-fpm \
    git

# configure server applications

RUN echo "daemon off;" >> /etc/nginx/nginx.conf
ADD ./build/nginx/default /etc/nginx/sites-enabled/default
ADD ./build/supervisor/supervisord.conf /etc/supervisor/supervisord.conf
ADD ./build/php-fpm/php-fpm.conf /etc/php5/fpm/php-fpm.conf

RUN echo "cgi.fix_pathinfo = 0;" >> /etc/php5/fpm/php.ini

RUN curl -sS https://getcomposer.org/installer | php \
  && mv composer.phar /usr/bin/composer

CMD ["/home/repo-man/run.sh"]

# Move application files into place
COPY src/ /home/repo-man/

RUN chmod +x /home/repo-man/run.sh

# make cache directory writable by web server
RUN chown www-data:www-data /home/repo-man/cache/
RUN chmod +w /home/repo-man/cache

# remove any development cruft
RUN rm -rf /home/repo-man/cache/* /home/repo-man/vendor/*

WORKDIR /home/repo-man

# Install dependencies
RUN composer install --prefer-dist && \
    apt-get clean

USER root

