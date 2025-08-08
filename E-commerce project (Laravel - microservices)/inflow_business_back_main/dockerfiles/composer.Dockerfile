FROM composer:latest

WORKDIR /var/www/ifb_business

RUN apk add --no-cache shadow

RUN usermod -u 1000 www-data && groupmod -g 1000 www-data
USER www-data

ENTRYPOINT ["composer", "--ignore-platform-reqs"]
