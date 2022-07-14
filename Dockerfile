ARG PHP_IMAGE=php:8.1.8-cli

FROM ${PHP_IMAGE}

ARG UID=1000

# Setup
RUN set -eux; \
    apt-get update; \
    apt-get upgrade -y; \
    apt-get install -y --no-install-recommends \
            libzip-dev \
            zip \
            unzip; \
    rm -rf /var/lib/apt/lists/*

RUN set -eux; \
    docker-php-ext-install zip \
    && docker-php-source delete

# Install Composer
RUN export COMPOSER_ALLOW_SUPERUSER=1

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
    php composer-setup.php --filename=composer --install-dir=/usr/local/bin/ && \
    php -r "unlink('composer-setup.php');"

# Add user
RUN adduser --disabled-password --gecos "" --no-create-home --ingroup www-data --uid ${UID} user
RUN mkdir -p /home/user/.composer/ && \
    chown user:www-data /home/user/.composer/

USER user

WORKDIR /var/www
