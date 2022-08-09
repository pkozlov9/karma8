FROM php:7-apache

# Add non-root user for container
RUN useradd karma8_user && \
    mkdir /home/karma8_user && \
    chown -R karma8_user:karma8_user /home/karma8_user

# Make directory for php error log
RUN mkdir -p /var/log/php && \
    touch /var/log/php/error.log && \
    chmod -R 0777 /var/log/php

# Configure Apache
COPY config/php/app.ini /usr/local/etc/php/conf.d/
COPY config/apache/apache2.conf /etc/apache2/
RUN rm -rf /etc/apache2/sites-available/* /etc/apache2/sites-enabled/* /var/log/apache2/*
COPY config/apache/apache-site.conf /etc/apache2/sites-available/
RUN cd /etc/apache2/sites-available/ && \
    a2ensite * && \
    a2enmod rewrite headers
COPY config/cron/root /var/spool/cron/crontabs/root

WORKDIR /var/www

# Mounts
VOLUME [ "/var/www" ]