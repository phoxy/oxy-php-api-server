FROM php:fpm

RUN apt-get update && apt-get install -y \
		git \
	--no-install-recommends && rm -r /var/lib/apt/lists/*
RUN pecl install xdebug-2.6.0
RUN docker-php-ext-enable xdebug

COPY default.php /var/www/html/
COPY oxy/ /var/www/html/oxy
