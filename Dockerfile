FROM php:5.6-apache
RUN a2enmod rewrite
COPY . /var/www/html
RUN chown -R www-data /var/www/html
