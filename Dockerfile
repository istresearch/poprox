FROM php:5.6-apache
RUN apt-get update
RUN apt-get install -y vim
RUN a2enmod rewrite
COPY . /var/www/html
RUN chown -R www-data /var/www/html
