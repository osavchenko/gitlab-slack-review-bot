FROM mariadb

ENV COMPOSER_ALLOW_SUPERUSER=1
ENV PERFORMANCE_OPTIM=false
ENV MYSQL_ROOT_PASSWORD=root_db_password

WORKDIR /var/www

RUN apt-get update
RUN apt-get install -y --no-install-recommends ca-certificates

RUN apt-get -qq update && DEBIAN_FRONTEND=noninteractive apt-get -y install \
    screen \
    gnupg \
    ca-certificates \
    nginx \
    wget \
    unzip \
    php8.3 \
    php8.3-cli \
    php8.3-intl \
    php8.3-fpm \
    php8.3-xml \
    php8.3-mbstring \
    php8.3-mysql \
    php8.3-curl &&\
    apt-get clean && \
    rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* &&\
    php -r "readfile('https://getcomposer.org/installer');" | php -- \
             --install-dir=/usr/local/bin \
             --filename=composer &&\
    echo "daemon off;" >> /etc/nginx/nginx.conf &&\
    mkdir -p /run/php

VOLUME ["/etc/mysql", "/var/lib/mysql"]

ADD . /var/www
ADD ./build/nginx/default /etc/nginx/sites-enabled/default

EXPOSE 80

RUN chmod +x /var/www/build/entrypoint.sh
RUN chown -R root:www-data /var/www/var/cache

ENTRYPOINT ["/var/www/build/entrypoint.sh"]
