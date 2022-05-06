FROM php:8.1.0RC3-cli-alpine3.14

WORKDIR /var/www/html

COPY . /var/www/html

# Install composer.
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# Install xdebug.
RUN apk --no-cache add git pcre-dev ${PHPIZE_DEPS} \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && apk del pcre-dev ${PHPIZE_DEPS}

# Configure xdebug.
RUN echo -e "xdebug.mode=debug\n" \
    >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

EXPOSE 9003

# Create a user from which all of the actions will be done inside the container.
RUN addgroup -g 1000 -S docker
RUN adduser -u 1000 -D -H -s /bin/sh -G docker docker
USER docker

# Add entrypoint for container to run without stopping.
ENTRYPOINT ["tail", "-f", "/dev/null"]
