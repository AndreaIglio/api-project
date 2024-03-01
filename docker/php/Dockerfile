FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
        libsqlite3-dev \
    && docker-php-ext-install pdo pdo_sqlite \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
    libsqlite3-dev \
    vim \
    libicu-dev \
    zsh \
    wget  \
    zip \
    unzip \
    libzip-dev \
    && docker-php-ext-install -j5 \
        bcmath \
        intl \
        opcache \
        pdo \
        pdo_mysql \
        pdo_sqlite \
        zip

RUN sh -c "$(wget -O- https://github.com/deluan/zsh-in-docker/releases/download/v1.1.5/zsh-in-docker.sh)" --

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer