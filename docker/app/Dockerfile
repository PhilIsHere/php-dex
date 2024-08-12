ARG PHP_IMAGE

FROM ${PHP_IMAGE}

ARG APACHE_U_ID
ARG APACHE_USER
ARG APACHE_G_ID

ENV APACHE_RUN_USER ${APACHE_USER}
ENV APACHE_RUN_GROUP ${APACHE_USER:-www-data}
ENV APACHE_U_ID=${APACHE_U_ID}
ENV APACHE_G_ID ${APACHE_G_ID}

RUN apt update
RUN apt -y install libicu-dev libzip-dev git unzip curl sudo libpng-dev
RUN curl -sL https://deb.nodesource.com/setup_14.x | sudo bash -
RUN apt -y install nodejs
RUN npm i -g webpack yarn
RUN docker-php-ext-install pdo_mysql intl zip gd
RUN pecl install xdebug
RUN docker-php-ext-enable xdebug
RUN echo "xdebug.mode=develop,debug" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini && \
    echo "xdebug.discover_client_host=0"  >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini && \
    echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
RUN a2enmod rewrite

RUN mkdir -p /home/www-data/.composer
RUN usermod -s /bin/bash -G sudo -d /home/www-data www-data


COPY composer.json /var/www/html/

RUN curl -sS https://getcomposer.org/installer | php
RUN mv composer.phar /usr/local/bin/composer


RUN sed -i 's/\/var\/www\/html/\/var\/www\/html\/public/' /etc/apache2/sites-available/000-default.conf

RUN mkdir -p /var/www/logs && touch /var/www/logs/error.log

RUN if [ ${APACHE_U_ID} -ne 33 ]; then \
  usermod -u ${APACHE_U_ID} ${APACHE_USER}; \
fi
RUN if [ ${APACHE_G_ID} -ne 33 ]; then \
  groupmod -g ${APACHE_G_ID} ${APACHE_USER}; \
  usermod -g ${APACHE_G_ID} ${APACHE_USER}; \
fi

RUN chown -R ${APACHE_U_ID}:${APACHE_G_ID} /home/www-data
RUN chown -R ${APACHE_U_ID}:${APACHE_G_ID} /var/www

WORKDIR /var/www/html
USER ${APACHE_USER}
RUN composer install
RUN yarn install
