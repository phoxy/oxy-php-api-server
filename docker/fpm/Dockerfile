FROM php:fpm

RUN apt-get update && apt-get install -y \
		git \
	--no-install-recommends && rm -r /var/lib/apt/lists/*
RUN pecl install xdebug-2.6.0
RUN docker-php-ext-enable xdebug

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php -r "if (hash_file('SHA384', 'composer-setup.php') === '544e09ee996cdf60ece3804abc52599c22b1f40f4323403c44d44fdfdd586475ca9813a858088ffbc1f233e9b180f061') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
RUN php composer-setup.php
RUN php -r "unlink('composer-setup.php');"

COPY default.php /var/www/html/
COPY oxy/ /var/www/html/oxy

WORKDIR /var/www/html/oxy

RUN php composer.phar update
